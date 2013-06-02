<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kayue\WordpressBundle\Model\Term as ModelTerm;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @ORM\Table(name="terms")
 * @ORM\Entity
 */
class Term extends ModelTerm
{
    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="term_id", type="wordpressid", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="name", type="string", length=200)
     * @Constraints\NotBlank()
     */
    protected $name;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="slug", type="string", length=200)
     */
    protected $slug;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="term_group", type="bigint", length=10)
     */
    protected $group = 0;

    /**
     * {@inheritdoc}
     *
     * @ORM\OneToOne(targetEntity="Taxonomy", mappedBy="term")
     */
    protected $taxonomy;
}
