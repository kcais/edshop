<?php

namespace App\Presenters;

use Nette;

/** Trida resici odhlaseni uzivatele
 * Class LogoutPresenter
 * @package App\Presenters
 */
final class LogoutPresenter extends Nette\Application\UI\Presenter
{
    private $user;

    function __construct( Nette\Security\User $user)
    {
        $this->user = $user;
    }

    /**
     * Odhlaseni uzivatele
     */
    public function renderLogout() :void
    {
        $this->user->logOut();
    }
}