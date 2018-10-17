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

namespace App\Score;

use App\Entity\Content;
use App\Entity\Quiz;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class Score
{
    /**
     * @var Quiz
     */
    private $quiz;

    /**
     * @var float
     */
    private $score;

    /**
     * @var Content[]
     */
    private $contents;

    public function __construct(Quiz $quiz, float $score, array $contents)
    {
        $this->quiz = $quiz;
        $this->score = $score;
        $this->contents = $contents;
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function getContents(): array
    {
        return $this->contents;
    }
}
