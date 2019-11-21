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
        $grid->addColumnText('price', 'objectsGrid.price')->setSortable();
        $grid->addFilterText('name', 'objectsGrid.name')->setSplitWordsSearch(FALSE);
        $grid->addFilterText('description', 'objectsGrid.description');
        $grid->setTranslator(new \TranslatorCz('CZ'));
        return $grid;
    }
}
