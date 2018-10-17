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

use ApiPlatform\Core\Hydra\Serializer\ConstraintViolationListNormalizer;
use CoopTilleuls\ForgotPasswordBundle\Event\ForgotPasswordEvent;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class ForgotPasswordEventSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $templating;
    private $registry;
    private $validator;
    private $userPasswordEncoder;
    private $serializer;

    public function __construct(\Swift_Mailer $mailer, Environment $templating, ManagerRegistry $registry, ValidatorInterface $validator, UserPasswordEncoderInterface $userPasswordEncoder, SerializerInterface $serializer)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->registry = $registry;
        $this->validator = $validator;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->serializer = $serializer;
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

        $violations = $this->validator->validateProperty($user, 'plainPassword');
        if ($violations->count()) {
            throw new BadRequestHttpException(
                $this->serializer->serialize($violations, ConstraintViolationListNormalizer::FORMAT),
                null,
                0,
                ['Content-Type' => 'application/json']
            );
        }
        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $user->getPlainPassword()));
        $user->eraseCredentials();

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
