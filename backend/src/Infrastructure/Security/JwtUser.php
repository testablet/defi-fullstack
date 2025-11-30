<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

class JwtUser implements JWTUserInterface
{
    public function __construct(
        private readonly string $identifier
    ) {
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }

    public static function createFromPayload($username, array $payload): JWTUserInterface
    {
        return new self($username);
    }
}

