<?php

/*
 * This file is part of the Zero Dechet project.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class DocumentationNormalizer implements NormalizerInterface
{
    private $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);

        // Add POST /login path
        $docs['tags'][] = ['name' => 'Security'];
        $docs['paths']['/login']['post'] = [
            'tags' => ['Security'],
            'summary' => 'Creates access token.',
            'produces' => ['application/ld+json'],
            'parameters' => [
                [
                    'name' => 'credentials',
                    'in' => 'body',
                    'description' => 'The credentials',
                    'schema' => ['$ref' => '#/definitions/Login'],
                ],
            ],
            'responses' => [
                200 => [
                    'description' => 'Valid credentials',
                    'schema' => ['$ref' => '#/definitions/User-user:read'],
                ],
                401 => ['description' => 'Invalid credentials'],
            ],
        ];
        $docs['definitions']['Login'] = [
            'type' => 'object',
            'required' => ['username', 'password'],
            'properties' => [
                'username' => ['type' => 'string'],
                'password' => ['type' => 'string'],
            ],
        ];

        // Add POST /logout path
        $docs['paths']['/logout']['post'] = [
            'tags' => ['Security'],
            'summary' => 'Revokes access token.',
            'produces' => ['application/ld+json', 'application/json', 'text/html'],
            'responses' => [
                204 => ['description' => 'Access token successfully revoked'],
            ],
        ];

        // Add POST /forgot-password/ path
        $docs['tags'][] = ['name' => 'Forgot password'];
        $docs['paths']['/forgot-password/']['post'] = [
            'tags' => ['Forgot password'],
            'summary' => 'Generates a token and send email.',
            'produces' => ['application/json'],
            'parameters' => [
                [
                    'name' => 'email',
                    'in' => 'body',
                    'description' => 'The user email address',
                    'schema' => ['$ref' => '#/definitions/RequestPassword'],
                ],
            ],
            'responses' => [
                204 => ['description' => 'Valid email address, no matter if user exists or not'],
                400 => ['description' => 'Missing email parameter or invalid format'],
            ],
        ];
        $docs['definitions']['RequestPassword'] = [
            'type' => 'object',
            'required' => ['email'],
            'properties' => [
                'email' => ['type' => 'string'],
            ],
        ];

        // Add GET /forgot-password/{token} path
        $docs['paths']['/forgot-password/{token}']['get'] = [
            'tags' => ['Forgot password'],
            'summary' => 'Validates token.',
            'produces' => ['application/json'],
            'parameters' => [
                [
                    'name' => 'token',
                    'in' => 'path',
                    'required' => true,
                    'type' => 'string',
                ],
            ],
            'responses' => [
                200 => [
                    'description' => 'Authenticated user',
                    'schema' => [
                        '$ref' => '#/definitions/User-user:read',
                    ],
                ],
                400 => ['description' => 'Missing email parameter or invalid format'],
                404 => ['description' => 'Token not found'],
            ],
        ];

        // Add POST /forgot-password/{token} path
        $docs['paths']['/forgot-password/{token}']['post'] = [
            'tags' => ['Forgot password'],
            'summary' => 'Resets user password from token.',
            'produces' => ['application/json'],
            'parameters' => [
                [
                    'name' => 'token',
                    'in' => 'path',
                    'required' => true,
                    'type' => 'string',
                ],
                [
                    'name' => 'password',
                    'in' => 'body',
                    'description' => 'The user new password',
                    'schema' => ['$ref' => '#/definitions/ResetPassword'],
                ],
            ],
            'responses' => [
                204 => ['description' => 'Email address format valid, no matter if user exists or not'],
                400 => ['description' => 'Missing password parameter'],
                404 => ['description' => 'Token not found'],
            ],
        ];
        $docs['definitions']['ResetPassword'] = [
            'type' => 'object',
            'required' => ['password'],
            'properties' => [
                'password' => ['type' => 'string'],
            ],
        ];

        return $docs;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
