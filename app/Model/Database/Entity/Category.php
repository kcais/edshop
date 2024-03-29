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
class Category
{
    use Id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @var string
     */
    private $order_id;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent_cat_id")
     */
    protected $children;

    /**
     * @ORM\OneToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="parent_cat_id", referencedColumnName="id")
     */
    private $parent_cat;

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

    /**
     * Category constructor.
     * @param String $name
     * @param String|null $description
     */
    public function __construct(String $name = null, String $description = null, int $order = null, Category $parentCatObj = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->order_id = $order;
        if(isset($parentCatObj)) {
            $this->parent_cat = $parentCatObj;
        }
    }

    /**
     * @return Category|null
     */
    public function getParentCat() : ?Category
    {
        return $this->parent_cat;
    }

    /**
     * @param Category|null $parentCat
     */
    public function setParentCat(?Category $parentCat) : void
    {
        $this->parent_cat = $parentCat;
    }

    /**
     * @return int Id kategorie
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int $id Id kategorie
     */
    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    /**
     * @return String Vraci nazev kategorie
     */
    public function getName() : String
    {
        return $this->name;
    }

    /**
     *  Nastavuje jmeno kategorie
     */
    public function setName(String $name) : void
    {
        $this->name = $name;
    }

    /**
     * @return String Vraci popis
     */
    public function getDescription() : String
    {
        return $this->description;
    }

    /** Nastavuje popis kategorie
     * @param String $description Popis kategorie
     */
    public function setDescription(String $description) : void
    {
        $this->description = $description;
    }

    /**
     * @return int Vraci order_id
     */
    public function getOrderId(): int
    {
        return $this->order_id;
    }

    /** Nastavuje order_id
     * @param int $orderId
     */
    public function setOrderId(int $orderId) : void
    {
        $this->order_id = $orderId;
    }

    /**
     * @return String Created_on date
     */
    public function getCreatedOn() : String
    {
        return $this->created_on;
    }

    /**
     * @return String Updated_on date
     */
    public function getUpdatedOn() : String
    {
        return $this->updated_on;
    }

    /**
     * @return String|null deleted_on date
     */
    public function getDeletedOn() : ?String
    {
        return $this->deleted_on;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setDeletedOn(\DateTime $dateTime) : void
    {
        //$this->deleted_on = $dateTime->format('Y-m-d H:i:s');
        $this->deleted_on = $dateTime;
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
        //$this->created_on = $dateTime->format('Y-m-d H:i:s');
        //$this->updated_on = $dateTime->format('Y-m-d H:i:s');
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
        //$this->updated_on = $dateTime->format('Y-m-d H:i:s');
        $this->updated_on = $dateTime;
    }


}