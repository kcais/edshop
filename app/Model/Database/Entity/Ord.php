<?php

declare(strict_types=1);

namespace App\Model\Database\Entity;


use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use LogicException;


/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 **/
class Ord
{
    use Id;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=FALSE)
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $is_closed;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_on;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_on;

    /**
     * @ORM\Column(type="datetime", nullable = true)
     */
    private $deleted_on;

    ///////////////////////////////////
    /// getters and setters
    ///////////////////////////////////

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function isClosed(): int
    {
        return $this->is_closed;
    }

    /**
     * @param int $is_closed
     */
    public function setIsClosed(int $is_closed): void
    {
        $this->is_closed = $is_closed;
    }

    /**
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->created_on;
    }

    /**
     * @param \DateTime $created_on
     */
    public function setCreatedOn(\DateTime $created_on): void
    {
        $this->created_on = $created_on;
    }

    /**
     * @return mixed
     */
    public function getUpdatedOn()
    {
        return $this->updated_on;
    }

    /**
     * @param \DateTime $updated_on
     */
    public function setUpdatedOn(\DateTime $updated_on): void
    {
        $this->updated_on = $updated_on;
    }

    /**
     * @return mixed
     */
    public function getDeletedOn()
    {
        return $this->deleted_on;
    }

    /**
     * @param \DateTime $deleted_on
     */
    public function setDeletedOn(\DateTime $deleted_on): void
    {
        $this->deleted_on = $deleted_on;
    }

    ///////////////////////////////////
    /// functions
    ///////////////////////////////////

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {

        $dateTime = new \DateTime("now");
        $this->created_on = $dateTime;
        $this->updated_on = $dateTime;

        if ($this->id !== NULL) {
            throw new LogicException("Entity id field should be null during prePersistEvent");
        }
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate()
    {
        $dateTime = new \DateTime("now");
        $this->updated_on = $dateTime;
    }

}
