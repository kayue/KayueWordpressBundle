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
     * @return mixed
     */
    public function findFeaturedImageByPost(Post $post);
}
