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
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * todo How to replace request.attributes.get('object')?
 *
 * @ORM\Entity
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"content:read"}},
 *     "denormalization_context"={"groups"={"content:write"}}
 * }, subresourceOperations={
 *     "api_users_favorites_get_subresource"={
 *         "access_control"="is_granted('ROLE_ADMIN') or request.attributes.get('object') == user)"
 *     }
 * }, collectionOperations={
 *     "get"={"access_control"="is_granted('ROLE_USER')"},
 *     "post"={"access_control"="is_granted('ROLE_ADMIN')"}
 * }, itemOperations={
 *     "get"={"access_control"="is_granted('ROLE_USER')"},
 *     "put"={"access_control"="is_granted('ROLE_ADMIN')"},
 *     "delete"={"access_control"="is_granted('ROLE_ADMIN')"}
 * })
 */
class Content
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Groups({"content:write", "content:read"})
     */
    private $title;

    /**
     * @ORM\Column
     * @Gedmo\Slug(fields={"title"})
     * @Groups({"content:write", "content:read"})
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Groups({"content:write", "content:read"})
     */
    private $content;

    /**
     * @ORM\Column(name="is_published", type="boolean")
     * @Groups({"content:write", "admin:read"})
     */
    private $published = false;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }
}
