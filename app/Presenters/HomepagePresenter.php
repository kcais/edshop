<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\CategoryManager;
use App\Model\ObjectManager;
use Ublaboo\DataGrid\DataGrid;

final class HomepagePresenter extends BasePresenter
{
    private $objectManager;
    private $categoryManager;

    function __construct(ObjectManager $objectManager, CategoryManager $categoryManager)
    {
        $this->objectManager = $objectManager;
        $this->categoryManager = $categoryManager;
    }

    public function renderProducts($categoryId)
    {
        $this->template->categoryId = $categoryId;
        $session = $this->getSession();
        $section = $session->getSection('edshop');
        $section->categoryId = $categoryId;
        $this->template->categoryName = $this->categoryManager->getCategory((int)$categoryId)->fetch()->name;
    }

    public function renderDefault()
    {
        foreach($this->template->categories as $category)
        {
            $objectsInCategoryCount["$category[id]"]= $this->objectManager->getObjectsCount($category['id']);
        }

        $this->template->objectsInCategoryCount = $objectsInCategoryCount;
    }

    /** Komponenta datagridu pro kategorie
     * @return DataGrid
     */
    protected function createComponentObjectsGrid($name) : DataGrid
    {

        $session = $this->getSession();
        $section = $session->getSection('edshop');
        $grid = new DataGrid($this,$name);
        $grid->setDataSource($this->objectManager->getObjects($section->categoryId));
        $grid->addColumnText('name', 'objectsGrid.name')->setSortable();
        $grid->addColumnText('description', 'objectsGrid.description');
        $grid->addColumnText('price', 'objectsGrid.price')
            ->setRenderer(function ($row):String{return "$row->price Kč";})
            ->setSortable()
            ->setAlign('center')
        ;

        $grid->addAction('toBasket','Do košíku','ToBasket!')
            ->setClass('btn btn-primary')
        ;
        //$grid->addColumnLink('id','Do košíku','addToBasket');

        $grid->addFilterText('name', 'objectsGrid.name')->setSplitWordsSearch(FALSE);
        $grid->addFilterText('description', 'objectsGrid.description');
        $grid->setTranslator(new \TranslatorCz('CZ'));
        return $grid;
    }

    function renderBasket()
    {
        $session=$this->getSession();
        $section = $session->getSection('edshop');
        if(isset($section->basket)) {
            $this->template->basket = $section->basket;
        }
        else{
            $this->template->basket = "";
        }

    }

    function handleToBasket($id)
    {
        /*$session=$this->getSession();
        $section = $session->getSection('edshop');
        unset($section->basket);
        exit;*/
        //pridani do kosiku ulozeneho v databazi
       if($this->user->isLoggedIn()){

       }
       else{ //pridani do kosiku ulozeneho v session
           $session=$this->getSession();
           $section = $session->getSection('edshop');
           if(isset($section->basket)){
               $basket = unserialize($section->basket);
               if(isset($basket[$id])) {
                   $basket[$id] = $basket[$id]+1;
               }
               else{
                   $basket[$id] = 1;
               }

               $section->basket = serialize($basket);
           }
           else{
               $basket = [$id => 1];
           }

           echo print_r($basket);

           $section->basket = serialize($basket);
       }
    }
}
