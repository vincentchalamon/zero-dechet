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
            'operationId' => 'login',
            'summary' => 'Creates access token',
            'responses' => [
                200 => [
                    'description' => 'Valid credentials',
                    'content' => [
                        'application/ld+json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/User-user:read',
                            ],
                        ],
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/User-user:read',
                            ],
                        ],
                        'text/html' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/User-user:read',
                            ],
                        ],
                    ],
                ],
                401 => [
                    'description' => 'Invalid credentials',
                ],
            ],
            'requestBody' => [
                'description' => 'Login',
                'content' => [
                    'application/ld+json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/User:login',
                        ],
                    ],
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/User:login',
                        ],
                    ],
                ],
            ],
        ];
        $docs['components']['schemas']['User:login'] = [
            'type' => 'object',
            'description' => '',
            'required' => ['username', 'password'],
            'properties' => [
                'username' => [
                    'type' => 'string',
                ],
                'password' => [
                    'type' => 'string',
                ],
            ],
        ];

        // Add POST /logout path
        $docs['paths']['/logout']['post'] = [
            'tags' => ['Security'],
            'operationId' => 'logout',
            'summary' => 'Revokes access token',
            'responses' => [
                204 => [
                    'description' => 'Access token successfully revoked',
                ],
            ],
        ];

        // Add POST /forgot-password/ path
        $docs['tags'][] = ['name' => 'Forgot password'];
        $docs['paths']['/forgot-password/']['post'] = [
            'tags' => ['Forgot password'],
            'operationId' => 'postForgotPassword',
            'summary' => 'Generates a token and send email',
            'responses' => [
                204 => [
                    'description' => 'Valid email address, no matter if user exists or not',
                ],
                400 => [
                    'description' => 'Missing email parameter or invalid format',
                ],
            ],
            'requestBody' => [
                'description' => 'Request a new password',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/ForgotPassword:request',
                        ],
                    ],
                ],
            ],
        ];
        $docs['components']['schemas']['ForgotPassword:request'] = [
            'type' => 'object',
            'description' => '',
            'required' => ['email'],
            'properties' => [
                'email' => [
                    'type' => 'string',
                ],
            ],
        ];

        // Add GET /forgot-password/{token} path
        $docs['paths']['/forgot-password/{token}']['get'] = [
            'tags' => ['Forgot password'],
            'operationId' => 'getForgotPassword',
            'summary' => 'Validates token',
            'responses' => [
                200 => [
                    'description' => 'Authenticated user',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/ForgotPassword:validate',
                            ],
                        ],
                    ],
                ],
                404 => [
                    'description' => 'Token not found or expired',
                ],
            ],
            'parameters' => [
                [
                    'name' => 'token',
                    'in' => 'path',
                    'required' => true,
                    'schema' => [
                        'type' => 'string',
                    ],
                ],
            ],
        ];
        $docs['components']['schemas']['ForgotPassword:validate'] = [
            'type' => 'object',
            'description' => '',
        ];

        // Add POST /forgot-password/{token} path
        $docs['paths']['/forgot-password/{token}']['post'] = [
            'tags' => ['Forgot password'],
            'operationId' => 'postForgotPasswordToken',
            'summary' => 'Resets user password from token',
            'responses' => [
                204 => [
                    'description' => 'Email address format valid, no matter if user exists or not',
                ],
                400 => [
                    'description' => 'Missing password parameter',
                ],
                404 => [
                    'description' => 'Token not found',
                ],
            ],
            'parameters' => [
                [
                    'name' => 'token',
                    'in' => 'path',
                    'required' => true,
                    'schema' => [
                        'type' => 'string',
                    ],
                ],
            ],
            'requestBody' => [
                'description' => 'Reset password',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/ForgotPassword:reset',
                        ],
                    ],
                ],
            ],
        ];
        $docs['components']['schemas']['ForgotPassword:reset'] = [
            'type' => 'object',
            'description' => '',
            'required' => ['password'],
            'properties' => [
                'password' => [
                    'type' => 'string',
                ],
            ],
        ];

        return $docs;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
