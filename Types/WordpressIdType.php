<?php

/**
 * Datatype for WordPress's IDs
 *
 * WordPress use 0 to represent a guest user. It cause a lots of problems
 * in Doctrine because the user with id zero never exist. This datatype
 * convert 0 to null, make life easier.
 */

namespace Kayue\WordpressBundle\Types;

use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class WordpressIdType extends BigIntType
{
    const NAME = 'wordpressid';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === 0) {
            return null;
        }

        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return 0;
        }

        return $value;
    }

    public function getName()
    {
        return self::NAME;
    }
}
