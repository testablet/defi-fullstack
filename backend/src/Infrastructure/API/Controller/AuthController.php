<?php

declare(strict_types=1);

namespace App\Infrastructure\API\Controller;

use App\Infrastructure\Security\JwtUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtManager
    ) {
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Login credentials',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'username', type: 'string', example: 'admin'),
                new OA\Property(property: 'password', type: 'string', example: 'password'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'JWT token',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'token', type: 'string'),
            ]
        )
    )]
    public function login(Request $request): JsonResponse
    {
        // For demo purposes, accept any credentials and generate a JWT token
        // In production, validate credentials against a user database
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['username']) || !isset($data['password'])) {
            return $this->json(['error' => 'Missing credentials'], 400);
        }

        // Create a user and generate JWT token
        $user = new JwtUser($data['username'] ?? 'admin');
        $token = $this->jwtManager->create($user);
        
        return $this->json([
            'token' => $token,
        ]);
    }
}



