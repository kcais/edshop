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

    /**
     *  Renderer prodejniho kosiku
     */
    function renderDefault()
    {
        /*$session = $this->getSession();
        $section = $session->getSection('edshop');
        if (isset($section->basket)) {
            $this->template->basket = $section->basket;
        } else {
            $this->template->basket = "";
        }*/
    }

    protected function createComponentBasketGrid($name) : DataGrid
    {

        $session = $this->getSession();
        $section = $session->getSection('edshop');
        $keyIds = null;

        if(isset($section->basket) && $section->basket) {
            //print_r(unserialize($this->template->basket));
            foreach (unserialize($section->basket) as $basketKey => $basketValue) {
                //echo $basketKey."=>$basketValue<br>";
                $keyIds[] = $basketKey;
            }
            $selection = $this->objectManager->getObjectsFromIds($keyIds);
        }
        else{
            $selection = [];
        }


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