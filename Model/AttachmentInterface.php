<?php

namespace Kayue\WordpressBundle\Model;

interface AttachmentInterface
{
    /**
     * Retrieve permalink for attachment.
     *
     * @return string
     */
    public function getUrl();

    /**
     * @return array|bool Attachment meta field. False on failure.
     */
    public function getMetadata();

    /**
     * Retrieve URL for an attachment thumbnail.
     *
     * @param array $size
     * @return string
     */
    public function getThumbnailUrl($size = null);

    /**
     * @return string
     */
    public function getMimeType();
}
