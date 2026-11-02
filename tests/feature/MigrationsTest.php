<?php

namespace Dorguzen\Tests\Feature;

use Dorguzen\Core\Config\ConfigLoader;
use Dorguzen\Core\DGZ_DBAdapter;
use Dorguzen\Testing\TestCase;

/**
 * MigrationsTest proves that the framework's migrations really run on the test
 * database, and that a test run leaves the application's config cache alone.
 */
class MigrationsTest extends TestCase
{
    public function test_migrations_create_the_framework_tables()
    {
        $tables = container(DGZ_DBAdapter::class)->listTables();

        foreach (['dgz_migrations', 'dgz_jobs', 'users', 'gallery_albums'] as $table) {
            $this->assertContains($table, $tables);
        }
    }

    public function test_gallery_albums_has_the_featured_column()
    {
        $columns = container(DGZ_DBAdapter::class)->getTableSchema('gallery_albums');

        $this->assertStringContainsString('album_is_featured', json_encode($columns));
    }

    public function test_config_loader_without_cache_writes_no_cache_file()
    {
        // Uses its own config folder, because config files can only be loaded once per process
        $configDir = sys_get_temp_dir() . '/dgz_config_test_' . uniqid();
        mkdir($configDir);
        file_put_contents($configDir . '/sample.php', "<?php return ['key' => 'value'];");
        $cacheFile = $configDir . '/cache.php';

        (new ConfigLoader($configDir, $cacheFile, false))->load();

        $this->assertFileDoesNotExist($cacheFile);
        $this->assertFileDoesNotExist($cacheFile . '.meta');

        unlink($configDir . '/sample.php');
        rmdir($configDir);
    }
}
