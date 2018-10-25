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
 *     "normalization_context"={"groups"={"user_output", "place_output", "profile_output"}},
 *     "denormalization_context"={"groups"={"user_input", "profile_input"}}
 * }, collectionOperations={
 *     "post"={
 *         "validation_groups"={"Default", "registration"},
 *         "access_control"="is_granted('ROLE_ADMIN') or (is_granted('IS_AUTHENTICATED_ANONYMOUSLY') and is_feature_enabled('register'))"
 *     },
 *     "get"={"access_control"="is_granted('ROLE_ADMIN') or is_granted('ROLE_ADMIN_CITY')"},
 *     "import"={
 *         "method"="POST",
 *         "access_control"="is_granted('ROLE_ADMIN')",
 *         "path"="/users/import.{_format}"
 *     }
 * }, itemOperations={
 *     "get"={"access_control"="is_granted('ROLE_ADMIN') or (is_granted('ROLE_ADMIN_CITY') and is_in_the_same_city(object.getProfile())) or object == user"},
 *     "put"={"access_control"="is_granted('ROLE_ADMIN') or (is_granted('ROLE_ADMIN_CITY') and is_in_the_same_city(object.getProfile())) or object == user"},
 *     "delete"={"access_control"="is_granted('ROLE_ADMIN') or (is_granted('ROLE_ADMIN_CITY') and is_in_the_same_city(object.getProfile())) or object == user"},
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
 *         "access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY') and is_feature_enabled('register')"
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
 *         "normalization_context"={"groups"={"score_output"}},
 *         "access_control"="(is_granted('ROLE_ADMIN') or object == user or (is_granted('ROLE_ADMIN_CITY') and is_in_the_same_city(object.getProfile()))) and is_feature_enabled('quiz')"
 *     },
 *     "events"={
 *         "path"="/users/{id}/events.{_format}",
 *         "method"="GET",
 *         "controller"="App\Action\UserEvents",
 *         "requirements"={"id"=User::UUID_REQUIREMENT},
 *         "swagger_context"={
 *             "parameters"={
 *                 {"name"="id", "in"="path", "required"=true, "type"="string"}
 *             }
 *         },
 *         "normalization_context"={"groups"={"event_output"}},
 *         "access_control"="((request.query.has('token') and object.getToken() == request.query.get('token')) or object == user) and is_feature_enabled('event')"
 *     }
 * }, subresourceOperations={
 *     "quizzes_get_subresource"={
 *         "requirements"={"id"=User::UUID_REQUIREMENT}
 *     },
 *     "favorites_get_subresource"={
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
     * @Groups({"user_export"})
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
     * @Groups({"admin_output", "admin_input", "user_export"})
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
     * @Groups({"user_output", "user_input", "user_export"})
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
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Length(min="7")
     * @Groups({"user_input"})
     */
    private $plainPassword;

    /**
     * Not persisted in database, security for password update.
     *
     * @Groups({"user_input"})
     */
    private $currentPassword;

    /**
     * Not persisted in database, security for registration.
     *
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\IsTrue(groups={"registration"})
     * @Groups({"user_input"})
     */
    private $cgu = false;

    /**
     * @ORM\Column(type="boolean", name="is_newsletter")
     * @Groups({"user_input", "user_output", "user_export"})
     */
    private $newsletter = false;

    /**
     * @ORM\Column(type="array")
     * @Groups({"admin_output", "admin_input", "user_export"})
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
     * @Groups({"owner_input", "owner_output"})
     */
    private $ignoredPlaces;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Content", fetch="EXTRA_LAZY")
     * @Groups({"owner_input"})
     * @ApiSubresource
     */
    private $favorites;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Team", mappedBy="users")
     */
    private $teams;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Weighing", mappedBy="user", fetch="EXTRA_LAZY")
     * @ApiSubresource
     */
    private $weighings;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profile", mappedBy="user", cascade={"all"}, fetch="EAGER")
     * @Groups({"user_output", "user_export", "user_input"})
     */
    private $profile;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Registration", mappedBy="user", fetch="EXTRA_LAZY")
     */
    private $registrations;

    /**
     * @ORM\Column(type="array")
     * @Assert\Expression("'ROLE_ADMIN_CITY' not in this.getRoles() || value !== []")
     * @Groups({"admin_output", "admin_input", "user_export"})
     */
    private $cities = [];

    /**
     * @ORM\Column(type="boolean", name="is_notify_by_email")
     * @Groups({"owner_input", "owner_output"})
     */
    private $notifyByEmail = false;

    public function __construct()
    {
        $this->quizzes = new ArrayCollection();
        $this->ignoredPlaces = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->weighings = new ArrayCollection();
        $this->registrations = new ArrayCollection();
        $this->token = \bin2hex(\random_bytes(25));
    }

    public function getSalt(): void
    {
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

    public function isNewsletter(): bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(bool $newsletter): void
    {
        $this->newsletter = $newsletter;
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
     * @return Content[]
     */
    public function getFavorites(): array
    {
        return $this->favorites->getValues();
    }

    public function addFavorite(Content $content): void
    {
        if (!$this->favorites->contains($content)) {
            $this->favorites[] = $content;
        }
    }

    public function removeFavorite(Content $content): void
    {
        $this->favorites->removeElement($content);
    }

    /**
     * @return Team[]
     */
    public function getTeams(): array
    {
        return $this->teams->getValues();
    }

    public function addTeam(Team $team): void
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
        }
    }

    public function removeTeam(Team $team): void
    {
        $this->teams->removeElement($team);
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

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): void
    {
        $profile->setUser($this);
        $this->profile = $profile;
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
            $registration->setUser($this);
            $this->registrations[] = $registration;
        }
    }

    public function removeRegistration(Registration $registration): void
    {
        $this->registrations->removeElement($registration);
    }

    /**
     * @return Event[]
     */
    public function getEvents(): array
    {
        return $this->registrations->filter(function (Registration $registration) {
            return $registration->isValidated();
        })->map(function (Registration $registration) {
            return $registration->getEvent();
        })->getValues();
    }

    public function getAbsences(): int
    {
        return $this->registrations->filter(function (Registration $registration) {
            return $registration->getEvent()->isPast() && !$registration->isPresent();
        })->count();
    }

    public function getCities(): array
    {
        return $this->cities;
    }

    public function setCities(array $cities): void
    {
        $this->cities = \array_filter(\array_map('trim', $cities), function ($city) {
            return !empty($city);
        });
    }

    public function isNotifyByEmail(): bool
    {
        return $this->notifyByEmail;
    }

    public function setNotifyByEmail(bool $notifyByEmail): void
    {
        $this->notifyByEmail = $notifyByEmail;
    }
}
