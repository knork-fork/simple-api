<?php
declare(strict_types=1);

namespace App\Exception;

use App\Response\Response;
use Exception;
use Throwable;

final class UnauthorizedException extends Exception
{
    public function __construct(
        string $message = 'Unauthorized',
        int $code = Response::HTTP_UNAUTHORIZED,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
