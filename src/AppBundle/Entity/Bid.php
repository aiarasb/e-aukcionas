<?php

namespace AppBundle\Entity;

/**
 * Bid
 */
class Bid
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $sum;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Item
     */
    private $item;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sum
     *
     * @param string $sum
     *
     * @return Bid
     */
    public function setSum($sum)
    {
        $this->sum = $sum;

        return $this;
    }

    /**
     * Get sum
     *
     * @return string
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Bid
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Bid
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set item
     *
     * @param Item $item
     *
     * @return Bid
     */
    public function setItem(Item $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }
}
