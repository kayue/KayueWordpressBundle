<?php

namespace Kayue\WordpressBundle\Wordpress\Extra;

class VideoExtraTransformer implements ExtraTransformerInterface
{
    private $options = [
        'width'  => '100%',
        'height' => '400',
        'allowfullscreen' => true,
        'types' => ['youtube', 'vimeo']
    ];

    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }

    public function transform($content)
    {
        if (in_array('youtube', $this->options['types'])) {
            $content = $this->transformYoutubeLinks($content);
        }

        if (in_array('vimeo', $this->options['types'])) {
            $content = $this->transformVimeoLinks($content);
        }

        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    private function transformYoutubeLinks($content)
    {
        return preg_replace(
            '/https?:\/\/www\.youtube\.com\/watch\?v=([A-Za-z0-9\-\_]*)&?[^\s^<]*/',
            '<iframe width="' . $this->options['width'] . '" height="' .
                $this->options['height'] . '" src="//www.youtube.com/embed/$1" frameborder="0" ' .
                ($this->options['allowfullscreen'] ? 'allowfullscreen webkitallowfullscreen mozallowfullscreen' : '') .
                '></iframe>',
            $content
        );
    }

    /**
     * @param string $content
     * @return string
     */
    private function transformVimeoLinks($content)
    {
        return preg_replace(
            '/https?:\/\/vimeo\.com[^\s<]*\/(\d+)/',
            '<iframe src="//player.vimeo.com/video/$1" width="'.$this->options['width'].'" height="' .
                $this->options['height'] . '" frameborder="0"  '.
                ($this->options['allowfullscreen'] ? 'allowfullscreen webkitallowfullscreen mozallowfullscreen' : '').
                '></iframe>',
            $content
        );
    }
}
