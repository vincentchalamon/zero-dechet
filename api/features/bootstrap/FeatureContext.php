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

use ApiExtension\Context\ApiContext;
use ApiExtension\Helper\ApiHelper;
use App\Entity\Choice;
use App\Entity\Place;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Shop;
use App\Entity\User;
use App\Entity\UserQuiz;
use Behat\Gherkin\Node\PyStringNode;
use Behatch\Context\BaseContext;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 * todo Improve this class
 */
final class FeatureContext extends BaseContext
{
    use ContextTrait;

    private $registry;
    private $userRepository;
    private $helper;

    public function __construct(ManagerRegistry $registry, ApiHelper $helper)
    {
        $this->registry = $registry;
        $this->userRepository = $registry->getRepository(User::class);
        $this->helper = $helper;
    }

    /**
     * @When there are valid quizzes
     */
    public function createQuizzes(): void
    {
        $em = $this->registry->getManager();

        $cuisine = new Place();
        $cuisine->setName('Cuisine');
        $em->persist($cuisine);

        $salleDeBain = new Place();
        $salleDeBain->setName('Salle de bain');
        $em->persist($salleDeBain);

        $quiz = new Quiz();
        $quiz->setPlace($cuisine);
        $quiz->setPosition(0);

        $question = new Question();
        $question->setTitle('Comment faites-vous vos courses ?');
        $question->setUrls(['https://www.example.com']);
        foreach (['J\'achète en vrac' => true, 'En supermarché' => false] as $label => $valid) {
            $choice = new Choice();
            $choice->setName($label);
            $choice->setValid($valid);
            $question->addChoice($choice);
        }
        $quiz->addQuestion($question);

        $question = new Question();
        $question->setTitle('Comment achetez-vous vos légumes ?');
        $question->setUrls(['https://www.example.com']);
        foreach (['Sous emballage' => false, 'En vrac' => true] as $label => $valid) {
            $choice = new Choice();
            $choice->setName($label);
            $choice->setValid($valid);
            $question->addChoice($choice);
        }
        $quiz->addQuestion($question);

        $em->persist($quiz);

        $quiz = new Quiz();
        $quiz->setPlace($salleDeBain);

        $question = new Question();
        $question->setTitle('Quels produits utilisez-vous sous la douche ?');
        $question->setUrls(['https://www.example.com']);
        foreach (['Gel douche jetable' => false, 'Savon & shampoing solides' => true] as $label => $valid) {
            $choice = new Choice();
            $choice->setName($label);
            $choice->setValid($valid);
            $question->addChoice($choice);
        }
        $quiz->addQuestion($question);

        $em->persist($quiz);

        $quiz = new Quiz();
        $quiz->setPlace($cuisine);
        $quiz->setPosition(1);

        $question = new Question();
        $question->setTitle('Que faites-vous de vos pelures de légume ?');
        $question->setUrls(['https://www.example.com']);
        foreach (['Des soupes' => true, 'Je les composte' => true, 'Je les jette' => false] as $label => $valid) {
            $choice = new Choice();
            $choice->setName($label);
            $choice->setValid($valid);
            $question->addChoice($choice);
        }
        $quiz->addQuestion($question);

        $question = new Question();
        $question->setTitle('Quels fruits achetez-vous généralement ?');
        $question->setUrls(['https://www.example.com']);
        foreach (['Des fruits de saison' => true, 'N\'importe quels fruits' => false] as $label => $valid) {
            $choice = new Choice();
            $choice->setName($label);
            $choice->setValid($valid);
            $question->addChoice($choice);
        }
        $quiz->addQuestion($question);

        $em->persist($quiz);
        $em->flush();
        $em->clear();
    }

    /**
     * @Then the shop is inactive
     */
    public function theShopIsInactive()
    {
        if ($this->registry->getRepository(Shop::class)->findOneBy([])->isActive()) {
            throw new \Exception('Shop is active.');
        }
    }

    /**
     * @Then the shop is active
     */
    public function theShopIsActive()
    {
        if (!$this->registry->getRepository(Shop::class)->findOneBy([])->isActive()) {
            throw new \Exception('Shop is inactive.');
        }
    }

