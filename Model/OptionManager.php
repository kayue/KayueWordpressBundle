<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class OptionManager implements OptionManagerInterface
{
    /**
     * @var BlogManager
     */
    protected $blogManager;

    /**
     * @var ArrayCache
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @param EntityManager     $em
     */
    public function __construct(BlogManager $blogManager)
    {
        $this->blogManager = $blogManager;
        $this->cache      = new ArrayCache();

        $this->cacheAutoloadOptions();
    }

    public function findOneOptionByName($name)
    {
        if(false === $option = $this->cache->fetch($name)) {
            /** @var $option Option */
            $option = $this->getOptionRepository()->findOneBy(array(
                'name' => $name
            ));

            if($option !== null) {
                $this->cacheOption($option);
            }
        }

        return $option;
    }

    private function cacheAutoloadOptions()
    {
        $options = $this->getOptionRepository()->findBy(array(
            'autoload' => 'yes'
        ));

        foreach($options as $option) {
            $this->cacheOption($option);
        }
    }

    private function cacheOption(Option $option)
    {
        $this->cache->save($option->getName(), clone $option);
    }

    private function getOptionRepository()
    {
        return $this->blogManager->getCurrentBlog()->getEntityManager()->getRepository('KayueWordpressBundle:Option');
    }
}
