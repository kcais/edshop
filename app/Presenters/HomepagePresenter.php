<?php

declare(strict_types=1);

namespace App\Presenters;

use Ublaboo\DataGrid\DataGrid;

final class HomepagePresenter extends BasePresenter
{

    /** Zobrazeni kategorie produktu
     * @param $categoryId
     */
    public function renderProducts($categoryId)
    {
        $this->template->categoryId = $categoryId;
        $session = $this->getSession();
        $section = $session->getSection(\App\Common\Common::getSelectionName());
        $section->categoryId = $categoryId;

        $catObj = $this->em->getCategoryRepository()->find($categoryId);

        $this->template->categoryName = $catObj->getName();

        $this->template->childCategory = $this->em->getCategoryRepository()->findBy(['parent_cat' => $categoryId]);

        //pokud je nadrazena kategorie
        $parCatObj = $catObj->getParentCat();
        if($parCatObj){
            $this->template->parCategoryName = $parCatObj->getName();
            $this->template->parCategoryId = $parCatObj->getId();
        }
        else{
            unset($this->template->parCategoryName);
            unset($this->template->parCategoryId);
        }

    }

    /** Zobrazeni detailu produktu
     * @param $id
     */
    public function renderDetail($id)
    {
        $prodObj = $this->em->getProductRepository()->find($id);
        if($prodObj) {
            $this->template->categoryName = $prodObj->getCategory()->getName();
            $this->template->categoryId = $prodObj->getCategory()->getId();
            $this->template->productId = $prodObj->getId();
            $this->template->productName = $prodObj->getName();
            $this->template->productDescription = $prodObj->getDescription();
        }
    }

    /**
     *  Zobrazeni seznamu kategorii
     */
    public function renderDefault()
    {
        foreach($this->em->getCategoryRepository()->findby(['deleted_on' => null]) as $catObj)
        {
            $prodObjArr = $this->em->getProductRepository()->findBy(['category' => $catObj->getId(), 'deleted_on' => null]);
            $objCount = sizeof($prodObjArr);

            foreach($this->em->getCategoryRepository()->findBy(['deleted_on' => null, 'parent_cat' => $catObj]) as $parCatObj){
                $prodObjArr = $this->em->getProductRepository()->findBy(['category' => $parCatObj->getId(), 'deleted_on' => null]);
                $objCount += sizeof($prodObjArr);
            }


            $objectsInCategoryCount[$catObj->getId()]=$objCount;
        }

        $this->template->objectsInCategoryCount = $objectsInCategoryCount;
    }

    /** Komponenta datagridu pro kategorie
     * @return DataGrid
     */
    protected function createComponentProductsGrid($name) : DataGrid
    {
        $session = $this->getSession();
        $section = $session->getSection(\App\Common\Common::getSelectionName());

        $query = $this->em->createQuery(
            "
            select prod from \App\Model\Database\Entity\Product prod where prod.deleted_on is null 
            and prod.category = $section->categoryId 
            or prod.category in (select cat from \App\Model\Database\Entity\Category cat where cat.parent_cat = $section->categoryId
            and cat.deleted_on is null)");
        $prodObjArr = $query->getResult();

        $prodArr = null;

        foreach($prodObjArr as $prodObj){
            $prodArr[] = ['id' => $prodObj->getId(), 'name' => $prodObj->getName(), 'description' => $prodObj->getDescription(), 'price' => $prodObj->getPrice()];
        }

        if(!$prodArr)$prodArr = [];

        $grid = new DataGrid($this,$name);

        $grid->setDataSource($prodArr);

        $grid->addColumnText('image', '')
            ->setTemplate(__DIR__ . '/templates/components/datagrid/grid.img.latte')
            ->setAlign('center')
        ;

        $grid->addColumnLink('name', 'objectsGrid.name','detail')->setSortable();
        $grid->addColumnText('description', 'objectsGrid.description');
        $grid->addColumnText('price', 'objectsGrid.price')
            ->setRenderer(function ($row):String{return "$row[price] Kč";})
            ->setSortable()
            ->setAlign('center')
        ;

        $grid->addAction('toBasket','Do košíku','ToBasket!')
            ->setClass('ajax btn btn-primary')
        ;

        $grid->addFilterText('name', 'objectsGrid.name')->setSplitWordsSearch(FALSE);
        $grid->addFilterText('description', 'objectsGrid.description');

        $lang = $this->getSession()->getSection(\App\Common\Common::getSelectionName())->language;
        if(!isset($lang))$lang='CZ';

        $grid->setTranslator(new \Translator($lang));
        return $grid;
    }

    /** Handle udalosti pridani zbozi do kosiku
     * @param int $id ID produktu
     */
    function handleToBasket(int $id)
    {
            $basketObj = new \Basket($this, $this->em);
            $basketObj->addToBasket($id);
            $basketObj->calculateBasketPrice();

            $this->redirect('this');
    }

    /** Pridani produktu do kosiku
     * @param int $id
     */
    public function actionToBasket(int $id)
    {
        $basketObj = new \Basket($this, $this->em);
        $basketObj->addToBasket($id);
        $basketObj->calculateBasketPrice();
        $this->redirect("Homepage:detail",$id);
    }

    /** Nastaveni jazyka
     * @param $language
     */
    public function actionSetLanguage($language)
    {
        $this->getSession()->getSection(\App\Common\Common::getSelectionName())->language = $language;
        $this->redirect('Homepage:');
    }
}
