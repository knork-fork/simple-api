<?php
declare(strict_types=1);

namespace App\System;

final class JsonResponse extends AbstractResponse
{
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_NO_CONTENT = 204;

    /**
     * @param mixed[] $data
     */
    public function __construct(
        array $data,
        int $statusCode = self::HTTP_OK
    ) {
        parent::__construct($data, $statusCode);
    }
}
