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
            $orderId = $this->objectManager->getOpenOrderId($this->user->getId());
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
                $objectsArr[$orderObject->id] = $orderObject->pcs;
                $keyIds[] = $orderObject->id;
            }

            $selection = $this->objectManager->getObjectsFromIds($keyIds);

            $selectionArr = null;

            foreach ($selection as $row) {
                $selectionArr[] = ['id' => $row->id, 'name' => "$row->name", 'description' => "$row->description", 'price' => "$row->price", 'pcs' => $objectsArr[$row->id], 'totalPrice' => $objectsArr[$row->id] * $row->price];
            }

            $selection = $selectionArr;

            return $selection;

        }
    }

    /** Vypocet aktualni hodnoty zbozi v kosiku - pro neprihlaseneho uzivatele, nacita kosik ze session
     * @return float
     */
    public function calculateBasketPrice() : float
    {
        if(!isset($this->section->basket)){
            $this->section->basketPrice = 0.0;
            return 0.0;
        }

        if(!unserialize($this->section->basket)){
            $this->section->basketPrice = 0.0;
            return 0.0;
        }

        $basket = unserialize($this->section->basket);

        foreach ($basket as $basketKey => $basketValue) {
            $keyIds[] = $basketKey;
        }

        $selection = $this->objectManager->getObjectsFromIds($keyIds);
        $prices = null;

        foreach ($selection as $row){
            $prices[$row->id]= $row->price;
        }

        $totalPrice = 0.0;

        foreach ($basket as $basketKey => $basketValue) {
            $totalPrice += ($prices[$basketKey] * $basketValue);
        }

        $this->section->basketPrice = $totalPrice;
        $this->presenter->template->basketPrice = $totalPrice;

        return $totalPrice;
    }

    /** Pridani polozky do kosiku
     * @param int $id
     * @param float $pcs
     */
    public function AddToBasket(int $id, float $pcs=1.0)
    {
        //uzivatel neni prihlasen, pouziva se session
         if(!$this->user->isLoggedIn()) {
             if (isset($this->section->basket)) {
                $basket = unserialize($this->section->basket);
                 if (isset($basket[$id])) {
                    $basket[$id] = $basket[$id] + 1;
                } else {
                    $basket[$id] = 1;
                }

                $this->section->basket = serialize($basket);
            } else {
                $basket = ["$id" => 1];
            }

            $this->presenter->template->basket = $basket;
            $this->section->basket = serialize($basket);
        }
        //uzivatel je prihlasen, pouziva se databaze
        else{

        }
    }

    /** Odebrani polozky z kosiku
     * @param int $id
     */
    public function removeFromBasket(int $id)
    {
        $basket = unserialize($this->section->basket);
        unset($basket[$id]);
        $this->section->basket = serialize($basket);
    }
}

?>