<?php

use App\Model\Database\EntityManagerDecorator;
use App\Model\ObjectManager;


/**
 * Class Basket Trida reprezentujici kosik
 */
class Basket{

    private $user;
    private $section;
    private $presenter;

    private $em;

    public function __construct(Nette\Application\UI\Presenter &$presenter, EntityManagerDecorator &$em)
    {
        $this->user = $presenter->user;
        $this->presenter = $presenter;
        $this->section = $presenter->getSession()->getSection(\App\Common\Common::getSelectionName());

        $this->em = $em;
    }

    /** Vrati obsah kosiku
     * @return array
     */
    public function getBasketObjectsList()
    {
        //uzivatel neni prihlasen, pouzivaji se data ze session
        if(!$this->user->isLoggedIn()) {
            $keyIds = null;
            $objectCount = null;

            $basket = unserialize($this->section->basket);

            if (isset($this->section->basket) && $this->section->basket && is_array($basket) && !empty($basket)) {
                foreach ($basket as $basketKey => $basketValue) {
                    $keyIds[] = $basketKey;
                }

                $selObjArr = $this->em->getProductRepository()->findBy(['id' => $keyIds]);

                $selectionArr = null;

                foreach ($selObjArr as $selObj) {
                    $selectionArr[] = ['id' => $selObj->getId(), 'name' => $selObj->getName(), 'description' => $selObj->getDescription(), 'price' => $selObj->getPrice(), 'pcs' => $basket[$selObj->getId()], 'totalPrice' => $basket[$selObj->getId()] * $selObj->getPrice()];
                }

                $selection = $selectionArr;

            } else {
                $selection = [];
            }

            return $selection;
        }
        else{ //uzivatel je prihlasen, pouzivaji se data z databaze

            $order = $this->em->getOrderOpen($this->user->getId());

            if(is_numeric($order) && $order == 0){
                return [];
            }
            elseif(is_numeric($order) && $order == -1){
                die('<h2>Pri zpracovani kosiku doslo k chybe !</h2>');
            }

            $orderListArr = $this->em->getOrderProductRepository()->findby(['ord' => $order,'deleted_on' => null]);

            if(sizeof($orderListArr)!=0) { //objednavka ma polozky
                $objectsArr = null;
                $keyIds = null;

                foreach($orderListArr as $orderObject){
                    $objectsArr[$orderObject->getProduct()->getId()] = $orderObject->getPcs();
                    $keyIds[] = $orderObject->getProduct()->getId();
                }

                $ordObjArr = $this->em->getProductRepository()->findBy(['id' => $keyIds]);

                $selectionArr = null;

                foreach ($ordObjArr as $ordObj) {
                    $selectionArr[] = ['id' => $ordObj->getId(), 'name' => $ordObj->getName(), 'description' => $ordObj->getDescription(), 'price' => $ordObj->getPrice(), 'pcs' => $objectsArr[$ordObj->getId()], 'totalPrice' => $objectsArr[$ordObj->getId()] * $ordObj->getPrice()];
                }

                $selection = $selectionArr;
            }
            else{ //objednavka nema zadne polozky
                $selection = [];
            }

            return $selection;
        }
    }

