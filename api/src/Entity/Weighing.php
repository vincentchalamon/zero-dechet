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
 * @ORM\Entity
 * @ApiResource(attributes={
 *     "order"={"createdAt"="ASC"},
 *     "normalization_context"={"groups"={"weighing:read", "user:read"}},
 *     "denormalization_context"={"groups"={"weighing:write"}}
 * }, subresourceOperations={
 *     "api_users_weighings_get_subresource"={
 *         "access_control"="is_granted('ROLE_ADMIN') or request.attributes.get('object') == user"
 *     }
 * }, collectionOperations={
 *     "post"={"access_control"="is_granted('ROLE_USER')"},
 *     "get"={"access_control"="is_granted('ROLE_USER')"}
 * }, itemOperations={
 *     "get"={"access_control"="is_granted('ROLE_ADMIN') or object.getUser() == user"},
 *     "put"={"access_control"="is_granted('ROLE_ADMIN') or object.getUser() == user"},
 *     "delete"={"access_control"="is_granted('ROLE_ADMIN') or object.getUser() == user"}
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
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Groups({"weighing:read"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="weighings")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"weighing:write", "weighing:read"})
     * @Assert\NotNull
     */
    private $user;

    /**
     * @ORM\Column(type="float")
     * @Groups({"weighing:write", "weighing:read"})
     * @Assert\NotBlank
     */
    private $total;

    /**
     * @ORM\Column
     * @Groups({"weighing:write", "weighing:read"})
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
