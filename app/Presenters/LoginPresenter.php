<?php

namespace App\Presenters;


use App\Common\Common;
use Nette;
use Nette\Application\UI\Form;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

final class LoginPresenter extends BasePresenter
{
    public function renderForgottengen()
    {
        if(!isset($_GET["uuid"]) || sizeof($this->em->getUserRepository()->findBy(['uuid_lost_password' => $_GET["uuid"]])) == 0 ){
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

            $user = $this->em->getUserRepository()->findBy(['username' => $values['username']]);

            sizeof($user) == 1?$existUsername = 1:$existUsername = 0;

            if($existUsername && $user[0]->isActive()) {
                $this->getUser()->login($values["username"], $values["password"]);

                $basket = New \Basket($this, $this->em);
                $basket->fromSessionToDb();
                $this->template->basketPrice = $basket->calculateBasketPrice();

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
        $userObjArr = $this->em->getUserRepository()->findBy(['uuid_lost_password' => $section->uuid]);

        $userObjArr[0]->setUuidLostPassword(null);
        $userObjArr[0]->setPasswordHash(hash('sha256',$values['pass1']));

        $this->em->flush();

        //nastaveni noveho hesla a smazani uuid pro ztracene heslo

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
        $userObjArr = $this->em->getUserRepository()->findBy(['email' => $values["email"]]);

        if(sizeof($userObjArr)){
            //osetreni unikatnosti UUID, pokud jiz existuje vygeneruje nove - pokud se nepovede ani na 10 pokus, pokracuje - dale spadne na vyjimku unikatnosti klice
            for($a=0;$a<10;$a++){
                $uuid = Common::generateUUID();
                if(!$this->em->existUserUUID($uuid,'uuid_lost_password')){
                    break;
                }
            }

            //nastaveni noveho uuid pro obnovu hesla
            $userObjArr[0]->setUuidLostPassword($uuid);
            $this->em->flush();

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