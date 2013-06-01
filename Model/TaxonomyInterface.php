<?php

namespace Kayue\WordpressBundle\Model;

class TaxonomyInterface
{
    /**
     * @var int $id
     */
    protected $id;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $description
     */
    protected $description = '';

    /**
     * @var int $parent
     */
    protected $parent;

    /**
     * @var int $count
     */
    protected $count = 0;

    /**
     * @var TermInterface
     */
    protected $term;

    /**
     * @var PostInterface[]
     */
    protected $posts;

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
     * @param TermInterface $term
     */
    public function setTerm(TermInterface $term)
    {
        $this->term = $term;
    }

    /**
     * Get term
     *
     * @return TermInterface
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Add post
     *
     * @param PostInterface $post
     */
    public function addPosts(PostInterface $post)
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