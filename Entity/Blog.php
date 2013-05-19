<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kayue\WordpressBundle\Model\Blog as BaseBlog;

/**
 * @ORM\Table(name="blogs")
 * @ORM\Entity
 */
class Blog extends BaseBlog
{
    /**
     * @var int $id
     *
     * @ORM\Column(name="blog_id", type="bigint", length=20)
     * @ORM\Id
     */
    protected $id;

    /**
     * @var int $siteId
     *
     * @ORM\Column(name="site_id", type="bigint", length=20)
     * @ORM\Id
     */
    protected $siteId;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @param int $siteId
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }
}