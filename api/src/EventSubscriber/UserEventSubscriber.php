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

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Contact;
use App\Entity\Event;
use App\Entity\Profile;
use App\Entity\Registration;
use App\Entity\User;
use App\Entity\UserQuiz;
use App\Security\PasswordEncoder;
use Doctrine\Common\Persistence\ManagerRegistry;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class UserEventSubscriber implements EventSubscriberInterface
{
    private $registry;
    private $tokenStorage;
    private $passwordEncoder;

    public function __construct(ManagerRegistry $registry, TokenStorageInterface $tokenStorage, PasswordEncoder $passwordEncoder)
    {
        $this->registry = $registry;
        $this->tokenStorage = $tokenStorage;
        $this->passwordEncoder = $passwordEncoder;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['generateUserCanonicalFields', EventPriorities::PRE_VALIDATE],
                ['importUsers', EventPriorities::PRE_VALIDATE],
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
        $result = \mb_convert_case($user->getEmail(), MB_CASE_LOWER, $encoding ?: \mb_internal_encoding());
        $user->setEmailCanonical(Urlizer::unaccent($result));
    }

    public function importUsers(GetResponseForControllerResultEvent $event)
    {
        // todo Find a better way to manage this security
        $request = $event->getRequest();
        if ('api_users_import_collection' !== $request->attributes->get('_route')) {
            return;
        }

        $users = $event->getControllerResult();
        $em = $this->registry->getManagerForClass(User::class);
        foreach ($users as $user) {
            $em->persist($user);
        }
        $em->flush();
    }

    public function setUser(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $data = $request->attributes->get('data');
        if (!$request->isMethod(Request::METHOD_POST) || !\in_array($request->attributes->get('_api_resource_class'), [
            UserQuiz::class,
            Profile::class,
            Contact::class,
            Event::class,
            Registration::class,
        ], true) || null !== $data->getUser()) {
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

        $this->passwordEncoder->encodePassword($data);
    }
}
