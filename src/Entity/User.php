<?php
declare(strict_types=1);

namespace App\Entity;

use App\System\Database\Entity;

final class User extends Entity
{
    public string $username;
    public string $secret_hash;
}
