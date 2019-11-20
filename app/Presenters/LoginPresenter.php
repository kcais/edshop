<?php

namespace App\Presenters;


use App\Common\Common;
use Nette;
use Nette\Application\UI\Form;
use App\Model\UserManager;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

final class LoginPresenter extends BasePresenter//Nette\Application\UI\Presenter
{
    private $database;
    private $userManager;

    function __construct(Nette\Database\Context $database, UserManager $userManager)
    {
        $this->database = $database;
        $this->userManager = $userManager;
    }

    public function renderForgottengen()
    {
        if(!isset($_GET["uuid"]) || !$this->userManager->existUserUUID($_GET["uuid"], 'uuid_lost_password')){
            $this->redirect("Homepage:");
        }
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
        catch(Nette\Security\AuthenticationException $exception){
            $this->flashMessage("Uživatelský účet neexistuje nebo bylo zadáno chybné heslo.",'warning');
        }

    }

    /** Formular zadani noveho hesla
     * @return Form
     */
    protected function createComponentLoginForgottengenForm() : Form
    {
        $form = new Form;

        $form->addPassword('pass1','Nové heslo :')
            ->setRequired('Zadejte nové heslo')
            ->addRule(Form::MIN_LENGTH,'Heslo musí obsahovat minimálně 3 znaky',3)
        ;

        $form->addPassword('pass2','Nové heslo znovu:')
            ->setRequired('Zadejte znovu nové heslo')
            ->addRule(Form::EQUAL,'Zadaná hesla se neshodují',$form['pass1'])
        ;

        //if(isset($_GET['uuid']))$form->addHidden('uuid',$_GET['uuid']);

        if(isset($_GET['uuid'])) {
            $session = $this->getSession();
            $section = $session->getSection('edshop');
            $section->uuid = $_GET['uuid'];
        }

        $form->addSubmit('sendForgottenNew','Nastavit nové heslo');

        $form->onSuccess[] = [$this, 'forgottenNewFormSucceeded'];

        return $form;
    }

    public function forgottenNewFormSucceeded(Form $form, array $values) : void
    {
        $session = $this->getSession();
        $section = $session->getSection('edshop');
        $userid = $this->userManager->existUserUUID($section->uuid,'uuid_lost_password');

        //nastaveni noveho hesla a smazani uuid pro ztracene heslo
        $this->userManager->updateUser($userid,['password_hash'=>hash('sha256',$values['pass1']), 'uuid_lost_password'=>null]);

        $this->flashMessage('Heslo bylo změněno. Nyní se můžete přihlásit pomocí nového hesla.');
        $this->redirect("Login:login");
    }

    /** Formular odeslani emailu pro generovani noveho hesla
     * @return Form
     */
    protected function createComponentLoginForgottenForm() : Form
    {
        $form = new Form;

        $form->addText('email','Email použitý při registraci :')
            ->setRequired('Zadejte email použitý při registraci');

        $form->addSubmit('sendForgotten','Odeslat');

        $form->onSuccess[] = [$this, 'forgottenFormSucceeded'];

        return $form;
    }

    /** Generovani a zaslani mailu s odkazem na vytvoreni noveho hesla
     * @param Form $form
     * @param array $values
     * @throws Nette\Application\AbortException
     */
    public function forgottenFormSucceeded(Form $form, array $values) : void
    {
        $userId = $this->userManager->registrationemailExist($values["email"]);

        if($userId){
            //osetreni unikatnosti UUID, pokud jiz existuje vygeneruje nove - pokud se nepovede ani na 10 pokus, pokracuje - dale spadne na vyjimku unikatnosti klice
            for($a=0;$a<10;$a++){
                $uuid = Common::generateUUID();
                if(!$this->userManager->existUserUUID($uuid,'uuid_lost_password')){
                    break;
                }
            }

            //nastaveni noveho uuid pro obnovu hesla
            $this->userManager->setUserUUID($userId,$uuid);

            //sestaveni a odeslani mailu pro obnovu hesla
            $mail = new Message;
            $mail->setFrom("edshop@edshop.cz")
                ->addTo($values['email'])
                ->setSubject('Zapomenuté heslo - EdShop')
                ->setHtmlBody("Dobrý den,<br><br>posíláme odkaz pro obnovu zapomenutého hesla.
                        Pro změnu hesla klikněte zde : <a href='https://edshop.php5.cz/www/login/forgottengen?uuid=$uuid'>Změna hesla</a>
                        <br><br>");

            $mailer = new SendmailMailer;
            $mailer->send($mail);

            $this->flashMessage('Na Vaší emailovou adresu byly odeslány instrukce pro obnovu hesla.');
            $this->redirect("Homepage:");

        }
        else{
            $this->flashMessage('Vámi zadaný registrační email neexistuje. Zkuste to prosím znovu.','warning');
            $this->redirect("Login:forgotten");
        }
    }
}


?>