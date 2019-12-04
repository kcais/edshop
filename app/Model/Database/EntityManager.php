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

    /** Odebrani produktu z objednavky
     * @param Ord $orderObj
     * @param Product $productObj
     */
    public function deleteProductFromOrder(Ord $orderObj, Product $productObj)
    {
        $orderProductObj = $this->getOrderProductRepository()->findBy(['ord' => $orderObj, 'product' => $productObj]);

        if(sizeof($orderProductObj) == 1)
        {
            $this->remove($orderProductObj[0]);
            $this->flush();
        }

    }

    /** Vyprazdneni cele objednavky
     * @param Ord $orderObj
     * @throws \Exception
     */
    public function emptyOrderProduct(Ord $orderObj)
    {
        $dateTime = new \DateTime();
        $q = $this->createQuery("delete from App\Model\Database\Entity\OrdProduct op where op.ord=".$orderObj->getId());
        $q->execute();
    }

    /** Vytvoreni nove objednavky
     * @param User $user
     * @param bool $isClosed
     * @return Ord
     */
    public function createNewOrder(User $user, bool $isClosed=false)
    {
        $orderNew = new Ord();
        $orderNew->setUser($user);
        $orderNew->setIsClosed($isClosed);

        $this->persist($orderNew);
        $this->flush();
        return $orderNew;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getOrderProductRepository()
    {
        return $this->getRepository(OrdProduct::class);
    }

    /** Vlozeni nebo update poctu kusu produktu
     * @param Ord $order
     * @param $product
     * @param float $pcs
     * @return int
     */
    private function insertUpdateProductInOrder(Ord $order, $product, $pcs=1.0)
    {
        $ordProductObjArr = $this->getOrderProductRepository()->findBy(['ord' => $order, 'product' => $product]);

        if(sizeof($ordProductObjArr)==0) { //vkladam novy zaznam
            $newOrdProdObj = new OrdProduct($order, $product, $pcs=1.0);
            $this->persist($newOrdProdObj);
            $this->flush();
            return 1;
        }
        else{ //updatuju stavajici zaznam
            $ordProductObjArr[0]->setPcs($ordProductObjArr[0]->getPcs() + $pcs);
            $this->merge($ordProductObjArr[0]);
            $this->flush();

            return 0;
        }
    }

    /** Vytvoreni objednavky pokud existuje + vlozeni nebo update poctu kusu produktu
     * @param int $userId
     * @param int $productId
     * @param float $pcs
     * @return int
     */
    public function createUpdateOrderProduct(int $userId, int $productId, float $pcs=1.0)
    {
        $openOrderObj = $this->getOrderOpen($userId);
        $productObj = $this->getProductRepository()->find($productId);

        //chyba pri zjisteni id otevrene objednavky uzivatele
        if(is_numeric($openOrderObj) && $openOrderObj == -1)return -1;

        //uzivatel nema zadnou otevrenou objednavku, vytvorit
        if(is_numeric($openOrderObj) && $openOrderObj == 0){
            $userObj = $this->getUserRepository()->find($userId);
            $openOrder = $this->createNewOrder($userObj, false);
            $this->insertUpdateProductInOrder($openOrder, $productObj, $pcs);
            return 1;
        }
        else{ //uzivatel jiz ma otevrenou objednavku, editovat
            $this->insertUpdateProductInOrder($openOrderObj, $productObj, $pcs);
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
            $totalPrice += $orderProductObj->getProduct()->getPrice() * $orderProductObj->getPcs();
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