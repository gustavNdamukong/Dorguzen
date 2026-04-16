<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;


class TextPool extends BasePool
{
    protected static string $lorem = 
        'Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua';



    /**
     * Generate a random sentence.
     */
    public static function sentence(int $words = 8): string
    {
        $wordsArray = explode(' ', static::$lorem);
        shuffle($wordsArray);

        $slice = array_slice($wordsArray, 0, max(3, $words));

        return ucfirst(implode(' ', $slice)) . '.';
    }



    /**
     * Generate a random paragraph.
     */
    public static function paragraph(int $sentences = 3): string
    {
        $output = [];

        for ($i = 0; $i < max(1, $sentences); $i++) {
            $output[] = static::sentence(rand(6, 12));
        }

        return implode(' ', $output);
    }



    /**
     * Generate random text with approximate character length.
     */
    public static function text(int $length = 200): string
    {
        $text = '';

        while (strlen($text) < $length) {
            $text .= ' ' . static::sentence(rand(6, 12));
        }

        return trim(substr($text, 0, $length));
    }
}