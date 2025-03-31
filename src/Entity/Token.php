<?php
declare(strict_types=1);

namespace App\Entity;

use App\System\Auth;
use App\System\Database\Entity;
use App\System\Enum\HashMethod;

final class Token extends Entity
{
    public int $user_id;
    public string $token_hash;
    public string $token_id;
    public string $token_lookup;
    public ?string $description = null;
    public ?string $expires_at = null;

    private string $token;

    public static function getToken(): self
    {
        $token = new self();

        $token->generateToken();

        return $token;
    }

    public function getUnhashedToken(): ?string
    {
        if (isset($this->token)) {
            return $this->token;
        }

        return null;
    }

    private function generateToken(): void
    {
        $this->token = bin2hex(random_bytes(32));
        $this->token_hash = Auth::getHash($this->token, HashMethod::HMAC);
        $this->token_id = bin2hex(random_bytes(8));

        // last 12 characters of the token are used for lookup (database querying)
        $this->token_lookup = substr($this->token, -12);
    }
}
