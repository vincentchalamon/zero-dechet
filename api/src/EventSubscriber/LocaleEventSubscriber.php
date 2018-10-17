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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class LocaleEventSubscriber implements EventSubscriberInterface
{
    private const HEADER_NAME = 'X-Locale';

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['setLocale', 18], // before Symfony\Component\HttpKernel\EventListener\LocaleListener
            ],
        ];
    }

    public function setLocale(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->headers->has(self::HEADER_NAME) || empty(\trim($request->headers->get(self::HEADER_NAME)))) {
            return;
        }

        $request->attributes->set('_locale', $request->headers->get(self::HEADER_NAME));
    }
}
