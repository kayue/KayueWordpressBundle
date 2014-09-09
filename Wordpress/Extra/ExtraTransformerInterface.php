<?php

namespace Kayue\WordpressBundle\Wordpress\Extra;

interface ExtraTransformerInterface
{
    /**
     * @param  string $content
     * @return string
     */
    public function transform($content);

    /**
     * @param array $options
     */
    public function setOptions(array $options);
}
