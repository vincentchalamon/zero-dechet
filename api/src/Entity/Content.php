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

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Doctrine\ORM\Filter\FulltextSearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * todo Replace request.attributes.get('object') by object
 * @ORM\Entity
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"content_output"}},
 *     "denormalization_context"={"groups"={"content_input"}},
 *     "access_control"="(is_granted('ROLE_ADMIN') or request.attributes.get('object') == user or (is_granted('ROLE_ADMIN_CITY') and is_in_the_same_city(request.attributes.get('object').getProfile()))) and is_feature_enabled('content')"
 * }, collectionOperations={
 *     "get"={"access_control"="is_granted('ROLE_USER') and is_feature_enabled('content')"},
 *     "post"={"access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('content')"}
 * }, itemOperations={
 *     "get"={"access_control"="is_granted('ROLE_USER') and is_feature_enabled('content')"},
 *     "put"={"access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('content')"},
 *     "delete"={"access_control"="is_granted('ROLE_ADMIN') and is_feature_enabled('content')"}
 * })
 * @ApiFilter(SearchFilter::class, properties={"title"="ipartial", "content"="ipartial"})
 * @ApiFilter(FulltextSearchFilter::class, properties={"title", "content"})
 */
class Content
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Groups({"content_input", "content_output"})
     */
    private $title;

    /**
     * @ORM\Column
     * @Gedmo\Slug(fields={"title"})
     * @Groups({"content_input", "content_output"})
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Groups({"content_input", "content_output"})
     */
    private $content;

    /**
     * @ORM\Column(name="is_published", type="boolean")
     * @Groups({"content_input", "admin_output"})
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
