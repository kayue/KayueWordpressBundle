<?php

namespace Kayue\WordpressBundle\FixturesCommand;

use Doctrine\Bundle\FixturesBundle\Command\LoadDataFixturesDoctrineCommand as BaseLoadDataFixturesDoctrineCommand;

class LoadDataFixturesDoctrineCommand extends BaseLoadDataFixturesDoctrineCommand
{
    protected function getORMPurger($em)
    {
        return new ORMPurger($em, $this->getContainer()->getParameter('kayue_wordpress.table_prefix'));
    }
}
