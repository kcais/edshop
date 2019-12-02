<?php

declare(strict_types=1);

namespace App\Model\Database\Repository;

use Doctrine\ORM\EntityRepository;

final class CategoryRepository extends EntityRepository
{

    public function getById($id)
    {
        return $this->findOneBy([
            'id' => $id,
        ]);
    }
}

?>