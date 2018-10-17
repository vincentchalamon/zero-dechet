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

namespace App\EntityListener;

use App\Entity\Event;
use App\Entity\Notification;
use App\Entity\Registration;
use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class RegistrationEntityListener
{
    private const MAX_ABSENCES = 3;

    private $registry;
    private $requestStack;

    public function __construct(ManagerRegistry $registry, RequestStack $requestStack)
    {
        $this->registry = $registry;
        $this->requestStack = $requestStack;
    }

    public function prePersist(Registration $registration): void
    {
        $event = $registration->getEvent();
        if (true === $event->isAutoValidateRegistrations() && self::MAX_ABSENCES >= $registration->getUser()->getAbsences() && (null === $event->getLimit() || $event->getLimit() >= ($event->getNbAttendees() + $registration->getAttendees()))) {
            $registration->setStatus(Registration::STATUS_VALIDATED);
        }
    }

    public function postPersist(Registration $registration): void
    {
        if ($registration->isValidated()) {
            $this->theRegistrationHasBeenValidated($registration);
        }
        $this->notifyOrganizerAboutNewRegistration($registration);
    }

    public function postUpdate(Registration $registration): void
    {
        /** @var UnitOfWork $unitOfWork */
        $unitOfWork = $this->registry->getManagerForClass(Event::class)->getUnitOfWork();
        $changeSet = $unitOfWork->getEntityChangeSet($registration);
        if (isset($changeSet['status'])) {
            if ($registration->isValidated()) {
                $this->theRegistrationHasBeenValidated($registration);
            } elseif ($registration->isRefused()) {
                $this->theRegistrationHasBeenRefused($registration);
            }
        }
    }

    private function theRegistrationHasBeenValidated(Registration $registration)
    {
        $this->sendNotification($registration->getEvent(), $registration->getUser(), 'Votre inscription a l\'événement '.$registration->getEvent()->getTitle().' a été validée');
    }

    private function theRegistrationHasBeenRefused(Registration $registration)
    {
        $this->sendNotification($registration->getEvent(), $registration->getUser(), 'Votre inscription a l\'événement '.$registration->getEvent()->getTitle().' a été refusée');
    }

    private function notifyOrganizerAboutNewRegistration(Registration $registration)
    {
        $this->sendNotification($registration->getEvent(), $registration->getEvent()->getOrganizer(), 'Nouvelle inscription a votre événement '.$registration->getEvent()->getTitle());
    }

    private function sendNotification(Event $event, User $user, string $message): void
    {
        // Hack for Behat: do not send mail on fixtures load
        // todo To remove: do not add code for tests
        if (!$this->requestStack->getCurrentRequest()) {
            return;
        }
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setEvent($event);
        $notification->setMessage($message);
        $em = $this->registry->getManagerForClass(Notification::class);
        $em->persist($notification);
        $em->flush();
    }
}
