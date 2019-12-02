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
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="category")
     * @ORM\JoinColumn(nullable=FALSE)
     */
    private $parent_cat;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $created_on;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $updated_on;

    /**
     * @ORM\Column(type="string", nullable = true)
     * @var string
     */
    private $deleted_on;

    /**
     * Category constructor
     */
    public function __construct()
    {

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
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created_on = $this->getCurrentDate();

        if ($this->id !== NULL) {
            throw new LogicException("Entity id field should be null during prePersistEvent");
        }
    }

    /**
     * @ORM\PostPersist()
     * @param LifecycleEventArgs $args
     */
    public function onPostPersist(LifecycleEventArgs $args)
    {
        // $args->getEntity() and $this are pointers to the same objects
        if ($args->getEntity()->getId() === NULL) {
            throw new LogicException("Entity id field should be already filled during prePersistEvent");
        }
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate()
    {
        $this->updated_on = $this->getCurrentDate();
    }


}