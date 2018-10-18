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
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\Entity
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"choice_output"}},
 *     "denormalization_context"={"groups"={"choice_input"}},
 *     "access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('quiz')",
 *     "order"={"position"="ASC"}
 * }, collectionOperations={}, itemOperations={"get"})
 */
class Choice
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", inversedBy="choices")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotNull
     * @Groups({"choice_input"})
     */
    private $question;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Groups({"choice_input", "choice_output"})
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", name="is_valid")
     * @Groups({"choice_input", "admin_output"})
     */
    private $valid = false;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer")
     * @Groups({"choice_input", "admin_output"})
     */
    private $position;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): void
    {
        $this->question = $question;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): void
    {
        $this->valid = $valid;
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
