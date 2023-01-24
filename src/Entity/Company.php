<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/[а-яА-ЯёЁa-zA-Z0-9\.\s]+/',
        message: 'Название компании может содержать только латинские и кириллические буквы, точки и цифры.'
    )]
    #[Assert\Length(
        min: 3,
        max: 60,
        minMessage: 'Название компании должно содержать минимум {{ limit }} символов.',
        maxMessage: 'Название компании не может быть больше {{ limit }} символов.',
    )]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Recruiter::class)]
    private Collection $recruiters;

    public function __construct()
    {
        $this->recruiters = new ArrayCollection();
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
     * @return Collection<int, Recruiter>
     */
    public function getRecruiters(): Collection
    {
        return $this->recruiters;
    }

    public function addRecruiter(Recruiter $recruiter): self
    {
        if (!$this->recruiters->contains($recruiter)) {
            $this->recruiters->add($recruiter);
            $recruiter->setCompany($this);
        }

        return $this;
    }

    public function removeRecruiter(Recruiter $recruiter): self
    {
        if ($this->recruiters->removeElement($recruiter)) {
            if ($recruiter->getCompany() === $this) {
                $recruiter->setCompany(null);
            }
        }

        return $this;
    }
}