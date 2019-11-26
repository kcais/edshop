<?php

use App\Model\ObjectManager;


/**
 * Class Basket Trida reprezentujici kosik
 */
class Basket{

    private $objectManager;
    private $user;
    private $section;

    public function __construct(\App\Model\ObjectManager $objectManager, Nette\Security\User $user, Nette\Http\SessionSection $section)
    {
        $this->objectManager = $objectManager;
        $this->user = $user;
        $this->section = $section;
    }

    /** Vrati obsah kosiku
     * @return array|\Nette\Database\Table\Selection
     */
    public function getBasketObjectsList()
    {
        $keyIds = null;

        $basket = unserialize($this->section->basket);

        if(isset($this->section->basket) && $this->section->basket && is_array($basket) && !empty($basket)) {
            foreach ($basket as $basketKey => $basketValue) {
                $keyIds[] = $basketKey;
            }
            $selection = $this->objectManager->getObjectsFromIds($keyIds);
        }
        else{
            $selection = [];
        }

        return $selection;
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

        return $totalPrice;
    }

    /** Odebrani polozky z kosiku
     * @param int $id
     * @param \Nette\Http\SessionSection $section
     */
    public function removeFromBasket(int $id)
    {
        $basket = unserialize($this->section->basket);
        unset($basket[$id]);
        $this->section->basket = serialize($basket);
    }
}

?>