    /** Vypocet aktualni hodnoty zbozi v kosiku - pro neprihlaseneho uzivatele, nacita kosik ze session - pro prihlaseneho z databaze
     * @return float
     */
    public function calculateBasketPrice() : float
    {
        $totalPrice=0.0;

        if(!$this->user->isLoggedIn()) { //provadi se pokud neni uzivatel prihlasen - session
            if (!isset($this->section->basket)) {
                $this->section->basketPrice = 0.0;
                return 0.0;
            }

            if (!unserialize($this->section->basket)) {
                $this->section->basketPrice = 0.0;
                return 0.0;
            }

            $basket = unserialize($this->section->basket);

            $keyIds = null;

            foreach ($basket as $basketKey => $basketValue) {
                $keyIds[] = $basketKey;
            }

            $selObjArr = $this->em->getProductRepository()->findBy(['id' => $keyIds]);

            $prices = null;

            foreach($selObjArr as $selObj){
                $prices[$selObj->getId()] = $selObj->getPrice();
            }

            foreach ($basket as $basketKey => $basketValue) {
                $totalPrice += ($prices[$basketKey] * $basketValue);
            }
        }
        else{ //provadi se pokud je uzivatel prihlasen
            $order = $this->em->getOrderOpen($this->user->getId());
            $totalPrice = $this->em->getOrderPrice($order);
        }

        $this->section->basketPrice = $totalPrice;
        $this->presenter->template->basketPrice = $totalPrice;

        return $totalPrice;
    }

    /** Pridani polozky do kosiku
     * @param int $id Id polozky
     * @param float $pcs
     */
    public function addToBasket(int $objectId, float $pcs=1.0)
    {
        //uzivatel neni prihlasen, pouziva se session
         if(!$this->user->isLoggedIn()) {
             if (isset($this->section->basket)) {
                $basket = unserialize($this->section->basket);
                 if (isset($basket[$objectId])) {
                    $basket[$objectId] = $basket[$objectId] + 1;
                } else {
                    $basket[$objectId] = 1;
                }

                $this->section->basket = serialize($basket);
            } else {
                $basket = ["$objectId" => 1];
            }

            $this->presenter->template->basket = $basket;
            $this->section->basket = serialize($basket);
        }
        //uzivatel je prihlasen, pouziva se databaze
        else{
            $this->em->createUpdateOrderProduct($this->user->getId(), $objectId, $pcs);
        }
    }

    /** Odebrani polozky z kosiku
     * @param int $id
     */
    public function removeFromBasket(int $productId)
    {
        if(!$this->user->isLoggedIn()) { //uzivatel neni prihlasen, odebiram ze session
            $basket = unserialize($this->section->basket);
            unset($basket[$productId]);
            $this->section->basket = serialize($basket);
        }
        else{ //uzivatel je prihlasen, odebiram z db
            $orderObj = $this->em->getOrderOpen($this->user->getId());
            if(!is_numeric($orderObj)) {
                $productObj = $this->em->getProductRepository()->find($productId);
                $this->em->deleteProductFromOrder($orderObj, $productObj);
            }
        }
    }

    /**
     * Dokonceni objednavky
     * - v pripade prihlaseneho uzivatele oznaci objednavku jako closed
     * - dale vyprazdni kosik
     */
    public function orderDone()
    {
        if($this->user->isLoggedIn()){
            $openOrdObj = $this->em->getOrderOpen($this->user->getId());
            if(!is_numeric($openOrdObj)){
                $openOrdObj->setIsClosed(true);
                $this->em->merge($openOrdObj);
                $this->em->flush();
            }

        }
        $this->empty();
    }

    /**
     * Vyprazdneni kosiku
     */
    public function empty()
    {
        if(!$this->user->isLoggedIn()) { //uzivatel neni prihlasen, pouzit session
            unset($this->presenter->template->basket);
            unset($this->section->basket);
        }
        else{ //uzivatel je prihlasen, pouzit databazi
            $openOrderObj = $this->em->getOrderOpen($this->user->getId());
            if(!is_numeric($openOrderObj)) {
                $this->em->emptyOrderProduct($openOrderObj);
            }
        }

        unset($this->presenter->template->basketPrice);
        unset($this->section->basketPrice);
    }

    /**
     *  Prevedeni ulozeni kosiku ze session do databaze
     */
    public function fromSessionToDb()
    {
        if($this->presenter->user->isLoggedIn()){
            if(isset($this->section->basket)){
                $basket = unserialize($this->section->basket);
                foreach ($basket as $key => $value){
                    $this->addToBasket($key, $value);
                }
                unset($this->section->basket);
            }
        }
    }
}

?>