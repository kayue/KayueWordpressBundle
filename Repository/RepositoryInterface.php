<?php

namespace Kayue\WordpressBundle\Repository;

interface RepositoryInterface
{
    function getQueryBuilder();
    function getAlias();
} 
