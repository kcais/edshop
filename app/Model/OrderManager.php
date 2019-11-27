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
    public function createNewOrder(int $userId, bool $isClosed=false) : int
    {
        return $this->database->table('orders')
            ->insert(['user_id' => $userId, 'is_closed' => $isClosed]);

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
            $this->createNewOrder($userId, false);
            return 1;
        }
        else{ //uzivatel jiz ma otevrenou objednavku, editovat
            return 0;
        }

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

}