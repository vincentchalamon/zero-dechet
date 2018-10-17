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
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\Entity
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"question_output", "choice_output"}},
 *     "denormalization_context"={"groups"={"question_input", "choice_input"}},
 *     "access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('quiz')"
 * }, collectionOperations={}, itemOperations={"get"})
 */
class Question
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Groups({"question_input", "question_output"})
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Quiz", inversedBy="questions")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotNull
     * @Groups({"question_input"})
     */
    private $quiz;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Choice", mappedBy="question", cascade={"all"}, orphanRemoval=true, fetch="EAGER")
     * @Assert\NotNull
     * @Assert\Count(min="2")
     * @Groups({"question_input", "question_output"})
     */
    private $choices;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Content")
     * @Assert\Count(min="1")
     * @Groups({"question_input", "question_output"})
     */
    private $contents;

    public function __construct()
    {
        $this->choices = new ArrayCollection();
        $this->contents = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(Quiz $quiz): void
    {
        $this->quiz = $quiz;
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
            $choice->setQuestion($this);
            $this->choices[] = $choice;
        }
    }

    public function removeChoice(Choice $choice): void
    {
        $this->choices->removeElement($choice);
    }

    /**
     * @return Content[]
     */
    public function getContents(): array
    {
        return $this->contents->getValues();
    }

    public function addContent(Content $content): void
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
        }
    }

    public function removeContent(Content $content): void
    {
        $this->contents->removeElement($content);
    }
}
