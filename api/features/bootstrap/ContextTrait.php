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

use Alex\MailCatcher\Behat\MailCatcherContext;
use ApiExtension\Context\ApiContext;
use ApiExtension\Context\FixturesContext;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\MinkContext;
use Behatch\Context\JsonContext;
use Behatch\Context\RestContext;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
trait ContextTrait
{
    /**
     * @var RestContext
     */
    private $restContext;

    /**
     * @var MinkContext
     */
    private $minkContext;

    /**
     * @var JsonContext
     */
    private $jsonContext;

    /**
     * @var FixturesContext
     */
    private $fixturesContext;

    /**
     * @var ApiContext
     */
    private $apiContext;

    /**
     * @var MailCatcherContext
     */
    private $mailcatcherContext;

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        /** @var InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();
        $this->restContext = $environment->getContext(RestContext::class);
        $this->minkContext = $environment->getContext(MinkContext::class);
        $this->jsonContext = $environment->getContext(JsonContext::class);
        $this->fixturesContext = $environment->getContext(FixturesContext::class);
        $this->apiContext = $environment->getContext(ApiContext::class);
        $this->mailcatcherContext = $environment->getContext(MailCatcherContext::class);
    }
}
