<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * Kayue\WordpressBundle\Entity\UserMeta
 *
 * @ORM\Table(name="wp_usermeta")
 * @ORM\Entity
 */
class UserMeta
{
    /**
     * @var bigint $id
     *
     * @ORM\Column(name="umeta_id", type="bigint", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $key
     *
     * @ORM\Column(name="meta_key", type="string", length=255, nullable=true)
     * @Constraints\NotBlank()
     */
    private $key;

    /**
     * @var text $value
     *
     * @ORM\Column(name="meta_value", type="wordpressmeta", nullable=true)
     */
    private $value;

    /**
     * @var Kayue\WordpressBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Kayue\WordpressBundle\Entity\User", inversedBy="metas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="ID")
     * })
     */
    private $user;

    /**
     * Get id
     *
     * @return bigint
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set key
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set value
     *
     * @param text $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     *
     * @return text
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set user
     *
     * @param Kayue\WordpressBundle\Entity\User $user
     */
    public function setUser(\Kayue\WordpressBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Kayue\WordpressBundle\Entity\User | null
     */
    public function getUser()
    {
        if ($this->user instanceof \Doctrine\ORM\Proxy\Proxy) {
            try {
                // prevent lazy loading the user entity becuase it might not exist
                $this->user->__load();
            } catch (\Doctrine\ORM\EntityNotFoundException $e) {
                // return null if user does not exist
                $this->user = null;
            }
        }

        return $this->user;
    }
}
