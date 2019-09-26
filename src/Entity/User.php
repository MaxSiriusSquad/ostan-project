<?php

namespace App\Entity;

use App\Entity\Job;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Utils\Slugger as Slugger;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="Un compte existe déjà avec cette adresse mail.")
 * @UniqueEntity(fields={"username"}, message="Ce pseudo existe déjà.") 
 */
class User implements UserInterface, \Serializable, EquatableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * 
     * @Assert\File(
     * maxSize = "1024k", 
     * mimeTypes={ "image/gif", "image/jpeg", "image/png" },
     * mimeTypesMessage = "Please valid image format : gif, png, jpeg"
     * )
     * 
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * 
     */
    private $companyname;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     */
    private $siret;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\LessThanOrEqual("-16 years")
     */
    private $birthdate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $phonenumber;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="users")
     */
    private $role;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="user", cascade={"persist","remove"})
     */
    private $messages;

    /**
     * 
     * @ORM\ManyToMany(targetEntity="App\Entity\Job", inversedBy="users")
     */
    private $jobs;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="user", cascade={"persist","remove"})
     * 
     */
    private $posts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="users")
     * @Assert\Count(
     *      min = 1,
     *      max = 5,
     *      minMessage = "Vous devez choisir au moins 1 tag",
     *      maxMessage = "Vous ne pouvez pas choisir plus de {{ limit }} tags"
     * )
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", cascade={"persist","remove"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GalleryPost", mappedBy="user", cascade={"persist","remove"})
     */
    private $galleryPosts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="userReceiver", cascade={"persist","remove"})
     */
    private $messagesReceived;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Status", inversedBy="users")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=110)
     */
    private $slug;


    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->jobs = new ArrayCollection();
        $this->advicePosts = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->galleryPosts = new ArrayCollection();
        $this->messagesReceived = new ArrayCollection();
        $this->createdAt = new \Datetime();
        $this->updatedAt = new \Datetime();
    }

    public function __toString()
    {
        return $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $role = $this->role;
        // guarantee every user at least has ROLE_USER


        return [$this->getRole()->getCode()]; // ex USER_ADMIN

    }

    public function setRoles(array $role): self
    {
        $this->role = $role;

        return $this;
    }



    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getCompanyname(): ?string
    {
        return $this->companyname;
    }

    public function setCompanyname(string $companyname): self
    {
        $this->companyname = $companyname;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSiret(): ?int
    {
        return $this->siret;
    }

    public function setSiret(?int $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    //Note pour accepter le null , je dois enlever le typeint string pour que cela fonctionne
    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhonenumber(): ?int
    {
        return $this->phonenumber;
    }

    public function setPhonenumber(?int $phonenumber): self
    {
        $this->phonenumber = $phonenumber;

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

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Job[]
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs[] = $job;
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        if ($this->jobs->contains($job)) {
            $this->jobs->removeElement($job);
        }

        return $this;
    }


    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GalleryPost[]
     */
    public function getGalleryPosts(): Collection
    {
        return $this->galleryPosts;
    }

    public function addGalleryPost(GalleryPost $galleryPost): self
    {
        if (!$this->galleryPosts->contains($galleryPost)) {
            $this->galleryPosts[] = $galleryPost;
            $galleryPost->setUser($this);
        }

        return $this;
    }

    public function removeGalleryPost(GalleryPost $galleryPost): self
    {
        if ($this->galleryPosts->contains($galleryPost)) {
            $this->galleryPosts->removeElement($galleryPost);
            // set the owning side to null (unless already changed)
            if ($galleryPost->getUser() === $this) {
                $galleryPost->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesReceived(): Collection
    {
        return $this->messagesReceived;
    }

    public function addMessagesReceived(Message $messagesReceived): self
    {
        if (!$this->messagesReceived->contains($messagesReceived)) {
            $this->messagesReceived[] = $messagesReceived;
            $messagesReceived->setUserReceiver($this);
        }

        return $this;
    }

    public function removeMessagesReceived(Message $messagesReceived): self
    {
        if ($this->messagesReceived->contains($messagesReceived)) {
            $this->messagesReceived->removeElement($messagesReceived);
            // set the owning side to null (unless already changed)
            if ($messagesReceived->getUserReceiver() === $this) {
                $messagesReceived->setUserReceiver(null);
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

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return null;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    public function isEqualTo(UserInterface $user)
    {
        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }


    /**
     * indique a doctrine d'appliquer cette fonction sur notre propriété slug lorsqu'il juste avant (pre) d'etre enregistré pour la premiere fois (persist) OU mis a jour (update)
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function applySlug()
    {
        // slugger attend un parametre true ou false pour mettre la chaine passé en minuscule ou non
        $slugger = new Slugger(true);

        $slug = $slugger->slugify($this->username);
        $this->slug = $slug;
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
