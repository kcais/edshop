<?php

namespace App\Presenters;

use Nette;


final class AdminPresenter extends Nette\Application\UI\Presenter
{
    protected function startup()
    {
        //volani metody startup predka
        parent::startup();

        //overeni , zda je prihlaseny uzivatel prislusny roli admin
        if(!$this->user->isInRole("admin"))
        {
            $this->flashMessage("Přihlášený uživatel nemá roli admin !",'warning');
            $this->redirect("Homepage:");
        }
    }
}

?>