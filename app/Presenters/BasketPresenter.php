<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\ObjectManager;
use App\Model\OrderManager;
use Ublaboo\DataGrid\DataGrid;

final class BasketPresenter extends BasePresenter
{
    private $objectManager;
    private $orderManager;

    public function __construct(ObjectManager $objectManager, OrderManager $orderManager)
    {
        $this->objectManager = $objectManager;
        $this->orderManager = $orderManager;
    }

    /** Zobrazeni obsahu kosiku
     * @param $name
     * @return DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    protected function createComponentBasketGrid($name) : DataGrid
    {
        $grid=null;

        $section = $this->getSession()->getSection(\App\Common\Common::getSelectionName());

        $basketObj = new \Basket($this, $this->objectManager, $this->orderManager);

        $selection = $basketObj->getBasketObjectsList();

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($selection);

        $grid->addColumnNumber('id', 'objectsGrid.name')
            ->setDefaultHide();

        $grid->addColumnText('name', 'objectsGrid.name')->setSortable();
        $grid->addColumnText('description', 'objectsGrid.description');
        $grid->addColumnText('price', 'objectsGrid.pricePerPcs')
            ->setRenderer(function ($row): String {
                return "$row[price] Kč";
            })
            ->setSortable()
            ->setAlign('center');

        $grid->addColumnText('pcs', 'objectsGrid.pcs');

        $grid->addColumnText('totalPrice', 'objectsGrid.totalPrice')
            ->setRenderer(function ($row): String {
                return "$row[totalPrice] Kč";
            })
        ->setSortable()
        ->setAlign('center');

        $grid->addAction('fromBasket', 'Odebrat', 'FromBasket!')
            ->setClass('btn btn-primary');

        $grid->setTranslator(new \TranslatorCz('CZ'));

        return $grid;
    }

    /** Obsluha odebrani veci z kosiku
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    function handleFromBasket(int $id)
    {
        $section = $this->getSession()->getSection(\App\Common\Common::getSelectionName());
        $basketObj = new \Basket($this, $this->objectManager, $this->orderManager);

        $basketObj->removeFromBasket($id);
        $basketObj->calculateBasketPrice();
        $this->redirect("Basket:");
    }

    /** Vyprazdneni kosiku
     * @throws \Nette\Application\AbortException
     */
    public function renderEmpty()
    {
        $session = $this->getSession();
        $section = $session->getSection(\App\Common\Common::getSelectionName());

        unset($this->template->basket);
        unset($section->basket);

        unset($this->template->basketPrice);
        unset($this->getSession()->getSection(\App\Common\Common::getSelectionName())->basketPrice);

        $this->redirect("Basket:");
    }
}