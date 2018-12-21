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
use App\Doctrine\ORM\Filter\GeocodingFilter;
use App\Doctrine\ORM\Filter\TagsFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\Entity
 * @ORM\EntityListeners("App\EntityListener\GeocoderEntityListener")
 * @ApiResource(attributes={
 *     "order"={"name"="ASC"},
 *     "normalization_context"={"groups"={"shop:read"}},
 *     "denormalization_context"={"groups"={"shop:write"}},
 *     "access_control"="is_granted('ROLE_USER')"
 * }, itemOperations={
 *     "get",
 *     "put"={"access_control"="is_granted('ROLE_ADMIN')"},
 *     "delete"={"access_control"="is_granted('ROLE_ADMIN')"}
 * })
 * @ApiFilter(GeocodingFilter::class)
 * @ApiFilter(TagsFilter::class)
 * todo Replace by ES?
 * @ApiFilter(SearchFilter::class, properties={"name"="ipartial"})
 */
class Shop implements GeocoderInterface
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
     * @ORM\Column
     * @Assert\NotBlank
     * @Groups({"shop:write", "shop:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", name="is_active")
     * @Groups({"admin:write", "admin:read"})
     */
    private $active = false;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Groups({"shop:write", "shop:read"})
     */
    private $address;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Assert\Length(min="5", max="5")
     * @Groups({"shop:write", "shop:read"})
     */
    private $postcode;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Groups({"shop:write", "shop:read"})
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag")
     * @ORM\OrderBy({"name"="ASC"})
     * @Groups({"shop:write", "shop:read"})
     */
    private $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
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

    public function getCoordinates(): ?string
    {
        return $this->coordinates;
    }

    public function setCoordinates(string $coordinates): void
    {
        $this->coordinates = $coordinates;
    }

    /**
     * @return Tag[]
     */
    public function getTags(): array
    {
        return $this->tags->getValues();
    }

    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
    }

    public function removeTag(Tag $tag): void
    {
        $this->tags->removeElement($tag);
    }
}
