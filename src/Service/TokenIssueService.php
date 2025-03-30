<?php
declare(strict_types=1);

namespace App\Service;

use App\Dto\Auth\TokenIssueRequest;
use App\Dto\Auth\TokenIssueResponse;
use App\Entity\Token;
use App\Entity\User;
use App\Exception\BadRequestException;
use RuntimeException;

final class TokenIssueService
{
    public function issueToken(TokenIssueRequest $tokenIssueRequest): TokenIssueResponse
    {
        $user = $this->getUserIfExists($tokenIssueRequest->username);

        if ($user === null) {
            $user = $this->createUser($tokenIssueRequest->username, $tokenIssueRequest->secret);
        } else {
            if (!password_verify($tokenIssueRequest->secret, $user->secret_hash)) {
                // Intentionally misleading error message to prevent user enumeration
                throw new BadRequestException('Failed to save user');
            }
        }

        $token = Token::getToken();
        $token->user_id = (int) $user->id;
        $token->description = $tokenIssueRequest->description;
        $token->expires_at = $tokenIssueRequest->expires;
        $token->save();
        if ($token->id === null || $token->getUnhashedToken() === null) {
            throw new RuntimeException('Failed to save token');
        }

        return new TokenIssueResponse(
            $token->token_id,
            $token->getUnhashedToken(),
            $tokenIssueRequest->description,
            $tokenIssueRequest->expires
        );
    }

    private function getUserIfExists(string $username): ?User
    {
        try {
            $user = (new User())->getBy('username', $username);
        } catch (RuntimeException) {
            return null;
        }

        return $user;
    }

    private function createUser(string $username, string $secret): User
    {
        $user = (new User())->hydrate([
            'username' => $username,
            'secret_hash' => password_hash($secret, \PASSWORD_BCRYPT),
        ]);

        try {
            $user->save();
        } catch (RuntimeException $e) {
            throw new BadRequestException('Failed to save user');
        }
        if ($user->id === null) {
            throw new RuntimeException('Failed to save user');
        }

        return $user;
    }
}
