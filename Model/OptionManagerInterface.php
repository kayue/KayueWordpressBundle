<?php

namespace Kayue\WordpressBundle\Model;

interface OptionManagerInterface
{
    public function findAllAutoloadOptions();

    public function findOptionByName($name);
}
