<?php

/*
 * This file is part of the Zero-Dechet project.
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
 *     "normalization_context"={"groups"={"quiz_output", "place_output", "question_output", "choice_output"}},
 *     "denormalization_context"={"groups"={"quiz_input", "question_input", "choice_input"}},
 *     "access_control"="is_granted('ROLE_USER') and is_feature_enabled('quiz')",
 *     "order"={"position"="ASC"}
 *     }, collectionOperations={
 *     "get",
 *     "post"={"access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('quiz')"}
 *     }, itemOperations={
 *     "get",
 *     "put"={"access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('quiz') and 0 == object.countQuizzes()"},
 *     "delete"={"access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('quiz') and 0 == object.countQuizzes()"}
 * })
 * @ApiFilter(SearchFilter::class, properties={"place"})
 */
class Quiz
{
    /**
     * @ORM\Id
     * @ORM\Column
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="App\Doctrine\Generator\UuidGenerator")
     */
    private $id;

    /**
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="App\Entity\Place")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotNull
     * @Groups({"quiz_input", "quiz_output"})
     */
    private $place;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="quiz", cascade={"all"}, orphanRemoval=true, fetch="EAGER")
     * @Assert\NotNull
     * @Assert\Count(min="1")
     * @Groups({"quiz_input", "quiz_output"})
     */
    private $questions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserQuiz", mappedBy="quiz", fetch="EXTRA_LAZY")
     */
    private $quizzes;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer")
     * @Groups({"quiz_input", "quiz_output"})
     */
    private $position;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPlace(): Place
    {
        return $this->place;
    }

    public function setPlace(Place $place): void
    {
        $this->place = $place;
    }

    /**
     * @return Question[]
     */
    public function getQuestions(): array
    {
        return $this->questions->getValues();
    }

    public function addQuestion(Question $question): void
    {
        if (!$this->questions->contains($question)) {
            $question->setQuiz($this);
            $this->questions[] = $question;
        }
    }

    public function removeQuestion(Question $question): void
    {
        $this->questions->removeElement($question);
    }

    /**
     * @return UserQuiz[]
     */
    public function getQuizzes(): array
    {
        return $this->quizzes->getValues();
    }

    public function countQuizzes(): int
    {
        return $this->quizzes->count();
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
