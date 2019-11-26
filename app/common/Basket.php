<?php

use App\Model\ObjectManager;


/**
 * Class Basket Trida reprezentujici kosik
 */
class Basket{

    private $objectManager;

    public function __construct(\App\Model\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /** Vrati obsah kosiku
     * @param \Nette\Http\SessionSection $section
     * @return array|\Nette\Database\Table\Selection
     */
    public function getBasketObjectsList(Nette\Http\SessionSection $section)
    {
        $keyIds = null;

        $basket = unserialize($section->basket);

        if(isset($section->basket) && $section->basket && is_array($basket) && !empty($basket)) {
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
     * @param \Nette\Http\SessionSection $section
     * @return float
     */
    public function calculateBasketPriceSession(Nette\Http\SessionSection $section) : float
    {
        if(!isset($section->basket)){
            $section->basketPrice = 0.0;
            return 0.0;
        }

        if(!unserialize($section->basket)){
            $section->basketPrice = 0.0;
            return 0.0;
        }

        $basket = unserialize($section->basket);

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


        $section->basketPrice = $totalPrice;

        return $totalPrice;
    }

    /** Odebrani polozky z kosiku
     * @param int $id
     * @param \Nette\Http\SessionSection $section
     */
    public function removeFromBasketSession(int $id, Nette\Http\SessionSection $section)
    {
        $basket = unserialize($section->basket);
        unset($basket[$id]);
        $section->basket = serialize($basket);

    }
}

?>