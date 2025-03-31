<?php
declare(strict_types=1);

namespace App\System\Enum;

enum HashMethod
{
    /** Secure, used for hashing passwords */
    case Bcrypt;
    /** Fast, used for hashing generated high-entropy tokens */
    case HMAC;
}
