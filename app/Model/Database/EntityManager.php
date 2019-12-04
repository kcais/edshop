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

    /**Kontrola existence UUID v tabulce users v db
     * @param String $uuid Hledane uuid
     * @param string $cell Kde hledat uuid
     * @return int Vraci 0 pokud nenaslo uuid, jinak id uzivatele
     */
    public function existUserUUID(String $uuid, $cell='uuid_registration') : int
    {
        $userObjArr = $this->getUserRepository()->findBy([$cell => $uuid]);
        if(sizeof($userObjArr)>0){
            return $userObjArr[0]->getId();
        }
        else{
            return 0;
        }
    }

    /** Oznaci uzivatele jako aktivniho na zaklade UUID
     * @param $uuid
     * @return int Vraci pocet zmenenych radku
     */
    public function activateUser($uuid)
    {
        $q = $this->createQuery("update App\Model\Database\Entity\User user set user.is_active = 1 where user.uuid_registration='$uuid'");
        return $q->execute();
    }


    /** Vytvoreni noveho uzivatelskeho uctu
     * @param String $username Uzivatelske jmeno zakaznika
     * @param String $firstname Jmeno zakaznika
     * @param String $surname Prijmeni zakaznika
     * @param String $password Heslo v ciste forme
     * @param String $email Email zakaznika
     * @param String $uuid_registration
     * @return int Pocet vlozenych radku
     */
    public function createUser(String $username,String $firstname,String $surname,String $password, String $email, $uuid_registration=null)
    {
        $query = $this->createQuery("select user from App\Model\Database\Entity\User user where user.username = '$username' or user.email = '$email'");
        $userObjArr = $query->getResult();

        if(sizeof($userObjArr) == 0){
            $newUserObj = new User();
            $newUserObj->setUsername($username);
            $newUserObj->setFirstname($firstname);
            $newUserObj->setSurname($surname);
            $newUserObj->setPasswordHash(hash('sha256', $password));
            $newUserObj->setEmail($email);
            $newUserObj->setUuidRegistration($uuid_registration);
            $newUserObj->setLanguage('CZ');
            $newUserObj->setIsActive(false);
            $newUserObj->setIsAdmin(false);
            $newUserObj->setRegistrationMailSended(false);

            $this->persist($newUserObj);
            $this->flush();

            return 0;
        }
        else{
            return 1; //zadane uzivatelske jmeno jiz existuje
        }
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