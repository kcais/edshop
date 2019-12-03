<?php

use App\Model\Database\EntityManagerDecorator;
use App\Model\ObjectManager;


/**
 * Class Basket Trida reprezentujici kosik
 */
class Basket{

    private $objectManager;
    private $orderManager;
    private $user;
    private $section;
    private $presenter;

    private $em;

    public function __construct(Nette\Application\UI\Presenter &$presenter, \App\Model\ObjectManager $objectManager, \App\Model\OrderManager $orderManager, EntityManagerDecorator &$em)
    {
        $this->objectManager = $objectManager;
        $this->orderManager = $orderManager;
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

                //$selection = $this->objectManager->getObjectsFromIds($keyIds);

                $selectionArr = null;

                foreach ($selObjArr as $selObj) {
                    $selectionArr[] = ['id' => $selObj->getId(), 'name' => $selObj->getName(), 'description' => $selObj->getDescription(), 'price' => $selObj->getPrice(), 'pcs' => $basket[$selObj->getId()], 'totalPrice' => $basket[$selObj->getId()] * $selObj->getPrice()];
                }

                //foreach ($selection as $row) {
                //    $selectionArr[] = ['id' => $row->id, 'name' => "$row->name", 'description' => "$row->description", 'price' => "$row->price", 'pcs' => $basket[$row->id], 'totalPrice' => $basket[$row->id] * $row->price];
                //}

                $selection = $selectionArr;

            } else {
                $selection = [];
            }

            return $selection;
        }
        else{ //uzivatel je prihlasen, pouzivaji se data z databaze

            $order = $this->em->getOrderOpen($this->user->getId());
            //$orderId = $this->orderManager->getOpenOrderId($this->user->getId());

            if(is_numeric($order) && $order == 0){
                return [];
            }
            elseif(is_numeric($order) && $order == -1){
                die('<h2>Pri zpracovani kosiku doslo k chybe !</h2>');
            }

            echo "order_id=".$order->getId();

            $orderListArr = $this->em->getOrderProductRepository()->findby(['ord' => $order]);

            //echo $orderListArr[0]->getProduct()->getId();
            //$orderList = $this->orderManager->getOrderObjectsList($orderId);


            //die();

            if(sizeof($orderListArr)!=0) { //objednavka ma polozky
                $objectsArr = null;
                $keyIds = null;

                foreach($orderListArr as $orderObject){
                    $objectsArr[$orderObject->getProduct()->getId()] = $orderObject->getPcs();
                    $keyIds[] = $orderObject->getProduct()->getId();
                }

                //$selection = $this->objectManager->getObjectsFromIds($keyIds);

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

            //$selection = $this->objectManager->getObjectsFromIds($keyIds);

            $selObjArr = $this->em->getProductRepository()->findBy(['id' => $keyIds]);

            $prices = null;

            foreach($selObjArr as $selObj){
                $prices[$selObj->getId()] = $selObj->getPrice();
            }

            //foreach ($selection as $row) {
            //    $prices[$row->id] = $row->price;
            //}

            foreach ($basket as $basketKey => $basketValue) {
                $totalPrice += ($prices[$basketKey] * $basketValue);
            }
        }
        else{ //provadi se pokud je uzivatel prihlasen
            $order = $this->em->getOrderOpen($this->user->getId());
            $totalPrice = $this->em->getOrderPrice($order);
            //$orderId = $this->orderManager->getOpenOrderId($this->user->getId());
            //$totalPrice = $this->orderManager->getOrderPrice($orderId);
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
            //$this->orderManager->createUpdateOrderObject($this->user->getId(), $objectId, $pcs);
            $this->em->createUpdateOrderProduct($this->user->getId(), $objectId, $pcs);
        }
    }

    /** Odebrani polozky z kosiku
     * @param int $id
     */
    public function removeFromBasket(int $objectId)
    {
        if(!$this->user->isLoggedIn()) { //uzivatel neni prihlasen, odebiram ze session
            $basket = unserialize($this->section->basket);
            unset($basket[$objectId]);
            $this->section->basket = serialize($basket);
        }
        else{ //uzivatel je prihlasen, odebiram z db
            $orderId = $this->orderManager->getOpenOrderId($this->user->getId());
            $this->orderManager->deleteObjectFromOrder($orderId,$objectId);
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
            $this->orderManager->setOrderClose($this->orderManager->getOpenOrderId($this->user->getId()));
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
            $this->orderManager->emptyOrder($this->orderManager->getOpenOrderId($this->user->getId()));
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