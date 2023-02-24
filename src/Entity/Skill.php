<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SkillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/[а-яА-ЯёЁa-zA-Z0-9\-\–\—\s\!]+/',
        message: 'Название навыка содержит недопустимые символы'
    )]
    #[Assert\Length(
        min: 1,
        max: 50,
        minMessage: 'Название навыка должно содержать минимум {{ limit }} символ.',
        maxMessage: 'Название навыка не может быть больше {{ limit }} символов.',
    )]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Resume::class, mappedBy: 'skills')]
    private Collection $resumes;

    #[ORM\ManyToMany(targetEntity: Vacancy::class, mappedBy: 'skills')]
    private Collection $vacancies;

    public function __construct()
    {
        $this->resumes = new ArrayCollection();
        $this->vacancies = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $resume->addSkill($this);
        }

        return $this;
    }

    public function removeResume(Resume $resume): self
    {
        if ($this->resumes->removeElement($resume)) {
            $resume->removeSkill($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Vacancy>
     */
    public function getVacancies(): Collection
    {
        return $this->vacancies;
    }

    public function addVacancy(Vacancy $vacancy): self
    {
        if (!$this->vacancies->contains($vacancy)) {
            $this->vacancies->add($vacancy);
            $vacancy->addSkill($this);
        }

        return $this;
    }

    public function removeVacancy(Vacancy $vacancy): self
    {
        if ($this->vacancies->removeElement($vacancy)) {
            $vacancy->removeSkill($this);
        }

        return $this;
    }
}
