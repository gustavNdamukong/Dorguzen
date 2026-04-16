<?php

namespace Dorguzen\Config;

use Dorguzen\Models\BaseSettings;
use Dorguzen\Core\DGZ_Exception;


/**
 * Config only 
 *  -stores, 
 *  -gets & 
 *  -sets config values
 * 
 */
class Config
{
    
    private $baseSettings = [];

    protected array $items = [];

    public function __construct(array $items = [])
    {
        // do not allow the Config object to be used wrongly
        if ($items === null) {
            throw new DGZ_Exception(
                'Wrong use of Config object',
                DGZ_Exception::EXCEPTION,
                "Config must either be resolved from container, GLOBALS['config'], or golal helper config(), not instantiated directly."
            );
        }
        $this->items = $items;
    }



    public function all(): array
    {
        return $this->items;
    }



    public function get(string $key, mixed $default = null): mixed
    {
        if ($key === '') {
            return $this->items;
        }

        $segments = explode('.', $key);
        $value = $this->items;

        foreach ($segments as $segment) 
        {
            if (is_array($value) && array_key_exists($segment, $value)) 
            {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }



    public function has(string $key): bool
    {
        return $this->get($key, '__dgz_missing__') !== '__dgz_missing__';
    }



    public function set(string $key, $value): void
    {
        $segments = explode('.', $key);
        $cursor = &$this->items;

        foreach ($segments as $segment) {
            if (!isset($cursor[$segment]) || !is_array($cursor[$segment])) {
                $cursor[$segment] = [];
            }
            $cursor = &$cursor[$segment];
        }

        $cursor = $value;
    }

    


    /** 
     * Get the configuration of a specific config file by its key.
     * If no key is passed, it will return the configuration of the main config file (configs/app.php).
     * 
     * Example usage:
     * 
     *      $DBcredentials = $config->getConfig('database.DBcredentials');
     *   OR
     *      $DBcredentials = $config->getConfig('database.Neo4jCredentials');
     */
    public function getConfig($key = '')
    {
        if ($key != '')
        {
            return $this->get($key);
        } else {
            return $this->get('app'); 
        }
    }



    /**
     * This will specifically grab and return from the baseSettings, only the color theme of your application.
     * The baseSettings DB table has various color themes you can choose from. Whatever color you have set as
     * the color theme for your application will be pulled by this method for you to use anywhere in your app,
     * for example your layouts
     *
     */
    public function getAppColorTheme()
    {
        $colorTheme = $this->getBaseSettings()['app_color_theme'];
        return $colorTheme;
    }
    

    public function getFileRootPath()
    {
        // When running via `php dgz serve` (PHP's built-in server), asset URLs must
        // point to the built-in server, not the MAMP/Apache URL stored in .env.
        // PHP exposes the correct host and port via $_SERVER automatically.
        if (php_sapi_name() === 'cli-server') {
            $host = $_SERVER['SERVER_NAME'] ?? 'localhost';
            $port = $_SERVER['SERVER_PORT'] ?? '8000';
            return "http://{$host}:{$port}/";
        }

        if ($this->get('app.live') == 'true')
        {
            return $this->get('app.fileRootPathLive');
        }
        else
        {
            return $this->get('app.fileRootPathLocal');
        }
    }



    /**
     * Get the URL to the home page of the app to link to
     */
    public function getHomePage()
    {
        if ($this->get('app.live') == 'true')
        {
            return $this->get('app.liveUrl');
        }
        else
        {
            return $this->get('app.localUrl');
        }
    }



    /**
     * Get the URL to the live home page of the app using SSL
     */
    public function getHomePageSecure()
    {
        return $this->get('app.liveUrlSecure');
    }



    public function getNumLatestPostsToShow()
    {
        return $this->get('app.numLatestPostsToShow');
    }



    /**
     * This method gets the DB settings from the baseSettings table and stores them in this class's
     * private property $baseSettings. This ensures that you now have here all your application settings
     * file-driven (in this Settings class) and database-driven, all in one place.
     *
     */
    private function setBaseSettings()
    {
        $dbSettings = container(BaseSettings::class);
        $rawSettings = $dbSettings->getAll('settings_id');
        foreach ($rawSettings as $raw)
        {
            $this->baseSettings[$raw['settings_name']] = $raw['settings_value'];
        }
    }



    /**
     * Returns the database-driven settings stored in this class's private property $baseSettings.
     * If that member has not yet received the database-driven settings, it loads that data from the DB
     * into that $baseSettings property before returning its contents. This is lazy-loaded, so you should 
     * call this as needed.
     *
     * @return array
     */
    public function getBaseSettings()
    {
        if ($this->baseSettings)
        {
            return $this->baseSettings;
        }
        else
        {
            $this->setBaseSettings();
            return $this->baseSettings;
        }
    }
}