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

use App\Entity\Choice;
use App\Entity\Content;
use App\Entity\Quiz;
use App\Entity\User;
use App\Entity\UserQuiz;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class ScoreManager
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getScores(User $user): array
    {
        $scores = [];
        foreach ($this->registry->getRepository(Quiz::class)->findAll() as $quiz) {
            $place = $quiz->getPlace();
            $scores[$place->getName()][$quiz->getPosition()] = $this->getScore($quiz, $user);
        }
        \array_map('ksort', $scores);
        \ksort($scores);

        return $scores;
    }

    public function getScore(Quiz $quiz, User $user): Score
    {
        // Calculate score from last UserQuiz filled
        $userQuizzes = \array_filter($user->getQuizzes(), function (UserQuiz $userQuiz) use ($quiz) {
            return $quiz === $userQuiz->getQuiz();
        });
        $userQuiz = \end($userQuizzes);
        if (!$userQuiz) {
            return new Score($quiz, 0, []);
        }

        $invalidChoices = \array_filter($userQuiz->getChoices(), function (Choice $choice) {
            return !$choice->isValid();
        });
        if ($invalidChoices) {
            $contents = \array_filter(\array_unique(\array_merge(...\array_map(function (Choice $choice) {
                return $choice->getQuestion()->getContents();
            }, $invalidChoices))), function (Content $content) {
                return $content->isPublished();
            });
        }

        $score = \round((\count(\array_filter($userQuiz->getChoices(), function (Choice $choice) {
            return $choice->isValid();
        })) / \count($userQuiz->getChoices())) * 100, 2);

        return new Score($quiz, $score, $contents ?? []);
    }
}
