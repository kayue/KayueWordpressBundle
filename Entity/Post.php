<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kayue\WordpressBundle\Annotation as Kayue;
use Kayue\WordpressBundle\Model\Post as ModelPost;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * Post
 *
 * @ORM\Table(name="posts")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Kayue\WPTable
 */
class Post extends ModelPost
{
    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="ID", type="wordpressid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="post_date", type="datetime", nullable=false)
     */
    protected $date;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="post_date_gmt", type="datetime", nullable=false)
     */
    protected $dateGmt;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="post_content", type="text", nullable=false)
     * @Constraints\NotBlank()
     */
    protected $content;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="post_title", type="text", nullable=false)
     * @Constraints\NotBlank()
     */
    protected $title;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="post_excerpt", type="text", nullable=false)
     * @Constraints\NotBlank()
     */
    protected $excerpt;

    /**
     * {@inheritdoc}
     */
    protected $excerptLength = 100;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="post_status", type="string", length=20, nullable=false)
     */
    protected $status = "publish";

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="comment_status", type="string", length=20, nullable=false)
     */
    protected $commentStatus = "open";

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="ping_status", type="string", length=20, nullable=false)
     */
    protected $pingStatus = "open";

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="post_password", type="string", length=20, nullable=false)
     */
    protected $password = "";

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="post_name", type="string", length=200, nullable=false)
     */
    protected $slug;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="to_ping", type="text", nullable=false)
     */
    protected $toPing = "";

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="pinged", type="text", nullable=false)
     */
    protected $pinged = "";

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="post_modified", type="datetime", nullable=false)
     */
    protected $modifiedDate;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="post_modified_gmt", type="datetime", nullable=false)
     */
    protected $modifiedDateGmt;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="post_content_filtered", type="text", nullable=false)
     */
    protected $contentFiltered = "";

    /**
     * {@inheritdoc}
     *
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="children")
     * @ORM\JoinColumn(name="post_parent", referencedColumnName="ID")
     */
    protected $parent;

    /**
     * {@inheritdoc}
     *
     * @ORM\OneToMany(targetEntity="Post", mappedBy="parent")
     */
    protected $children;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="guid", type="string", length=255, nullable=false)
     */
    protected $guid = "";

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="menu_order", type="integer", length=11, nullable=false)
     */
    protected $menuOrder = 0;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="post_type", type="string", nullable=false)
     */
    protected $type = "post";

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="post_mime_type", type="string", length=100, nullable=false)
     */
    protected $mimeType = "";

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="comment_count", type="bigint", length=20, nullable=false)
     */
    protected $commentCount = 0;

    /**
     * {@inheritdoc}
     *
     * @ORM\OneToMany(targetEntity="Kayue\WordpressBundle\Entity\PostMeta", mappedBy="post", cascade={"persist"})
     */
    protected $metas;

    /**
     * {@inheritdoc}
     *
     * @ORM\OneToMany(targetEntity="Kayue\WordpressBundle\Entity\Comment", mappedBy="post", cascade={"persist"})
     */
    protected $comments;

    /**
     * {@inheritdoc}
     *
     * @ORM\ManyToOne(targetEntity="Kayue\WordpressBundle\Entity\User", inversedBy="posts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="post_author", referencedColumnName="ID")
     * })
     */
    protected $user;

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
