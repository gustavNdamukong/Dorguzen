<?php
namespace Dorguzen\Core;



interface DGZ_FormatterInterface
{
    public function format(mixed $data): string;
    public function contentType(): string;
}

?>