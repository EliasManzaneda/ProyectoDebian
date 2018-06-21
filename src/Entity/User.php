<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;


use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;




/**
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"group1"})
     */
    private $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="user.username.not_blank")
     * @Groups({"group1"})
     * @Assert\Length(
     *      min = 2,
     *      max = 30,
     *      minMessage = "user.username.min_message",
     *      maxMessage = "user.username.max_message",
     * )
     */
    private $username;






    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="user")
     */
    private $questions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Answer", mappedBy="user")
     */
    private $answers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", mappedBy="users")
     */
    private $roles;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Achievement", inversedBy="users")
     */
    private $achievements;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Title", inversedBy="users")
     */
    private $titles;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registerDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $banDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $banStrikes;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Image(
     *     minWidth = 16,
     *     minWidthMessage = "user.avatar.minwidth",
     *     maxWidth = 256,
     *     maxWidthMessage = "user.avatar.maxwidth",
     *     minHeight = 16,
     *     minHeightMessage = "user.avatar.minheight",
     *     maxHeight = 256,
     *     maxHeightMessage = "user.avatar.maxheight",
     *     allowLandscape = false,
     *     allowLandscapeMessage = "user.avatar.landscape",
     *     allowPortrait = false,
     *     allowPortraitMessage = "user.avatar.portrait",
     *     mimeTypesMessage = "user.avatar.mimetype"
     * )
     */
    private $avatarPath;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $oldAvatar;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     *
     * @Assert\Image(
     *     minWidth = 16,
     *     minWidthMessage = "user.avatar.minwidth",
     *     maxWidth = 256,
     *     maxWidthMessage = "user.avatar.maxwidth",
     *     minHeight = 16,
     *     minHeightMessage = "user.avatar.minheight",
     *     maxHeight = 256,
     *     maxHeightMessage = "user.avatar.maxheight",
     *     allowLandscape = false,
     *     allowLandscapeMessage = "user.avatar.landscape",
     *     allowPortrait = false,
     *     allowPortraitMessage = "user.avatar.portrait",
     *     mimeTypesMessage = "user.avatar.mimetype"
     * )
     */
    private $newAvatar;



    public function __construct()
    {
        $this->isActive = true;
        $this->questions = new ArrayCollection();
        $this->answers = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->achievements = new ArrayCollection();
        $this->titles = new ArrayCollection();
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid('', true));
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    public function getRoles()
    {
        /*
        if(strtolower($this->username) == "admin"){
            return array('ROLE_ADMIN');
        }else{
            return array('ROLE_USER');
        }
        */

        // return array('ROLE_USER');
        // return $this->roles;-

        $rolesArray = [];
        foreach($this->roles as $role){
            array_push($rolesArray, $role->getName());
        }
        return $rolesArray;
        // return $this->roles;

    }

    public function eraseCredentials()
    {
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
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setUser($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            // set the owning side to null (unless already changed)
            if ($question->getUser() === $this) {
                $question->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setUser($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->contains($answer)) {
            $this->answers->removeElement($answer);
            // set the owning side to null (unless already changed)
            if ($answer->getUser() === $this) {
                $answer->setUser(null);
            }
        }

        return $this;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
            $role->addUser($this);
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
            $role->removeUser($this);
        }

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    /**
     * @return Collection|Achievement[]
     */
    public function getAchievements(): Collection
    {
        return $this->achievements;
    }

    public function addAchievement(Achievement $achievement): self
    {
        if (!$this->achievements->contains($achievement)) {
            $this->achievements[] = $achievement;
        }

        return $this;
    }

    public function removeAchievement(Achievement $achievement): self
    {
        if ($this->achievements->contains($achievement)) {
            $this->achievements->removeElement($achievement);
        }

        return $this;
    }

    /**
     * @return Collection|Title[]
     */
    public function getTitles(): Collection
    {
        return $this->titles;
    }

    public function addTitle(Title $title): self
    {
        if (!$this->titles->contains($title)) {
            $this->titles[] = $title;
        }

        return $this;
    }

    public function removeTitle(Title $title): self
    {
        if ($this->titles->contains($title)) {
            $this->titles->removeElement($title);
        }

        return $this;
    }

    public function user(ValidatorInterface $validator)
    {
        $author = new User();

        // ... do something to the $author object

        $errors = $validator->validate($author);

        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $errorsString = (string)$errors;

            return new Response($errorsString);
        }

        return new Response('The author is valid! Yes!');

    }

    public function getRegisterDate(): ?\DateTimeInterface
    {
        return $this->registerDate;
    }

    public function setRegisterDate(\DateTimeInterface $registerDate): self
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    public function getBanDate(): ?\DateTimeInterface
    {
        return $this->banDate;
    }

    public function setBanDate(?\DateTimeInterface $banDate): self
    {
        $this->banDate = $banDate;

        return $this;
    }

    public function getBanStrikes(): ?int
    {
        return $this->banStrikes;
    }

    public function setBanStrikes(int $banStrikes): self
    {
        $this->banStrikes = $banStrikes;

        return $this;
    }

    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }

    public function setAvatarPath(string $avatarPath): self
    {
        $this->avatarPath = $avatarPath;

        return $this;
    }

    public function getOldAvatar(): ?string
    {
        return $this->oldAvatar;
    }

    public function setOldAvatar(?string $oldAvatar): self
    {
        $this->oldAvatar = $oldAvatar;

        return $this;
    }

    public function getNewAvatar(): ?string
    {
        return $this->newAvatar;
    }

    public function setNewAvatar(?string $newAvatar): self
    {
        $this->newAvatar = $newAvatar;

        return $this;
    }

}
