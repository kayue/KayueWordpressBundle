<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @ORM\Table(name="commentmeta")
 * @ORM\Entity
 */
class CommentMeta
{
    /**
     * @var int $id
     *
     * @ORM\Column(name="meta_id", type="bigint", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $key
     *
     * @ORM\Column(name="meta_key", type="string", length=255, nullable=true)
     * @Constraints\NotBlank()
     */
    private $key;

    /**
     * @var string $value
     *
     * @ORM\Column(name="meta_value", type="wordpressmeta", nullable=true)
     */
    private $value;

    /**
     * @var Comment
     *
     * @ORM\ManyToOne(targetEntity="Kayue\WordpressBundle\Entity\Comment", inversedBy="metas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comment_id", referencedColumnName="comment_ID")
     * })
     */
    private $comment;

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
     * @param Comment $comment
     */
    public function setComment(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return Comment
     */
    public function getComment()
    {
        return $this->comment;
    }
}
