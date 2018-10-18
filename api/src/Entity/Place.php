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

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\Entity
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"place_output"}},
 *     "denormalization_context"={"groups"={"place_input"}},
 *     "access_control"="is_granted('ROLE_USER') and is_feature_enabled('quiz')"
 * }, collectionOperations={
 *     "get",
 *     "post"={"access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('quiz')"}
 * }, itemOperations={
 *     "get",
 *     "put"={"access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('quiz')"},
 *     "delete"={"access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('quiz')"}
 * })
 */
class Place
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Groups({"place_input", "place_output"})
     */
    private $name;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
