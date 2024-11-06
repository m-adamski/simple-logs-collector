<?php

namespace App\Security;

use App\Repository\ClientRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface {
    public function __construct(
        private readonly ClientRepository $clientRepository,
    ) {}

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge {
        if (null !== ($client = $this->clientRepository->findOneBy(["secretToken" => $accessToken, "active" => true]))) {
            return new UserBadge($client->getId());
        }

        throw new BadCredentialsException("Attempt to gain access using invalid authorization token");
    }
}
