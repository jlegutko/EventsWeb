<?php
/**
 * ProfilePhoto entity.
 */
namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ProfilePhoto.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ProfilePhotoRepository")
 * @ORM\Table(
 *     name="profilePhotos",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="UQ_profilePhotos_1",
 *              columns={"file"},
 *          ),
 *     },
 * )
 *
 * @UniqueEntity(
 *     fields={"file"}
 * )
 */
class ProfilePhoto implements Serializable
{
    /**
     * Primary key.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Created at.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @Assert\DateTime
     */
    private $createdAt;

    /**
     * Updated at.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="update")
     *
     * @Assert\DateTime
     */
    private $updatedAt;

    /**
     * File.
     *
     * @ORM\Column(
     *     type="string",
     *     length=191,
     *     nullable=false,
     *     unique=true,
     * )
     *
     * @Assert\NotBlank
     * @Assert\Image(
     *     maxSize = "1024k",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg", "image/jpeg", "image/pjpeg"},
     * )
     */
    private $file;

    /**
     * User.
     *
     * @ORM\OneToOne(
     *     targetEntity="App\Entity\User",
     *     inversedBy="profilePhoto"
     * )
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for Created at.
     *
     * @return DateTimeInterface|null Created at
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Setter for Created at.
     *
     * @param DateTimeInterface $createdAt Created at
     */
    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Getter for Updated at.
     *
     * @return DateTimeInterface|null Updated at
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Setter for Updated at.
     *
     * @param DateTimeInterface $updatedAt Updated at
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Getter for File.
     *
     * @return mixed|null File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Setter for File name.
     *
     * @param mixed|null $file File
     *
     * @throws Exception
     */
    public function setFile($file = null): void
    {
        $this->file = $file;
    }

    /**
     * Getter for User.
     *
     * @return User|null User entity
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Setter for User.
     *
     * @param User $user User entity
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @see \Serializable::serialize()
     *
     * @return string Serialized object
     */
    public function serialize(): string
    {
        $file = $this->getFile();

        return serialize(
            [
                $this->id,
                ($file instanceof File) ? $file->getFilename() : $file,
            ]
        );
    }

    /**
     * @see \Serializable::unserialize()
     *
     * @param string $serialized Serialized object
     */
    public function unserialize($serialized): void
    {
        list($this->id) = unserialize($serialized);
    }
}
