<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="App\Repository\GradeRepository")
 * @ORM\Table(
 *     name="grades")
 */
class Grade
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="float")
     */
    private $grade;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="grades")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="grades")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getGrade(): ?int
    {
        return $this->grade;
    }
    public function setGrade(int $grade): self
    {
        $this->grade = $grade;
        return $this;
    }
    public function getEvent(): ?Event
    {
        return $this->event;
    }
    public function setEvent(?Event $event): self
    {
        $this->event = $event;
        return $this;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
}