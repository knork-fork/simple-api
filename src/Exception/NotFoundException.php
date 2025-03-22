<?php
declare(strict_types=1);

namespace App\Exception;

use App\System\ExceptionResponse;
use Exception;
use Throwable;

final class NotFoundException extends Exception
{
    public function __construct(
        string $message = 'Not Found',
        int $code = ExceptionResponse::HTTP_NOT_FOUND,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
