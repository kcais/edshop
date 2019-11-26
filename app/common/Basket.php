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

    public function getBasketObjectsList(Nette\Http\SessionSection $section)
    {
        $keyIds = null;

        if(isset($section->basket) && $section->basket) {
            foreach (unserialize($section->basket) as $basketKey => $basketValue) {
                $keyIds[] = $basketKey;
            }
            $selection = $this->objectManager->getObjectsFromIds($keyIds);
        }
        else{
            $selection = [];
        }

        return $selection;
    }

    public function calculateBasketPrice(Nette\Http\SessionSection $section) : float
    {
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
}

?>