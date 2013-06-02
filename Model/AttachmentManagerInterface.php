<?php

namespace Kayue\WordpressBundle\Model;

interface AttachmentManagerInterface
{
    /**
     * @param Post $post
     * @return AttachmentInterface[]
     */
    public function findAttachmentsByPost(Post $post);

    /**
     * @param $id integer
     * @return AttachmentInterface[]
     */
    public function findOneAttachmentById($id);

    /**
     * @param Post $post
     * @param array $size A 2-item array representing width and height in pixels, e.g. array(32,32).
     * @return mixed
     */
    public function findFeaturedImageByPost(Post $post, $size = null);
}
