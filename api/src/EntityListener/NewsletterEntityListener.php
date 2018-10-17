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

use App\Entity\Newsletter;
use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Twig\Environment;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class NewsletterEntityListener
{
    private $mailer;
    private $templating;
    private $registry;

    public function __construct(\Swift_Mailer $mailer, Environment $templating, ManagerRegistry $registry)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->registry = $registry;
    }

    public function postPersist(Newsletter $newsletter): void
    {
        if (Newsletter::STATUS_SENDING !== $newsletter->getStatus()) {
            return;
        }

        $swiftMessage = new \Swift_Message(
            $newsletter->getSubject(),
            $this->templating->render('newsletter.html.twig', [
                'subject' => $newsletter->getSubject(),
                'content' => $newsletter->getContent(),
            ]),
            'text/html'
        );
        $users = $this->registry->getRepository(User::class)->findBy(['newsletter' => true]);
        foreach ($users as $user) {
            $swiftMessage->setTo($user->getEmail());
            if (0 === $this->mailer->send($swiftMessage)) {
                throw new \RuntimeException('Unable to send email');
            }
        }
    }

    public function postUpdate(Newsletter $newsletter): void
    {
        $this->postPersist($newsletter);
    }
}
