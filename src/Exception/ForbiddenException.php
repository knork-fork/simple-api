<?php
declare(strict_types=1);

namespace App\Exception;

use App\Response\Response;
use Exception;
use Throwable;

final class ForbiddenException extends Exception
{
    public function __construct(
        string $message = 'Forbidden',
        int $code = Response::HTTP_FORBIDDEN,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
