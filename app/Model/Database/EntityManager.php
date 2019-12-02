<?php
declare(strict_types=1);

namespace App\Model\Database;

use App\Model\Database\Entity\Category;
use App\Model\Database\Entity\Product;
use App\Model\Database\Repository\CategoryRepository;
use Nettrine\ORM\EntityManagerDecorator as NettrineEntityManagerDecorator;


final class EntityManagerDecorator extends NettrineEntityManagerDecorator
{
    public function getCategoryRepository()
    {
        return $this->getRepository(Category::class);
    }

    public function getProductRepository()
    {
        return $this->getRepository(Product::class);
    }
}

?>