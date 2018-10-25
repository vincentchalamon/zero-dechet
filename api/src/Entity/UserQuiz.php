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
 *     "normalization_context"={"groups"={"user_quiz_output", "choice_output"}},
 *     "denormalization_context"={"groups"={"user_quiz_input"}}
 * }, subresourceOperations={
 *     "api_users_quizzes_get_subresource"={
 *         "access_control"="(is_granted('ROLE_ADMIN') or request.attributes.get('object') == user or (is_granted('ROLE_ADMIN_CITY') and is_in_the_same_city(request.attributes.get('object').getProfile()))) and is_feature_enabled('quiz')"
 *     }
 * }, collectionOperations={
 *     "post"={"access_control"="is_granted('ROLE_USER') and is_feature_enabled('quiz')"}
 * }, itemOperations={
 *     "get"={"access_control"="(is_granted('ROLE_ADMIN') or object.getUser() == user) and is_feature_enabled('quiz')"}
 * })
 */
class UserQuiz
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="quizzes")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Quiz", inversedBy="quizzes")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotNull
     * @Groups({"user_quiz_input"})
     */
    private $quiz;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Choice")
     * @Assert\Count(min="1")
     * @Groups({"user_quiz_input", "user_quiz_output"})
     */
    private $choices;

    public function __construct()
    {
        $this->choices = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(Quiz $quiz): void
    {
        $this->quiz = $quiz;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Choice[]
     */
    public function getChoices(): array
    {
        return $this->choices->getValues();
    }

    public function addChoice(Choice $choice): void
    {
        if (!$this->choices->contains($choice)) {
            $this->choices[] = $choice;
        }
    }

    public function removeChoice(Choice $choice): void
    {
        $this->choices->removeElement($choice);
    }
}
