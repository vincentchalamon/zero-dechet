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
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\Entity
 * @ORM\EntityListeners({"App\EntityListener\RegistrationEntityListener"})
 * @UniqueEntity({"user", "event"})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"registration_output"}},
 *     "denormalization_context"={"groups"={"registration_input"}}
 * }, collectionOperations={"post"}, itemOperations={
 *     "get",
 *     "delete",
 *     "put"={"access_control"="(is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getEvent().getOrganizer() == user)) and is_feature_enabled('event')"}
 * })
 */
class Registration
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_VALIDATED = 'validated';
    public const STATUS_REFUSED = 'refused';

    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Groups({"registration_output"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="registrations")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"registration_output", "admin_input"})
     * @Assert\Expression("value != null and value.isActive() and this.getEvent().getOrganizer() != value")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="registrations")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"registration_output", "registration_input"})
     * @Assert\Expression("value != null and value.isActive() and !value.isPast()")
     */
    private $event;

    /**
     * @ORM\Column
     * @Groups({"organizer_output", "organizer_input"})
     */
    private $status = self::STATUS_PENDING;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"registration_output", "registration_input"})
     */
    private $attendees = 1;

    /**
     * @ORM\Column(type="boolean", name="is_present")
     * @Groups({"organizer_output", "organizer_input"})
     */
    private $present = false;

    public function isPending(): bool
    {
        return self::STATUS_PENDING === $this->getStatus();
    }

    public function isValidated(): bool
    {
        return self::STATUS_VALIDATED === $this->getStatus();
    }

    public function isRefused(): bool
    {
        return self::STATUS_REFUSED === $this->getStatus();
    }

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

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): void
    {
        $this->event = $event;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getAttendees(): int
    {
        return $this->attendees;
    }

    public function setAttendees(int $attendees): void
    {
        $this->attendees = $attendees;
    }

    public function isPresent(): bool
    {
        return $this->present;
    }

    public function setPresent(bool $present): void
    {
        $this->present = $present;
    }
}
