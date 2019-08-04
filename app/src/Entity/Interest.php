<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InterestRepository")
 *  * @ORM\Table(
 *     name="interests")
 */
class Interest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="interests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="interests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Event|null
     */
    public function getEvent(): ?Event
    {
        return $this->event;
    }

    /**
     * @param Event|null $event
     * @return Interest
     */
    public function setEvent(?Event $event): self
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return Interest
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
}