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
    public function getAllCategories()
    {
        return $this->database->table('categories')
            ->where('deleted_on',NULL)
            ->order('name ASC')
            ;
    }

    /** Vytvori novou kategorii prodejnich polozek
     * @param String $name Nazev kategorie prodejnich polozek
     * @param String $comment Popis kategorie prodejnich polozek
     * @param int $order Cislo urcujici poradi kategorie
     * @param int $parent_cat_id Id nadrazene kategorie
     * @return int Vraci 1 - pokud se podarilo kategorii vytvorit, 0 - pokud chyba
     */
    public function createNewCategory(String $name, String $comment, int $order_id=1, int $parent_cat_id = null) : int
    {
        try{
            $this->database->table("categories")->insert([
                "name" => $name,
                "comment" => $comment,
                "order_id" => $order_id,
                "parent_cat_id" => $parent_cat_id,
            ]);

            return 1;
        }
        catch(\Exception $e){
            return 0;
        }
    }

}
