<?php

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

    public function __construct(Nette\Application\UI\Presenter &$presenter, \App\Model\ObjectManager $objectManager, \App\Model\OrderManager $orderManager)
    {
        $this->objectManager = $objectManager;
        $this->orderManager = $orderManager;
        $this->user = $presenter->user;
        $this->presenter = $presenter;
        $this->section = $presenter->getSession()->getSection(\App\Common\Common::getSelectionName());
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

                $selection = $this->objectManager->getObjectsFromIds($keyIds);

                $selectionArr = null;

                foreach ($selection as $row) {
                    $selectionArr[] = ['id' => $row->id, 'name' => "$row->name", 'description' => "$row->description", 'price' => "$row->price", 'pcs' => $basket[$row->id], 'totalPrice' => $basket[$row->id] * $row->price];
                }

                $selection = $selectionArr;

            } else {
                $selection = [];
            }

            return $selection;
        }
        else{ //uzivatel je prihlasen, pouzivaji se data z databaze
            $orderId = $this->orderManager->getOpenOrderId($this->user->getId());
            if($orderId == 0){
                return [];
            }
            elseif($orderId == -1){
                die('<h2>Pri zpracovani kosiku doslo k chybe !</h2>');
            }

            $orderList = $this->orderManager->getOrderObjectsList($orderId);

            $objectsArr = null;
            $keyIds = null;

            foreach($orderList as $orderObject){
                $objectsArr[$orderObject->object_id] = $orderObject->pcs;
                $keyIds[] = $orderObject->object_id;
            }

            if($orderList->count()!=0) { //objednavka nema zadne polozky
                $selection = $this->objectManager->getObjectsFromIds($keyIds);
                $selectionArr = null;

                foreach ($selection as $row) {
                    $selectionArr[] = ['id' => $row->id, 'name' => "$row->name", 'description' => "$row->description", 'price' => "$row->price", 'pcs' => $objectsArr[$row->id], 'totalPrice' => $objectsArr[$row->id] * $row->price];
                }

                $selection = $selectionArr;
            }
            else{
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

            foreach ($basket as $basketKey => $basketValue) {
                $keyIds[] = $basketKey;
            }

            $selection = $this->objectManager->getObjectsFromIds($keyIds);
            $prices = null;

            foreach ($selection as $row) {
                $prices[$row->id] = $row->price;
            }

            foreach ($basket as $basketKey => $basketValue) {
                $totalPrice += ($prices[$basketKey] * $basketValue);
            }
        }
        else{ //provadi se pokud je uzivatel prihlasen
            $orderId = $this->orderManager->getOpenOrderId($this->user->getId());
            $totalPrice = $this->orderManager->getOrderPrice($orderId);
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
            $this->orderManager->createUpdateOrderObject($this->user->getId(), $objectId, $pcs);
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