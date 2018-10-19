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
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class DatabaseContext implements Context
{
    /**
     * @BeforeSuite
     */
    public static function disableAutocommit()
    {
        StaticDriver::setKeepStaticConnections(true);
    }

    /**
     * @BeforeScenario
     */
    public function beginTransaction()
    {
        StaticDriver::beginTransaction();
    }

    /**
     * @AfterScenario
     */
    public function rollbackTransaction()
    {
        StaticDriver::rollBack();
    }
}
