<?php

namespace Kayue\WordpressBundle\Repository;

use Kayue\WordpressBundle\Entity\Post;
use Kayue\WordpressBundle\Entity\Taxonomy;

class TermRepository extends AbstractRepository
{
    public function findByPost(Post $post, $taxonomy = null)
    {
        $result = array();
        $taxonmies = $post->getTaxonomies();

        if($taxonomy === null) {
            foreach($taxonmies as $tax) {
                /** @var $tax Taxonomy */
                $result[] = $tax->getTerm();
            }
        } else {
            if (is_string($taxonomy)) {
                $taxonomy = $this->getEntityManager()->getRepository('KayueWordpressBundle:Taxonomy')->findOneBy(['name' => $taxonomy]);
            }

            foreach($taxonmies->filter(function(Taxonomy $tax) use ($taxonomy) {
                return $tax->getName() === $taxonomy->getName();
            }) as $tax) {
                /** @var $tax Taxonomy */
                $result[] = $tax->getTerm();
            }
        }

        return $result;
    }

    public function getAlias()
    {
        return 't';
    }
}
