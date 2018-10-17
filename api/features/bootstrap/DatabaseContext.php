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

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class DatabaseContext implements Context
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @BeforeScenario
     */
    public function beginTransaction()
    {
        $this->registry->getConnection()->setAutoCommit(false);
    }

    /**
     * @AfterScenario
     */
    public function rollbackTransaction()
    {
        $this->registry->getConnection()->rollBack();
    }
}
