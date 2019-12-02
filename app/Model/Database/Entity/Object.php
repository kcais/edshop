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
class Object
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
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="category")
     * @ORM\JoinColumn(nullable=FALSE)
     */
    private $category;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $is_available;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $is_visible;

    /**
     * @ORM\Column(type="DateTime")
     */
    private $created_on;

    /**
     * @ORM\Column(type="DateTime")
     */
    private $updated_on;

    /**
     * @ORM\Column(type="DateTime", nullable = true)
     */
    private $deleted_on;

    /**
     * Object constructor.
     * @param int $category_id
     * @param String $name
     * @param String $description
     * @param float $price
     */
    public function __construct(int $category_id, String $name, String $description, float $price)
    {
        $this->category = $category_id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
    }

    /**
     * @return int Id objektu
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id Id objektu
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string Jmeno objektu
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name Jmeno objektu
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string Popis objektu
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description Popis objektu
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Category Kategorie objektu
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category Kategorie objektu
     */
    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    /**
     * @return float Cena objektu
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price Cena objektu
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return bool 1-pokud je zbozi dostupne
     */
    public function isIsAvailable(): bool
    {
        return $this->is_available;
    }

    /**
     * @param bool $is_available 1-pokud je zbozi dostupne
     */
    public function setIsAvailable(bool $is_available): void
    {
        $this->is_available = $is_available;
    }

    /**
     * @return bool 1-pokud je viditelne
     */
    public function isIsVisible(): bool
    {
        return $this->is_visible;
    }

    /**
     * @param bool $is_visible 1-pokud je viditelne
     */
    public function setIsVisible(bool $is_visible): void
    {
        $this->is_visible = $is_visible;
    }

    /**
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->created_on;
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
        $this->updated_on = $updated_on->format('Y-m-d H:i:s');
    }

    /**
     * @return String|null
     */
    public function getDeletedOn() : ?String
    {
        return $this->deleted_on;
    }

    /**
     * @param mixed \DateTime
     */
    public function setDeletedOn(\DateTime $deletedOn): void
    {
        $this->deleted_on = $deletedOn->format('Y-m-d H:i:s');
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {

        $dateTime = new \DateTime("now");
        $this->created_on = $dateTime->format('Y-m-d H:i:s');
        $this->updated_on = $dateTime->format('Y-m-d H:i:s');

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
        $this->updated_on = $dateTime->format('Y-m-d H:i:s');
    }

}

?>