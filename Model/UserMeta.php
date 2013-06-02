<?php

namespace Kayue\WordpressBundle\Model;

class UserMeta
{
    /**
     * @var int $id
     */
    protected $id;

    /**
     * @var string $key
     */
    protected $key;

    /**
     * @var string $value
     */
    protected $value;

    /**
     * @var UserInterface
     */
    protected $user;

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
     * Set user
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return UserInterface|null
     */
    public function getUser()
    {
        return $this->user;
    }
}