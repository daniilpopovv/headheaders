<?php

namespace App\Entity;

use App\Repository\VacancyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity(repositoryClass: VacancyRepository::class)]
class Vacancy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Constraints\NotBlank]
    private ?string $specialization = null;

    #[ORM\Column(length: 2000, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 8, nullable: false)]
    #[Constraints\NotBlank]
    private ?int $salary = null;

    #[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'vacancies')]
    private Collection $skills;

    #[ORM\ManyToOne(inversedBy: 'vacancies')]
    #[ORM\JoinColumn(nullable: false)]
    #[Constraints\NotBlank]
    private ?Recruiter $recruiter = null;

    #[ORM\ManyToMany(targetEntity: Resume::class, mappedBy: 'invites')]
    private Collection $invitedResumes;

    #[ORM\ManyToMany(targetEntity: Resume::class, inversedBy: 'respondedVacancies')]
    private Collection $responses;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->invitedResumes = new ArrayCollection();
        $this->responses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->specialization;
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

    public function getSpecialization(): ?string
    {
        return $this->specialization;
    }

    public function setSpecialization(string $specialization): self
    {
        $this->specialization = $specialization;

        return $this;
    }

    public function getSalary(): ?int
    {
        return $this->salary;
    }

    public function setSalary(?int $salary): self
    {
        $this->salary = $salary;

        return $this;
    }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): self
    {
        $this->skills->removeElement($skill);

        return $this;
    }

    public function getRecruiter(): ?Recruiter
    {
        return $this->recruiter;
    }

    public function setRecruiter(?Recruiter $recruiter): self
    {
        $this->recruiter = $recruiter;

        return $this;
    }

    /**
     * @return Collection<int, Resume>
     */
    public function getInvitedResumes(): Collection
    {
        return $this->invitedResumes;
    }

    public function addInvitedResume(Resume $invitedResume): self
    {
        if (!$this->invitedResumes->contains($invitedResume)) {
            $this->invitedResumes->add($invitedResume);
            $invitedResume->addInvite($this);
        }

        return $this;
    }

    public function removeInvitedResume(Resume $invitedResume): self
    {
        if ($this->invitedResumes->removeElement($invitedResume)) {
            $invitedResume->removeInvite($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Resume>
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function addResponse(Resume $response): self
    {
        if (!$this->responses->contains($response)) {
            $this->responses->add($response);
        }

        return $this;
    }

    public function removeResponse(Resume $response): self
    {
        $this->responses->removeElement($response);

        return $this;
    }
}
