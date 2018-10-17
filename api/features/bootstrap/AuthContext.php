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

use App\Entity\User;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class AuthContext implements Context
{
    use ContextTrait;

    private $session;
    private $registry;

    public function __construct(ManagerRegistry $registry, SessionInterface $session)
    {
        $this->session = $session;
        $this->registry = $registry;
    }

    /**
     * @Given I am authenticated as :email
     */
    public function iAmAuthenticated(string $email): void
    {
        $this->restContext->iAddHeaderEqualTo('Authorization', 'Bearer '.$this->jwtManager->create($this->registry->getRepository(User::class)->findOneBy(['email' => $email])));
    }

    /**
     * @When I get a private resource
     */
    public function iGetAPrivateResource(): void
    {
        $this->restContext->iAddHeaderEqualTo('Accept', 'application/ld+json');
        $this->restContext->iSendARequestTo(Request::METHOD_GET, '/users');
    }

    /**
     * @When I log in with :username :password
     */
    public function iLogInWith(string $username, string $password): void
    {
        $this->restContext->iAddHeaderEqualTo('Content-Type', 'application/json');
        $this->restContext->iSendARequestToWithBody('POST', '/login', new PyStringNode([\sprintf(<<<'JSON'
{
    "username": "%s",
    "password": "%s"
}
JSON
            , $username, $password
        )], 0));
    }

    /**
     * @Then I get a valid token & user
     */
    public function validateAuthResponse(): void
    {
        $this->minkContext->assertResponseStatus(200);
        $this->jsonContext->theResponseShouldBeInJson();
        $this->jsonContext->theJsonShouldBeValidAccordingToThisSchema(new PyStringNode([<<<'JSON'
{
    "type": "object",
    "properties": {
        "token": {
            "type": "string"
        },
        "user": {
            "type": "string"
        }
    },
    "required": ["token", "user"]
}
JSON
            ], 0));
    }
}
