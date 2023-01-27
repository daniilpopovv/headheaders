<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ResumeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ResumeRepository::class)]
class Resume
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(nullable: false)]
	#[Assert\NotBlank]
	#[Assert\Regex(
		pattern: '/[а-яА-ЯёЁa-zA-Z0-9\.\s]+/',
		message: 'Название специальности может содержать только латинские и кириллические буквы, точки и цифры.'
	)]
	#[Assert\Length(
		min: 3,
		max: 100,
		minMessage: 'Название специальности должно содержать минимум {{ limit }} символа.',
		maxMessage: 'Название специальности не должно превышать {{ limit }} символов.',
	)]
	private ?string $specialization = null;


	#[ORM\Column(nullable: true)]
	#[Assert\Length(
		max: 2000,
		maxMessage: 'Описание резюме не должно превышать {{ limit }} символов.',
	)]
	private ?string $description = null;

	#[ORM\Column(nullable: false)]
	#[Assert\NotBlank]
	#[Assert\Type(
		type: 'integer',
		message: 'Пожалуйста, введите целое число',
	)]
	#[Assert\Range(
		notInRangeMessage: 'Зарплата должна быть в диапазоне от 13 890 до 2 000 000 рублей.',
		min: 13890,
		max: 2000000,
	)]
	#[Assert\Length(
		min: 5,
		max: 8,
		minMessage: 'Зарплата должна содержать минимум {{ limit }} символов.',
		maxMessage: 'Зарплата не должна превышать {{ limit }} символов.',
	)]
	private ?int $salary = null;

	#[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'resumes', fetch: 'EAGER')]
	private Collection $skills;

	#[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'resumes')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Seeker $seeker = null;

	#[ORM\ManyToMany(targetEntity: Vacancy::class, inversedBy: 'invitedResumes')]
	private Collection $invites;

	#[ORM\ManyToMany(targetEntity: Vacancy::class, mappedBy: 'replies')]
	private Collection $repliedVacancies;

	#[ORM\ManyToMany(targetEntity: Recruiter::class, inversedBy: 'invitedResumes')]
	private Collection $whoInvited;

	public function __construct() {
		$this->skills = new ArrayCollection();
		$this->invites = new ArrayCollection();
		$this->repliedVacancies = new ArrayCollection();
		$this->whoInvited = new ArrayCollection();
	}

	public function __toString(): string {
		return $this->specialization;
	}

	public function getId(): ?int {
		return $this->id;
	}

	public function getSpecialization(): ?string {
		return $this->specialization;
	}

	public function setSpecialization(string $specialization): self {
		$this->specialization = $specialization;

		return $this;
	}

	public function getDescription(): ?string {
		return $this->description;
	}

	public function setDescription(?string $description): self {
		$this->description = $description;

		return $this;
	}

	public function getSalary(): ?int {
		return $this->salary;
	}

	public function setSalary(int $salary): self {
		$this->salary = $salary;

		return $this;
	}

	/**
	 * @return Collection<int, Skill>
	 */
	public function getSkills(): Collection {
		return $this->skills;
	}

	public function addSkill(Skill $skill): self {
		if (!$this->skills->contains($skill)) {
			$this->skills->add($skill);
		}

		return $this;
	}

	public function removeSkill(Skill $skill): self {
		$this->skills->removeElement($skill);

		return $this;
	}

	public function getSeeker(): ?Seeker {
		return $this->seeker;
	}

	public function setSeeker(?Seeker $seeker): self {
		$this->seeker = $seeker;

		return $this;
	}

	/**
	 * @return Collection<int, Vacancy>
	 */
	public function getInvites(): Collection {
		return $this->invites;
	}

	public function addInvite(Vacancy $invite): self {
		if (!$this->invites->contains($invite)) {
			$this->invites->add($invite);
		}

		return $this;
	}

	public function removeInvite(Vacancy $invite): self {
		$this->invites->removeElement($invite);

		return $this;
	}

	/**
	 * @return Collection<int, Vacancy>
	 */
	public function getRepliedVacancies(): Collection {
		return $this->repliedVacancies;
	}

	public function addRepliedVacancy(Vacancy $repliedVacancy): self {
		if (!$this->repliedVacancies->contains($repliedVacancy)) {
			$this->repliedVacancies->add($repliedVacancy);
			$repliedVacancy->addReply($this);
		}

		return $this;
	}

	public function removeRepliedVacancy(Vacancy $repliedVacancy): self {
		if ($this->repliedVacancies->removeElement($repliedVacancy)) {
			$repliedVacancy->removeReply($this);
		}

		return $this;
	}

	/**
	 * @return Collection<int, Recruiter>
	 */
	public function getWhoInvited(): Collection {
		return $this->whoInvited;
	}

	public function addWhoInvited(Recruiter $whoInvited): self {
		if (!$this->whoInvited->contains($whoInvited)) {
			$this->whoInvited->add($whoInvited);
		}

		return $this;
	}

	public function removeWhoInvited(Recruiter $whoInvited): self {
		$this->whoInvited->removeElement($whoInvited);

		return $this;
	}
}
