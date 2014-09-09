<?php

namespace Kayue\WordpressBundle\Wordpress\Extra;

class ExtraTransformerRegistry
{
    /**
     * @var ExtraTransformerInterface[]
     */
    private $transformers;

    /**
     * @var array
     */
    private $disabledTransformers;

    public function __construct()
    {
        $this->transformers = array();
        $this->disabledTransformers = array();
    }

    /**
     * @param string                    $name
     * @param ExtraTransformerInterface $transformer
     * @throws \RuntimeException
     */
    public function addTransformer($name, ExtraTransformerInterface $transformer)
    {
        if (isset($this->transformers[$name])) {
            throw new \RuntimeException(
                sprintf('The transformer %s is already registered', $name)
            );
        }

        $this->transformers[$name] = $transformer;
    }

    /**
     * @param  string $content
     * @return string
     */
    public function transform($content)
    {
        foreach($this->transformers as $name => $transformer) {
            if (!in_array($name, $this->disabledTransformers)) {
                $content = $transformer->transform($content);
            }
        }

        return $content;
    }

    /**
     * @param string $name
     * @throws \RunTimeException
     */
    public function disable($name)
    {
        $this->disabledTransformers[] = $name;
    }
}
