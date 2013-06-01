<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kayue\WordpressBundle\Model\PostInterface;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * Post
 *
 * @ORM\Table(name="posts")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Post extends PostInterface
{
    /**
     * @var int $id
     *
     * @ORM\Column(name="ID", type="wordpressid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \DateTime $date
     *
     * @ORM\Column(name="post_date", type="datetime", nullable=false)
     */
    protected $date;

    /**
     * @var \DateTime $dateGmt
     *
     * @ORM\Column(name="post_date_gmt", type="datetime", nullable=false)
     */
    protected $dateGmt;

    /**
     * @var string $content
     *
     * @ORM\Column(name="post_content", type="text", nullable=false)
     * @Constraints\NotBlank()
     */
    protected $content;

    /**
     * @var string $title
     *
     * @ORM\Column(name="post_title", type="text", nullable=false)
     * @Constraints\NotBlank()
     */
    protected $title;

    /**
     * @var string $excerpt
     *
     * @ORM\Column(name="post_excerpt", type="text", nullable=false)
     * @Constraints\NotBlank()
     */
    protected $excerpt;

    /**
     * @var int excerpt length
     */
    protected $excerptLength = 100;

    /**
     * @var string $status
     *
     * @ORM\Column(name="post_status", type="string", length=20, nullable=false)
     */
    protected $status = "publish";

    /**
     * @var string $commentStatus
     *
     * @ORM\Column(name="comment_status", type="string", length=20, nullable=false)
     */
    protected $commentStatus = "open";

    /**
     * @var string $pingStatus
     *
     * @ORM\Column(name="ping_status", type="string", length=20, nullable=false)
     */
    protected $pingStatus = "open";

    /**
     * @var string $password
     *
     * @ORM\Column(name="post_password", type="string", length=20, nullable=false)
     */
    protected $password = "";

    /**
     * @var string $slug
     *
     * @ORM\Column(name="post_name", type="string", length=200, nullable=false)
     */
    protected $slug;

    /**
     * @var string $toPing
     *
     * @ORM\Column(name="to_ping", type="text", nullable=false)
     */
    protected $toPing = "";

    /**
     * @var string $pinged
     *
     * @ORM\Column(name="pinged", type="text", nullable=false)
     */
    protected $pinged = "";

    /**
     * @var \DateTime $modifiedDate
     *
     * @ORM\Column(name="post_modified", type="datetime", nullable=false)
     */
    protected $modifiedDate;

    /**
     * @var \DateTime $modifiedDateGmt
     *
     * @ORM\Column(name="post_modified_gmt", type="datetime", nullable=false)
     */
    protected $modifiedDateGmt;

    /**
     * @var string $contentFiltered
     *
     * @ORM\Column(name="post_content_filtered", type="text", nullable=false)
     */
    protected $contentFiltered = "";

    /**
     * @var Post $parent
     *
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="children")
     * @ORM\JoinColumn(name="post_parent", referencedColumnName="ID")
     */
    protected $parent;

    /**
     * @var Post $children
     *
     * @ORM\OneToMany(targetEntity="Post", mappedBy="parent")
     */
    protected $children;

    /**
     * @var string $guid
     *
     * @ORM\Column(name="guid", type="string", length=255, nullable=false)
     */
    protected $guid = "";

    /**
     * @var integer $menuOrder
     *
     * @ORM\Column(name="menu_order", type="integer", length=11, nullable=false)
     */
    protected $menuOrder = 0;

    /**
     * @var string $type
     *
     * @ORM\Column(name="post_type", type="string", nullable=false)
     */
    protected $type = "post";

    /**
     * @var string $mimeType
     *
     * @ORM\Column(name="post_mime_type", type="string", length=100, nullable=false)
     */
    protected $mimeType = "";

    /**
     * @var int $commentCount
     *
     * @ORM\Column(name="comment_count", type="bigint", length=20, nullable=false)
     */
    protected $commentCount = 0;

    /**
     * @var PostMeta
     *
     * @ORM\OneToMany(targetEntity="Kayue\WordpressBundle\Entity\PostMeta", mappedBy="post", cascade={"persist"})
     */
    protected $metas;

    /**
     * @var Comment
     *
     * @ORM\OneToMany(targetEntity="Kayue\WordpressBundle\Entity\Comment", mappedBy="post", cascade={"persist"})
     */
    protected $comments;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Kayue\WordpressBundle\Entity\User", inversedBy="posts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="post_author", referencedColumnName="ID")
     * })
     */
    protected $user;

    /**
     * @var Taxonomy
     *
     * @ORM\ManyToMany(targetEntity="Kayue\WordpressBundle\Entity\Taxonomy", inversedBy="posts")
     * @ORM\JoinTable(name="term_relationships",
     *   joinColumns={
     *     @ORM\JoinColumn(name="object_id", referencedColumnName="ID")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="term_taxonomy_id", referencedColumnName="term_taxonomy_id")
     *   }
     * )
     */
    protected $taxonomies;

    public function __construct()
    {
        $this->metas      = new ArrayCollection();
        $this->comments   = new ArrayCollection();
        $this->taxonomies = new ArrayCollection();
        $this->children   = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->date            = new \DateTime('now');
        $this->dateGmt         = new \DateTime('now', new \DateTimeZone('GMT'));
        $this->modifiedDate    = new \DateTime('now');
        $this->modifiedDateGmt = new \DateTime('now', new \DateTimeZone('GMT'));
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->modifiedDate     = new \DateTime('now');
        $this->modifiedDateGmt  = new \DateTime('now', new \DateTimeZone('GMT'));
    }

    /**
     * Get user
     *
     * @return \Kayue\WordpressBundle\Model\UserInterface|null
     */
    public function getUser()
    {
        if ($this->user instanceof \Doctrine\ORM\Proxy\Proxy) {
            try {
                // prevent lazy loading the user entity because it might not exist
                $this->user->__load();
            } catch (\Doctrine\ORM\EntityNotFoundException $e) {
                // return null if user does not exist
                $this->user = null;
            }
        }

        return $this->user;
    }
}
