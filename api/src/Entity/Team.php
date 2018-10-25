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
use App\Doctrine\ORM\Filter\WeighingsTeamFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\Entity
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"team_output", "user_output"}},
 *     "denormalization_context"={"groups"={"team_input"}},
 *     "order"={"name"="ASC"}
 * }, collectionOperations={
 *     "get"={"access_control"="is_granted('ROLE_USER') and is_feature_enabled('weighing')"},
 *     "post"={"access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('weighing')"}
 * }, itemOperations={
 *     "get"={"access_control"="is_granted('ROLE_USER') and is_feature_enabled('weighing')"},
 *     "put"={"access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('weighing')"},
 *     "delete"={"access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('weighing')"}
 * })
 * @ApiFilter(WeighingsTeamFilter::class)
 */
class Team
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
     * @Groups({"team_input", "team_output"})
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="teams")
     * @Groups({"team_input", "team_output"})
     * @Assert\NotNull
     * @Assert\Count(min="2")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users->getValues();
    }

    public function addUser(User $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }
    }

    public function removeUser(User $user): void
    {
        $this->users->removeElement($user);
    }
}
