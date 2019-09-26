<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GalleryPostRepository")
 */
class GalleryPost
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @Assert\File(
     * maxSize = "1024k", 
     * mimeTypes={ "image/gif", "image/jpeg", "image/png" },
     * mimeTypesMessage = "Please valid image format : gif, png, jpeg"
     * )
     * 
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture1;

    /**
     * @Assert\File(
     * maxSize = "1024k", 
     * mimeTypes={ "image/gif", "image/jpeg", "image/png" },
     * mimeTypesMessage = "Please valid image format : gif, png, jpeg"
     * )
     * 
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture2;

    /**
     * @Assert\File(
     * maxSize = "1024k", 
     * mimeTypes={ "image/gif", "image/jpeg", "image/png" },
     * mimeTypesMessage = "Please valid image format : gif, png, jpeg"
     * )
     * 
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture3;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="galleryPosts")
     */
    private $user;
    public function __construct()
    {
        
        $this->createdAt = new \Datetime();
        $this->updatedAt = new \Datetime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPicture1()
    {
        return $this->picture1;
    }

    public function setPicture1($picture1): self
    {
        $this->picture1 = $picture1;

        return $this;
    }

    public function getPicture2()
    {
        return $this->picture2;
    }

    public function setPicture2($picture2): self
    {
        $this->picture2 = $picture2;

        return $this;
    }

    public function getPicture3()
    {
        return $this->picture3;
    }

    public function setPicture3($picture3): self
    {
        $this->picture3 = $picture3;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
