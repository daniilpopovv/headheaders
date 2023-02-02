<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\VacancyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VacancyRepository::class)]
class Vacancy
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
		maxMessage: 'Описание вакансии не должно превышать {{ limit }} символов.',
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
		minMessage: 'Зарплата должна содержать минимум {{ limit }} символов. МРОТ: 13 890 руб.',
		maxMessage: 'Зарплата не должна превышать {{ limit }} символов.',
	)]
	private ?int $salary = null;

	#[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'vacancies', fetch: 'EAGER')]
	private Collection $skills;

	#[ORM\ManyToMany(targetEntity: Resume::class, mappedBy: 'invites')]
	private Collection $invitedResumes;

	#[ORM\ManyToMany(targetEntity: Resume::class, inversedBy: 'repliedVacancies')]
	private Collection $replies;

	#[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'vacancies')]
	#[ORM\JoinColumn(nullable: false)]
	private ?User $owner = null;

	public function __construct() {
		$this->skills = new ArrayCollection();
		$this->invitedResumes = new ArrayCollection();
		$this->replies = new ArrayCollection();
	}

	public function getId(): ?int {
		return $this->id;
	}

	public function __toString(): string {
		return $this->specialization;
	}

	public function getDescription(): ?string {
		return $this->description;
	}

	public function setDescription(?string $description): self {
		$this->description = $description;

		return $this;
	}

	public function getSpecialization(): ?string {
		return $this->specialization;
	}

	public function setSpecialization(string $specialization): self {
		$this->specialization = $specialization;

		return $this;
	}

	public function getSalary(): ?int {
		return $this->salary;
	}

	public function setSalary(?int $salary): self {
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

	/**
	 * @return Collection<int, Resume>
	 */
	public function getInvitedResumes(): Collection {
		return $this->invitedResumes;
	}

	public function addInvitedResume(Resume $invitedResume): self {
		if (!$this->invitedResumes->contains($invitedResume)) {
			$this->invitedResumes->add($invitedResume);
			$invitedResume->addInvite($this);
		}

		return $this;
	}

	public function removeInvitedResume(Resume $invitedResume): self {
		if ($this->invitedResumes->removeElement($invitedResume)) {
			$invitedResume->removeInvite($this);
		}

		return $this;
	}

	/**
	 * @return Collection<int, Resume>
	 */
	public function getReplies(): Collection {
		return $this->replies;
	}

	public function addReply(Resume $reply): self {
		if (!$this->replies->contains($reply)) {
			$this->replies->add($reply);
		}

		return $this;
	}

	public function removeReply(Resume $reply): self {
		$this->replies->removeElement($reply);

		return $this;
	}

	public function getOwner(): ?User {
		return $this->owner;
	}

	public function setOwner(?User $owner): self {
		$this->owner = $owner;

		return $this;
	}
}
