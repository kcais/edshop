<?php

namespace App\Model;

use Nette;

class ObjectManager
{
    use Nette\SmartObject;

    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    /** Vlozeni noveho produktu
     * @param int $category_id Id kategorie ke ktere produkt patri
     * @param String $name Jmeno produktu
     * @param String $description Popis produktu
     * @param float $price Cena produktu
     * @return Nette\Database\Table\ActiveRow Vraci vlozene radky
     */
    public function createNewObject(int $category_id, String $name, String $description, float $price) : Nette\Database\Table\ActiveRow
    {
        return $this->database->table('objects')->insert([
            'name'=> $name,
            'description' => $description,
            'category_id' => $category_id,
            'price' => $price
        ]);
    }

    /**
     * @param int $category Id kategorie prodejnich polozek
     * @param bool $objectVisibility Nastaveni zda pocitat pouze viditelne objekty
     * @param bool $objectDeleted Nastaveni pokud pocitat i smazane objekty
     * @return int
     */
    public function  getObjectsCount(int $category,$objectVisibility=true, $objectDeleted=false) : int
    {
        if($objectDeleted){
            $notDeleted='NOT';
        }
        else{
            $notDeleted='';
        }

            return $this->database->table('objects')
                ->where('is_visible = ', $objectVisibility)
                ->where("deleted_on $notDeleted", null)
                ->where('category_id', $category)
                ->count('id')
                ;
    }

    /** Nacte a vrati vsechny prodejni polozky podle zadanych kriterii
     * @param bool $objectVisibility 1-nacte pouze objekty oznacene jako viditelne, jinak nacte vsechny
     * @param bool $objectDeleted 1-nacte i objekty oznacene jako smazane
     * @return Nette\Database\Table\Selection
     * @throws \Exception
     */
    public function getObjects($category=0,$objectVisibility=true, $objectDeleted=false) : Nette\Database\Table\Selection
    {
        if($objectDeleted){
            $notDeleted='NOT';
        }
        else{
            $notDeleted='';
        }

            return $this->database->table('objects')
                ->where('is_visible = ', $objectVisibility)
                ->where("deleted_on $notDeleted", null)
                ->where('category_id', $category);

    }

    /** Nacte a vrati prodejni polozky podle id specifikovanych v poli
     * @param array $ids Pole idcek objektu pro vraceni
     * @return Nette\Database\Table\Selection
     */
    public function getObjectsFromIds(array $ids) : Nette\Database\Table\Selection
    {
        return $this->database->table('objects')
            ->where('id IN ',$ids)
            ;
    }

    /** Zjisteni otevrene objednavky u uzivatele
     * @param int $userId
     * @return int Vraci -1 - pokud vice nez jedna otevrena(aktualne chyba), 0-pokud zadna otevrena, id_order - id otevrene objednavky
     */
    public function getOpenOrderId(int $userId):int
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
    public function getOrderObjectsList(int $orderId)
    {
        $selection = $this->database->table('order_objects')
            ->where('order_id',$orderId)
            ->where('deleted_on',null)
            ->select('object_id,pcs')
        ;

        return $selection;
    }

}
?>