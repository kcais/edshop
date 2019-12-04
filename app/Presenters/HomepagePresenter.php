<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\CategoryManager;
use App\Model\ObjectManager;
use App\Model\OrderManager;
use Ublaboo\DataGrid\DataGrid;

final class HomepagePresenter extends BasePresenter
{
    private $objectManager;
    private $orderManager;
    private $categoryManager;

    function __construct(ObjectManager $objectManager, CategoryManager $categoryManager, OrderManager $orderManager)
    {
        $this->objectManager = $objectManager;
        $this->orderManager = $orderManager;
        $this->categoryManager = $categoryManager;
    }

    /** Zobrazeni kategorie produktu
     * @param $categoryId
     */
    public function renderProducts($categoryId)
    {
        $this->template->categoryId = $categoryId;
        $session = $this->getSession();
        $section = $session->getSection(\App\Common\Common::getSelectionName());
        $section->categoryId = $categoryId;
        $this->template->categoryName = $this->em->getCategoryRepository()->find($categoryId)->getName();
    }

    /**
     *  Zobrazeni seznamu kategorii
     */
    public function renderDefault()
    {
        foreach($this->template->categories as $category)
        {
            $objCount = sizeof($this->em->getProductRepository()->findBy(['category' => $category['id']]));
            $objectsInCategoryCount["$category[id]"]=$objCount;
        }

        $this->template->objectsInCategoryCount = $objectsInCategoryCount;
    }

    /** Komponenta datagridu pro kategorie
     * @return DataGrid
     */
    protected function createComponentObjectsGrid($name) : DataGrid
    {
        $session = $this->getSession();
        $section = $session->getSection(\App\Common\Common::getSelectionName());

        $prodObjArr = $this->em->getProductRepository()->findBy(['category' => $section->categoryId]);
        $prodArr = null;

        foreach($prodObjArr as $prodObj){
            $prodArr[] = ['id' => $prodObj->getId(), 'name' => $prodObj->getName(), 'description' => $prodObj->getDescription(), 'price' => $prodObj->getPrice()];
        }

        if(!$prodArr)$prodArr = [];

        $grid = new DataGrid($this,$name);

        $grid->setDataSource($prodArr);
        $grid->addColumnText('name', 'objectsGrid.name')->setSortable();
        $grid->addColumnText('description', 'objectsGrid.description');
        $grid->addColumnText('price', 'objectsGrid.price')
            ->setRenderer(function ($row):String{return "$row[price] Kč";})
            ->setSortable()
            ->setAlign('center')
        ;

        $grid->addAction('toBasket','Do košíku','ToBasket!')
            ->setClass('btn btn-primary')
        ;

        $grid->addFilterText('name', 'objectsGrid.name')->setSplitWordsSearch(FALSE);
        $grid->addFilterText('description', 'objectsGrid.description');
        $grid->setTranslator(new \TranslatorCz('CZ'));
        return $grid;
    }

    /** Handle udalosti pridani zbozi do kosiku
     * @param int $id ID produktu
     */
    function handleToBasket(int $id)
    {
            $basketObj = new \Basket($this, $this->objectManager, $this->orderManager, $this->em);
            $basketObj->addToBasket($id);
            $basketObj->calculateBasketPrice();

    }

}
