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
 *     "normalization_context"={"groups"={"tag_output"}},
 *     "denormalization_context"={"groups"={"tag_input"}},
 *     "access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('shop')"
 *     }, collectionOperations={
 *     "get"={"access_control"="is_granted('ROLE_USER') and is_feature_enabled('shop')"},
 *     "post"
 *     }, itemOperations={
 *     "get"={"access_control"="is_granted('ROLE_USER') and is_feature_enabled('shop')"},
 *     "put",
 *     "delete"
 * })
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\Column
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="App\Doctrine\Generator\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column
     * @Groups({"tag_input", "tag_output"})
     * @Assert\NotBlank
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
