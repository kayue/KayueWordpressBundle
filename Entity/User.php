<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Constraints;
use Kayue\WordpressBundle\Model\User as ModelUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Kayue\WordpressBundle\Entity\User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 * @UniqueEntity({"fields": "email", "message": "Sorry, that email address is already used.", "groups": {"register", "edit"}})
 * @UniqueEntity({"fields": "username", "message": "Sorry, that username is already used.", "groups": {"register", "edit"}})
 * @UniqueEntity({"fields": "nicename", "message": "Sorry, that nicename is already used.", "groups": {"unused"}})
 * @UniqueEntity({"fields": "displayName", "message": "Sorry, that display name has already been taken.", "groups": {"edit"}})
 * @ORM\HasLifecycleCallbacks
 */
class User extends ModelUser
{
    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="ID", type="integer", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_login", type="string", length=60, unique=true)
     * @Constraints\NotBlank(groups={"register", "edit"})
     */
    protected $username;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_pass", type="string", length=64)
     * @Constraints\NotBlank(groups={"register", "edit"})
     */
    protected $password;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_nicename", type="string", length=64)
     * @Constraints\NotBlank(groups={"unused"})
     */
    protected $nicename;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_email", type="string", length=100)
     * @Constraints\NotBlank(groups={"register", "edit"})
     * @Constraints\Email(groups={"register", "edit"})
     */
    protected $email;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_url", type="string", length=100)
     * @Constraints\Url()
     */
    protected $url = '';

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_registered", type="datetime")
     */
    protected $registeredDate;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_activation_key", type="string", length=60)
     */
    protected $activationKey = '';

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_status", type="integer", length=11)
     */
    protected $status = 0;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="display_name", type="string", length=250)
     * @Constraints\NotBlank(groups={"edit"})
     */
    protected $displayName;

    /**
     * {@inheritdoc}
     *
     * @ORM\OneToMany(targetEntity="Kayue\WordpressBundle\Entity\UserMeta", mappedBy="user", cascade={"persist"})
     */
    protected $metas;

    public function __construct()
    {
        $this->metas = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->registeredDate = new \DateTime('now');
    }
}
