<?php

namespace App\Entity;

use App\Repository\RiderRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RiderRepository::class)
 */
class Rider
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity=Slot::class, mappedBy="rider")
     */
    private $slots;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="rider", cascade={"persist", "remove"})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Slot>
     */
    public function getSlots(): Collection
    {
        return $this->slots;
    }

    public function addSlot(Slot $slot): self
    {
        if (!$this->slots->contains($slot)) {
            $this->slots[] = $slot;
            $slot->setRider($this);
        }

        return $this;
    }

    public function removeSlot(Slot $slot): self
    {
        if ($this->slots->removeElement($slot)) {
            // set the owning side to null (unless already changed)
            if ($slot->getRider() === $this) {
                $slot->setRider(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getSurname());
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setRider(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getRider() !== $this) {
            $user->setRider($this);
        }

        $this->user = $user;

        return $this;
    }

    public function getShort(): string
    {
        return sprintf('%s.%s.', substr($this->getFirstName(), 0, 1), substr($this->getSurname(), 0, 1));
    }
}
