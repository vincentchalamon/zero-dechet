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

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * todo Replace request.attributes.get('object') by object
 * @ORM\Entity
 * @ApiResource(attributes={
 *     "order"={"createdAt"="ASC"},
 *     "normalization_context"={"groups"={"weighing_output", "user_output"}},
 *     "denormalization_context"={"groups"={"weighing_input"}},
 *     "access_control"="(is_granted('ROLE_ADMIN') or request.attributes.get('object') == user or (is_granted('ROLE_ADMIN_CITY') and is_in_the_same_city(request.attributes.get('object').getProfile()))) and is_feature_enabled('weighing')"
 * }, collectionOperations={
 *     "post"={"access_control"="is_granted('ROLE_USER') and is_feature_enabled('weighing')"},
 *     "get"={"access_control"="is_granted('ROLE_USER') and is_feature_enabled('weighing')"}
 * }, itemOperations={
 *     "get"={"access_control"="(is_granted('ROLE_ADMIN') or object.getUser() == user or (is_granted('ROLE_ADMIN_CITY') and is_in_the_same_city(object.getUser().getProfile()))) and is_feature_enabled('weighing')"},
 *     "put"={"access_control"="(is_granted('ROLE_ADMIN') or object.getUser() == user or (is_granted('ROLE_ADMIN_CITY') and is_in_the_same_city(object.getUser().getProfile()))) and is_feature_enabled('weighing')"},
 *     "delete"={"access_control"="(is_granted('ROLE_ADMIN') or object.getUser() == user or (is_granted('ROLE_ADMIN_CITY') and is_in_the_same_city(object.getUser().getProfile()))) and is_feature_enabled('weighing')"}
 * })
 * @ApiFilter(SearchFilter::class, properties={"user", "type", "user.profile.city"})
 * @ApiFilter(RangeFilter::class, properties={"total"})
 */
class Weighing
{
    public const TYPE_RECYCLABLE = 'recyclable';
    public const TYPE_NON_RECYCLABLE = 'non-recyclable';
    public const TYPE_BIODEGRADABLE = 'biodegradable';

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Groups({"weighing_output"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="weighings")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"weighing_input", "weighing_output"})
     * @Assert\NotNull
     */
    private $user;

    /**
     * @ORM\Column(type="float")
     * @Groups({"weighing_input", "weighing_output"})
     * @Assert\NotBlank
     */
    private $total;

    /**
     * @ORM\Column
     * @Groups({"weighing_input", "weighing_output"})
     * @Assert\NotBlank
     * @Assert\Choice(choices={
     *     Weighing::TYPE_BIODEGRADABLE,
     *     Weighing::TYPE_NON_RECYCLABLE,
     *     Weighing::TYPE_RECYCLABLE
     * })
     */
    private $type;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
