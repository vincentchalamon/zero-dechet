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

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\Entity
 * @ORM\EntityListeners("App\EntityListener\NewsletterEntityListener")
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"newsletter_output"}},
 *     "denormalization_context"={"groups"={"newsletter_input"}},
 *     "access_control"="(is_granted('ROLE_ADMIN') or 'sent' == object.getStatus()) and is_feature_enabled('newsletter')"
 * }, collectionOperations={
 *     "post"={"access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('newsletter')"},
 *     "get"={"access_control"="is_granted('ROLE_USER') and is_feature_enabled('newsletter')"}
 * }, itemOperations={
 *     "get",
 *     "put"={"access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('newsletter')"},
 *     "delete"={"access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('newsletter')"}
 * })
 */
class Newsletter
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_SENDING = 'sending';
    public const STATUS_SENT = 'sent';

    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Groups({"newsletter_input", "newsletter_output"})
     */
    private $subject;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Groups({"newsletter_input", "newsletter_output"})
     */
    private $content;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Assert\Choice(choices={
     *     Newsletter::STATUS_PENDING,
     *     Newsletter::STATUS_SENDING,
     *     Newsletter::STATUS_SENT
     * })
     * @Groups({"newsletter_input", "newsletter_output"})
     */
    private $status = self::STATUS_PENDING;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
