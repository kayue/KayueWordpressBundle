<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Proxy\Proxy;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @ORM\Table(name="comments")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Comment
{
    /**
     * @var int $id
     *
     * @ORM\Column(name="comment_ID", type="wordpressid", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $author
     *
     * @ORM\Column(name="comment_author", type="text")
     * @Constraints\NotBlank()
     */
    private $author;

    /**
     * @var string $authorEmail
     *
     * @ORM\Column(name="comment_author_email", type="string")
     * @Constraints\Email()
     */
    private $authorEmail = '';

    /**
     * @var string $authorUrl
     *
     * @ORM\Column(name="comment_author_url", type="string")
     * @Constraints\Url()
     */
    private $authorUrl = '';

    /**
     * @var string $authorIp
     *
     * @ORM\Column(name="comment_author_IP", type="string")
     * @Constraints\Ip()
     */
    private $authorIp;

    /**
     * @var \DateTime $date
     *
     * @ORM\Column(name="comment_date", type="datetime")
     */
    private $date;

    /**
     * @var \DateTime $dateGmt
     *
     * @ORM\Column(name="comment_date_gmt", type="datetime")
     */
    private $dateGmt;

    /**
     * @var string $content
     *
     * @ORM\Column(name="comment_content", type="text")
     * @Constraints\NotBlank()
     */
    private $content;

    /**
     * @var integer $karma
     *
     * @ORM\Column(name="comment_karma", type="integer")
     */
    private $karma = 0;

    /**
     * @var string $approved
     *
     * @ORM\Column(name="comment_approved", type="string")
     */
    private $approved = 1;

    /**
     * @var string $agent
     *
     * @ORM\Column(name="comment_agent", type="string")
     */
    private $agent;

    /**
     * @var string $type
     *
     * @ORM\Column(name="comment_type", type="string")
     */
    private $type = '';

    /**
     * @var int $parent
     *
     * @ORM\OneToOne(targetEntity="Comment")
     * @ORM\JoinColumn(name="comment_parent", referencedColumnName="comment_ID")
     */
    private $parent;

    /**
     * @var CommentMeta
     *
     * @ORM\OneToMany(targetEntity="Kayue\WordpressBundle\Entity\CommentMeta", mappedBy="comment")
     */
    private $metas;

    /**
     * @var Post
     *
     * @ORM\ManyToOne(targetEntity="Kayue\WordpressBundle\Entity\Post", inversedBy="comments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comment_post_ID", referencedColumnName="ID", nullable=false)
     * })
     */
    private $post;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Kayue\WordpressBundle\Entity\User", inversedBy="comments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="ID")
     * })
     */
    private $user;

    public function __construct()
    {
        $this->authorUrl = "";
        $this->authorIp = "";
        $this->karma = 0;
        $this->approved = 1;
        $this->agent = "";
        $this->type = "";
        $this->parent = null;
        $this->user = null;

        $this->metas = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getContent();
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->date    = new \DateTime('now');
        $this->dateGmt = new \DateTime('now', new \DateTimeZone('GMT'));
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set author
     *
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set authorEmail
     *
     * @param string $authorEmail
     */
    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;
    }

    /**
     * Get authorEmail
     *
     * @return string
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    /**
     * Set authorUrl
     *
     * @param string $authorUrl
     */
    public function setAuthorUrl($authorUrl)
    {
        $this->authorUrl = $authorUrl;
    }

    /**
     * Get authorUrl
     *
     * @return string
     */
    public function getAuthorUrl()
    {
        return $this->authorUrl;
    }

    /**
     * Set authorIp
     *
     * @param string $authorIp
     */
    public function setAuthorIp($authorIp)
    {
        $this->authorIp = $authorIp;
    }

    /**
     * Get authorIp
     *
     * @return string
     */
    public function getAuthorIp()
    {
        return $this->authorIp;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date_gmt
     *
     * @param \DateTime $dateGmt
     */
    public function setDateGmt($dateGmt)
    {
        $this->dateGmt = $dateGmt;
    }

    /**
     * Get date_gmt
     *
     * @return \DateTime
     */
    public function getDateGmt()
    {
        return $this->dateGmt;
    }

    /**
     * Set content
     *
     * @param string $commentContent
     */
    public function setContent($commentContent)
    {
        $this->content = $commentContent;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set karma
     *
     * @param integer $karma
     */
    public function setKarma($karma)
    {
        $this->karma = $karma;
    }

    /**
     * Get karma
     *
     * @return integer
     */
    public function getKarma()
    {
        return $this->karma;
    }

    /**
     * Set approved
     *
     * @param string $approved
     */
    public function setApproved($approved)
    {
        if (is_bool($approved)) {
            $this->approved = $approved ? 1 : 0;
        }

        $this->approved = $approved;
    }

    /**
     * Get approved
     *
     * @return string
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * Set agent
     *
     * @param string $agent
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
    }

    /**
     * Get agent
     *
     * @return string
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Set type
     *
     * @param string $commentType
     */
    public function setType($commentType)
    {
        $this->type = $commentType;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set parent
     *
     * @param Comment $comment
     */
    public function setParent(Comment $comment)
    {
        $this->parent = $comment;
    }

    /**
     * Get parent
     *
     * @return Comment
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add meta
     *
     * @param CommentMeta $meta
     */
    public function addMeta(CommentMeta $meta)
    {
        $this->metas[] = $meta;
    }

    /**
     * Get metas
     *
     * @return Collection
     */
    public function getMetas()
    {
        return $this->metas;
    }

    /**
     * Set post
     *
     * @param Post $post
     */
    public function setPost(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get post
     *
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set user
     *
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        $this->author      = $user->getDisplayName();
        $this->authorUrl   = $user->getUrl();
        $this->authorEmail = $user->getEmail();
    }

    /**
     * Get user
     *
     * @return User
     * @return null
     */
    public function getUser()
    {
        if ($this->user instanceof Proxy) {
            try {
                // prevent lazy loading the user entity becuase it might not exist
                $this->user->__load();
            } catch (EntityNotFoundException $e) {
                // return null if user does not exist
                $this->user = null;
            }
        }

        return $this->user;
    }
}
