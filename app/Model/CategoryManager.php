<?php

namespace App\Model;

use Nette;

class CategoryManager
{
    use Nette\SmartObject;

    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    /** Nacte vsechny kategorie prodejnich polozek ktere nejsou oznaceny jako deleted_on
     * @return Nette\Database\Table\Selection
     */
    public function getAllCategory()
    {
        return $this->database->table('categories')
            ->where('deleted_on',NULL)
            ->order('name ASC')
            ;
    }
}
