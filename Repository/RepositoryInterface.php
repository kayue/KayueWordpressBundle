<?php

namespace Kayue\WordpressBundle\Repository;

interface RepositoryInterface
{
    public function getQueryBuilder();
    public function getAlias();
}
