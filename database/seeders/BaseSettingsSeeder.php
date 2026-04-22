<?php

namespace Dorguzen\Database\Seeders;

use Dorguzen\Core\Database\Seeders\Seeder;

/**
 * Seeds your application's baseSettings table with some default values.
 */
class BaseSettingsSeeder extends Seeder
{
    protected string $table = 'baseSettings';

    public function run(): void
    {
        $key = env('DB_KEY', '');

        $sql = "INSERT IGNORE INTO `baseSettings` (
                    `settings_name`, `settings_value`) 
                VALUES ('show_brand_slider', 'true'),
                ('brand_slider_source', 'assets/images/gallery'),
                ('app_color_theme', 'blue')";

        $this->db->execute($sql);
    }

    public function getTable(): string
    {
        return $this->table;
    }
}