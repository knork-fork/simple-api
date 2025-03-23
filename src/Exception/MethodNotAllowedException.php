<?php
declare(strict_types=1);

namespace App\Exception;

use App\Response\Response;
use Exception;
use Throwable;

final class MethodNotAllowedException extends Exception
{
    public function __construct(
        string $message = 'Method Not Allowed',
        int $code = Response::HTTP_METHOD_NOT_ALLOWED,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
