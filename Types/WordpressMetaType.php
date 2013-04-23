<?php

/**
 * Datatype for WordPress's meta value
 */

namespace Kayue\WordpressBundle\Types;

use Doctrine\DBAL\Types\TextType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class WordPressMetaType extends TextType
{
    const NAME = 'wordpressmeta';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($this->isSerialized($value)) {
            return @unserialize($value);
        }

        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (is_array($value) || is_object($value)) {
            return serialize($value);
        }

        return $value;
    }

    public function getName()
    {
        return self::NAME;
    }

    /**
     * Check value to find if it was serialized.
     *
     * If $data is not an string, then returned value will always be false.
     * Serialized data is always a string.
     *
     * @param  mixed $data Value to check to see if was serialized.
     * @return bool  False if not serialized and true if it was.
     */
    private function isSerialized($data)
    {
        // if it isn't a string, it isn't serialized
        if (!is_string($data))
            return false;
        $data = trim($data);
        if ('N;' == $data)
            return true;
        $length = strlen($data);
        if ($length < 4)
            return false;
        if (':' !== $data[1])
            return false;
        $lastc = $data[$length-1];
        if (';' !== $lastc && '}' !== $lastc)
            return false;
        $token = $data[0];
        switch ($token) {
            case 's' :
                if ( '"' !== $data[$length-2] )
                    return false;
            case 'a' :
            case 'O' :
                return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
            case 'b' :
            case 'i' :
            case 'd' :
                return (bool) preg_match( "/^{$token}:[0-9.E-]+;\$/", $data );
        }

        return false;
    }
}
