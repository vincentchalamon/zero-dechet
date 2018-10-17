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

namespace App\Serializer;

use App\Entity\Profile;
use App\Entity\User;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class UserNormalizer implements NormalizerInterface, NormalizerAwareInterface, DenormalizerInterface, DenormalizerAwareInterface
{
    use NormalizerAwareTrait;
    use DenormalizerAwareTrait;

    private $propertyInfo;
    private $ready = true;

    public function __construct(PropertyInfoExtractorInterface $propertyInfo)
    {
        $this->propertyInfo = $propertyInfo;
    }

    /**
     * @param User $object
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        $this->ready = false;
        $data = $this->normalizer->normalize($object, $format, $context);
        $data['roles'] = \implode(',', $object->getRoles());
        $data['cities'] = \implode(',', $object->getCities());
        // todo Fix this shit
        foreach ($this->propertyInfo->getProperties(Profile::class, ['serializer_groups' => $context['groups']]) as $property) {
            $method = 'get'.\ucfirst($property);
            if ('bool' === $this->propertyInfo->getTypes(Profile::class, $property)[0]->getBuiltinType()) {
                $method = 'is'.\ucfirst($property);
            }
            $data[$property] = $object->getProfile() ? \call_user_func([$object->getProfile(), $method]) : null;
        }
        unset($data['profile']);
        $this->ready = true;

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof User && CsvEncoder::FORMAT === $format && $this->ready;
    }

    public function denormalize($data, $class, $format = null, array $context = []): User
    {
        $dataWithoutProfile = $data;
        unset($dataWithoutProfile['profile']);

        $this->ready = false;
        /** @var User $user */
        $user = $this->denormalizer->denormalize($dataWithoutProfile, $class, $format, $context);
        $this->ready = true;

        // Handle profile update
        if (null === ($profile = $user->getProfile())) {
            $profile = new Profile();
            $user->setProfile($profile);
        }
        $properties = \array_intersect($this->propertyInfo->getProperties(Profile::class, ['serializer_groups' => $context['groups']]), \array_keys($data['profile']));
        foreach ($properties as $property) {
            \call_user_func([$profile, 'set'.\ucfirst($property)], $data['profile'][$property]);
        }

        return $user;
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return \is_a($type, User::class, true) && $this->ready && isset($data['profile']);
    }
}
