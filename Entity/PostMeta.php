<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kayue\WordpressBundle\Annotation as Wordpress;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * PostMeta
 *
 * @ORM\Table(name="postmeta")
 * @ORM\Entity
 * @Wordpress\WordpressTable
 */
class PostMeta
{
    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="meta_id", type="bigint", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="meta_key", type="string", length=255, nullable=true)
     * @Constraints\NotBlank()
     */
    protected $key;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="meta_value", type="wordpressmeta", nullable=true)
     */
    protected $value;

    /**
     * {@inheritdoc}
     *
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="metas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="post_id", referencedColumnName="ID")
     * })
     */
    protected $post;

    /**
     * Get post meta ID
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
}
