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
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Doctrine\ORM\Filter\GeocodingFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\Entity
 * @ORM\EntityListeners({"App\EntityListener\GeocoderEntityListener", "App\EntityListener\EventEntityListener"})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"event:read"}},
 *     "denormalization_context"={"groups"={"event:write"}},
 *     "order"={"startAt"="ASC"}
 * }, collectionOperations={
 *     "get"={"access_control"="is_granted('ROLE_USER')"},
 *     "post"={"access_control"="is_granted('ROLE_USER')"}
 * }, itemOperations={
 *     "get"={"access_control"="is_granted('ROLE_USER')"},
 *     "put"={"access_control"="(is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and user == object.getOrganizer()))"},
 *     "delete"={"access_control"="(is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and user == object.getOrganizer()))"},
 *     "like"={
 *         "path"="/events/{id}/like.{_format}",
 *         "controller"="App\Action\UserLikeEvent",
 *         "method"="PUT",
 *         "swagger_context"={
 *             "parameters"={
 *                 {"name"="id", "in"="path", "required"=true, "type"="string"}
 *             }
 *         },
 *         "access_control"="is_granted('ROLE_USER')"
 *     }
 * }, subresourceOperations={
 *     "registrations_get_subresource"={
 *         "requirements"={"id"=User::UUID_REQUIREMENT}
 *     }
 * })
 * @ApiFilter(GeocodingFilter::class)
 * @ApiFilter(SearchFilter::class, properties={"title"="ipartial", "startAt", "endAt"})
 * @ApiFilter(OrderFilter::class, properties={"startAt"})
 */
class Event implements GeocoderInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Groups({"event:write", "event:read"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Groups({"event:write", "event:read"})
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Groups({"event:write", "event:read"})
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Groups({"event:write", "event:read"})
     */
    private $endAt;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Groups({"event:write", "event:read"})
     */
    private $address;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Assert\Length(min="5", max="5")
     * @Groups({"event:write", "event:read"})
     */
    private $postcode;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Groups({"event:write", "event:read"})
     */
    private $city;

    /**
     * @ORM\Column(type="geometry", options={"geometry_type"="POINT"})
     */
    private $coordinates;

    /**
     * @ORM\Column(type="float")
     * @Groups({"shop:read"})
     */
    private $longitude;

    /**
     * @ORM\Column(type="float")
     * @Groups({"shop:read"})
     */
    private $latitude;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $organizer;

    /**
     * @ORM\Column(type="integer", nullable=true, name="registrations_limit")
     * @Groups({"event:write", "event:read"})
     */
    private $limit;

    /**
     * @ORM\Column(type="boolean", name="is_auto_validate_registrations")
     * @Groups({"event:write", "event:read"})
     */
    private $autoValidateRegistrations = false;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", fetch="EXTRA_LAZY")
     * @Groups({"event:write", "event:read"})
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Registration", mappedBy="event", fetch="EXTRA_LAZY")
     * @Groups({"event:write", "event:read"})
     * @ApiSubresource
     */
    private $registrations;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->registrations = new ArrayCollection();
    }

    public function isPast(): bool
    {
        return new \DateTime() > $this->getStartAt();
    }

    /**
     * @Groups({"event:read"})
     */
    public function getFullAddress(): string
    {
        return $this->getAddress().' '.$this->getPostcode().' '.$this->getCity();
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

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getStartAt(): \DateTime
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTime $startAt): void
    {
        $this->startAt = $startAt;
    }

    public function getEndAt(): \DateTime
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTime $endAt): void
    {
        $this->endAt = $endAt;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getCoordinates(): ?string
    {
        return $this->coordinates;
    }

    public function setCoordinates(string $coordinates): void
    {
        $this->coordinates = $coordinates;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(User $organizer): void
    {
        $this->organizer = $organizer;
    }

    /**
     * Alias for getOrganizer.
     */
    public function getUser(): ?User
    {
        return $this->getOrganizer();
    }

    /**
     * Alias for setOrganizer.
     */
    public function setUser(User $user): void
    {
        $this->setOrganizer($user);
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function isAutoValidateRegistrations(): bool
    {
        return $this->autoValidateRegistrations;
    }

    public function setAutoValidateRegistrations(bool $autoValidateRegistrations): void
    {
        $this->autoValidateRegistrations = $autoValidateRegistrations;
    }

    /**
     * @return User[]
     */
    public function getLikes(): array
    {
        return $this->likes->getValues();
    }

    public function addLike(User $user): void
    {
        if (!$this->likes->contains($user)) {
            $this->likes[] = $user;
        }
    }

    public function removeLike(User $user): void
    {
        $this->likes->removeElement($user);
    }

    /**
     * @return Registration[]
     */
    public function getRegistrations(): array
    {
        return $this->registrations->getValues();
    }

    public function addRegistration(Registration $registration): void
    {
        if (!$this->registrations->contains($registration)) {
            $registration->setEvent($this);
            $this->registrations[] = $registration;
        }
    }

    public function removeRegistration(Registration $registration): void
    {
        $this->registrations->removeElement($registration);
    }

    /**
     * @return User[]
     */
    public function getValidatedRegistrationsUser(): array
    {
        return $this->registrations->filter(function (Registration $registration) {
            return $registration->isValidated();
        })->map(function (Registration $registration) {
            return $registration->getUser();
        })->getValues();
    }

    /**
     * @return User[]
     */
    public function getPendingRegistrationsUser(): array
    {
        return $this->registrations->filter(function (Registration $registration) {
            return $registration->isPending();
        })->map(function (Registration $registration) {
            return $registration->getUser();
        })->getValues();
    }

    /**
     * @return User[]
     */
    public function getRefusedRegistrationsUser(): array
    {
        return $this->registrations->filter(function (Registration $registration) {
            return $registration->isRefused();
        })->map(function (Registration $registration) {
            return $registration->getUser();
        })->getValues();
    }

    /**
     * @return User[]
     */
    public function getAttendeesRegistrationsUser(): array
    {
        return $this->registrations->filter(function (Registration $registration) {
            return !$registration->isRefused();
        })->map(function (Registration $registration) {
            return $registration->getUser();
        })->getValues();
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->registrations->map(function (Registration $registration) {
            return $registration->getUser();
        })->getValues();
    }

    public function getNbAttendees(): int
    {
        return \array_sum($this->registrations->filter(function (Registration $registration) {
            return $registration->isValidated();
        })->map(function (Registration $registration) {
            return $registration->getAttendees();
        })->getValues());
    }
}
