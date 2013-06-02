<?php

namespace Kayue\WordpressBundle\Model;

class Attachment extends Post implements AttachmentInterface
{
    protected $post;
    protected $url;
    protected $metadata;
    protected $thumbnailUrl;
    protected $mimeType;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }

    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function setThumbnailUrl($thumbnail)
    {
        $this->thumbnailUrl = $thumbnail;
    }

    public function getThumbnailUrl($size = null)
    {
        return $this->thumbnailUrl;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
