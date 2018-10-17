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

use App\Security\PasswordEncoder;
use CoopTilleuls\ForgotPasswordBundle\Event\ForgotPasswordEvent;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class ForgotPasswordEventSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $templating;
    private $registry;
    private $passwordEncoder;

    public function __construct(\Swift_Mailer $mailer, Environment $templating, ManagerRegistry $registry, PasswordEncoder $passwordEncoder)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->registry = $registry;
        $this->passwordEncoder = $passwordEncoder;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ForgotPasswordEvent::CREATE_TOKEN => 'onCreateToken',
            ForgotPasswordEvent::UPDATE_PASSWORD => 'onUpdatePassword',
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    public function onCreateToken(ForgotPasswordEvent $event): void
    {
        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();

        $swiftMessage = new \Swift_Message('Réinitialisation de votre mot de passe', $this->templating->render('forgotPassword.html.twig', ['token' => $passwordToken->getToken()]));
        $swiftMessage->setTo($user->getEmail());
        $swiftMessage->setContentType('text/html');
        if (0 === $this->mailer->send($swiftMessage)) {
            throw new \RuntimeException('Unable to send email');
        }
    }

    public function onUpdatePassword(ForgotPasswordEvent $event): void
    {
        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();
        $user->setPlainPassword($event->getPassword());
        $this->passwordEncoder->encodePassword($user);

        $entityManager = $this->registry->getManagerForClass(\get_class($user));
        $entityManager->persist($user);
        $entityManager->flush();
    }

    public function onException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();
        if (!$exception instanceof HttpException || Response::HTTP_BAD_REQUEST !== $exception->getStatusCode() || 'application/json' !== ($exception->getHeaders()['Content-Type'] ?? '')) {
            return;
        }

        $event->setResponse(new JsonResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST, $exception->getHeaders(), true));
    }
}
