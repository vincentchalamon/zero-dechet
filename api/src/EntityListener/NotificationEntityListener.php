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

use App\Entity\Notification;
use Twig\Environment;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class NotificationEntityListener
{
    private $mailer;
    private $templating;

    public function __construct(\Swift_Mailer $mailer, Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function postPersist(Notification $notification): void
    {
        if (!$notification->getUser()->isNotifyByEmail()) {
            return;
        }

        $swiftMessage = new \Swift_Message(
            $notification->getMessage(),
            // todo Change template
            $this->templating->render('notification.html.twig', ['message' => $notification->getMessage()]),
            'text/html'
        );
        $swiftMessage->setTo($notification->getUser()->getEmail());
        $count = $this->mailer->send($swiftMessage);
        if (0 === $count) {
            throw new \RuntimeException('Unable to send email');
        }
    }
}
