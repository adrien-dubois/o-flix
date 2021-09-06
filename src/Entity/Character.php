<?php

namespace App\Entity;

use App\Repository\CharacterRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CharacterRepository::class)
 * @ORM\Table(name="`character`")
 */
class Character
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"character_list", "character_detail"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ajouter un prénom")
     * @Groups({"character_list", "character_detail"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ajouter un nom au personnage")
     * @Groups({"character_list", "character_detail"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Renseigner le sexe du personnage")
     * @Groups({"character_list", "character_detail"})
     */
    private $gender;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="Merci de donner la biographie de votre personnage")
     * 
     */
    private $bio;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     */
    private $age;

    /**
     * @ORM\ManyToMany(targetEntity=TvShow::class, inversedBy="characters")
     * @Assert\NotBlank(message="Séléctionnez la série à laquelle apartient votre personnage")
     * @Groups({"character_list", "character_detail"})
     */
    private $tvshows;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"character_list", "character_detail"})
     */
    private $truename;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgUpload;

    public function __construct()
    {
        $this->tvshows = new ArrayCollection();
    }

    public function __toString()
{
    return $this->firstname;
}
    public function getFullName()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

    /**
     * @return Collection|TvShow[]
     */
    public function getTvshows(): Collection
    {
        return $this->tvshows;
    }

 

    public function addTvshow(TvShow $tvshow): self
    {
        if (!$this->tvshows->contains($tvshow)) {
            $this->tvshows[] = $tvshow;
        }

        return $this;
    }

    public function removeTvshow(TvShow $tvshow): self
    {
        $this->tvshows->removeElement($tvshow);

        return $this;
    }

    public function setTvshows(string $tvshows): self
    {
        $this->tvshows = $tvshows;
        
        return $this;
    }

    public function getTruename(): ?string
    {
        return $this->truename;
    }

    public function setTruename(string $truename): self
    {
        $this->truename = $truename;

        return $this;
    }

    public function getImgUpload(): ?string
    {
        return $this->imgUpload;
    }

    public function setImgUpload(?string $imgUpload): self
    {
        $this->imgUpload = $imgUpload;

        return $this;
    }
}
