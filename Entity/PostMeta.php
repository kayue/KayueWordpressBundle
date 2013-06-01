<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kayue\WordpressBundle\Model\PostMetaInterface;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * PostMeta
 *
 * @ORM\Table(name="postmeta")
 * @ORM\Entity
 */
class PostMeta extends PostMetaInterface
{
    /**
     * @var int $id
     *
     * @ORM\Column(name="meta_id", type="bigint", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string $key
     *
     * @ORM\Column(name="meta_key", type="string", length=255, nullable=true)
     * @Constraints\NotBlank()
     */
    protected $key;

    /**
     * @var string $value
     *
     * @ORM\Column(name="meta_value", type="wordpressmeta", nullable=true)
     */
    protected $value;

    /**
     * @var Post
     *
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="metas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="post_id", referencedColumnName="ID")
     * })
     */
    protected $post;
}
