<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kayue\WordpressBundle\Entity\Option;

class OptionManager implements OptionManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var ArrayCache
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @param EntityManager     $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository('KayueWordpressBundle:Option');
        $this->cache      = new ArrayCache();

        $this->cacheAutoloadOptions();
    }

    public function findOneOptionByName($name)
    {
        if(false === $option = $this->cache->fetch($name)) {
            /** @var $option Option */
            $option = $this->repository->findOneBy(array(
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
        $options = $this->repository->findBy(array(
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
}
