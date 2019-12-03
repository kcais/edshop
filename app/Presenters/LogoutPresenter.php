<?php

namespace App\Presenters;

use App\Model\ObjectManager;
use App\Model\OrderManager;
use Nette;

/** Trida resici odhlaseni uzivatele
 * Class LogoutPresenter
 * @package App\Presenters
 */
final class LogoutPresenter extends BasePresenter//Nette\Application\UI\Presenter
{
    private $user;
    private $objectManager;
    private $orderManager;

    function __construct( Nette\Security\User $user, ObjectManager $objectManager, OrderManager $orderManager)
    {
        $this->user = $user;
        $this->objectManager = $objectManager;
        $this->orderManager = $orderManager;
    }

    /**
     * Odhlaseni uzivatele
     */
    public function renderLogout() :void
    {
        $this->user->logOut();
        $basket = new \Basket($this, $this->objectManager, $this->orderManager, $this->em);
        $this->template->basketPrice = $basket->calculateBasketPrice();
    }
}
