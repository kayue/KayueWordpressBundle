<?php

namespace Kayue\WordpressBundle\Model;

class Comment implements CommentInterface
{
    /**
     * @var int $id
     */
    protected $id;

    /**
     * @var string $author
     */
    protected $author;

    /**
     * @var string $authorEmail
     */
    protected $authorEmail = '';

    /**
     * @var string $authorUrl
     */
    protected $authorUrl = '';

    /**
     * @var string $authorIp
     */
    protected $authorIp = '';

    /**
     * @var \DateTime $date
     *
     */
    protected $date;

    /**
     * @var \DateTime $dateGmt
     *
     */
    protected $dateGmt;

    /**
     * @var string $content
     */
    protected $content;

    /**
     * @var integer $karma
     */
    protected $karma = 0;

    /**
     * @var string $approved
     */
    protected $approved = 1;

    /**
     * @var string $agent
     */
    protected $agent = '';

    /**
     * @var string $type
     */
    protected $type = '';

    /**
     * @var int $parent
     */
    protected $parent;

    /**
     * @var CommentMeta
     */
    protected $metas;

    /**
     * @var Post
     */
    protected $post;

    /**
     * @var UserInterface|null
     */
    protected $user;

    public function __toString()
    {
        return $this->getContent();
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
     * @return CommentMeta[]
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
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
        $this->author      = $user->getDisplayName();
        $this->authorUrl   = $user->getUrl();
        $this->authorEmail = $user->getEmail();
    }

    /**
     * Get user
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}