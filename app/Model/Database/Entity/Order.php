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
class Order
{
    use Id;


}
