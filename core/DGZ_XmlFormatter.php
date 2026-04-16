<?php
namespace Dorguzen\Core;



class DGZ_XmlFormatter implements DGZ_FormatterInterface
{
    public function format(mixed $data): string
    {
        $xml = new \SimpleXMLElement('<root/>');
        $this->arrayToXml($data, $xml);
        return $xml->asXML();
    }

    private function arrayToXml(array $data, &$xml)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $subnode = $xml->addChild($key);
                $this->arrayToXml($value, $subnode);
            } else {
                // Escape to make sure we stay XML-safe
                $xml->addChild($key, htmlspecialchars((string)$value, ENT_XML1, 'UTF-8'));
            }
        }
    }

    public function contentType(): string
    {
        return 'application/xml';
    }
}

?>