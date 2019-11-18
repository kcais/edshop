<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

final class LoginPresenter extends Nette\Application\UI\Presenter
{
    private $database;

    function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    /** Formular pro prihlaseni
     * @return Form
     */
    protected function createComponentLoginLoginForm() : Form
    {
        $form = new Form;

        $form->addText('username','Username :' )
            ->setRequired('Zadejte přihlašovací jméno');

        $form->addPassword('password','Heslo :' )
            ->setRequired('Zadejte přihlašovací heslo');

        $form->addSubmit('login','Přihlásit');

        $form->onSuccess[] = [$this, 'loginFormSucceeded'];

        return $form;
    }

    /** Zpracovani prihlaseni uzivatele
     * @param Form $form
     * @param array $values
     */
    public function loginFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->getUser()->login($values["username"], $values["password"]);
            $this->redirect("Homepage:");
        }
        catch(Nette\Security\AuthenticationException $excetion){
            $this->flashMessage("Chyba při přihlášení : ".$excetion->getMessage().". Zkuste se prosím přihlásit znovu.");
        }

    }

    /** Formular odeslani emailu pro generovani noveho hesla
     * @return Form
     */
    protected function createComponentLoginForgottenForm() : Form
    {
        $form = new Form;

        $form->addText('username','Email použitý při registraci :')
            ->setRequired('Zadejte email použitý při registraci');

        $form->addSubmit('sendForgotten','Odeslat');

        $form->onSuccess[] = [$this, 'forgottenFormSucceeded'];

        return $form;
    }
}


?>