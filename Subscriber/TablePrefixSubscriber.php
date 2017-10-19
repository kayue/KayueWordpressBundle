<?php

namespace Kayue\WordpressBundle\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationReader;
use Kayue\WordpressBundle\Annotation\WordpressTable;

class TablePrefixSubscriber implements EventSubscriber
{
    protected $prefix;
    protected $annotatonReader;

    public function __construct($prefix, Reader $annotatonReader = null)
    {
        $this->prefix = (string) $prefix;

        if (null === $annotatonReader) {
            $annotatonReader = new AnnotationReader();
        }

        $this->annotatonReader = $annotatonReader;
    }

    public function getSubscribedEvents()
    {
        return array('loadClassMetadata');
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $classMetadata = $args->getClassMetadata();

        if (!$classMetadata->getReflectionClass()) {
            return;
        }

        // Get class annotations
        $classAnnotations = $this->annotatonReader->getClassAnnotations($classMetadata->getReflectionClass());

        // Search for WordpressTable annotation
        $found = false;
        foreach ($classAnnotations as $classAnnotation) {
            if ($classAnnotation instanceof WordpressTable) {
                $found = true;
                break;
            }
        }

        // Only apply to classes having WPTable annotation
        if (!$found) {
            return;
        }

        // set table prefix
        $prefix = $this->getPrefix($classMetadata->name, $args->getEntityManager());

        $classMetadata->setPrimaryTable(array(
            'name' => $prefix.$classMetadata->getTableName(),
        ));

        // set table prefix to associated entity
        // TODO: make sure prefix won't apply to user table
        foreach ($classMetadata->associationMappings as &$mapping) {
            if (isset($mapping['joinTable']) && !empty($mapping['joinTable'])) {
                $mapping['joinTable']['name'] = $prefix.$mapping['joinTable']['name'];
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
                $prefix = $prefix.$blogId.'_';
            }
        }

        return $prefix;
    }
}
