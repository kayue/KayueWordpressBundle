<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

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
    protected static $cache;

    private static $cacheWarmedUp;

    /**
     * Constructor.
     *
     * @param EntityManager     $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository('KayueWordpressBundle:Option');

        $this->cacheAutoloadOptions();
    }

    private function getOptionCache()
    {
        $hash = spl_object_hash($this->em);

        if (!isset(self::$cache[$hash])) {
            self::$cache[$hash] = new ArrayCache();
        }

        return self::$cache[$hash];
    }

    public function findOneOptionByName($name)
    {
        if (false === $option = $this->getOptionCache()->fetch($name)) {
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
        $hash = spl_object_hash($this->em);

        if (!isset(self::$cacheWarmedUp[$hash]))
        {
            $options = $this->repository->findBy(array(
                'autoload' => 'yes'
            ));

            foreach($options as $option) {
                $this->cacheOption($option);
            }

            self::$cacheWarmedUp[$hash] = true;
        }
    }

    private function cacheOption(Option $option)
    {
        $this->getOptionCache()->save($option->getName(), clone $option);
    }
}
