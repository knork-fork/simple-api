<?php
declare(strict_types=1);

namespace App\System;

abstract class AbstractResponse
{
    /**
     * @param mixed[] $data
     */
    public function __construct(
        private array $data,
        private int $statusCode
    ) {
    }

    public function output(): void
    {
        header('Content-Type: application/json');
        http_response_code($this->statusCode);
        echo json_encode($this->data);
    }
}
