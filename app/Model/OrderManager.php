<?php

namespace App\Model;

use Nette;

class OrderManager
{
    use Nette\SmartObject;

    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    /** Vytvori novou objednavku
     * @param int $userId Uzivatelske id v db
     * @param bool $isClosed Objednavka 0-otevrena, 1-uzavrena
     * @return int Vraci id vytvorene objednavky
     */
    private function createNewOrder(int $userId, bool $isClosed=false) : int
    {
        return $this->database->table('orders')
            ->insert(['user_id' => $userId, 'is_closed' => $isClosed])->id;

    }

    /** Vytvori novy zaznam v otevrene objednavce uzivatele nebo updatuje pocet konkretnich objectId v objednavce
     * @param int $userId
     * @param int $objectId
     * @param int $pcs
     * @return int Vraci 1-Objekt vlozen, 0-pocet updatovan, -1 - nastala chyba
     */
    public function createUpdateOrderObject(int $userId, int $objectId, float $pcs=1.0) : int
    {
        $openOrderId = $this->getOpenOrderId($userId);

        //chyba pri zjisteni id otevrene objednavky uzivatele
        if($openOrderId == -1)return -1;

        //uzivatel nema zadnou otevrenou objednavku, vytvorit
        if($openOrderId == 0){
            $openOrderId = $this->createNewOrder($userId, false);
            $createOrderObject = $this->insertUpdateObjectInOrder($openOrderId, $objectId, $pcs);
            return 1;
        }
        else{ //uzivatel jiz ma otevrenou objednavku, editovat
            $this->insertUpdateObjectInOrder($openOrderId, $objectId, $pcs);
            return 0;
        }
    }

    /** Vlozi novy objekt do objednavky, pripadne updatuje pocet kusu
     * @param $orderId
     * @param $objectId
     * @param float $pcs
     * @return int 0-probehl update stavajiciho zaznamu, 1-byl proveden insert
     */
    private function insertUpdateObjectInOrder($orderId, $objectId, $pcs=1.0) : int
    {
        $selection = $this->database->table('order_objects')
        ->where('order_id', $orderId)
        ->where('object_id',$objectId)
        ->where('deleted_on', null)
        ->select('pcs');

        if($selection->count()==0) { //vkladam novy zaznam
            $this->database->table('order_objects')
                ->insert(['order_id' => $orderId, 'object_id' => $objectId, 'pcs' => $pcs]);
            return 1;
        }
        else{ //updatuju stavajici zaznam
            $this->database->table('order_objects')
                ->where('order_id',$orderId)
                ->where('object_id',$objectId)
                ->update(['pcs' => $selection->fetch()->pcs + $pcs])
            ;
            return 0;
        }
    }

    /** Vraci pocet prodejnich polozek v objednavce
     * @param $objectId Id prodejni polozky(objektu)
     * @param $orderId Id objednavky
     * @return float 0-neni v objednavce, pocet - pocet kusu v objednavce
     */
    public function isObjectInOrder($orderId,$objectId) : float
    {
        return $this->database->table('order_objects')
            ->select('pcs')
            ->where('order_id',$orderId)
            ->where('object_id',$objectId)
            ->where('deleted_on',null)->pcs;
    }

    /** Zjisteni otevrene objednavky u uzivatele
     * @param int $userId
     * @return int Vraci -1 - pokud vice nez jedna otevrena(aktualne chyba), 0-pokud zadna otevrena, id_order - id otevrene objednavky
     */
    public function getOpenOrderId(int $userId) : int
    {
        $orderRow = $this->database->table('orders')
            ->where('user_id',$userId)
            ->where('is_closed',0)
            ->select('id');

        if($orderRow->count()==0){
            return 0;
        }
        elseif($orderRow->count()>1){
            return -1;
        }
        else{
            return $orderRow->fetch()->id;
        }
    }

    /** Vraci selection s id a pocty kusu objektu z objednavky
     * @param int $orderId
     * @return Nette\Database\Table\Selection
     */
    public function getOrderObjectsList(int $orderId) : Nette\Database\Table\Selection
    {
        $selection = $this->database->table('order_objects')
            ->where('order_id',$orderId)
            ->where('deleted_on',null)
            ->select('object_id,pcs')
        ;

        return $selection;
    }

    /** Vrati celkovou hodnotu objednavky
     * @param int $orderId
     * @return float
     */
    public function getOrderPrice2(int $orderId) : float
    {
        $selectionObjects = $this->database->table('order_objects')
            ->select('order_objects.object_id')
            ->select( 'order_objects.pcs')
            ->select('object.price')
            ->where('order_objects.order_id',$orderId)
            ->where('order_objects.deleted_on', null)
        ;


        if($selectionObjects->count()==0){ //objednavka nema zadne polozky
            return 0.0;
        }

        $totalPrice = 0.0;

        foreach($selectionObjects as $selectionObject){
            $totalPrice += $selectionObject->pcs * $selectionObject->price;
        }

        return $totalPrice;
    }

    /** Smazani konkretni prodejni polozky z objednavky
     * @param int $orderId Id objednavky
     * @param int $objectId Id odebirane prodejni polozky
     * @return int Vraci pocet smazanych radku
     */
    public function deleteObjectFromOrder(int $orderId, int $objectId) : int
    {
        return $this->database->table('order_objects')
            ->where('order_id',$orderId)
            ->where('object_id',$objectId)
            ->delete();
    }

    /** Vyprazdni objednavku - tz. smaze vsechny polozky objednavky
     * @param int $orderId Id objednavky
     * @return int Vraci pocet smazanych radek
     */
    public function emptyOrder(int $orderId) : int
    {
        return $this->database->table('order_objects')
            ->where('order_id',$orderId)
            ->delete();
    }

}