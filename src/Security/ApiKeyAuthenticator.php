<?php

namespace App\Security;

use App\DataAccessLayer\Repository\ApiKeyRepository;
use App\Domain\Entity\ApiKey;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private ApiKeyRepository $apiKeyRepository,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('api-token');
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get('api-token');
        if (null === $apiToken) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new AuthenticationException('No API token provided');
        }

        $apiKey = $this->apiKeyRepository->findOneBy(['key' => $apiToken]);

        if (!$apiKey instanceof ApiKey) {
            throw new AuthenticationException('Invalid API token');
        }

        return new SelfValidatingPassport(new UserBadge($apiKey->getAttendee()->getUserIdentifier()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            ['code' => Response::HTTP_UNAUTHORIZED, 'message' => $exception->getMessage()],
            Response::HTTP_UNAUTHORIZED
        );
    }
}