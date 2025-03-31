<?php
declare(strict_types=1);

namespace App\Response;

final class NoContentResponse extends Response
{
    public function __construct(
        private int $statusCode = Response::HTTP_NO_CONTENT
    ) {
    }

    public function output(?int $statusCode = null): void
    {
        http_response_code($statusCode ?? $this->statusCode);
    }
}
