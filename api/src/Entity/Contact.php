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
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\Entity
 * @ORM\EntityListeners({"App\EntityListener\ContactEntityListener"})
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"contact_output"}},
 *     "denormalization_context"={"groups"={"contact_input"}}
 * }, collectionOperations={
 *     "get"={"access_control"="(is_granted('ROLE_ADMIN') or is_granted('ROLE_ADMIN_CITY')) and is_feature_enabled('contact')"},
 *     "post"={"access_control"="is_granted('ROLE_USER') and is_feature_enabled('contact')"}
 * }, itemOperations={
 *     "get"={"access_control"="(is_granted('ROLE_ADMIN') or (is_granted('ROLE_ADMIN_CITY') and is_in_the_same_city(object.getUser()->getProfile()))) and is_feature_enabled('contact')"}
 * })
 * @ApiFilter(SearchFilter::class, properties={"status"="exact", "user"})
 */
class Contact
{
    public const STATUS_READ = 'read';
    public const STATUS_UNREAD = 'unread';

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
     * @Groups({"user_export"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"contact_output"})
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Groups({"contact_input", "contact_output"})
     */
    private $body;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Groups({"contact_input", "contact_output"})
     */
    private $status = self::STATUS_UNREAD;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
