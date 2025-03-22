<?php
declare(strict_types=1);

namespace App\System;

use Throwable;

final class ExceptionResponse extends AbstractResponse
{
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;

    public function __construct(
        private Throwable $exception,
        public readonly bool $suppressThrow = false,
    ) {
        parent::__construct(
            ['error' => $exception->getMessage()],
            (int) $this->exception->getCode()
        );
    }
}
