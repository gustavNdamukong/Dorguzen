<?php
namespace Dorguzen\Core;

use Dorguzen\Core\DGZ_JsonFormatter;
use Dorguzen\Core\DGZ_Logger;


class DGZ_Response
{
    protected int $status = 200;
    protected array $headers = [];
    protected mixed $data = null;
    protected DGZ_FormatterInterface $formatter;

    public function __construct(mixed $data = null, int $status = 200, ?DGZ_FormatterInterface $formatter = null)
    {
        $this->data = $data;
        $this->status = $status;
        $this->formatter = $formatter ?? new DGZ_JsonFormatter(); 
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setData(mixed $data): self
    {
        $this->data = $data;
        return $this;
    }

     public function getData(): mixed
    {
        return $this->data;
    }

    public function setFormatter(DGZ_FormatterInterface $formatter): self
    {
        $this->formatter = $formatter;
        return $this;
    }


    public function json(mixed $data, int $status = 200): self
    {
        $this->setStatus($status);
        $this->setFormatter(new DGZ_JsonFormatter());
        $this->setData($data);

        return $this;
    }


    public function send()
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // set the header based on the current formatter
        $this->setHeader('Content-Type', $this->formatter->contentType());
        //--------------------
        $json = json_encode($this->data, JSON_PRETTY_PRINT);
        if ($json === false) {
            DGZ_Logger::error('JSON encoding failed', ['reason' => json_last_error_msg()]);
            http_response_code(500);
            echo json_encode(['error' => 'An internal error occurred while encoding the response.']);
            return;
        }
        //--------------------
        echo $this->formatter->format($this->data);
    }


    /**
     * eset() is handy fo clearing responses between requests, especially 
     * during testing. 
     * @return void
     */
    public function reset(): void
    {
        $this->status = 200;
        $this->headers = [];
        $this->data = null;
    }
}

?>