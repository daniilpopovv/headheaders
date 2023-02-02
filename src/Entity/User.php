<?php

namespace App\Entity;

use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(
	fields: ['username'],
	message: 'Пользователь с таким логином уже существует',
)]
#[UniqueEntity(
	fields: ['email'],
	message: 'Пользователь с такой почтой уже существует',
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(unique: true)]
	#[Assert\NotBlank]
	#[Assert\Regex(
		pattern: '/[a-zA-Z0-9]+/',
		message: 'Логин должен состоять только из латинских букв и цифр.'
	)]
	#[Assert\Length(
		min: 4,
		max: 20,
		minMessage: 'Логин должен содержать минимум {{ limit }} символа.',
		maxMessage: 'Длина логина не должна превышать {{ limit }} символов.',
	)]
	private ?string $username = null;

	#[ORM\Column(type: 'simple_array')]
	private array $roles = [];

	/**
	 * @var string|null The hashed password
	 */
	#[ORM\Column]
	private ?string $password = null;

	#[ORM\Column(nullable: false)]
	#[Assert\NotBlank]
	#[Assert\Regex(
		pattern: '/[а-яА-ЯёЁa-zA-Z0-9\-\–\—\s]+/',
		message: 'ФИО может содержать только латинские и кириллические буквы, тире'
	)]
	#[Assert\Length(
		min: 4,
		max: 100,
		minMessage: 'ФИО должно состоять минимум из {{ limit }} символов.',
		maxMessage: 'ФИО не должно превышать {{ limit }} символов.',
	)]
	private ?string $fullName = null;

	#[ORM\Column(nullable: false)]
	#[Assert\NotBlank]
	#[Assert\Email]
	#[Assert\Length(
		min: 6,
		max: 50,
		minMessage: 'Email должен состоять минимум из {{ limit }} символов.',
		maxMessage: 'Email не должен превышать {{ limit }} символов.',
	)]
	private ?string $email = null;

	#[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'staff')]
	private ?Company $company = null;

	#[ORM\OneToMany(mappedBy: 'owner', targetEntity: Resume::class, orphanRemoval: true)]
	private Collection $resumes;

	#[ORM\OneToMany(mappedBy: 'owner', targetEntity: Vacancy::class, orphanRemoval: true)]
	private Collection $vacancies;

	public function __construct() {
		$this->resumes = new ArrayCollection();
		$this->vacancies = new ArrayCollection();
	}

	public function __toString(): string {
		return $this->fullName . ' aka ' . $this->username;
	}

	public function getId(): ?int {
		return $this->id;
	}

	public function getUsername(): ?string {
		return $this->username;
	}

	public function setUsername(string $username): self {
		$this->username = $username;

		return $this;
	}

	/**
	 * A visual identifier that represents this user.
	 *
	 * @see UserInterface
	 */
	public function getUserIdentifier(): string {
		return (string)$this->username;
	}

	/**
	 * @see UserInterface
	 */
	public function getRoles(): array {
		$roles = $this->roles;
		$roles[] = RoleEnum::user->value;

		return array_unique($roles);
	}

	public function setRoles(array $roles): self {
		$this->roles = $roles;

		return $this;
	}

	/**
	 * @see PasswordAuthenticatedUserInterface
	 */
	public function getPassword(): string {
		return $this->password;
	}

	public function setPassword(string $password): self {
		$this->password = $password;

		return $this;
	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials() {
		// If you store any temporary, sensitive data on the user, clear it here
		// $this->plainPassword = null;
	}

	public function getFullName(): ?string {
		return $this->fullName;
	}

	public function setFullName(string $fullName): self {
		$this->fullName = $fullName;

		return $this;
	}

	/**
	 * @return Collection<int, Resume>
	 */
	public function getResumes(): Collection {
		return $this->resumes;
	}

	public function addResume(Resume $resume): self {
		if (!$this->resumes->contains($resume)) {
			$this->resumes->add($resume);
			$resume->setOwner($this);
		}

		return $this;
	}

	public function removeResume(Resume $resume): self {
		if ($this->resumes->removeElement($resume)) {
			// set the owning side to null (unless already changed)
			if ($resume->getOwner() === $this) {
				$resume->setOwner(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, Vacancy>
	 */
	public function getVacancies(): Collection {
		return $this->vacancies;
	}

	public function addVacancy(Vacancy $vacancy): self {
		if (!$this->vacancies->contains($vacancy)) {
			$this->vacancies->add($vacancy);
			$vacancy->setOwner($this);
		}

		return $this;
	}

	public function removeVacancy(Vacancy $vacancy): self {
		if ($this->vacancies->removeElement($vacancy)) {
			// set the owning side to null (unless already changed)
			if ($vacancy->getOwner() === $this) {
				$vacancy->setOwner(null);
			}
		}

		return $this;
	}

	public function getCompany(): ?Company {
		return $this->company;
	}

	public function setCompany(?Company $company): self {
		$this->company = $company;

		return $this;
	}

	public function getEmail(): ?string {
		return $this->email;
	}

	public function setEmail(string $email): self {
		$this->email = $email;

		return $this;
	}
}
