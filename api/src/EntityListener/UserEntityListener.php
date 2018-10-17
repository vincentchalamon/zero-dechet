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

use App\Entity\User;
use App\Security\PasswordEncoder;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Twig\Environment;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class UserEntityListener
{
    private $passwordEncoder;
    private $authorizationChecker;
    private $mailer;
    private $templating;

    public function __construct(PasswordEncoder $passwordEncoder, AuthorizationCheckerInterface $authorizationChecker, \Swift_Mailer $mailer, Environment $templating)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->authorizationChecker = $authorizationChecker;
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function prePersist(User $user): void
    {
        $this->setPassword($user);
        try {
            if ($user->isActive() || !$this->authorizationChecker->isGranted('IS_AUTHENTICATED_ANONYMOUSLY')) {
                return;
            }
        } catch (AuthenticationCredentialsNotFoundException $exception) {
            return;
        }

        $swiftMessage = new \Swift_Message(
            'Validation de votre adresse email',
            $this->templating->render('registration.html.twig', ['user' => $user]),
            'text/html'
        );
        $swiftMessage->setTo($user->getEmail());
        $count = $this->mailer->send($swiftMessage);
        if (0 === $count) {
            throw new \RuntimeException('Unable to send email');
        }
    }

    public function preUpdate(User $user): void
    {
        $this->setPassword($user);
    }

    private function setPassword(User $user): void
    {
        $encoding = \mb_detect_encoding($user->getEmail(), \mb_detect_order(), true);
        $user->setEmailCanonical(Urlizer::unaccent(\mb_convert_case($user->getEmail(), MB_CASE_LOWER, $encoding ?: \mb_internal_encoding())));

        $this->passwordEncoder->encodePassword($user);
    }
}
