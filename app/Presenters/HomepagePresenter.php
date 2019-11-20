<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\ObjectManager;
use Ublaboo\DataGrid\DataGrid;

final class HomepagePresenter extends BasePresenter
{
    private $objectManager;

    function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function renderProducts($categoryId)
    {
        $this->template->categoryId = $categoryId;
        $session = $this->getSession();
        $section = $session->getSection('edshop');
        $section->categoryId = $categoryId;
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
        $grid->addColumnText('name', 'Název')->setSortable();
        $grid->addColumnText('description', 'Popis');
        $grid->addFilterText('name', 'Název')->setSplitWordsSearch(FALSE);
        $grid->addFilterText('description', 'Popis');
        return $grid;
    }
}
