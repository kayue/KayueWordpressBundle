<?php

namespace Kayue\WordpressBundle\Model;

class TermManager implements TermManagerInterface
{
    public function findTermsByPost(Post $post, Taxonomy $taxonomy = null)
    {
        $result = array();
        $taxonmies = $post->getTaxonomies();

        if($taxonomy === null) {
            foreach($taxonmies as $tax) {
                /** @var $tax Taxonomy */
                $result[] = $tax->getTerm();
            }
        } else {
            foreach($taxonmies->filter(function(Taxonomy $tax) use ($taxonomy) {
                return $tax->getName() === $taxonomy->getName();
            }) as $tax) {
                /** @var $tax Taxonomy */
                $result[] = $tax->getTerm();
            }
        }

        return $result;
    }
}
