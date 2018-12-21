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
use ApiExtension\SchemaGenerator\SchemaGeneratorInterface;
use App\Entity\Choice;
use App\Entity\Event;
use App\Entity\Notification;
use App\Entity\Place;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Registration;
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
    private const ICS_PATTERN = '/^BEGIN:VCALENDAR\nVERSION:2\.0\nCALSCALE:GREGORIAN\n(?:BEGIN:VEVENT\nSUMMARY:[^\n]+\nDTSTART;TZID=UTC:\d{8}T\d{6}\nDTEND;TZID=UTC:\d{8}T\d{6}\nLOCATION:[^\n]+\nDESCRIPTION:[^\n]*\nSTATUS:CONFIRMED\nSEQUENCE:3\nBEGIN:VALARM\nTRIGGER:\-PT10M\nACTION:DISPLAY\nEND:VALARM\nEND:VEVENT\n)+END:VCALENDAR$/m';
    use ContextTrait;

    private $registry;
    private $helper;
    private $userRepository;
    private $schemaGenerator;

    public function __construct(ManagerRegistry $registry, ApiHelper $helper, SchemaGeneratorInterface $schemaGenerator)
    {
        $this->registry = $registry;
        $this->helper = $helper;
        $this->schemaGenerator = $schemaGenerator;
        $this->userRepository = $registry->getRepository(User::class);
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
                        'required' => ['quiz', 'score', 'contents'],
                        'properties' => [
                            'quiz' => [
                                'type' => 'object',
                                'required' => ['@id', '@type', 'place', 'questions', 'position'],
                                'properties' => [
                                    '@id' => [
                                        'pattern' => '^/quizzes/[\\w-]+$',
                                    ],
                                    '@type' => [
                                        'pattern' => '^Quiz$',
                                    ],
                                    'place' => [
                                        'type' => 'object',
                                        'required' => ['@id', '@type', 'name'],
                                        'properties' => [
                                            '@id' => [
                                                'pattern' => '^/places/[\\w-]+$',
                                            ],
                                            '@type' => [
                                                'pattern' => '^Place$',
                                            ],
                                            'name' => [
                                                'type' => 'string',
                                            ],
                                        ],
                                    ],
                                    'questions' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'required' => ['@id', '@type', 'title', 'choices'],
                                            'properties' => [
                                                '@id' => [
                                                    'pattern' => '^/questions/[\\w-]+$',
                                                ],
                                                '@type' => [
                                                    'pattern' => '^Question$',
                                                ],
                                                'title' => [
                                                    'type' => 'string',
                                                ],
                                                'choices' => [
                                                    'type' => 'array',
                                                    'items' => [
                                                        'type' => 'object',
                                                        'required' => ['@id', '@type', 'name'],
                                                        'properties' => [
                                                            '@id' => [
                                                                'pattern' => '^/choices/[\\w-]+$',
                                                            ],
                                                            '@type' => [
                                                                'pattern' => '^Choice$',
                                                            ],
                                                            'name' => [
                                                                'type' => 'string',
                                                            ],
                                                            'position' => [
                                                                'type' => 'number',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'position' => [
                                        'type' => 'number',
                                    ],
                                ],
                            ],
                            'score' => [
                                'type' => 'number',
                            ],
                            'contents' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'required' => ['@id', '@type', 'title', 'content'],
                                    'properties' => [
                                        '@id' => [
                                            'pattern' => '^/contents/[\\w-]+$',
                                        ],
                                        '@type' => [
                                            'pattern' => '^Content$',
                                        ],
                                        'title' => [
                                            'type' => 'string',
                                        ],
                                        'content' => [
                                            'type' => 'string',
                                        ],
                                    ],
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
                        'required' => ['quiz', 'score', 'contents'],
                        'properties' => [
                            'quiz' => [
                                'pattern' => '^/quizzes/[\\w-]+$',
                            ],
                            'score' => [
                                'type' => 'number',
                            ],
                            'contents' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'required' => ['@id', '@type', 'title', 'content'],
                                    'properties' => [
                                        '@id' => [
                                            'pattern' => '^/contents/[\\w-]+$',
                                        ],
                                        '@type' => [
                                            'pattern' => '^Content$',
                                        ],
                                        'title' => [
                                            'type' => 'string',
                                        ],
                                        'content' => [
                                            'type' => 'string',
                                        ],
                                    ],
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
        $this->restContext->iSendARequestToWithBody(Request::METHOD_POST, '/user_quizzes', new PyStringNode([\json_encode([
            'quiz' => '/quizzes/'.$this->registry->getRepository(Quiz::class)->findOneBy([])->getId(),
            'choices' => \array_map(function (Choice $choice) {
                return '/choices/'.$choice->getId();
            }, $this->registry->getRepository(Choice::class)->findAll()),
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
                return '/choices/'.$choice->getId();
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
     * @When I register to an event
     * @When I register to this event
     * @When I register to event :event
     * @When I register :nb attendees to an event
     * @When I register :nb attendees to this event
     * @When I register :nb attendees to event :event
     */
    public function sendPostRequestToEvent(int $nb = 1, Event $event = null): void
    {
        $this->restContext->iAddHeaderEqualTo('Accept', ApiContext::FORMAT);
        $this->restContext->iAddHeaderEqualTo('Content-Type', ApiContext::FORMAT);
        $id = $event ? $event->getId() : $this->registry->getRepository(Event::class)->findOneBy([])->getId();
        $this->restContext->iSendARequestTo(Request::METHOD_POST, '/registrations', new PyStringNode([\json_encode([
            'event' => '/events/'.$id,
            'attendees' => $nb,
        ])], 0));
    }

    /**
     * @When I register user :user to an event
     */
    public function sendPostRequestToEventWithUser(User $user): void
    {
        $this->restContext->iAddHeaderEqualTo('Accept', ApiContext::FORMAT);
        $this->restContext->iAddHeaderEqualTo('Content-Type', ApiContext::FORMAT);
        $id = $this->registry->getRepository(Event::class)->findOneBy([])->getId();
        $this->restContext->iSendARequestToWithBody(Request::METHOD_POST, '/registrations', new PyStringNode([\json_encode([
            'event' => '/events/'.$id,
            'user' => '/users/'.$user->getId(),
        ])], 0));
    }

    /**
     * @Then I am registered to this event
     */
    public function checkEventUsers(): void
    {
        $event = $this->registry->getRepository(Event::class)->findBy([], ['startAt' => 'DESC'], 1)[0];
        $user = $this->registry->getRepository(User::class)->findBy([], ['email' => 'DESC'], 1)[0];
        $this->registry->getManagerForClass(Event::class)->refresh($event);
        $users = \array_map(function (Registration $registration) {
            return $registration->getUser();
        }, $event->getRegistrations());
        if (!\in_array($user, $users, true)) {
            throw new \Exception(\sprintf('Event "%s" does not contain user "%s".', $event->getTitle(), $user->getEmail()));
        }
    }

    /**
     * @Given I'm registered to this event
     * @Given I'm registered and :status to this event
     * @Given I'm registered to these events
     * @Given I'm registered and :status to these events
     * @Given I'm registered to event :event
     * @Given I'm registered and :status to event :event
     * @Given user :user is registered to this event
     * @Given user :user is registered and :status to this event
     * @Given user :user is registered to these events
     * @Given user :user is registered and :status to these events
     * @Given user :user is registered to event :event
     * @Given user :user is registered and :status to event :event
     */
    public function addEventUser(User $user = null, Event $event = null, string $status = Registration::STATUS_PENDING): void
    {
        $events = null === $event ? $this->registry->getRepository(Event::class)->findBy([]) : [$event];
        $user = $user ?: $this->registry->getRepository(User::class)->findOneBy([]);
        $em = $this->registry->getManager();
        foreach ($events as $event) {
            $registration = new Registration();
            $registration->setUser($user);
            $registration->setEvent($event);
            $registration->setStatus($status);
            $em->persist($registration);
        }
        $em->flush();
        $em->clear();
    }

    /**
     * @Given user :user is not registered to this event
     */
    public function userIsNotRegisteredToEvent(User $user): void
    {
        if (null !== $this->registry->getRepository(Registration::class)->findOneBy(['user' => $user])) {
            throw new \Exception(\sprintf('A registration has been found for user %s.', $user->getEmail()));
        }
    }

    /**
     * @Given user :user is successfully registered to this event
     */
    public function userIsRegisteredToEvent(User $user): void
    {
        if (null === $this->registry->getRepository(Registration::class)->findOneBy(['user' => $user])) {
            throw new \Exception(\sprintf('No registration has been found for user %s.', $user->getEmail()));
        }
    }

    /**
     * @When I export my events in webcal
     * @When I export a user events in webcal
     */
    public function sendGetRequestToEventsListInWebcal()
    {
        $this->restContext->iAddHeaderEqualTo('Accept', 'text/calendar');
        $user = $this->userRepository->findOneBy([]);
        $this->restContext->iSendARequestTo(Request::METHOD_GET, '/users/'.$user->getId().'/events.ics?token='.$user->getToken());
    }

    /**
     * @When I export a user events in webcal without the key
     */
    public function sendGetRequestToEventsListInWebcalWithoutKey()
    {
        $this->restContext->iAddHeaderEqualTo('Accept', 'text/calendar');
        $user = $this->userRepository->findOneBy([]);
        $this->restContext->iSendARequestTo(Request::METHOD_GET, '/users/'.$user->getId().'/events.ics');
    }

    /**
     * @When I export an invalid user events in webcal
     */
    public function sendGetRequestToEventsListInWebcalWithInvalidUser()
    {
        $this->restContext->iAddHeaderEqualTo('Accept', 'text/calendar');
        $this->restContext->iSendARequestTo(Request::METHOD_GET, '/users/foo/events.ics');
    }

    /**
     * @Then I see the event webcal
     */
    public function checkWebcalEventsList()
    {
        $this->minkContext->assertResponseStatus(200);
        if (!\preg_match(self::ICS_PATTERN, $this->minkContext->getSession()->getPage()->getContent())) {
            throw new \Exception('Webcal seems not valid.');
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
     * @Transform :event
     */
    public function getEventByTitle(string $event): ?Event
    {
        return $this->registry->getRepository(Event::class)->findOneBy(['title' => $event]);
    }

    /**
     * @When I get a list of contents filtered by title :title
     */
    public function sendGetRequestToContentsFilteredBy(string $title)
    {
        $this->restContext->iAddHeaderEqualTo('Accept', ApiContext::FORMAT);
        $this->restContext->iSendARequestTo(Request::METHOD_GET, '/contents?title='.$title);
    }

    /**
     * @Given user :user has favorites
     */
    public function addFavoritesToUser(User $user)
    {
        $contents = $this->registry->getRepository(Content::class)->findAll();
        foreach ($contents as $content) {
            $user->addFavorite($content);
        }
        $em = $this->registry->getManagerForClass(User::class);
        $em->persist($user);
        $em->flush();
        $em->clear();
    }

    /**
     * @When I get user :user favorites
     */
    public function sendGetRequestToUserFavorites(User $user): void
    {
        $this->restContext->iAddHeaderEqualTo('Accept', ApiContext::FORMAT);
        $this->restContext->iSendARequestTo(Request::METHOD_GET, \sprintf('/users/%s/favorites', $user->getId()));
    }

    /**
     * @Then I see the user's favorites
     */
    public function checkFavorites(): void
    {
        $this->minkContext->assertResponseStatus(200);
        $this->jsonContext->theResponseShouldBeInJson();
        $this->jsonContext->theJsonShouldBeValidAccordingToThisSchema(new PyStringNode([\json_encode([
            'type' => 'object',
            'properties' => [
                '@context' => ['pattern' => '^/contexts/Content$'],
                '@id' => ['pattern' => '^/users/[\w-]+/favorites$'],
                '@type' => ['pattern' => '^hydra:Collection$'],
                'hydra:totalItems' => ['type' => 'integer'],
                'hydra:member' => [
                    'type' => 'array',
                    'minItems' => 3,
                    'maxItems' => 3,
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            '@id' => ['pattern' => '^/contents/[\w-]+$'],
                            '@type' => ['pattern' => '^Content$'],
                            'title' => ['type' => 'string'],
                            'content' => ['type' => 'string'],
                        ],
                        'required' => ['@id', '@type', 'title', 'content'],
                    ],
                ],
            ],
            'required' => ['@context', '@id', '@type', 'hydra:totalItems', 'hydra:member'],
        ])], 0));
    }

    /**
     * @When I add favorites
     * @When I add favorites to :user
     */
    public function addUserFavorites(User $user = null): void
    {
        if (!$user) {
            $user = $this->userRepository->findOneBy([]);
        }
        $contents = $this->registry->getRepository(Content::class)->findBy([]);
        $this->apiContext->sendPutRequestToItem('user', [
            'favorites' => \array_map(function (Content $content) {
                return $content->getTitle();
            }, $contents),
        ], ['id' => $user->getId()]);
    }

    /**
     * @Then user has :nb favorites
     * @Then user :user has :nb favorites
     */
    public function checkUserHasFavorites(int $nb, User $user = null): void
    {
        if (!$user) {
            $user = $this->userRepository->findOneBy([]);
        }
        $this->registry->getManagerForClass(User::class)->refresh($user);
        if ($nb !== ($count = \count($user->getFavorites()))) {
            throw new \Exception(\sprintf('User has %s favorites.', 0 === $count ? 'no' : $count));
        }
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
     * @When I export event :event registrations in CSV
     */
    public function exportEventRegistrationsInCSV(Event $event): void
    {
        $this->restContext->iAddHeaderEqualTo('Accept', 'text/csv');
        $this->restContext->iSendARequestTo(Request::METHOD_GET, '/events/'.$event->getId().'/registrations');
    }

    /**
     * @When I export weighings in CSV for user :user
     */
    public function sendGetRequestToExportWeighingsForUser(User $user): void
    {
        $this->restContext->iAddHeaderEqualTo('Accept', 'text/csv');
        $this->restContext->iSendARequestTo(Request::METHOD_GET, '/users/'.$user->getId().'/weighings');
    }

    /**
     * @Then I get a list of users in CSV
     */
    public function validateCsvResponse(): void
    {
        $this->minkContext->assertResponseStatus(200);
        $csv = \array_map('str_getcsv', \explode("\n", <<<'CSV'
active,email,roles,cities,firstName,lastName,familySize,nbAdults,nbChildren,nbBabies,nbPets,mobile,phone,address,postcode,city,biFlow
1,admin@example.com,ROLE_ADMIN,,,,,,,,,,,,,,
1,bar@example.com,ROLE_USER,,Jane,DOE,0,0,0,0,0,,,,,,
1,foo@example.com,ROLE_USER,,John,DOE,3,2,1,0,1,,,"123 chemin du moulin",75000,Lille,
CSV
        ));
        $data = \array_map('str_getcsv', \explode("\n", $this->minkContext->getSession()->getPage()->getContent()));
        $adminRow = \current(\array_filter($data, function ($row) {
            return 'admin@example.com' === $row[1];
        }));
        $fooRow = \current(\array_filter($data, function ($row) {
            return 'foo@example.com' === $row[1];
        }));
        $barRow = \current(\array_filter($data, function ($row) {
            return 'bar@example.com' === $row[1];
        }));
        if ($csv[0] !== $data[0] || $csv[1] !== $adminRow || $csv[2] !== $barRow || $csv[3] !== $fooRow) {
            throw new \Exception('CSV response seems not valid.');
        }
    }

    /**
     * @Then CSV should contain :nb lines
     */
    public function csvShouldContainNbLines(int $nb): void
    {
        $this->minkContext->assertResponseStatus(200);
        $data = \array_map('str_getcsv', \explode("\n", $this->minkContext->getSession()->getPage()->getContent()));
        \array_shift($data); // Remove headers
        if ($nb !== \count($data)) {
            throw new \Exception(\sprintf('CSV response does not have the same number of lines: %d expected, got %d.', $nb, \count($data)));
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
     * @When I get weighings filtered by city :city
     */
    public function sendGetRequestToWeighingsDataFilteredByCity(string $city): void
    {
        $this->restContext->iAddHeaderEqualTo('Accept', ApiContext::FORMAT);
        $this->restContext->iSendARequestTo(Request::METHOD_GET, '/weighings?user.profile.city='.$city);
    }

//    /**
//     * @Then I see a list of weighings data
//     */
//    public function validateResponseUserWeighings(): void
//    {
//        $schema = $this->schemaGenerator->generate(new \ReflectionClass(Weighing::class), ['collection' => true, 'root' => true]);
//        unset($schema['properties']['@id']);
//        $this->apiContext->validateCollectionJsonSchema('weighing', null, $schema);
//    }

    /**
     * @When I like an event
     */
    public function ILikeAnEvent(): void
    {
        /** @var Event $event */
        $event = $this->registry->getRepository(Event::class)->findOneBy([]);
        $this->restContext->iAddHeaderEqualTo('Accept', ApiContext::FORMAT);
        $this->restContext->iAddHeaderEqualTo('Content-Type', ApiContext::FORMAT);
        $this->restContext->iSendARequestTo(Request::METHOD_PUT, '/events/'.$event->getId().'/like');
    }

    /**
     * @Given user :user likes the event :event
     */
    public function userLikesTheEvent(User $user, Event $event): void
    {
        $event->addLike($user);
        $em = $this->registry->getManager();
        $em->persist($event);
        $em->flush();
        $em->clear();
    }

    /**
     * @When I get event :event registrations
     * @When I get an event registrations
     */
    public function sendGetRequestToEventRegistrations(Event $event = null): void
    {
        $event = $event ?: $this->registry->getRepository(Event::class)->findOneBy([]);
        $this->restContext->iAddHeaderEqualTo('Accept', ApiContext::FORMAT);
        $this->restContext->iSendARequestTo(Request::METHOD_GET, '/events/'.$event->getId().'/registrations');
    }

    /**
     * @When I validate user :user registration
     */
    public function validateUserRegistration(User $user): void
    {
        $registration = $this->registry->getRepository(Registration::class)->findOneBy(['user' => $user]);
        $this->apiContext->sendPutRequestToItem('registration', [
            'status' => Registration::STATUS_VALIDATED,
        ], ['id' => $registration->getId()]);
    }

    /**
     * @When I refuse user :user registration
     */
    public function refuseUserRegistration(User $user): void
    {
        $registration = $this->registry->getRepository(Registration::class)->findOneBy(['user' => $user]);
        $this->apiContext->sendPutRequestToItem('registration', [
            'status' => Registration::STATUS_REFUSED,
        ], ['id' => $registration->getId()]);
    }

    /**
     * @Then the registration is :status
     * @Then user :user registration is :status
     */
    public function checkRegistrationStatus(string $status, User $user = null): void
    {
        $registration = $this->registry->getRepository(Registration::class)->findBy($user ? ['user' => $user] : [], ['createdAt' => 'DESC'], 1)[0];
        $this->registry->getManager()->refresh($registration);
        if ($status !== $registration->getStatus()) {
            throw new \Exception(\sprintf('Invalid registration status: %s expected, got %s.', $status, $registration->getStatus()));
        }
    }

    /**
     * @Then I see a list of event registrations
     */
    public function validateEventRegistrationsSchema(): void
    {
        $this->apiContext->validateCollectionJsonSchema('registrations', null, [
            'type' => 'object',
            'properties' => [
                '@id' => [
                    'type' => 'string',
                    'pattern' => '^/events/[\\w-]+/registrations$',
                ],
                '@type' => [
                    'type' => 'string',
                    'pattern' => '^hydra:Collection$',
                ],
                '@context' => [
                    'type' => 'string',
                    'pattern' => '^/contexts/Registration$',
                ],
                'hydra:member' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            '@id' => [
                                'type' => 'string',
                                'pattern' => '^/registrations/[\\w-]+$',
                            ],
                            '@type' => [
                                'type' => 'string',
                                'pattern' => '^Registration$',
                            ],
                            'createdAt' => [
                                'type' => [
                                    'string',
                                ],
                                'pattern' => '^\\d{4}-\\d{2}-\\d{2}T\\d{2}:\\d{2}:\\d{2}\\+00:00$',
                            ],
                            'user' => [
                                'type' => [
                                    'string',
                                ],
                                'pattern' => '/users/[\\w-]+',
                            ],
                            'event' => [
                                'type' => [
                                    'string',
                                ],
                                'pattern' => '/events/[\\w-]+',
                            ],
                            'attendees' => [
                                'type' => [
                                    'integer',
                                ],
                            ],
                        ],
                    ],
                ],
                'hydra:totalItems' => [
                    'type' => 'integer',
                ],
            ],
        ]);
    }

    /**
     * @Then no notification is sent
     * @Then :nb notification is sent
     * @Then :nb notifications are sent
     * @Then :nb notification is sent to :user
     * @Then :nb notifications are sent to :user
     * @Then :nb notification is sent to :user with message :message
     * @Then :nb notifications are sent to :user with message :message
     */
    public function notificationIsSent(int $nb = 0, User $user = null, string $message = null): void
    {
        $criteria = [];
        if (null !== $user) {
            $criteria['user'] = $user;
        }
        if (null !== $message) {
            $criteria['message'] = $message;
        }
        $notifications = $this->registry->getRepository(Notification::class)->findBy($criteria);
        if ($nb !== \count($notifications)) {
            throw new \Exception(\sprintf('Expected %d notification(s), got %d.', $nb, \count($notifications)));
        }
    }
}
