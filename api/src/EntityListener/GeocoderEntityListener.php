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

use App\Entity\GeocoderInterface;
use GuzzleHttp\Client;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class GeocoderEntityListener
{
    private $client;
    private $attemps = 0;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function prePersist(GeocoderInterface $object)
    {
        if (null === $object->getCoordinates()) {
            $this->preUpdate($object);
        }
    }

    public function preUpdate(GeocoderInterface $object)
    {
        [$longitude, $latitude] = $this->getCoordinatesFromGoogle($object->getFullAddress());
        $object->setLongitude($longitude);
        $object->setLatitude($latitude);
        $object->setCoordinates(\sprintf('POINT(%s %s)', $longitude, $latitude));
    }

    private function getCoordinatesFromGoogle(string $address): array
    {
        $response = $this->client->get(\sprintf('/maps/api/geocode/json?address=%s&sensor=false', \urlencode($address)));
        $json = \json_decode($response->getBody()->getContents(), true);
        if ('OK' !== $json['status']) {
            if ('OVER_QUERY_LIMIT' === $json['status'] && 3 < $this->attemps) {
                ++$this->attemps;
                \sleep(2);

                return $this->getCoordinatesFromGoogle($address);
            }
            throw new \RuntimeException(\sprintf('Unable to retrieve coordinates for address "%s": %s', $address, $json['status']));
        }
        $location = $json['results'][0]['geometry']['location'];

        return [$location['lng'], $location['lat']];
    }
}
