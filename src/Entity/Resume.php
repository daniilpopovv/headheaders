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
    #[Assert\NotBlank(
        message: 'vacancy.specialization.notBlank'
    )]
    #[Assert\Regex(
        pattern: '/[А-яёЁA-z0-9\.\s]+/',
        message: 'vacancy.specialization.regex'
    )]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'vacancy.specialization.length.minMessage',
        maxMessage: 'vacancy.specialization.length.maxMessage',
    )]
    private ?string $specialization = null;


    #[ORM\Column(nullable: true)]
    #[Assert\Length(
        max: 2000,
        maxMessage: 'vacancy.description.length.maxMessage',
    )]
    private ?string $description = null;

    #[ORM\Column(nullable: false)]
    #[Assert\NotBlank(
        message: 'vacancy.salary.notBlank'
    )]
    #[Assert\Type(
        type: 'integer',
        message: 'vacancy.salary.type',
    )]
    #[Assert\Range(
        notInRangeMessage: 'vacancy.salary.range.notInRangeMessage',
        min: 13890,
        max: 2000000,
    )]
    private ?int $salary = null;

    #[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'resumes', fetch: 'EAGER')]
    private Collection $skills;

    #[ORM\ManyToMany(targetEntity: Vacancy::class, inversedBy: 'invites')]
    private Collection $invites;

    #[ORM\ManyToMany(targetEntity: Vacancy::class, mappedBy: 'replies')]
    private Collection $replies;

    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'resumes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->invites = new ArrayCollection();
        $this->replies = new ArrayCollection();
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
    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function addReply(Vacancy $reply): self
    {
        if (!$this->replies->contains($reply)) {
            $this->replies->add($reply);
            $reply->addReply($this);
        }

        return $this;
    }

    public function removeReply(Vacancy $reply): self
    {
        if ($this->replies->removeElement($reply)) {
            $reply->removeReply($this);
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
