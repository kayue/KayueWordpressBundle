<?php

namespace Kayue\WordpressBundle\Model;

class CommentMetaInterface
{
    /**
     * @var int $id
     */
    protected $id;

    /**
     * @var string $key
     */
    protected $key;

    /**
     * @var string $value
     */
    protected $value;

    /**
     * @var CommentInterface
     */
    protected $comment;

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
     * Set key
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set comment
     *
     * @param CommentInterface $comment
     */
    public function setComment(CommentInterface $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return CommentInterface
     */
    public function getComment()
    {
        return $this->comment;
    }
}