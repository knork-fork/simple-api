<?php
declare(strict_types=1);

namespace App\Dto\Auth;

use App\Dto\AbstractRequestDto;

final class TokenIssueRequest extends AbstractRequestDto
{
    public function __construct(
        public readonly string $username,
        public readonly string $secret,
        public readonly ?string $description = null,
        public readonly ?string $expires = null,
        public readonly bool $is_read_only = false,
        // public readonly mixed $scope, permissions etc...
    ) {
    }
}
