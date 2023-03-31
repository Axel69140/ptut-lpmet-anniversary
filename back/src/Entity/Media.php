<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[Groups(['user-return', 'article-return', 'activity-return'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['user-return', 'article-return', 'activity-return'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $name = null;

    #[Groups(['user-return', 'article-return', 'activity-return'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $path = null;

    #[Groups(['user-return', 'article-return', 'activity-return'])]
    #[ORM\Column(length: 255)]
    private ?string $format = null;

    #[Groups(['user-return'])]
    #[ORM\ManyToOne(inversedBy: 'medias')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    #[Groups(['user-return'])]
    #[ORM\ManyToOne(inversedBy: 'medias')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Activity $activity = null;

    #[Groups(['user-return'])]
    #[ORM\OneToOne(mappedBy: 'media', cascade: ['persist', 'remove'])]
    private ?TimelineStep $timelineStep = null;

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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getTimelineStep(): ?TimelineStep
    {
        return $this->timelineStep;
    }

    public function setTimelineStep(?TimelineStep $timelineStep): self
    {
        // unset the owning side of the relation if necessary
        if ($timelineStep === null && $this->timelineStep !== null) {
            $this->timelineStep->setMedia(null);
        }

        // set the owning side of the relation if necessary
        if ($timelineStep !== null && $timelineStep->getMedia() !== $this) {
            $timelineStep->setMedia($this);
        }

        $this->timelineStep = $timelineStep;

        return $this;
    }
}
