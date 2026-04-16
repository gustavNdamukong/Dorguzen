<?php

namespace Dorguzen\Core\Exceptions;

use Exception;

class ValidationException extends Exception 
{
    protected array $errors;
    protected array $input;
    protected array $validationErrorMessages;
    protected int $errorCode;
    public ?string $redirectTo;

    public function __construct(
        array $errors = [], 
        array $input = [], 
        array $validationErrorMessages = [],
        string $message = "Validation failed", 
        int $errorCode = 0,
        ?string $redirectTo = null
    )
    {
        parent::__construct($message);
        $this->errors = $errors;
        $this->input = $input;
        $this->validationErrorMessages = $validationErrorMessages;
        $this->errorCode = $errorCode;
        $this->redirectTo = $redirectTo;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getValidationErrorMessages(): array
    {
        return $this->validationErrorMessages;
    }

    public function getInput(): array
    {
        return $this->input;
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function getRedirectTo(): ?string
    {
        return $this->redirectTo;
    }
}