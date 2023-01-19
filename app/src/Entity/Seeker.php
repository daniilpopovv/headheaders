<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SeekerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: SeekerRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Seeker implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    #[Constraints\NotBlank]
    #[Constraints\Unique(message: 'Пользователь с таким логином уже существует',)]
    #[Constraints\Regex(
        pattern: '/[a-zA-Z0-9]+/',
        message: 'Логин должен состоять только из латинских букв и цифр.'
    )]
    #[Constraints\Length(
        min: 4,
        max: 20,
        minMessage: 'Логин должен содержать минимум {{ limit }} символа.',
        maxMessage: 'Длина логина не должна превышать {{ limit }} символов.',
    )]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string|null The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(nullable: false)]
    #[Constraints\NotBlank]
    #[Constraints\Regex(
        pattern: '/[а-яА-ЯёЁa-zA-Z0-9\-\–\—\s]+/',
        message: 'ФИО может содержать только латинские и кириллические буквы, тире'
    )]
    #[Constraints\Length(
        min: 4,
        max: 100,
        minMessage: 'ФИО должно состоять минимум из {{ limit }} символов.',
        maxMessage: 'ФИО не должно превышать {{ limit }} символов.',
    )]
    private ?string $fullName = null;

    #[ORM\OneToMany(mappedBy: 'seeker', targetEntity: Resume::class, orphanRemoval: true)]
    private Collection $resumes;

    #[ORM\Column(nullable: false)]
    #[Constraints\NotBlank]
    #[Constraints\Email]
    #[Constraints\Unique(message: 'Пользователь с такой почтой уже существует',)]
    #[Constraints\Length(
        min: 6,
        max: 50,
        minMessage: 'Email должен состоять минимум из {{ limit }} символов.',
        maxMessage: 'Email не должен превышать {{ limit }} символов.',
    )]
    private ?string $email = null;

    public function __construct()
    {
        $this->resumes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->fullName.' aka '.$this->username;
    }

    #[ORM\PrePersist]
    public function setSeekerRole()
    {
        $this->roles = ['ROLE_USER', 'ROLE_SEEKER'];
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return Collection<int, Resume>
     */
    public function getResumes(): Collection
    {
        return $this->resumes;
    }

    public function addResume(Resume $resume): self
    {
        if (!$this->resumes->contains($resume)) {
            $this->resumes->add($resume);
            $resume->setSeeker($this);
        }

        return $this;
    }

    public function removeResume(Resume $resume): self
    {
        if ($this->resumes->removeElement($resume)) {
            // set the owning side to null (unless already changed)
            if ($resume->getSeeker() === $this) {
                $resume->setSeeker(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
