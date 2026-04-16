<?php
namespace Dorguzen\Core;



class DGZ_JsonFormatter implements DGZ_FormatterInterface
{
    public function format(mixed $data): string
    {
        // Ensure valid UTF-8 encoding recursively
        $data = $this->utf8ize($data);
        $json = json_encode($data, JSON_PRETTY_PRINT);

        if ($json === false) {
            return json_encode([
                'error' => 'JSON encoding failed',
                'message' => json_last_error_msg(),
                'data_type' => gettype($data)
            ], JSON_PRETTY_PRINT);
        }
        return $json;
    }

    public function contentType(): string
    {
        return 'application/json';
    }


    /**
     * Safely sanitize all data before encoding
     * @param mixed $mixed
     */
    private function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = $this->utf8ize($value);
        }
    } elseif (is_object($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed->$key = $this->utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        if (!mb_check_encoding($mixed, 'UTF-8')) {
            $mixed = mb_convert_encoding($mixed, 'UTF-8', 'UTF-8');
        }
    }
    return $mixed;
}
}

?>