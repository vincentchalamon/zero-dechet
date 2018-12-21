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
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Constraints\CurrentPassword;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="`user`")
 * @ORM\EntityListeners({"App\EntityListener\UserEntityListener"})
 * @UniqueEntity("emailCanonical", errorPath="email")
 * @CurrentPassword
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"user:read"}},
 *     "denormalization_context"={"groups"={"user:write"}}
 * }, collectionOperations={
 *     "post"={
 *         "validation_groups"={"Default", "user:create"},
 *         "access_control"="is_granted('ROLE_ADMIN') or is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *         "denormalization_context"={"groups"={"user:write", "user:create"}}
 *     },
 *     "get"={"access_control"="is_granted('ROLE_ADMIN')"}
 * }, itemOperations={
 *     "get"={"access_control"="is_granted('ROLE_ADMIN') or object == user"},
 *     "put"={"access_control"="is_granted('ROLE_ADMIN') or object == user"},
 *     "delete"={"access_control"="is_granted('ROLE_ADMIN') or object == user"},
 *     "validate"={
 *         "path"="/users/{id}/validate.{_format}",
 *         "method"="GET",
 *         "controller"="App\Action\UserValidation",
 *         "swagger_context"={
 *             "parameters"={
 *                 {"name"="token", "in"="query", "required"=true, "type"="string"},
 *                 {"name"="id", "in"="path", "required"=true, "type"="string"}
 *             }
 *         },
 *         "access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"
 *     },
 *     "scores"={
 *         "path"="/users/{id}/scores.{_format}",
 *         "method"="GET",
 *         "controller"="App\Action\UserScores",
 *         "swagger_context"={
 *             "parameters"={
 *                 {"name"="id", "in"="path", "required"=true, "type"="string"}
 *             }
 *         },
 *         "normalization_context"={"groups"={"score:read"}},
 *         "access_control"="is_granted('ROLE_ADMIN') or object == user"
 *     }
 * }, subresourceOperations={
 *     "quizzes_get_subresource"={
 *         "requirements"={"id"=User::UUID_REQUIREMENT}
 *     },
 *     "weighings_get_subresource"={
 *         "requirements"={"id"=User::UUID_REQUIREMENT}
 *     }
 * })
 * @ApiFilter(SearchFilter::class, properties={"active", "email"})
 */
class User implements UserInterface
{
    // todo Move to a better place
    public const UUID_REQUIREMENT = '^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$';

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
     * @ORM\Column(type="boolean", name="is_active")
     * @Groups({"admin:read", "admin:write"})
     */
    private $active = false;

    /**
     * @ORM\Column(length=200)
     */
    private $token;

    /**
     * @ORM\Column(length=200)
     * @Assert\NotBlank
     * @Assert\Email
     * @Groups({"user:write", "user:read", "weighing:read"})
     */
    private $email;

    /**
     * @ORM\Column(length=200, unique=true)
     */
    private $emailCanonical;

    /**
     * @ORM\Column(length=64)
     */
    private $password;

    /**
     * Not persisted in database, for password encoding only.
     *
     * @Assert\NotBlank(groups={"user:create"})
     * @Assert\Length(min="7")
     * @Groups({"user:write"})
     */
    private $plainPassword;

    /**
     * Not persisted in database, security for password update.
     *
     * @Groups({"user:write"})
     */
    private $currentPassword;

    /**
     * Not persisted in database, security for user create.
     *
     * @Assert\NotBlank(groups={"user:create"})
     * @Assert\IsTrue(groups={"user:create"})
     * @Groups({"user:create"})
     */
    private $cgu = false;

    /**
     * @ORM\Column(type="array")
     * @Groups({"admin:read", "admin:write"})
     */
    private $roles = ['ROLE_USER'];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserQuiz", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"createdAt"="DESC"})
     * @ApiSubresource(maxDepth=1)
     */
    private $quizzes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Place")
     * @Groups({"owner:write", "owner:read"})
     */
    private $ignoredPlaces;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Weighing", mappedBy="user", fetch="EXTRA_LAZY")
     * @ApiSubresource
     */
    private $weighings;

    public function __construct()
    {
        $this->quizzes = new ArrayCollection();
        $this->ignoredPlaces = new ArrayCollection();
        $this->weighings = new ArrayCollection();
        $this->token = \bin2hex(\random_bytes(25));
    }

    public function getSalt(): ?string
    {
        return $this->getToken();
    }

    public function getUsername(): string
    {
        return $this->getEmail();
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
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

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmailCanonical(): ?string
    {
        return $this->emailCanonical;
    }

    public function setEmailCanonical(string $emailCanonical): void
    {
        $this->emailCanonical = $emailCanonical;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getCurrentPassword(): ?string
    {
        return $this->currentPassword;
    }

    public function setCurrentPassword(?string $currentPassword): void
    {
        $this->currentPassword = $currentPassword;
    }

    public function isCgu(): bool
    {
        return $this->cgu;
    }

    public function setCgu(bool $cgu): void
    {
        $this->cgu = $cgu;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return UserQuiz[]
     */
    public function getQuizzes(): array
    {
        return $this->quizzes->getValues();
    }

    /**
     * @return Place[]
     */
    public function getIgnoredPlaces(): array
    {
        return $this->ignoredPlaces->getValues();
    }

    public function addIgnoredPlace(Place $place): void
    {
        if (!$this->ignoredPlaces->contains($place)) {
            $this->ignoredPlaces[] = $place;
        }
    }

    public function removeIgnoredPlace(Place $place): void
    {
        $this->ignoredPlaces->removeElement($place);
    }

    /**
     * @return Weighing[]
     */
    public function getWeighings(): array
    {
        return $this->weighings->getValues();
    }

    public function addWeighing(Weighing $weighing): void
    {
        if (!$this->weighings->contains($weighing)) {
            $this->weighings[] = $weighing;
        }
    }

    public function removeWeighing(Weighing $weighing): void
    {
        $this->weighings->removeElement($weighing);
    }
}
