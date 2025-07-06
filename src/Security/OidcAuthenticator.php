<?php

namespace App\Security;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class OidcAuthenticator extends AbstractAuthenticator
{
    private string $jwksUri;

    public function __construct(private string $issuer)
    {
        $this->issuer = rtrim($this->issuer, '/') . '/';
        $this->jwksUri = $this->issuer . 'jwks/';
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('oidc-token');
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get('oidc-token');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new AuthenticationException('No bearer token found.');
        }

        $jwt = substr($authHeader, 7);
        $claims = $this->validateToken($jwt);

        return new SelfValidatingPassport(
            new UserBadge($claims['sub'], function () use ($claims) {
                return new OidcUser($claims['sub'], $claims);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // continue
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error'   => 'Unauthorized',
            'message' => $exception->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }

    private function validateToken(string $jwt): array
    {
        $jwks = json_decode(file_get_contents($this->jwksUri), true, 512, JSON_THROW_ON_ERROR);
        $keys = JWK::parseKeySet($jwks);

        $decoded = JWT::decode($jwt, $keys);
        $claims = (array)$decoded;

        if (($claims['iss'] ?? null) !== $this->issuer) {
            throw new AuthenticationException('Invalid issuer');
        }

        if (($claims['exp'] ?? 0) < time()) {
            throw new AuthenticationException('Token expired');
        }

        return $claims;
    }
}