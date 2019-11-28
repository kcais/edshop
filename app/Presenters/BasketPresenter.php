<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\ObjectManager;
use App\Model\OrderManager;
use App\Model\UserManager;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Ublaboo\DataGrid\DataGrid;

final class BasketPresenter extends BasePresenter
{
    private $objectManager;
    private $orderManager;
    private $userManager;

    public function __construct(ObjectManager $objectManager, OrderManager $orderManager, UserManager $userManager)
    {
        $this->objectManager = $objectManager;
        $this->orderManager = $orderManager;
        $this->userManager = $userManager;
    }

    /** Souhrn objednavanych polozek a daplneni kontaktu
     * @throws \Nette\Application\AbortException
     */
    public function renderOrderconfirm()
    {
        $basket = new \Basket($this, $this->objectManager, $this->orderManager);
        $this->template->basketOrderList = $basket->getBasketObjectsList();
        if(!$this->template->basketPrice)$this->redirect('Homepage:');

    }

    /** Dokonceni objednavky a odeslani informaci o objednavce na email
     * @param Form $form
     * @param array $values
     */
    public function orderDataSucceeded(Form $form, array $values)
    {
        //TODO
    }

    /** Formular pro zobrazeni dorucovacich udaju
     * @return Form
     */
    protected function createComponentOrderData(): Form
    {
        $form = new Form;

        $userName='';
        $userSurname='';
        $userEmail='';

        if($this->user->isLoggedIn()){
            $userData = $this->userManager->getUser($this->user->getId());

            $userName = $userData['firstname'];
            $userSurname = $userData['surname'];
            $userEmail = $userData['email'];
        }

        $form->addText('name','Jméno :')
            ->setDefaultValue($userName)
        ;
        $form->addText('surname','Příjmení :')
            ->setDefaultValue($userSurname)
        ;
        $form->addText('email','E-mail :')
            ->setDefaultValue($userEmail)
        ;

        $form->addText('address','Adresa :');

        $form->onSuccess[] = [$this, 'orderDataSucceeded'];

        return $form;
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
        $basket = new \Basket($this, $this->objectManager, $this->orderManager);
        $basket->empty();
        $this->redirect("Basket:");
    }
}