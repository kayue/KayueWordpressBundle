<?php

namespace Kayue\WordpressBundle\Purger;

use Doctrine\Common\DataFixtures\Purger\ORMPurger as BaseORMPurger;

class ORMPurger extends BaseORMPurger
{
    protected $tablePrefix;

    public function __construct($em, $tablePrefix)
    {
        parent::__construct($em);
        $this->tablePrefix = $tablePrefix;
    }

    protected function getAllMetadata()
    {
        $metadatas = array();

        $allMetadata = $this->getObjectManager()->getMetadataFactory()->getAllMetadata();
        foreach($allMetadata as $metadata) {
            if (strpos($metadata->table['name'], $this->tablePrefix) === 0) continue;

            $metadatas[] = $metadata;
        }

        return $metadatas;
    }
}
