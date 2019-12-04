<?php

namespace App\Presenters;

use Nette;

/** Trida resici odhlaseni uzivatele
 * Class LogoutPresenter
 * @package App\Presenters
 */
final class LogoutPresenter extends BasePresenter//Nette\Application\UI\Presenter
{
    private $user;

    function __construct( Nette\Security\User $user)
    {
        parent::__construct();
        $this->user = $user;
    }

    /**
     * Odhlaseni uzivatele
     */
    public function renderLogout() :void
    {
        $this->user->logOut();
        $basket = new \Basket($this, $this->em);
        $this->template->basketPrice = $basket->calculateBasketPrice();
    }
}
