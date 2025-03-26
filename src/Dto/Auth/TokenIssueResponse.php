<?php
declare(strict_types=1);

namespace App\Dto\Auth;

use App\Dto\AbstractResponseDto;

final class TokenIssueResponse extends AbstractResponseDto
{
    public function __construct(
        public readonly string $token_id,
        public readonly string $token,
        public readonly ?string $description = null,
        public readonly ?string $expires = null,
    ) {
    }
}
