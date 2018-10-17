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

use App\Entity\User;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\BrowserKit\Client as BrowserKitClient;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class AuthContext implements Context
{
    use ContextTrait;

    private $registry;
    private $session;

    public function __construct(ManagerRegistry $registry, SessionInterface $session)
    {
        $this->registry = $registry;
        $this->session = $session;
    }

    /**
     * @Given I am authenticated as :email
     */
    public function iAmAuthenticated(string $email): void
    {
        /** @var User|null $user */
        $user = $this->registry->getManagerForClass(User::class)->getRepository(User::class)->loadUserByUsername($email);
        if (!$user) {
            throw new UsernameNotFoundException(\sprintf('User %s is not valid.'));
        }

        $token = new UsernamePasswordToken($email, null, 'main', $user->getRoles());
        $this->session->set('_security_main', \serialize($token));
        $this->session->save();

        /** @var BrowserKitClient $client */
        $client = $this->minkContext->getSession()->getDriver()->getClient();
        $client->getCookieJar()->set(new Cookie($this->session->getName(), $this->session->getId()));
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
        $this->apiContext->validateItemJsonSchema('user');
    }
}
