<?php

namespace App\Presenters;


use Nette;
use Nette\Application\UI\Form;
use App\Model\UserManager;

final class LoginPresenter extends Nette\Application\UI\Presenter
{
    private $database;
    private $userManager;

    function __construct(Nette\Database\Context $database, UserManager $userManager)
    {
        $this->database = $database;
        $this->userManager = $userManager;
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
            $existUsername = $this->userManager->usernameExist($values["username"]);

            if($existUsername && $this->userManager->isUserActivated($values["username"])) {
                $this->getUser()->login($values["username"], $values["password"]);
                $this->redirect("Homepage:");
            }
            elseif($existUsername){
                $this->flashMessage('Uživatelský účet ještě nebyl přes email s odkazem aktivován.','warning');
            }
            else{
                $this->flashMessage('Uživatelský účet neexistuje nebo bylo zadáno chybné heslo.','warning');
            }
        }
        catch(Nette\Security\AuthenticationException $excetion){
            $this->flashMessage("Uživatelský účet neexistuje nebo bylo zadáno chybné heslo.",'warning');
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