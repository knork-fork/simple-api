<?php
declare(strict_types=1);

namespace App\Controller;

use App\System\JsonResponse;

final class StatusController
{
    public static function status(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'ok',
        ]);
    }
}
