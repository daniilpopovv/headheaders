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
    #[Assert\NotBlank(
        message: 'company.name.notBlank'
    )]
    #[Assert\Regex(
        pattern: '/[А-яёЁA-z0-9\.\s]+/',
        message: 'company.name.regex'
    )]
    #[Assert\Length(
        min: 3,
        max: 60,
        minMessage: 'company.name.length.minMessage',
        maxMessage: 'company.name.length.maxMessage',
    )]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: User::class)]
    private Collection $staff;

    public function __construct()
    {
        $this->staff = new ArrayCollection();
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
     * @return Collection<int, User>
     */
    public function getStaff(): Collection
    {
        return $this->staff;
    }

    public function addStaff(User $staff): self
    {
        if (!$this->staff->contains($staff)) {
            $this->staff->add($staff);
            $staff->setCompany($this);
        }

        return $this;
    }

    public function removeStaff(User $staff): self
    {
        if ($this->staff->removeElement($staff)) {
            // set the owning side to null (unless already changed)
            if ($staff->getCompany() === $this) {
                $staff->setCompany(null);
            }
        }

        return $this;
    }
}
