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

        if($this->user->isLoggedIn()){
            //TODO Implementace kosiku pri prihlaseni (ukladani do DB)
        }
        else { //pridani do kosiku ulozeneho v session

            $section = $this->getSession()->getSection('edshop');

            $basketObj = new \Basket($this->objectManager);

            $selection = $basketObj->getBasketObjectsList($section);

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
        }

        return $grid;
    }

    function handleFromBasket(int $id)
    {
        if($this->user->isLoggedIn()){
            //TODO Implementace kosiku pri prihlaseni (ukladani do DB)
        }
        else { //pridani do kosiku ulozeneho v session
            $basketObj = new \Basket($this->objectManager);
            $section = $this->getSession()->getSection('edshop');

            $basketObj->removeFromBasketSession($id,$section);
            $basketObj->calculateBasketPriceSession($section);
            $this->redirect("Basket:");
        }
    }

    /** Vyprazdneni kosiku
     * @throws \Nette\Application\AbortException
     */
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