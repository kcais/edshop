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

    /** Nacte vsechny prodejni polozky podle zadanych kriterii
     * @param bool $objectVisibility 1-nacte pouze objekty oznacene jako viditelne, jinak nacte vsechny
     * @param bool $objectDeleted 1-nacte i objekty oznacene jako smazane
     * @return Nette\Database\Table\Selection
     * @throws \Exception
     */
    public function getAllObjects($objectVisibility=true, $objectDeleted=false)
    {
        if(!$objectDeleted) {
            return $this->database->table('objects')
                ->where('is_visible = ', $objectVisibility)
                ->where('deleted_on is null')
                ;
        }
        else{
            return $this->database->table('objects')
                ->where('is_visible = ', $objectVisibility)
                ->where('deleted_on is not null')
                ;
        }


    }
}
?>