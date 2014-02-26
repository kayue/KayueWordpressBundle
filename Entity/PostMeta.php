<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kayue\WordpressBundle\Annotation as Kayue;
use Kayue\WordpressBundle\Model\PostMeta as ModelPostMeta;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * PostMeta
 *
 * @ORM\Table(name="postmeta")
 * @ORM\Entity
 * @Kayue\WPTable
 */
class PostMeta extends ModelPostMeta
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
}
