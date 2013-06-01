<?php

namespace Kayue\WordpressBundle\Model;

class TermInterface
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
     * @var string $slug
     */
    protected $slug;

    /**
     * @var int $group
     */
    protected $group = 0;

    /**
     * @var TaxonomyInterface
     **/
    protected $taxonomy;

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
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set group
     *
     * @param int $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * Get group
     *
     * @return int
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set taxonomy
     *
     * @param TaxonomyInterface $taxonomy
     */
    public function setTaxonomy(TaxonomyInterface $taxonomy)
    {
        $this->taxonomy = $taxonomy;
    }

    /**
     * Get taxonomy
     *
     * @return TaxonomyInterface
     */
    public function getTaxonomy()
    {
        return $this->taxonomy;
    }
}