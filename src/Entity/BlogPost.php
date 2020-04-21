<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\EntityInterface\PostableInterface;
use App\EntityInterface\RouteableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Criticalmass\Router\Annotation as Routing;

/**
 * @ORM\Table(name="blog_post")
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="App\Repository\BlogPostRepository")
 * @Routing\DefaultRoute(name="caldera_criticalmass_blog_post")
 * @Routing\DefaultParameter(routeParameterName="host", parameterName="domain.blog")
 */
class BlogPost implements RouteableInterface, PostableInterface, ViewableEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Routing\RouteParameter(name="blogPostId")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Routing\RouteParameter(name="slug")
     */
    protected $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Blog", inversedBy="blogPosts")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $blog;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="blogPost")
     */
    protected $comments;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $text;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="blogPosts")
     */
    protected $user;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $intro;

    /**
     * @var File $imageFile
     * @Vich\UploadableField(mapping="blog_post_teaser", fileNameProperty="imageName", size="imageSize", mimeType="imageMimeType")
     */
    protected $imageFile;

    /**
     * @var string $imageName
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $imageName;

    /**
     * @var int $imageSize
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $imageSize;

    /**
     * @var string $imageMimeType
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $imageMimeType;

    /**
     * @ORM\Column(type="integer")
     */
    protected $views = 0;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): BlogPost
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): BlogPost
    {
        $this->slug = $slug;

        return $this;
    }

    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    public function setBlog(?Blog $blog): BlogPost
    {
        $this->blog = $blog;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): BlogPost
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): BlogPost
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Post $comment): BlogPost
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setBlogPost($this);
        }

        return $this;
    }

    public function removeComment(Post $comment): BlogPost
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getBlogPost() === $this) {
                $comment->setBlogPost(null);
            }
        }

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): BlogPost
    {
        $this->text = $text;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): BlogPost
    {
        $this->user = $user;

        return $this;
    }

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setIntro(?string $intro): BlogPost
    {
        $this->intro = $intro;

        return $this;
    }

    public function setImageFile(File $image = null): BlogPost
    {
        $this->imageFile = $image;

        if ($image) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(string $imageName = null): BlogPost
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(int $imageSize = null): BlogPost
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function getImageMimeType(): ?string
    {
        return $this->imageMimeType;
    }

    public function setImageMimeType(string $imageMimeType = null): BlogPost
    {
        $this->imageMimeType = $imageMimeType;

        return $this;
    }

    public function setViews(int $views): ViewableEntity
    {
        $this->views = $views;

        return $this;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function incViews(): ViewableEntity
    {
        ++$this->views;

        return $this;
    }
}
