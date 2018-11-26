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

namespace App\EntityListener;

use App\Entity\Event;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class EventEntityListener
{
    private $registry;
    private $tokenStorage;
    private $requestStack;

    public function __construct(ManagerRegistry $registry, TokenStorageInterface $tokenStorage, RequestStack $requestStack)
    {
        $this->registry = $registry;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    public function postPersist(Event $event): void
    {
        $this->sendNotifications($event, $this->registry->getRepository(User::class)->findBy(['active' => true]), 'Nouvel événement Zéro Déchet : '.$event->getTitle(), 'Votre événement '.$event->getTitle().' a été validé');
    }

    public function postUpdate(Event $event): void
    {
        /** @var UnitOfWork $unitOfWork */
        $unitOfWork = $this->registry->getManagerForClass(Event::class)->getUnitOfWork();
        $changeSet = $unitOfWork->getEntityChangeSet($event);
        if (isset($changeSet['startAt']) || isset($changeSet['endAt'])) {
            $this->sendNotifications($event, $event->getAttendeesRegistrationsUser(), 'La date de l\'événement '.$event->getTitle().' a été modifiée');
        }
        if (isset($changeSet['address']) || isset($changeSet['zipcode']) || isset($changeSet['city'])) {
            $this->sendNotifications($event, $event->getAttendeesRegistrationsUser(), 'L\'adresse de l\'événement '.$event->getTitle().' a été modifiée');
        }
    }

    public function preRemove(Event $event): void
    {
        $this->sendNotifications($event, $event->getAttendeesRegistrationsUser(), 'L\'événement '.$event->getTitle().' a été annulé', 'Votre événement '.$event->getTitle().' a été annulé');
    }

    private function sendNotifications(Event $event, array $users, string $message, string $organizerMessage = null): void
    {
        if (!$this->requestStack->getCurrentRequest()) {
            return;
        }
        $token = $this->tokenStorage->getToken();
        $em = $this->registry->getManagerForClass(Notification::class);
        $users[] = $event->getOrganizer();
        foreach (\array_unique($users, SORT_REGULAR) as $i => $user) {
            if (null !== $token && $token->getUser() instanceof User && $token->getUser() === $user) {
                continue;
            }
            $notification = new Notification();
            $notification->setUser($user);
            $notification->setEvent($event);
            $notification->setMessage($message);
            if (null !== $organizerMessage && $user === $event->getOrganizer()) {
                $notification->setMessage($organizerMessage);
            }
            $em->persist($notification);
            if (0 === $i % 50) {
                $em->flush();
            }
        }
        $em->flush();
    }
}
