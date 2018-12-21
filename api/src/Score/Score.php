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

use App\Entity\Quiz;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class Score
{
    /**
     * @var Quiz
     * @Groups({"score:read"})
     */
    private $quiz;

    /**
     * @var float
     * @Groups({"score:read"})
     */
    private $score;

    /**
     * @var string[]
     * @Groups({"score:read"})
     */
    private $urls;

    public function __construct(Quiz $quiz, float $score, array $urls)
    {
        $this->quiz = $quiz;
        $this->score = $score;
        $this->urls = $urls;
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function getUrls(): array
    {
        return $this->urls;
    }
}
