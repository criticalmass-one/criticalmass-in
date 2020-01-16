<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\ViewStorage\ViewInterface\ViewEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="blog_post_view")
 * @ORM\Entity()
 */
class BlogPostView implements ViewEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="blog_post_views")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="BlogPost", inversedBy="blog_post_views")
     * @ORM\JoinColumn(name="blog_post_id", referencedColumnName="id")
     */
    protected $blogPost;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateTime;

    public function __construct()
    {
        $this->dateTime = new \DateTime();
    }

    public function setId(int $id): ViewEntity
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user = null): ViewEntity
    {
        $this->user = $user;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): ViewEntity
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(BlogPost $blogPost = null): BlogPostView
    {
        $this->blogPost = $blogPost;

        return $this;
    }
}
