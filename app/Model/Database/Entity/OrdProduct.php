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
class OrdProduct
{

    use Id;

    /**
     * @var Ord
     * @ORM\ManyToOne(targetEntity="Ord", inversedBy="ord")
     * @ORM\JoinColumn(nullable=FALSE)
    */
    private $ord;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="product")
     * @ORM\JoinColumn(nullable=FALSE)
     */
    private $product;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $pcs;

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
     * @return Ord
     */
    public function getOrd(): Ord
    {
        return $this->ord;
    }

    /**
     * @param Ord $order
     */
    public function setOrd(Ord $order): void
    {
        $this->order = $order;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return float
     */
    public function getPcs(): float
    {
        return $this->pcs;
    }

    /**
     * @param float $pcs
     */
    public function setPcs(float $pcs): void
    {
        $this->pcs = $pcs;
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