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

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Entity\UserQuiz;
use Doctrine\Common\Persistence\ManagerRegistry;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class UserEventSubscriber implements EventSubscriberInterface
{
    private $registry;
    private $tokenStorage;
    private $userPasswordEncoder;

    public function __construct(ManagerRegistry $registry, TokenStorageInterface $tokenStorage, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->registry = $registry;
        $this->tokenStorage = $tokenStorage;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['generateUserCanonicalFields', EventPriorities::PRE_VALIDATE],
                ['setUser', EventPriorities::PRE_VALIDATE],
                ['encodePassword', EventPriorities::POST_VALIDATE],
            ],
        ];
    }

    public function generateUserCanonicalFields(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $user = $request->attributes->get('data');
        if ($request->isMethodSafe(false) || !$user instanceof User || null === $user->getEmail()) {
            return;
        }

        $encoding = \mb_detect_encoding($user->getEmail(), \mb_detect_order(), true);
        $user->setEmailCanonical(Urlizer::unaccent(\mb_convert_case($user->getEmail(), MB_CASE_LOWER, $encoding ?: \mb_internal_encoding())));
    }

    public function setUser(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $data = $request->attributes->get('data');
        if (!$request->isMethod(Request::METHOD_POST) || UserQuiz::class !== $request->attributes->get('_api_resource_class') || null !== $data->getUser()) {
            return;
        }

        $data->setUser($this->tokenStorage->getToken()->getUser());
    }

    public function encodePassword(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $data = $request->attributes->get('data');
        if ($request->isMethodSafe(false)
            || $request->isMethod(Request::METHOD_DELETE)
            || !$data instanceof User
        ) {
            return;
        }

        $data->setPassword($this->userPasswordEncoder->encodePassword($data, $data->getPlainPassword()));
        $data->eraseCredentials();
    }
}
