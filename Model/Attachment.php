<?php

namespace Kayue\WordpressBundle\Model;

class Attachment extends Post implements AttachmentInterface
{
    protected $post;
    protected $metadata;
    protected $url;
    protected $thumbnailUrl;

    public function __construct(Post $post)
    {
        $this->metadata = $post->getMetas()
            ->filter(function (PostMeta $meta) {
                return '_wp_attachment_metadata' == $meta->getKey();
            })
            ->first()
        ;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }

    public function getThumbnailUrl($size = 'post-thumbnail')
    {
        $rawMetadata = $this->metadata->getValue();

        if (isset($rawMetadata['sizes'][$size])) {
            return dirname($rawMetadata['file']) . '/' . $rawMetadata['sizes'][$size]['file'];
        }

        return null;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        if (!$this->url) {
            $rawMeta = $this->metadata->getValue();
            $this->url = $rawMeta['file'];
        }

        return $this->url;
    }
}
