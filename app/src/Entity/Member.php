<?php
/**
 * Member entity.
 */
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MemberRepository")
 * @ORM\Table(
 *     name="members")
 */
class Member
{
    /**
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See http://symfony.com/doc/current/best_practices/configuration.html#constants-vs-configuration-options
     *
     * @constant int NUMBER_OF_ITEMS
     */
    const NUMBER_OF_ITEMS = 10;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="members")
     * @ORM\JoinColumn(nullable=false)
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Group", inversedBy="members")
     * @ORM\JoinColumn(nullable=false)
     */
    private $community;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getMember(): ?User
    {
        return $this->member;
    }

    /**
     * @param User|null $member
     *
     * @return Member
     */
    public function setMember(?User $member): self
    {
        $this->member = $member;

        return $this;
    }

    /**
     * @return Group|null
     */
    public function getCommunity(): ?Group
    {
        return $this->community;
    }

    /**
     * @param Group|null $community
     *
     * @return Member
     */
    public function setCommunity(?Group $community): self
    {
        $this->community = $community;

        return $this;
    }
}
