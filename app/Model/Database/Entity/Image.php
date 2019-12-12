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
class Image
{
    use Id;

    /**
     * @var Product
     * @ORM\OneToOne(targetEntity="Product")
     * @ORM\JoinColumn(nullable=FALSE)
     */
    private $product;

    /**
     * @ORM\Column(type="blob")
     */
    private $image_icon;

    /**
     * @ORM\Column(type="blob")
     */
    private $image_mini;

    /**
     * @ORM\Column(type="blob")
     */
    private $image_normal;

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
     * @return mixed
     */
    public function getImageIcon()
    {
        return $this->image_icon;
    }

    /**
     * @param mixed $imageIcon
     */
    public function setImageIcon($imageIcon): void
    {
        $this->image_icon = $imageIcon;
    }

    /**
     * @return mixed
     */
    public function getImageMini()
    {
        return $this->image_mini;
    }

    /**
     * @param mixed $imageMini
     */
    public function setImageMini($imageMini): void
    {
        $this->image_mini = $imageMini;
    }

    /**
     * @return mixed
     */
    public function getImageNormal()
    {
        return $this->image_normal;
    }

    /**
     * @param mixed $imageNormal
     */
    public function setImageNormal($imageNormal): void
    {
        $this->image_normal = $imageNormal;
    }

    /**
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->created_on;
    }

    /**
     * @param mixed $created_on
     */
    public function setCreatedOn($created_on): void
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
     * @param mixed $updated_on
     */
    public function setUpdatedOn($updated_on): void
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
     * @param mixed $deleted_on
     */
    public function setDeletedOn($deleted_on): void
    {
        $this->deleted_on = $deleted_on;
    }

    ///////////////////////////////////
    /// functions
    ///////////////////////////////////

    /** Vytvoreni souboru obrazku
     * @param int $id
     */
    public function createImageFile()
    {
        if($this->image_icon) {
            $fp = fopen("./img/".$this->product->getId().".jpg", 'wb');
            if(is_resource($this->image_icon)){
                $imageData = stream_get_contents($this->image_icon);
            }
            else{
                $imageData = $this->image_icon;
            }
            fwrite($fp, $imageData);
            fclose($fp);
        }
    }

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
     * @ORM\PostPersist
     */
    public function onPostPersist()
    {
        //vytvoreni souboru s daty ikony obrazku do ./img/$id.jpg
        $this->createImageFile();
    }

    /**
     * @ORM\PostUpdate
     */
    public function onPostUpdate()
    {
        //vytvoreni souboru s daty ikony obrazku do ./img/$id.jpg
        $this->createImageFile();
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
