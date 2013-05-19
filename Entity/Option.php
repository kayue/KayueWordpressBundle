<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * Kayue\WordpressBundle\Entity\Option
 *
 * @ORM\Table(name="options")
 * @ORM\Entity
 */
class Option
{
    /**
     * @var int $id
     *
     * @ORM\Column(name="option_id", type="bigint", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="option_name", type="string", length=64, nullable=false, unique=true)
     * @Constraints\NotBlank()
     */
    private $name;

    /**
     * @var string $value
     *
     * @ORM\Column(name="option_value", type="wordpressmeta", nullable=false)
     */
    private $value;

    /**
     * @var string $autoload
     *
     * @ORM\Column(name="autoload", type="string", length=20, nullable=false)
     * @Constraints\NotBlank()
     */
    private $autoload = 'yes';

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set autoload
     *
     * @param string $autoload Yes or no
     */
    public function setAutoload($autoload)
    {
        $this->autoload = $autoload;
    }

    /**
     * Get autoload
     *
     * @return string
     */
    public function getAutoload()
    {
        return $this->autoload;
    }
}
