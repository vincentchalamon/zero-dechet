<?php

/*
 * This file is part of the Zero-Dechet project.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\Registration;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ContextBuilder implements SerializerContextBuilderInterface
{
    private $decorated;
    private $authorizationChecker;
    private $tokenStorage;
    private $propertyInfoExtractor;

    public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker, TokenStorageInterface $tokenStorage, PropertyInfoExtractorInterface $propertyInfoExtractor)
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->propertyInfoExtractor = $propertyInfoExtractor;
    }

    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $request->attributes->get('_api_resource_class');
        $hasAdmin = 0 < \count($this->propertyInfoExtractor->getProperties($resourceClass, ['serializer_groups' => [$normalization ? 'admin_output' : 'admin_input']]));
        if ($hasAdmin && $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $context['groups'][] = $normalization ? 'admin_output' : 'admin_input';
        }

        if ($request->attributes->get('data') instanceof User
            && $this->authorizationChecker->isGranted('ROLE_USER')
            && $request->attributes->get('data') === $this->tokenStorage->getToken()->getUser()
        ) {
            $context['groups'][] = $normalization ? 'owner_output' : 'owner_input';
        }

        if ($request->attributes->get('data') instanceof Registration
            && $this->authorizationChecker->isGranted('ROLE_USER')
            && $request->attributes->get('data')->getEvent()->getOrganizer() === $this->tokenStorage->getToken()->getUser()
        ) {
            $context['groups'][] = $normalization ? 'organizer_output' : 'organizer_input';
        }

        return $context;
    }
}
