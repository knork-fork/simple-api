<?php
declare(strict_types=1);

namespace App\Dto;

final class TestRequestDto extends AbstractRequestDto
{
    public function __construct(
        public readonly string $body_parameter_1,
        public readonly ?int $body_parameter_2,
        public readonly ?string $optional_string_parameter = null,
    ) {
    }
}
