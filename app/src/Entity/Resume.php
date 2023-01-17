<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ResumeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity(repositoryClass: ResumeRepository::class)]
class Resume
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

    #[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'resumes')]
    private Collection $skills;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photoFilename = null;

    #[ORM\ManyToOne(inversedBy: 'resumes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seeker $seeker = null;

    #[ORM\ManyToMany(targetEntity: Vacancy::class, inversedBy: 'invitedResumes')]
    private Collection $invites;

    #[ORM\ManyToMany(targetEntity: Vacancy::class, mappedBy: 'responses')]
    private Collection $respondedVacancies;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->invites = new ArrayCollection();
        $this->respondedVacancies = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->specialization;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSalary(): ?int
    {
        return $this->salary;
    }

    public function setSalary(int $salary): self
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

    public function getPhotoFilename(): ?string
    {
        return $this->photoFilename;
    }

    public function setPhotoFilename(?string $photoFilename): self
    {
        $this->photoFilename = $photoFilename;

        return $this;
    }

    public function getSeeker(): ?Seeker
    {
        return $this->seeker;
    }

    public function setSeeker(?Seeker $seeker): self
    {
        $this->seeker = $seeker;

        return $this;
    }

    /**
     * @return Collection<int, Vacancy>
     */
    public function getInvites(): Collection
    {
        return $this->invites;
    }

    public function addInvite(Vacancy $invite): self
    {
        if (!$this->invites->contains($invite)) {
            $this->invites->add($invite);
        }

        return $this;
    }

    public function removeInvite(Vacancy $invite): self
    {
        $this->invites->removeElement($invite);

        return $this;
    }

    /**
     * @return Collection<int, Vacancy>
     */
    public function getRespondedVacancies(): Collection
    {
        return $this->respondedVacancies;
    }

    public function addRespondedVacancy(Vacancy $respondedVacancy): self
    {
        if (!$this->respondedVacancies->contains($respondedVacancy)) {
            $this->respondedVacancies->add($respondedVacancy);
            $respondedVacancy->addResponse($this);
        }

        return $this;
    }

    public function removeRespondedVacancy(Vacancy $respondedVacancy): self
    {
        if ($this->respondedVacancies->removeElement($respondedVacancy)) {
            $respondedVacancy->removeResponse($this);
        }

        return $this;
    }
}
