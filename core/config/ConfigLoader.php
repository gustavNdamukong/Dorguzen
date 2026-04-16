<?php 

namespace Dorguzen\Core\Config;


use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use RuntimeException;

/**
 * ConfigLoader does these:
 *      -scans & recursively loads files from /configs/ safely
 *      -detects file extension
 *      -loads file content using the correct parser
 *      -merges everything into one array
 *      -caches the data
*/
class ConfigLoader
{
    protected string $configPath;
    protected string $cachePath;
    protected bool $useCache;
    protected array $config = [];


    /**
     * @param string $configPath Path to configs directory (e.g. __DIR__ . '/../../configs')
     * @param string $cachePath Path to cache file (bootstrap/cache/config.php)
     * @param bool $useCache Whether to use cache when possible
     */
    public function __construct(string $configPath, string $cachePath, bool $useCache = true)
    {
        $this->configPath = rtrim($configPath, DIRECTORY_SEPARATOR);
        $this->cachePath = $cachePath;
        $this->useCache = $useCache;
    }



    /**
     * Load and return merged config array.
     */
    public function load(): array
    {
        if ($this->useCache && $this->isCacheFresh()) {
            $cached = $this->loadCache();
            if (is_array($cached)) {
                return $cached;
            }
        }

        $this->config = [];

        if (!is_dir($this->configPath)) {
            throw new RuntimeException("Config path does not exist: {$this->configPath}");
        }

        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->configPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        // match files with ext php, xml, yml, yaml
        $regex = new RegexIterator($it, '/\.(php|xml|yml|yaml)$/i', RegexIterator::MATCH);

        foreach ($regex as $file) {
            /** @var \SplFileInfo $file */
            $filePath = $file->getPathname();
            $ext = strtolower($file->getExtension());
            $relative = ltrim(str_replace($this->configPath, '', $file->getPath()), DIRECTORY_SEPARATOR);
            $keyBase = $file->getBasename('.' . $ext);

            // build dotted key from subfolders: modules/auth.yaml => modules.auth
            $prefix = $relative !== '' ? str_replace(DIRECTORY_SEPARATOR, '.', $relative) . '.' : '';
            $key = $prefix . $keyBase;

            $data = null;
            switch ($ext) {
                case 'php':
                    $data = $this->loadPhp($filePath);
                    break;
                case 'xml':
                    $data = $this->loadXml($filePath);
                    break;
                case 'yml':
                case 'yaml':
                    $data = $this->loadYaml($filePath);
                    break;
            }

            if (!is_array($data)) {
                // ensure every config file yields an array
                throw new RuntimeException("Config file must return an array or parse into an array: {$filePath}");
            }

            // merge under the dotted key
            $this->setByDottedKey($key, $data);
        }

        // write cache
        $this->writeCache($this->config);

        return $this->config;
        
    }



    protected function loadPhp(string $filePath): array
    {
        // using include in isolated scope
        $data = include $filePath;
        if ($data === 1) {
            // include returns 1 if file has no return statement — treat as empty
            return [];
        }
        return is_array($data) ? $data : [];
    }

    protected function loadXml(string $filePath): array
    {
        $xml = simplexml_load_file($filePath, "SimpleXMLElement", LIBXML_NOCDATA);
        if ($xml === false) {
            throw new RuntimeException("Failed to parse XML config: {$filePath}");
        }
        return json_decode(json_encode($xml), true) ?: [];
    }

    protected function loadYaml(string $filePath): array
    {
        // prefer native yaml extension
        if (function_exists('yaml_parse_file')) {
            $parsed = @yaml_parse_file($filePath);
            return is_array($parsed) ? $parsed : [];
        }

        // fallback to Symfony Yaml if available
        if (class_exists('\Symfony\Component\Yaml\Yaml')) {
            $parsed = \Symfony\Component\Yaml\Yaml::parseFile($filePath);
            return is_array($parsed) ? $parsed : [];
        }

        throw new RuntimeException("No YAML parser available. Install ext-yaml or symfony/yaml via Composer to parse {$filePath}");
    }

    protected function setByDottedKey(string $dotted, array $value): void
    {
        $parts = explode('.', $dotted);
        $cursor = &$this->config;
        while (count($parts) > 1) {
            $p = array_shift($parts);
            if (!isset($cursor[$p]) || !is_array($cursor[$p])) {
                $cursor[$p] = [];
            }
            $cursor = &$cursor[$p];
        }
        $last = array_shift($parts);
        // merge existing keys with new data (deep)
        $cursor[$last] = $this->arrayMergeRecursiveDistinct($cursor[$last] ?? [], $value);
    }

    protected function arrayMergeRecursiveDistinct(array $a, array $b): array
    {
        $merged = $a;

        foreach ($b as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->arrayMergeRecursiveDistinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Cache handling
     */
    protected function isCacheFresh(): bool
    {
        if (!file_exists($this->cachePath)) {
            return false;
        }

        $metaPath = $this->cachePath . '.meta';
        if (!file_exists($metaPath)) {
            return false;
        }

        $meta = @unserialize(@file_get_contents($metaPath));
        if (!is_array($meta) || !isset($meta['fingerprint'])) {
            return false;
        }

        $current = $this->computeFingerprint();
        return hash_equals($meta['fingerprint'], $current);
    }

    protected function loadCache(): ?array
    {
        if (!file_exists($this->cachePath)) {
            return null;
        }

        /** The cache file returns an array */
        $data = include $this->cachePath;
        return is_array($data) ? $data : null;
    }

    protected function writeCache(array $data): void
    {
        $dir = dirname($this->cachePath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        $export = '<?php return ' . var_export($data, true) . ';' . PHP_EOL;
        file_put_contents($this->cachePath, $export, LOCK_EX);

        $meta = [
            'fingerprint' => $this->computeFingerprint(),
            'generated_at' => time(),
        ];

        file_put_contents($this->cachePath . '.meta', serialize($meta), LOCK_EX);
    }

    /**
     * computeFingerprint() works out if the app's configuration data (cached) has changed. 
     * The cache will be refreshed if so. 
     * The fingerprint changes if ANY of these change:
     *      -A config file is added
     *      -A config file is deleted
     *      -A config file is modified
     *      -A config file grows or shrinks
     * 
     * @return string
     */
    protected function computeFingerprint(): string
    {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->configPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $hashParts = [];
        foreach ($it as $file) {
            /** @var \SplFileInfo $file */
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, ['php','xml','yml','yaml'])) {
                continue;
            }
            $path = $file->getPathname();
            $hashParts[] = $path . '|' . filemtime($path) . '|' . filesize($path);
        }

        sort($hashParts);
        return sha1(implode("\n", $hashParts));
    }
}