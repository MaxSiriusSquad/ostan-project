<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Utils\Slugger as Slugger;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="posts" )
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="post", cascade={"persist","remove"})
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Status", inversedBy="posts")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=110)
     */
    private $slug;



    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->createdAt = new \Datetime();
        $this->updatedAt = new \Datetime();
    }

    public function __toString()
    {
        return $this->title;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
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

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAd($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAd() === $this) {
                $comment->setAd(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }


    /**
     * indique a doctrine d'appliquer cette fonction sur notre propriété slug lorsqu'il juste avant (pre) d'etre enregistré pour la premiere fois (persist) OU mis a jour (update)
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function applySlug(){
        // slugger attend un parametre true ou false pour mettre la chaine passé en minuscule ou non
        //$slugger = new Slugger(true);
        
        //$slug = $slugger->slugify($this->title);
        //$this->slug = $slug;
     }


     
    /**
     * Get the value of slug
     */ 
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the value of slug
     *
     * @return  self
     */ 
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    

}
