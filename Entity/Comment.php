<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kayue\WordpressBundle\Model\Comment as ModelComment;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @ORM\Table(name="comments")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Comment extends ModelComment
{
    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="comment_ID", type="wordpressid", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="comment_author", type="text")
     * @Constraints\NotBlank()
     */
    protected $author;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="comment_author_email", type="string")
     * @Constraints\Email()
     */
    protected $authorEmail = '';

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="comment_author_url", type="string")
     * @Constraints\Url()
     */
    protected $authorUrl = '';

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="comment_author_IP", type="string")
     * @Constraints\Ip()
     */
    protected $authorIp;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="comment_date", type="datetime")
     */
    protected $date;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="comment_date_gmt", type="datetime")
     */
    protected $dateGmt;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="comment_content", type="text")
     * @Constraints\NotBlank()
     */
    protected $content;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="comment_karma", type="integer")
     */
    protected $karma = 0;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="comment_approved", type="string")
     */
    protected $approved = 1;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="comment_agent", type="string")
     */
    protected $agent;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="comment_type", type="string")
     */
    protected $type = '';

    /**
     * {@inheritdoc}
     *
     * @ORM\OneToOne(targetEntity="Comment")
     * @ORM\JoinColumn(name="comment_parent", referencedColumnName="comment_ID")
     */
    protected $parent;

    /**
     * {@inheritdoc}
     *
     * @ORM\OneToMany(targetEntity="Kayue\WordpressBundle\Entity\CommentMeta", mappedBy="comment")
     */
    protected $metas;

    /**
     * {@inheritdoc}
     *
     * @ORM\ManyToOne(targetEntity="Kayue\WordpressBundle\Entity\Post", inversedBy="comments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comment_post_ID", referencedColumnName="ID", nullable=false)
     * })
     */
    protected $post;

    /**
     * {@inheritdoc}
     *
     * @ORM\ManyToOne(targetEntity="Kayue\WordpressBundle\Entity\User", inversedBy="comments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="ID")
     * })
     */
    protected $user;

    public function __construct()
    {
        $this->metas = new ArrayCollection();
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
