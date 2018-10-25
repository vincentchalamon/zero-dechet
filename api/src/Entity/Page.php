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
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\Entity
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"page_output"}},
 *     "denormalization_context"={"groups"={"page_input"}}
 * }, collectionOperations={
 *     "get"={"access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"},
 *     "post"={"access_control"="is_granted('ROLE_ADMIN')"}
 * }, itemOperations={
 *     "get"={"access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"},
 *     "put"={"access_control"="is_granted('ROLE_ADMIN')"},
 *     "delete"={"access_control"="is_granted('ROLE_ADMIN')"}
 * })
 * @ApiFilter(SearchFilter::class, properties={"title"="ipartial"})
 */
class Page
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
     * @Groups({"page_input", "page_output"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Groups({"page_input", "page_output"})
     */
    private $content;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Groups({"page_input", "page_output"})
     */
    private $url;

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

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
