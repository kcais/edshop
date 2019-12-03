<?php
declare(strict_types=1);

namespace App\Model\Database;

use App\Model\Database\Entity\Category;
use App\Model\Database\Entity\Ord;
use App\Model\Database\Entity\OrdProduct;
use App\Model\Database\Entity\Product;
use App\Model\Database\Entity\User;
use App\Model\Database\Repository\CategoryRepository;
use Nettrine\ORM\EntityManagerDecorator as NettrineEntityManagerDecorator;


final class EntityManagerDecorator extends NettrineEntityManagerDecorator
{
    /////////////////////////////
    /// Category part
    /////////////////////////////

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getCategoryRepository()
    {
        return $this->getRepository(Category::class);
    }

    ////////////////////////////
    ///  Product part
    ////////////////////////////

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getProductRepository()
    {
        return $this->getRepository(Product::class);
    }

    ////////////////////////////
    /// User part
    ////////////////////////////

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getUserRepository()
    {
        return $this->getRepository(User::class);
    }

    ///////////////////////////
    /// OrderProduct part
    ///////////////////////////

    public function createNewOrder($user, bool $isClosed=false)
    {
        $orderNew = new Ord();
        $orderNew->setUser($user);
        $orderNew->setIsClosed($isClosed);

        $this->persist($orderNew);
        $this->flush();
    }

    public function getOrderProductRepository()
    {
        return $this->getRepository(OrdProduct::class);
    }

    public function createUpdateOrderProduct(int $userId, int $objectId, float $pcs=1.0)
    {
        $openOrder = $this->getOrderOpen($userId);

        //chyba pri zjisteni id otevrene objednavky uzivatele
        if($openOrder == -1)return -1;

        //uzivatel nema zadnou otevrenou objednavku, vytvorit
        if($openOrder == 0){
            $openOrderId = $this->createNewOrder($userId, false);
            $createOrderObject = $this->insertUpdateObjectInOrder($openOrderId, $objectId, $pcs);
            return 1;
        }
        else{ //uzivatel jiz ma otevrenou objednavku, editovat
            $this->insertUpdateObjectInOrder($openOrderId, $objectId, $pcs);
            return 0;
        }

    }

    ///////////////////////////
    /// Order part
    ///////////////////////////

    /** Vrati celkovou hodnotu objednavky
     * @param $order Ord
     * @return float Celkova hodnota objednavky
     */
    public function getOrderPrice($order)
    {
        $orderProductObjArr = $this->getRepository(OrdProduct::class)->findBy(['ord' => $order, 'deleted_on' => null]);

        $totalPrice = 0.0;
        $orderList = [];
        $priceList = null;

        foreach($orderProductObjArr as $orderProductObj){
            $productId = $orderProductObj->getId();
            $orderList[$productId] = $orderProductObj->getPcs();
            $priceList[$productId] = $this->getRepository(Product::class)->find($productId)->getPrice();
        }

        foreach($orderList as $keyId => $pcs){
            $totalPrice += $pcs * $priceList[$keyId];
        }

        return $totalPrice;
    }

    /** Zjisteni otevrene objednavky u uzivatele
     * @param int $userId
     * @return int | object Vraci -1 - pokud vice nez jedna otevrena(aktualne chyba), 0-pokud zadna otevrena, Order object - otevrenou objednavku
     */
    public function getOrderOpen(int $userId)
    {
        $user = $this->getRepository(User::class)->find($userId);

        $orderObjArr = $this->getRepository(Ord::class)->findBy(['user' => $user, 'is_closed' => false]);

        if(sizeof($orderObjArr)==0){
            return 0;
        }
        elseif(sizeof($orderObjArr)>1){
            return -1;
        }
        else{
            return $orderObjArr[0];
        }
    }
}

?>