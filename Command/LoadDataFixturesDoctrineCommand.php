<?php

namespace Kayue\WordpressBundle\Command;

use Doctrine\Bundle\FixturesBundle\Command\LoadDataFixturesDoctrineCommand as BaseLoadDataFixturesDoctrineCommand;
use Kayue\WordpressBundle\Purger\ORMPurger;

class LoadDataFixturesDoctrineCommand extends BaseLoadDataFixturesDoctrineCommand
{
    protected function getORMPurger($em)
    {
        return new ORMPurger($em, $this->getContainer()->getParameter('kayue_wordpress.table_prefix'));
    }
}
