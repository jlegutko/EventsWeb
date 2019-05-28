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
     * @ORM\OneToMany(targetEntity="App\Entity\Picture", mappedBy="event", orphanRemoval=true)
     */
    private $pictures;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="event")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Group", mappedBy="event", orphanRemoval=true)
     */
    private $groups;
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->interests = new ArrayCollection();
        $this->grades = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->groups = new ArrayCollection();
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
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    public function getEventDate(): ?\DateTimeInterface
    {
        return $this->eventDate;
    }
    public function setEventDate(\DateTimeInterface $eventDate): self
    {
        $this->eventDate = $eventDate;
        return $this;
    }
    public function getPrice(): ?float
    {
        return $this->price;
    }
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }
    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }
    public function getPlace(): ?string
    {
        return $this->place;
    }
    public function setPlace(string $place): self
    {
        $this->place = $place;
        return $this;
    }
    public function getEventSize(): ?string
    {
        return $this->eventSize;
    }
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
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setEvent($this);
        }
        return $this;
    }
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
    public function addInterest(Interest $interest): self
    {
        if (!$this->interests->contains($interest)) {
            $this->interests[] = $interest;
            $interest->setEvent($this);
        }
        return $this;
    }
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
    public function addGrade(Grade $grade): self
    {
        if (!$this->grades->contains($grade)) {
            $this->grades[] = $grade;
            $grade->setEvent($this);
        }
        return $this;
    }
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
     * @return Collection|Picture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }
    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setEvent($this);
        }
        return $this;
    }
    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->contains($picture)) {
            $this->pictures->removeElement($picture);
            // set the owning side to null (unless already changed)
            if ($picture->getEvent() === $this) {
                $picture->setEvent(null);
            }
        }
        return $this;
    }
    public function getCategory(): ?Category
    {
        return $this->category;
    }
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
    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->setEvent($this);
        }
        return $this;
    }
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
}