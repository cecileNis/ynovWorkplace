<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PrivateMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Controller\PostPrivateMessageController;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PrivateMessageRepository::class)]
#[ApiResource(
    operations: [
        new Post(security: "is_granted('ROLE_USER')", controller: PostPrivateMessageController::class),
        new Delete(security: "is_granted('ROLE_ADMIN') or object.getOwner() == user"),
    ],
    denormalizationContext: ['groups' => ['privateMessage:write']],
)]

#[ApiResource(
    uriTemplate: '/conversations/{conversation_id}/messages',
    operations: [new GetCollection(
        security: "is_granted('ROLE_USER')"
    )],
    uriVariables: [
        'conversation_id' => new Link(toProperty: 'conversation', fromClass: Conversation::class),
    ],
    denormalizationContext: ['groups' => ['privateMessage:read']]
)]

class PrivateMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['privateMessage:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'privateMessages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['privateMessage:read'])]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'privateMessages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['privateMessage:read', 'privateMessage:write'])]
    private ?Conversation $conversation = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['privateMessage:write', 'privateMessage:read'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['privateMessage:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
