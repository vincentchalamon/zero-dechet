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
use CoopTilleuls\ForgotPasswordBundle\Manager\PasswordTokenManager;
use Symfony\Bridge\Doctrine\ManagerRegistry;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class ForgotPasswordContext implements Context
{
    use ContextTrait;

    private $registry;
    private $passwordTokenManager;

    public function __construct(ManagerRegistry $registry, PasswordTokenManager $passwordTokenManager)
    {
        $this->registry = $registry;
        $this->passwordTokenManager = $passwordTokenManager;
    }

    /**
     * @Given I have a valid token
     */
    public function iHaveAValidToken()
    {
        $this->passwordTokenManager->createPasswordToken($this->getUser());
    }

    /**
     * @Given I have an expired token
     */
    public function iHaveAnExpiredToken()
    {
        $this->passwordTokenManager->createPasswordToken($this->getUser(), new \DateTime('-1 minute'));
    }

    /**
     * @When I reset my password
     */
    public function iResetMyPassword()
    {
        $this->restContext->iAddHeaderEqualTo('Accept', 'application/json');
        $this->restContext->iSendARequestToWithBody('POST', '/forgot_password/', new PyStringNode([<<<'JSON'
{
    "email": "foo@example.com"
}
JSON
        ], 0));
    }

    /**
     * @When I reset my password using invalid email address
     */
    public function iResetMyPasswordUsingInvalidEmailAddress()
    {
        $this->restContext->iAddHeaderEqualTo('Accept', 'application/json');
        $this->restContext->iSendARequestToWithBody('POST', '/forgot_password/', new PyStringNode([<<<'JSON'
{
    "email": "john.doe@example.com"
}
JSON
        ], 0));
    }

    /**
     * @When I reset my password using no email address
     */
    public function iResetMyPasswordUsingNoEmailAddress()
    {
        $this->restContext->iAddHeaderEqualTo('Accept', 'application/json');
        $this->restContext->iSendARequestTo('POST', '/forgot_password/');
    }

    /**
     * @When I set a new invalid password
     */
    public function iUpdateMyPasswordUsingInvalidPassword()
    {
        $token = $this->passwordTokenManager->createPasswordToken($this->getUser());

        $this->restContext->iAddHeaderEqualTo('Accept', 'application/json');
        $this->restContext->iSendARequestToWithBody('POST', '/forgot_password/'.$token->getToken(), new PyStringNode([<<<'JSON'
{
    "password": "foo"
}
JSON
        ], 0));
    }

    /**
     * @When I set a new password
     */
    public function iUpdateMyPassword()
    {
        $token = $this->passwordTokenManager->createPasswordToken($this->getUser());

        $this->restContext->iAddHeaderEqualTo('Accept', 'application/json');
        $this->restContext->iSendARequestToWithBody('POST', '/forgot_password/'.$token->getToken(), new PyStringNode([<<<'JSON'
{
    "password": "loremipsum"
}
JSON
        ], 0));
    }

    /**
     * @When I update my password using no password
     */
    public function iUpdateMyPasswordUsingNoPassword()
    {
        $token = $this->passwordTokenManager->createPasswordToken($this->getUser());

        $this->restContext->iAddHeaderEqualTo('Accept', 'application/json');
        $this->restContext->iSendARequestTo('POST', '/forgot_password/'.$token->getToken());
    }

    /**
     * @When I update my password using an invalid token
     */
    public function iUpdateMyPasswordUsingAnInvalidToken()
    {
        $this->restContext->iAddHeaderEqualTo('Accept', 'application/json');
        $this->restContext->iSendARequestToWithBody('POST', '/forgot_password/12345', new PyStringNode([<<<'JSON'
{
    "password": "foo"
}
JSON
        ], 0));
    }

    /**
     * @When I update my password using an expired token
     */
    public function iUpdateMyPasswordUsingAnExpiredToken()
    {
        $token = $this->passwordTokenManager->createPasswordToken($this->getUser(), new \DateTime('-1 minute'));

        $this->restContext->iAddHeaderEqualTo('Accept', 'application/json');
        $this->restContext->iSendARequestToWithBody('POST', '/forgot_password/'.$token->getToken(), new PyStringNode([<<<'JSON'
{
    "password": "foo"
}
JSON
        ], 0));
    }

    /**
     * @Then I get a password token
     */
    public function iGetAPasswordToken()
    {
        $token = $this->passwordTokenManager->createPasswordToken($this->getUser());
        $token->setToken('d7xtQlJVyN61TzWtrY6xy37zOxB66BqMSDXEbXBbo2Mw4Jjt9C');
        $this->registry->getManager()->persist($token);
        $this->registry->getManager()->flush();

        $this->restContext->iSendARequestTo('GET', '/forgot_password/'.$token->getToken());
    }

    /**
     * @Then I see a password token
     */
    public function iShouldGetAPasswordToken()
    {
        $this->minkContext->assertResponseStatus(200);
        $this->jsonContext->theResponseShouldBeInJson();
    }

    /**
     * @Then I get a password token using an expired token
     */
    public function iGetAPasswordTokenUsingAnExpiredToken()
    {
        $token = $this->passwordTokenManager->createPasswordToken($this->getUser(), new \DateTime('-1 minute'));

        $this->restContext->iSendARequestTo('GET', '/forgot_password/'.$token->getToken());
    }

    /**
     * @Then my password has been updated
     */
    public function myPasswordHasBeenUpdated()
    {
        $this->minkContext->assertResponseStatus(204);
    }

    private function getUser(): User
    {
        return $this->registry->getRepository(User::class)->findOneBy(['email' => 'foo@example.com']);
    }
}
