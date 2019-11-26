<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\ObjectManager;
use Ublaboo\DataGrid\DataGrid;

final class BasketPresenter extends BasePresenter
{
    private $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    protected function createComponentBasketGrid($name) : DataGrid
    {

        $section = $this->getSession()->getSection('edshop');

        $basketObj = new \Basket($this->objectManager);

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($basketObj->getBasketObjectsList($section));
        $grid->addColumnText('name', 'objectsGrid.name')->setSortable();
        $grid->addColumnText('description', 'objectsGrid.description');
        $grid->addColumnText('price', 'objectsGrid.price')
            ->setRenderer(function ($row): String {
                return "$row->price Kč";
            })
            ->setSortable()
            ->setAlign('center');

        $grid->setTranslator(new \TranslatorCz('CZ'));

        return $grid;
    }

    public function renderEmpty()
    {
        $session = $this->getSession();
        $section = $session->getSection('edshop');

        unset($this->template->basket);
        unset($section->basket);

        unset($this->template->basketPrice);
        unset($this->getSession()->getSection('edshop')->basketPrice);

        $this->redirect("Basket:");
    }


}