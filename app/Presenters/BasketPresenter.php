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

    /** Zobrazeni obsahu kosiku
     * @param $name
     * @return DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    protected function createComponentBasketGrid($name) : DataGrid
    {
        $grid=null;

        $section = $this->getSession()->getSection(\App\Common\Common::getSelectionName());

        $basketObj = new \Basket($this->objectManager,$this->user,$section);

        $selection = $basketObj->getBasketObjectsList();

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($selection);
        $grid->addColumnText('name', 'objectsGrid.name')->setSortable();
        $grid->addColumnText('description', 'objectsGrid.description');
        $grid->addColumnText('price', 'objectsGrid.price')
            ->setRenderer(function ($row): String {
                return "$row->price Kč";
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
        $basketObj = new \Basket($this->objectManager,$this->user, $section);

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