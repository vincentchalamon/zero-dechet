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

use App\Authorization\AuthorizationCheckerInterface;
use App\Entity\Contact;
use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Twig\Environment;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class ContactEntityListener
{
    private $mailer;
    private $templating;
    private $userRepository;
    private $authorizationChecker;

    public function __construct(\Swift_Mailer $mailer, Environment $templating, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function prePersist(Contact $contact, LifecycleEventArgs $event): void
    {
        $swiftMessage = new \Swift_Message(
            'Nouveau message depuis l\'application Zéro Déchet',
            $this->templating->render('contact.html.twig', ['message' => $contact->getBody()]),
            'text/html'
        );
        $swiftMessage->setFrom($contact->getUser()->getEmail());
        /** @var User[] $recipients */
        $recipients = $event->getEntityManager()->getRepository(User::class)->findAdmins();
        foreach ($recipients as $recipient) {
            // todo Find a better way to check if recipient has ROLE_ADMIN_CITY
            if (\in_array('ROLE_ADMIN_CITY', $recipient->getRoles(), true) && !$this->authorizationChecker->isInTheSameCity($contact->getUser()->getProfile(), $recipient)) {
                continue;
            }
            $swiftMessage->setTo($recipient->getEmail());
            $count = $this->mailer->send($swiftMessage);
            if (0 === $count) {
                throw new \RuntimeException('Unable to send email');
            }
        }
    }
}
