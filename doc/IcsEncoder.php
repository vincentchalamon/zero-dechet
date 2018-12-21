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

namespace App\Serializer;

use App\Entity\Event;
use Symfony\Component\Serializer\Encoder\ContextAwareEncoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

/**
 * todo Restrict to /events.
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class IcsEncoder implements EncoderInterface, ContextAwareEncoderInterface
{
    public const FORMAT = 'ics';
    public const CONTENT_TYPE = 'text/calendar';

    /**
     * @param Event[] $events
     */
    public function encode($events, $format, array $context = []): string
    {
        $ics = <<<'ICS'
BEGIN:VCALENDAR
VERSION:2.0
CALSCALE:GREGORIAN

ICS;
        foreach ($events as $event) {
            $ics .= \sprintf(<<<'ICS'
BEGIN:VEVENT
SUMMARY:%s
DTSTART;TZID=%s
DTEND;TZID=%s
LOCATION:%s
DESCRIPTION:%s
STATUS:CONFIRMED
SEQUENCE:3
BEGIN:VALARM
TRIGGER:-PT10M
ACTION:DISPLAY
END:VALARM
END:VEVENT

ICS
                , $event['title'], $event['startAt'], $event['endAt'], $event['fullAddress'], $event['description']
            );
        }
        $ics .= <<<'ICS'
END:VCALENDAR

ICS;

        return $ics;
    }

    public function supportsEncoding($format, array $context = []): bool
    {
        return self::FORMAT === $format;
    }
}
