<?php

namespace Kayue\WordpressBundle\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

class TablePrefixSubscriber implements EventSubscriber
{
    protected $prefix = '';

    public function __construct($prefix)
    {
        $this->prefix = (string) $prefix;
    }

    public function getSubscribedEvents()
    {
        return array('loadClassMetadata');
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $classMetadata = $args->getClassMetadata();

        // only apply to WordpressBundle's Entitiy
        if ($classMetadata->namespace !== 'Kayue\WordpressBundle\Entity') {
            return;
        }

        // set table prefix
        $prefix = $this->getPrefix($classMetadata->name, $args->getEntityManager());

        $classMetadata->setPrimaryTable(array(
            'name' => $prefix . $classMetadata->getTableName()
        ));

        // set table prefix to associated entity
        // TODO: make sure prefix won't apply to user table
        foreach ($classMetadata->associationMappings as &$mapping) {
            if (isset($mapping['joinTable']) && !empty($mapping['joinTable'])) {
                $mapping['joinTable']['name'] = $prefix . $mapping['joinTable']['name'];
            }
        }
    }

    /**
     * Returns the table prefix for entity, with blog ID appened if needed
     *
     * @param  string        $entityName fully-qualified class name of the persistent class.
     * @param  EntityManager $em
     * @return string
     */
    private function getPrefix($entityName, $em)
    {
        $prefix = $this->prefix;

        // users and usermeta table won't have blog ID appended.
        if ($entityName === 'Kayue\WordpressBundle\Entity\User' ||
            $entityName === 'Kayue\WordpressBundle\Entity\UserMeta') {
            return $this->prefix;
        }

        if (method_exists($em, 'getBlogId')) {
            $blogId  = $em->getBlogId();

            // append blog ID to prefix
            if ($blogId > 1) {
                $prefix = $prefix . $blogId . '_';
            }
        }

        return $prefix;
    }
}