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

use ApiPlatform\Core\EventListener\EventPriorities;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class ApiSubresourceEventSubscriber implements EventSubscriberInterface
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['secureSubresources', EventPriorities::POST_READ],
            ],
        ];
    }

    public function secureSubresources(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->attributes->has('_api_subresource_context')) {
            return;
        }

        $class = $request->attributes->get('_api_subresource_context')['identifiers'][0][1];
        if (null === ($object = $this->registry->getRepository($class)->find($request->attributes->get('id')))) {
            throw new NotFoundHttpException();
        }
        $request->attributes->set('object', $object);
    }
}
