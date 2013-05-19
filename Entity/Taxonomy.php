<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @ORM\Table(name="term_taxonomy")
 * @ORM\Entity
 */
class Taxonomy
{
    /**
     * @var int $id
     *
     * @ORM\Column(name="term_taxonomy_id", type="wordpressid", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="taxonomy", type="string", length=32)
     * @Constraints\NotBlank()
     */
    private $name;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description = '';

    /**
     * @var int $parent
     *
     * @ORM\Column(name="parent", type="bigint", length=20)
     */
    private $parent;

    /**
     * @var int $count
     *
     * @ORM\Column(name="count", type="bigint", length=20)
     */
    private $count = 0;

    /**
     * @var Term
     *
     * @ORM\OneToOne(targetEntity="Kayue\WordpressBundle\Entity\Term", inversedBy="taxonomy")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="term_id", referencedColumnName="term_id", unique=true)
     * })
     */
    private $term;

    /**
     * @var Post
     *
     * @ORM\ManyToMany(targetEntity="Post", mappedBy="taxonomies")
     **/
    private $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set parent
     *
     * @param int $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set count
     *
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * Get count
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set term
     *
     * @param Term $term
     */
    public function setTerm(Term $term)
    {
        $this->term = $term;
    }

    /**
     * Get term
     *
     * @return Term
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Add post
     *
     * @param Post $post
     */
    public function addPosts(Post $post)
    {
        $this->posts[] = $post;
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }
}
