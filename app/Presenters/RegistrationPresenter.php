<?php
namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

final class RegistrationPresenter extends Nette\Application\UI\Presenter
{
    protected function createComponentRegistrationNewForm() : Form
    {
        $form = new Form;
        $form->addText('username','Uživatelské jméno :')
            ->setRequired("Zadejte uživatelské jméno");

        $form->addText('firstname','Vaše jméno :')
            ->setRequired("Zadejte Vaše jméno");

        $form->addText('lastname','Vaše příjmení :')
            ->setRequired("Zadejte Vaše příjmení");

        $form->addText('email','E-mail :')
            ->addRule(Form::EMAIL,'Zadaný email nemá validní tvar')
            ->setRequired("Zadejte registrační email");

        $form->addPassword('pass1','Heslo :')
            ->setRequired("Zadejte heslo");

        $form->addPassword('pass2','Heslo znovu :')
            ->setRequired("Zadejte heslo pro potvrzení");

        $form->addSubmit('send','Registrovat');

        $form->onSuccess[] = [$this, 'registrationFormSucceeded'];

        return $form;

    }
}


?>