<?php

namespace App\Entity;

use App\Repository\SlotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SlotRepository::class)
 */
class Slot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="slots")
     */
    private $rider;

    /**
     * @ORM\Column(type="datetime")
     */
    private $beginAt;

    /**
     * @ORM\ManyToMany(targetEntity=Option::class, inversedBy="slots")
     */
    private $options;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endAt;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRider(): ?User
    {
        return $this->rider;
    }

    public function setRider(?User $rider): self
    {
        $this->rider = $rider;

        return $this;
    }

    public function getBeginAt(): ?\DateTimeInterface
    {
        return $this->beginAt;
    }

    public function setBeginAt(\DateTimeInterface $beginAt): self
    {
        $this->beginAt = $beginAt;

        return $this;
    }

    /**
     * @return Collection<int, Option>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
        }

        return $this;
    }

    public function removeOption(Option $option): self
    {
        $this->options->removeElement($option);

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getBeginAtDate(): ?string
    {
        return $this->getBeginAt() ? $this->getBeginAt()->format('Y-m-d') : null;
    }

    public function getBeginAtTime(): ?string
    {
        return $this->getBeginAt() ? $this->getBeginAt()->format('H:i') : null;
    }

    public function getEndAtDate(): ?string
    {
        return $this->getEndAt() ? $this->getEndAt()->format('Y-m-d') : null;
    }

    public function getEndAtTime(): ?string
    {
        return $this->getEndAt() ? $this->getEndAt()->format('H:i') : null;
    }
}
