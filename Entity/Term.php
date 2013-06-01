<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kayue\WordpressBundle\Model\TermInterface;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @ORM\Table(name="terms")
 * @ORM\Entity
 */
class Term extends TermInterface
{
    /**
     * @var int $id
     *
     * @ORM\Column(name="term_id", type="wordpressid", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=200)
     * @Constraints\NotBlank()
     */
    protected $name;

    /**
     * @var string $slug
     *
     * @ORM\Column(name="slug", type="string", length=200)
     */
    protected $slug;

    /**
     * @var int $group
     *
     * @ORM\Column(name="term_group", type="bigint", length=10)
     */
    protected $group = 0;

    /**
     * @var Taxonomy
     *
     * @ORM\OneToOne(targetEntity="Taxonomy", mappedBy="term")
     */
    protected $taxonomy;
}
