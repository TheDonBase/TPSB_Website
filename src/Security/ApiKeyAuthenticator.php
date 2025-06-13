<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function supports(Request $request): ?bool
    {
        $isApiRoute = str_starts_with($request->getPathInfo(), '/api/');
        error_log('API Route check: ' . $request->getPathInfo() . ' - Is API: ' . ($isApiRoute ? 'true' : 'false'));
        return $isApiRoute;
    }

    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get('X-API-Key');
        error_log('Received API Key: ' . ($apiKey ? 'present' : 'missing'));

        if (null === $apiKey) {
            throw new CustomUserMessageAuthenticationException('No API key provided');
        }

        if ($this->apiKey !== $apiKey) {
            throw new CustomUserMessageAuthenticationException('Invalid API key');
        }

        return new SelfValidatingPassport(new UserBadge($apiKey, function() {
            return new ApiKeyUser(['ROLE_BOT']);
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'message' => $exception->getMessage()
        ], Response::HTTP_UNAUTHORIZED);
    }
}