    /**
     * @When I create a new quiz
     */
    public function sendPostRequestToQuiz(): void
    {
        $this->apiContext->sendPostRequestToCollection('quiz', [
            'place' => $this->registry->getRepository(Place::class)->findOneBy([])->getName(),
            'questions' => [
                [
                    'title' => 'Quelle est la couleur du cheval blanc d\'Henri IV ?',
                    'choices' => [
                        [
                            'name' => 'Blanc',
                            'valid' => true,
                        ],
                        [
                            'name' => 'Bleu',
                            'valid' => false,
                        ],
                    ],
                ],
                [
                    'title' => 'Quelle est la question sur la vie, l\'univers et tout le reste ?',
                    'choices' => [
                        [
                            'name' => 'La vie',
                            'valid' => false,
                        ],
                        [
                            'name' => '42',
                            'valid' => true,
                        ],
                        [
                            'name' => 'L\'univers',
                            'valid' => false,
                        ],
                        [
                            'name' => 'Tout le reste',
                            'valid' => false,
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @When I update an existing quiz
     */
    public function sendPutRequestToQuiz(): void
    {
        $this->apiContext->sendPutRequestToItem('quiz', [
            'questions' => [
                [
                    'title' => 'Quelle est la couleur du cheval blanc d\'Henri IV ?',
                    'choices' => [
                        [
                            'name' => 'Blanc',
                            'valid' => true,
                        ],
                        [
                            'name' => 'Bleu',
                            'valid' => false,
                        ],
                    ],
                ],
                [
                    'title' => 'Quelle est la question sur la vie, l\'univers et tout le reste ?',
                    'choices' => [
                        [
                            'name' => 'La vie',
                            'valid' => false,
                        ],
                        [
                            'name' => '42',
                            'valid' => true,
                        ],
                        [
                            'name' => 'L\'univers',
                            'valid' => false,
                        ],
                        [
                            'name' => 'Tout le reste',
                            'valid' => false,
                        ],
                    ],
                ],
            ],
        ], ['id' => $this->registry->getRepository(Quiz::class)->findOneBy([])->getId()]);
    }

    /**
     * @When I get user :user
     */
    public function sendGetRequestToUser(User $user): void
    {
        $this->apiContext->sendGetRequestToItem('user', ['id' => $user->getId()]);
    }

    /**
     * @When I get user :user quizzes
     */
    public function sendGetRequestToUserQuizzes(User $user): void
    {
        $this->restContext->iAddHeaderEqualTo('Accept', ApiContext::FORMAT);
        $this->restContext->iSendARequestTo(Request::METHOD_GET, \sprintf('/users/%s/quizzes', $user->getId()));
    }

    /**
     * @When I update user :user
     */
    public function sendPutRequestToUser(User $user): void
    {
        $this->apiContext->sendPutRequestToItem('user', [
            'email' => 'john.doe@example.com',
        ], ['id' => $user->getId()]);
    }

    /**
     * @When I delete user :user
     */
    public function sendDeleteRequestToUser(User $user): void
    {
        $this->apiContext->sendDeleteRequestToItem('user', ['id' => $user->getId()]);
    }

    /**
     * @When I register with password :password
     * @When I register a user
     * @When I register
     */
    public function sendPostRequestToUserWithPassword(string $password = 'p4$$w0rd'): void
    {
        $this->apiContext->sendPostRequestToCollection('user', [
            'email' => 'jOhN.dOe@eXaMpLe.cOm',
            'plainPassword' => $password,
            'cgu' => true,
        ]);
    }

    /**
     * @When I register with no CGU
     */
    public function sendPostRequestToUserWithoutCGU(): void
    {
        $this->apiContext->sendPostRequestToCollection('user', [
            'email' => 'jOhN.dOe@eXaMpLe.cOm',
            'plainPassword' => 'p4$$w0rd',
        ]);
    }

    /**
     * @Then user has been successfully created
     */
    public function ensureUserExistsInDatabase(): void
    {
        if (null === $this->userRepository->findOneBy(['emailCanonical' => 'john.doe@example.com'])) {
            throw new \Exception('User has not been created.');
        }
    }

    /**
     * @When I update my password with current password equal to :currentPassword
     * @When I update my password
     */
    public function updatePassword(string $currentPassword = 'p4ssw0rd'): void
    {
        $this->apiContext->sendPutRequestToItem('user', [
            'currentPassword' => $currentPassword,
            'plainPassword' => 's3cur3d-p4$$w0rd',
        ], ['id' => $this->userRepository->findOneBy(['email' => 'foo@example.com'])->getId()]);
    }

    /**
     * @Then the user :user has been successfully deleted
     */
    public function userHasBeSuccessfullyDeleted(User $user = null): void
    {
        if (null !== $user) {
            $this->registry->getManagerForClass(User::class)->refresh($user);
        }
        $this->apiContext->itemShouldHaveBeSuccessfullyDeleted('user');
        if (null !== $user && null === $user->getDeletedAt()) {
            throw new \Exception('User not successfully deleted.');
        }
    }

    /**
     * @Given user :user has quizzes
     */
    public function userHasQuizzes(User $user): void
    {
        $em = $this->registry->getManager();
        $cuisine = $this->registry->getRepository(Place::class)->findOneBy(['name' => 'Cuisine']);
        $salleDeBain = $this->registry->getRepository(Place::class)->findOneBy(['name' => 'Salle de bain']);

        // Cuisine niveau 1 : 100%
        $userQuiz = new UserQuiz();
        $userQuiz->setUser($user);
        $userQuiz->setQuiz($this->registry->getRepository(Quiz::class)->findOneBy(['place' => $cuisine, 'position' => 0]));
        $userQuiz->addChoice($this->registry->getRepository(Choice::class)->findOneBy(['name' => 'J\'achète en vrac']));
        $userQuiz->addChoice($this->registry->getRepository(Choice::class)->findOneBy(['name' => 'En vrac']));
        $userQuiz->setCreatedAt(new \DateTime('2018-01-10 10:01:30'));
        $em->persist($userQuiz);

        // Salle de bain niveau 1 : 0%
        $userQuiz = new UserQuiz();
        $userQuiz->setUser($user);
        $userQuiz->setQuiz($this->registry->getRepository(Quiz::class)->findOneBy(['place' => $salleDeBain]));
        $userQuiz->addChoice($this->registry->getRepository(Choice::class)->findOneBy(['name' => 'Gel douche jetable']));
        $userQuiz->setCreatedAt(new \DateTime('2018-01-10 10:10:28'));
        $em->persist($userQuiz);

        // Cuisine niveau 2 : 50%
        $userQuiz = new UserQuiz();
        $userQuiz->setUser($user);
        $userQuiz->setQuiz($this->registry->getRepository(Quiz::class)->findOneBy(['place' => $cuisine, 'position' => 1]));
        $userQuiz->addChoice($this->registry->getRepository(Choice::class)->findOneBy(['name' => 'Je les jette']));
        $userQuiz->addChoice($this->registry->getRepository(Choice::class)->findOneBy(['name' => 'Des fruits de saison']));
        $userQuiz->setCreatedAt(new \DateTime('2018-01-10 12:35:12'));
        $em->persist($userQuiz);

        $em->flush();
        $em->clear();
    }

    /**
     * @Then I see user scores
     */
    public function checkScores()
    {
        $this->minkContext->assertResponseStatus(200);
        $this->jsonContext->theResponseShouldBeInJson();
        $this->jsonContext->theJsonShouldBeValidAccordingToThisSchema(new PyStringNode([\json_encode([
            'type' => 'object',
            'properties' => [
                'Cuisine' => [
                    'type' => 'array',
                    'minItems' => 2,
                    'maxItems' => 2,
                    'items' => [
                        'type' => 'object',
                        'required' => ['quiz', 'score', 'urls'],
                        'properties' => [
                            'quiz' => [
                                'pattern' => '^/quizzes/[\\w-]+$',
                            ],
                            'score' => [
                                'type' => 'number',
                            ],
                            'urls' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'string',
                                    'pattern' => '^https?://.*',
                                ],
                            ],
                        ],
                    ],
                ],
                'Salle de bain' => [
                    'type' => 'array',
                    'minItems' => 1,
                    'maxItems' => 1,
                    'items' => [
                        'type' => 'object',
                        'required' => ['quiz', 'score', 'urls'],
                        'properties' => [
                            'quiz' => [
                                'pattern' => '^/quizzes/[\\w-]+$',
                            ],
                            'score' => [
                                'type' => 'number',
                            ],
                            'urls' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'string',
                                    'pattern' => '^https?://.*',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ])], 0));
    }

    /**
     * @Then I see the user's quizzes
     */
    public function checkQuizzes(): void
    {
        $this->minkContext->assertResponseStatus(200);
        $this->jsonContext->theResponseShouldBeInJson();
        $this->jsonContext->theJsonShouldBeValidAccordingToThisSchema(new PyStringNode([\json_encode([
            'type' => 'object',
            'properties' => [
                '@context' => ['pattern' => '^/contexts/UserQuiz$'],
                '@id' => ['pattern' => '^/users/[\w-]+/quizzes$'],
                '@type' => ['pattern' => '^hydra:Collection$'],
                'hydra:totalItems' => ['type' => 'integer'],
                'hydra:member' => [
                    'type' => 'array',
                    'minItems' => 3,
                    'maxItems' => 3,
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            '@id' => ['pattern' => '^/user_quizzes/[\w-]+$'],
                            '@type' => ['pattern' => '^UserQuiz$'],
                            'score' => ['type' => 'integer'],
                        ],
                        'required' => ['@id', '@type', 'score'],
                    ],
                ],
                'required' => ['@context', '@id', '@type', 'hydra:totalItems', 'hydra:member'],
            ],
        ])], 0));
    }

    /**
     * @When I create a new userQuiz
     */
    public function sendPostRequestToUserQuiz(): void
    {
        $this->restContext->iAddHeaderEqualTo('Accept', ApiContext::FORMAT);
        $this->restContext->iAddHeaderEqualTo('Content-Type', ApiContext::FORMAT);
        $quiz = $this->registry->getRepository(Quiz::class)->findOneBy([]);
        $this->restContext->iSendARequestToWithBody(Request::METHOD_POST, '/user_quizzes', new PyStringNode([\json_encode([
            'quiz' => '/quizzes/'.$quiz->getId(),
            'choices' => \array_map(function (Choice $choice) {
                return $choice->getId();
            }, \array_merge(...\array_map(function (Question $question) {
                return $question->getChoices();
            }, $quiz->getQuestions()))),
        ])], 0));
    }

    /**
     * @When I create a new userQuiz with user :user
     */
    public function sendPostRequestToUserQuizWithUser(User $user): void
    {
        $this->restContext->iAddHeaderEqualTo('Accept', ApiContext::FORMAT);
        $this->restContext->iAddHeaderEqualTo('Content-Type', ApiContext::FORMAT);
        $this->restContext->iSendARequestToWithBody(Request::METHOD_POST, '/user_quizzes', new PyStringNode([\json_encode([
            'user' => '/users/'.$user->getId(),
            'quiz' => '/quizzes/'.$this->registry->getRepository(Quiz::class)->findOneBy([])->getId(),
            'choices' => \array_map(function (Choice $choice) {
                return $choice->getId();
            }, $this->registry->getRepository(Choice::class)->findAll()),
        ])], 0));
    }

    /**
     * @When I get user :user scores
     */
    public function sendGetRequestToUserScores(User $user): void
    {
        $this->restContext->iAddHeaderEqualTo('Accept', ApiContext::FORMAT);
        $this->restContext->iSendARequestTo(Request::METHOD_GET, '/users/'.$user->getId().'/scores');
    }

    /**
     * @When I find :name around :longitude,:latitude up to :distance kilometers
     */
    public function sendGetRequestToGeocoding(string $name, string $longitude, string $latitude, int $distance): void
    {
        $this->restContext->iAddHeaderEqualTo('Accept', ApiContext::FORMAT);
        $this->restContext->iSendARequestTo(Request::METHOD_GET, '/'.$name.'?longitude='.$longitude.'&latitude='.$latitude.'&distance='.($distance * 1000));
    }

    /**
     * @When I validate my account
     * @When I validate my account with token :token
     */
    public function iValidateMyAccount(string $token = null)
    {
        $this->restContext->iAddHeaderEqualTo('Accept', ApiContext::FORMAT);
        $user = $this->userRepository->findOneBy([]);
        $this->restContext->iSendARequestTo(Request::METHOD_GET, '/users/'.$user->getId().'/validate?token='.($token ?: $user->getToken()));
    }

    /**
     * @Then user has been validated
     */
    public function userHasBeenValidated()
    {
        $user = $this->userRepository->findOneBy([]);
        $this->registry->getManagerForClass(User::class)->refresh($user);
        if (!$user->isActive()) {
            throw new \Exception('User has not been successfully validated.');
        }
    }

    /**
     * @Then user has not been validated
     */
    public function userHasNotBeenValidated()
    {
        $user = $this->userRepository->findOneBy([]);
        $this->registry->getManagerForClass(User::class)->refresh($user);
        if ($user->isActive()) {
            throw new \Exception('User has been successfully validated.');
        }
    }

    /**
     * @Transform :user
     */
    public function getUserByEmail(string $user): ?User
    {
        return $this->registry->getRepository(User::class)->findOneBy(['email' => $user]);
    }

    /**
     * @Then the userQuiz should be attached to :user
     */
    public function checkUserQuizHasCorrectUser(User $user): void
    {
        $userQuiz = $this->registry->getRepository(UserQuiz::class)->findOneBy([]);
        if ($user !== $userQuiz->getUser()) {
            throw new \Exception('UserQuiz has been atatched to the wrong user: '.$userQuiz->getUser()->getEmail());
        }
    }

    /**
     * @When I get weighings filtered by user :user
     */
    public function sendGetRequestToWeighingsDataFilteredByUser(User $user): void
    {
        $this->restContext->iAddHeaderEqualTo('Accept', ApiContext::FORMAT);
        $this->restContext->iSendARequestTo(Request::METHOD_GET, '/users/'.$user->getId().'/weighings');
    }

    /**
     * @Then I receive an email to validate my registration
     */
    public function iReceiveAnEmailToValidateMyRegistration()
    {
        $this->mailcatcherContext->verifyMailsSent(1);
        $this->mailcatcherContext->seeMailSubject('Validation de votre adresse email');
        $this->mailcatcherContext->seeMailFrom('no-reply@zero-dechet.app');
        $this->mailcatcherContext->seeMailTo('jOhN.dOe@eXaMpLe.cOm');
    }
}
