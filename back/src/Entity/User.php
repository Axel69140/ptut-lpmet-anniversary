<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[Groups(['guest-return', 'user-return', 'article-return', 'anecdote-return', 'activity-return', 'participant-return'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['guest-return', 'user-return', 'article-return', 'anecdote-return', 'activity-return', 'participant-return'])]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[Groups(['guest-return', 'user-return', 'article-return', 'anecdote-return', 'activity-return', 'participant-return'])]
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Groups(['guest-return', 'user-return', 'article-return', 'anecdote-return', 'activity-return', 'participant-return'])]
    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['guest-return', 'user-return', 'article-return', 'anecdote-return', 'activity-return', 'participant-return'])]
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[Groups(['guest-return', 'user-return', 'article-return', 'anecdote-return', 'activity-return', 'participant-return'])]
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[Groups(['guest-return', 'user-return', 'article-return', 'anecdote-return', 'activity-return', 'participant-return'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maidenName = null;

    #[Groups(['guest-return', 'user-return', 'article-return', 'anecdote-return', 'activity-return', 'participant-return'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phoneNumber = null;

    #[Groups(['guest-return', 'user-return', 'article-return', 'anecdote-return', 'activity-return', 'participant-return'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    #[Groups(['guest-return', 'user-return', 'article-return', 'anecdote-return', 'activity-return', 'participant-return'])]
    #[ORM\Column]
    private ?bool $isParticipated = false;

    #[Groups(['guest-return', 'user-return', 'article-return', 'anecdote-return', 'activity-return', 'participant-return'])]
    #[ORM\Column]
    private ?bool $isPublicProfil = false;

    #[Groups(['guest-return', 'user-return', 'article-return', 'anecdote-return', 'activity-return', 'participant-return'])]
    #[ORM\Column(type: Types::ARRAY)]
    private array $activeYears = [];

    #[Groups(['guest-return', 'user-return', 'article-return', 'anecdote-return', 'activity-return', 'participant-return'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $function = null;

    #[Groups(['guest-return', 'user-return', 'article-return', 'anecdote-return', 'activity-return', 'participant-return'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $link = null;

    #[Groups(['user-return'])]
    #[ORM\OneToMany(mappedBy: 'invitedBy', targetEntity: Guest::class, orphanRemoval: true)]
    private Collection $guests;

    #[Groups(['user-return'])]
    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Activity::class)]
    private Collection $activities;

    #[Groups(['user-return'])]
    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Article::class)]
    private Collection $articles;

    #[Groups(['user-return'])]
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Media $profilPicture = null;

    #[Groups(['user-return'])]
    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Anecdote::class)]
    private Collection $anecdotes;

    public function __construct()
    {
        $this->guests = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->anecdotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getMaidenName(): ?string
    {
        return $this->maidenName;
    }

    public function setMaidenName(?string $maidenName): self
    {
        $this->maidenName = $maidenName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getIsParticipated(): ?bool
    {
        return $this->isParticipated;
    }

    public function setIsParticipated(bool $isParticipated): self
    {
        $this->isParticipated = $isParticipated;

        return $this;
    }

    public function getIsPublicProfil(): ?bool
    {
        return $this->isPublicProfil;
    }

    public function setIsPublicProfil(bool $isPublicProfil): self
    {
        $this->isPublicProfil = $isPublicProfil;

        return $this;
    }

    public function getActiveYears(): array
    {
        return $this->activeYears;
    }

    public function setActiveYears(array $activeYears): self
    {
        $this->activeYears = $activeYears;

        return $this;
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function setFunction(?string $function): self
    {
        $this->function = $function;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return Collection<int, Guest>
     */
    public function getGuests(): Collection
    {
        return $this->guests;
    }

    public function addGuest(Guest $guest): self
    {
        if (!$this->guests->contains($guest)) {
            $this->guests->add($guest);
            $guest->setInvitedBy($this);
        }

        return $this;
    }

    public function removeGuest(Guest $guest): self
    {
        if ($this->guests->removeElement($guest)) {
            // set the owning side to null (unless already changed)
            if ($guest->getInvitedBy() === $this) {
                $guest->setInvitedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->setCreator($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getCreator() === $this) {
                $activity->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setCreator($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getCreator() === $this) {
                $article->setCreator(null);
            }
        }

        return $this;
    }

    public function getProfilPicture(): ?Media
    {
        return $this->profilPicture;
    }

    public function setProfilPicture(?Media $profilPicture): self
    {
        $this->profilPicture = $profilPicture;

        return $this;
    }

    /**
     * @return Collection<int, Anecdote>
     */
    public function getAnecdotes(): Collection
    {
        return $this->anecdotes;
    }

    public function addAnecdote(Anecdote $anecdote): self
    {
        if (!$this->anecdotes->contains($anecdote)) {
            $this->anecdotes->add($anecdote);
            $anecdote->setUser($this);
        }

        return $this;
    }

    public function removeAnecdote(Anecdote $anecdote): self
    {
        if ($this->anecdotes->removeElement($anecdote)) {
            // set the owning side to null (unless already changed)
            if ($anecdote->getUser() === $this) {
                $anecdote->setUser(null);
            }
        }

        return $this;
    }
}
