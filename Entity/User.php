<?php

namespace Kayue\WordpressBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Constraints;
use Kayue\WordpressBundle\Model\UserInterface;
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
class User implements UserInterface, \Serializable
{
    /**
     * @var int $id
     *
     * @ORM\Column(name="ID", type="integer", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $username
     *
     * @ORM\Column(name="user_login", type="string", length=60, unique=true)
     * @Constraints\NotBlank(groups={"register", "edit"})
     */
    private $username;

    /**
     * @var string $password
     *
     * @ORM\Column(name="user_pass", type="string", length=64)
     * @Constraints\NotBlank(groups={"register", "edit"})
     */
    private $password;

    /**
     * @var string $nicename
     *
     * @ORM\Column(name="user_nicename", type="string", length=64)
     * @Constraints\NotBlank(groups={"unused"})
     */
    private $nicename;

    /**
     * @var string $email
     *
     * @ORM\Column(name="user_email", type="string", length=100)
     * @Constraints\NotBlank(groups={"register", "edit"})
     * @Constraints\Email(groups={"register", "edit"})
     */
    private $email;

    /**
     * @var string $url
     *
     * @ORM\Column(name="user_url", type="string", length=100)
     * @Constraints\Url()
     */
    private $url = '';

    /**
     * @var \DateTime $registeredDate
     *
     * @ORM\Column(name="user_registered", type="datetime")
     */
    private $registeredDate;

    /**
     * @var string $activationKey
     *
     * @ORM\Column(name="user_activation_key", type="string", length=60)
     */
    private $activationKey = '';

    /**
     * @var integer $status
     *
     * @ORM\Column(name="user_status", type="integer", length=11)
     */
    private $status = 0;

    /**
     * @var string $displayName
     *
     * @ORM\Column(name="display_name", type="string", length=250)
     * @Constraints\NotBlank(groups={"edit"})
     */
    private $displayName;

    /**
     * @var UserMeta
     *
     * @ORM\OneToMany(targetEntity="Kayue\WordpressBundle\Entity\UserMeta", mappedBy="user", cascade={"persist"})
     */
    private $metas;

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

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set nicename
     *
     * @param string $nicename
     */
    public function setNicename($nicename)
    {
        $this->nicename = $nicename;
    }

    /**
     * Get nicename
     *
     * @return string
     */
    public function getNicename()
    {
        return $this->nicename;
    }

    /**
     * Set email
     *
     * @param $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set registeredDate
     *
     * @param $registeredDate
     */
    public function setRegisteredDate($registeredDate)
    {
        $this->registeredDate = $registeredDate;
    }

    /**
     * Get registeredDate
     *
     * @return \DateTime
     */
    public function getRegisteredDate()
    {
        return $this->registeredDate;
    }

    /**
     * Set activationKey
     *
     * @param string $activationKey
     */
    public function setActivationKey($activationKey)
    {
        $this->activationKey = $activationKey;
    }

    /**
     * Get activationKey
     *
     * @return string
     */
    public function getActivationKey()
    {
        return $this->activationKey;
    }

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Add meta
     *
     * @param UserMeta $meta
     */
    public function addMeta(UserMeta $meta)
    {
        $this->metas[] = $meta;

        $meta->setUser($this);
    }

    /**
     * Get metas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMetas()
    {
        return $this->metas;
    }

    /**
     * Get metas by meta key
     *
     * @param $key
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMetasByKey($key)
    {
        return $this->getMetas()->filter(function(UserMeta $meta) use ($key) {
            return $meta->getKey() === $key;
        });
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return \Symfony\Component\Security\Core\Role\Role[] The user roles
     */
    public function getRoles()
    {
        $roles = array();
        $metas = $this->getMetasByKey('wp_capabilities');

        if ($metas->isEmpty()) {
            return array();
        }

        $capabilities = $metas->first()->getValue();

        if (!is_array($capabilities)) {
            return array();
        }

        foreach ($capabilities as $role => $value) {
            $roles[] = 'ROLE_WP_'.strtoupper($role);
        }

        return $roles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string The salt
     */
    public function getSalt()
    {

    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     *
     * @return void
     */
    public function eraseCredentials()
    {

    }

    /**
     * Returns whether or not the given user is equivalent to *this* user.
     *
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * @param UserInterface $user
     *
     * @return Boolean
     */
    public function equals(UserInterface $user)
    {
        return $this->getUsername() === $user->getUsername();
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
        ));
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));

        list(
            $this->id,
            $this->username,
        ) = $data;
    }

}
