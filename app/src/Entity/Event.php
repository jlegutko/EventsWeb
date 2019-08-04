<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 * @ORM\Table(
 *     name="events")
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;
    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;
    /**
     * @ORM\Column(type="datetime")
     */
    private $eventDate;
    /**
     * @ORM\Column(type="float")
     */
    private $price;
    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $place;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $eventSize;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="event", orphanRemoval=true)
     */
    private $comments;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Interest", mappedBy="event", orphanRemoval=true)
     */
    private $interests;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Grade", mappedBy="event", orphanRemoval=true)
     */
    private $grades;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="event", orphanRemoval=true)
     */
    private $category;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Group", mappedBy="event", orphanRemoval=true)
     */
    private $groups;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Photo", mappedBy="event", cascade={"persist", "remove"})
     */
    private $photo;
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->interests = new ArrayCollection();
        $this->grades = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Event
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     * @return Event
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface $updatedAt
     * @return Event
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getEventDate(): ?\DateTimeInterface
    {
        return $this->eventDate;
    }

    /**
     * @param \DateTimeInterface $eventDate
     * @return Event
     */
    public function setEventDate(\DateTimeInterface $eventDate): self
    {
        $this->eventDate = $eventDate;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return Event
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     * @return Event
     */
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlace(): ?string
    {
        return $this->place;
    }

    /**
     * @param string $place
     * @return Event
     */
    public function setPlace(string $place): self
    {
        $this->place = $place;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEventSize(): ?string
    {
        return $this->eventSize;
    }

    /**
     * @param string $eventSize
     * @return Event
     */
    public function setEventSize(string $eventSize): self
    {
        $this->eventSize = $eventSize;
        return $this;
    }
    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     * @return Event
     */
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setEvent($this);
        }
        return $this;
    }

    /**
     * @param Comment $comment
     * @return Event
     */
    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getEvent() === $this) {
                $comment->setEvent(null);
            }
        }
        return $this;
    }
    /**
     * @return Collection|Interest[]
     */
    public function getInterests(): Collection
    {
        return $this->interests;
    }

    /**
     * @param Interest $interest
     * @return Event
     */
    public function addInterest(Interest $interest): self
    {
        if (!$this->interests->contains($interest)) {
            $this->interests[] = $interest;
            $interest->setEvent($this);
        }
        return $this;
    }

    /**
     * @param Interest $interest
     * @return Event
     */
    public function removeInterest(Interest $interest): self
    {
        if ($this->interests->contains($interest)) {
            $this->interests->removeElement($interest);
            // set the owning side to null (unless already changed)
            if ($interest->getEvent() === $this) {
                $interest->setEvent(null);
            }
        }
        return $this;
    }
    /**
     * @return Collection|Grade[]
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }

    /**
     * @param Grade $grade
     * @return Event
     */
    public function addGrade(Grade $grade): self
    {
        if (!$this->grades->contains($grade)) {
            $this->grades[] = $grade;
            $grade->setEvent($this);
        }
        return $this;
    }

    /**
     * @param Grade $grade
     * @return Event
     */
    public function removeGrade(Grade $grade): self
    {
        if ($this->grades->contains($grade)) {
            $this->grades->removeElement($grade);
            // set the owning side to null (unless already changed)
            if ($grade->getEvent() === $this) {
                $grade->setEvent(null);
            }
        }
        return $this;
    }
    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     * @return Event
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }
    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * @param Group $group
     * @return Event
     */
    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->setEvent($this);
        }

        return $this;
    }

    /**
     * @param Group $group
     *
     * @return Event
     */
    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            // set the owning side to null (unless already changed)
            if ($group->getEvent() === $this) {
                $group->setEvent(null);
            }
        }

        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto(Photo $photo): self
    {
        $this->photo = $photo;

        // set the owning side of the relation if necessary
        if ($this !== $photo->getEvent()) {
            $photo->setEvent($this);
        }

        return $this;
    }
}