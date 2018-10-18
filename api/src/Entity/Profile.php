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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\Entity
 * @UniqueEntity("user")
 */
class Profile
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
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="profile")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE", unique=true)
     */
    private $user;

    /**
     * @ORM\Column(nullable=true)
     * @Groups({"profile_input", "profile_output", "user_output"})
     */
    private $firstName;

    /**
     * @ORM\Column(nullable=true)
     * @Groups({"profile_input", "profile_output", "user_output"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"profile_input", "profile_output", "user_output"})
     */
    private $familySize;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"profile_input", "profile_output", "user_output"})
     */
    private $nbAdults;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"profile_input", "profile_output", "user_output"})
     */
    private $nbChildren;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"profile_input", "profile_output", "user_output"})
     */
    private $nbBabies;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"profile_input", "profile_output", "user_output"})
     */
    private $nbPets;

    /**
     * @ORM\Column(nullable=true)
     * @Groups({"profile_input", "profile_output", "user_output"})
     */
    private $mobile;

    /**
     * @ORM\Column(nullable=true)
     * @Groups({"profile_input", "profile_output", "user_output"})
     */
    private $phone;

    /**
     * @ORM\Column(nullable=true)
     * @Groups({"profile_input", "profile_output", "user_output"})
     */
    private $address;

    /**
     * @ORM\Column(nullable=true)
     * @Groups({"profile_input", "profile_output", "user_output"})
     */
    private $postcode;

    /**
     * @ORM\Column(nullable=true)
     * @Groups({"profile_input", "profile_output", "user_output"})
     */
    private $city;

    /**
     * @ORM\Column(name="is_bi_flow", type="boolean")
     * @Groups({"profile_input", "profile_output", "user_output"})
     */
    private $biFlow = false;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFamilySize(): ?int
    {
        return $this->familySize;
    }

    public function setFamilySize(?int $familySize): void
    {
        $this->familySize = $familySize;
    }

    public function getNbAdults(): ?int
    {
        return $this->nbAdults;
    }

    public function setNbAdults(?int $nbAdults): void
    {
        $this->nbAdults = $nbAdults;
    }

    public function getNbChildren(): ?int
    {
        return $this->nbChildren;
    }

    public function setNbChildren(?int $nbChildren): void
    {
        $this->nbChildren = $nbChildren;
    }

    public function getNbBabies(): ?int
    {
        return $this->nbBabies;
    }

    public function setNbBabies(?int $nbBabies): void
    {
        $this->nbBabies = $nbBabies;
    }

    public function getNbPets(): ?int
    {
        return $this->nbPets;
    }

    public function setNbPets(?int $nbPets): void
    {
        $this->nbPets = $nbPets;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): void
    {
        $this->mobile = $mobile;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): void
    {
        $this->postcode = $postcode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function isBiFlow(): bool
    {
        return $this->biFlow;
    }

    public function setBiFlow(bool $biFlow): void
    {
        $this->biFlow = $biFlow;
    }
}
