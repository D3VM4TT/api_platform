<?php


namespace App\Entity;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\CheeseListingRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ApiResource(
 *     collectionOperations={"get", "post"},
 *     itemOperations={
 *       "get"={
 *          "normalization_context"={
 *              "groups"={"cheese_listing:read", "cheese_listing:item:get"}
 *          },
 *       },
 *       "put"
 *      },
 *     shortName="cheeses",
 *     normalizationContext={
 *       "groups"={"cheese_listing:read"}
 *     },
 *     denormalizationContext={
 *       "groups"={"cheese_listing:write"}
 *     },
 *     formats={"json", "jsonhal", "jsonld", "csv"={"text/csv"}}
 * )
 * @ApiFilter(BooleanFilter::class, properties={"isPublished"})
 * @ApiFilter(SearchFilter::class, properties={"title": "partial", "description": "partial"})
 * @ApiFilter(RangeFilter::class, properties={"price"})
 * @ApiFilter(PropertyFilter::class)
 * @ORM\Entity(repositoryClass=CheeseListingRepository::class)
 *
 */

class CheeseListing
{


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"cheese_listing:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"cheese_listing:read", "cheese_listing:write", "user:read"})
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=40,
     *     maxMessage="Please describe your cheese in less than 40 characters",
     *     min="2"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"cheese_listing:read", "user:read"})
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"cheese_listing:read", "cheese_listing:write", "user:read"})
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished = false;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="cheeseListings")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"cheese_listing:read", "cheese_listing:write"})
     * @Assert\Valid()
     */
    private $owner;

    /**
     * @param $createdAt
     */
    public function __construct(string $title = null)
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->title = $title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @Groups({"cheese_listing:read", "cheese_listing:write"})
     */
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) <= 40) {
            return $this->description;
        }

        return substr($this->description, 0, 40) . '...';

    }

    public function setDescription($description): self {
        $this->description = $description;
        return  $this;
    }


    /**
     * The description of the cheese listing as raw text
     * @Groups({"cheese_listing:write"})
     * @SerializedName("description")
     */
    public function setTextDescription(string $description): self
    {
        $this->description = nl2br($description);

        return $this;
    }


    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     *  How long ago in text the cheese listing was added
     *
     *  @Groups({"cheese_listing:read"})
     */
    public function getCreatedAtAgo(): string {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getOwner(): ?user
    {
        return $this->owner;
    }

    public function setOwner(?user $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
