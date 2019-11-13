<?php
namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

final class RegistrationPresenter extends Nette\Application\UI\Presenter
{
    /** Registracni formular noveho uzivatele
     *
     */
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
            ->setRequired("Zadejte heslo")
            ->addRule(Form::MIN_LENGTH,'Heslo musí obsahovat minimálně 3 znaky',3)
        ;

        $form->addPassword('pass2','Heslo znovu :')
            ->setRequired("Zadejte heslo pro potvrzení")
            ->addRule(Form::EQUAL,'Zadaná hesla se neshodují',$form['pass1'])
        ;

        $form->addSubmit('send','Registrovat');

        $form->onSuccess[] = [$this, 'registrationFormSucceeded'];

        return $form;

    }
}


?>