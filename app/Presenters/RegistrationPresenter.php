<?php

namespace App\Presenters;

use App\Common\Common;
use Nette;
use Nette\Application\UI\Form;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

final class RegistrationPresenter extends BasePresenter
{

    /**
     * @throws Nette\Application\AbortException
     */
    public function renderVerification(){
        $validation = $this->em->activateUser($_GET['uuid']);
        if($validation) {
            $this->redirect("Registration:validated");
        }
        else{
            $this->redirect("Registration:error");
        }
    }

    /**
     * Registracni formular noveho uzivatele
     */
    protected function createComponentRegistrationNewForm() : Form
    {
        $form = new Form;
        $form->addText('username','Uživatelské jméno :')
            ->setMaxLength(255)
            ->setRequired("Zadejte uživatelské jméno")
            ->addRule(Form::MIN_LENGTH,'Uživatelské jméno musí mít minimálně 3 znaky',3)
            ->addRule(Form::PATTERN, 'Uživatelské jméno může obsahovat jen písmena, čísla a znaky "-", "_".', '^[a-zA-Z0-9_-]*$');
        ;

        $form->addText('firstname','Vaše jméno :')
            ->setMaxLength(255)
            ->setRequired("Zadejte Vaše jméno");

        $form->addText('lastname','Vaše příjmení :')
            ->setMaxLength(255)
            ->setRequired("Zadejte Vaše příjmení");

        $form->addText('email','E-mail :')
            ->setMaxLength(1024)
            ->addRule(Form::EMAIL,'Zadaný email nemá validní tvar')
            ->setRequired("Zadejte registrační email");

        $form->addPassword('pass1','Heslo :')
            ->setMaxLength(255)
            ->setRequired("Zadejte heslo")
            ->addRule(Form::MIN_LENGTH,'Heslo musí obsahovat minimálně 3 znaky',3)
        ;

        $form->addPassword('pass2','Heslo znovu :')
            ->setMaxLength(255)
            ->setRequired("Zadejte heslo pro potvrzení")
            ->addRule(Form::EQUAL,'Zadaná hesla se neshodují',$form['pass1'])
        ;

        $form->addSubmit('send','Registrovat');

        $form->onSuccess[] = [$this, 'registrationFormSucceeded'];

        $lang = $this->getSession()->getSection(\App\Common\Common::getSelectionName())->language;
        if(!isset($lang))$lang='CZ';

        $form->setTranslator(new \Translator($lang));

        return $form;
    }

    /** Ulozeni noveho uzivatele
     * @param Form $form
     * @param array $values
     */
    public function registrationFormSucceeded(Form $form, array $values): void
    {
            //osetreni unikatnosti UUID, pokud jiz existuje vygeneruje nove - pokud se nepovede ani na 10 pokus, pokracuje - dale spadne na vyjimku unikatnosti klice
            for($a=0;$a<10;$a++){
                $uuid = Common::generateUUID();
                if(!$this->em->existUserUUID($uuid)){
                   break;
                }
            }

            //vytvoreni noveho uzivatelskeho uctu pres model
            $ret_val = $this->em->createUser(
                $values['username'],
                $values['firstname'],
                $values['lastname'],
                $values['pass1'],
                $values['email'],
                $uuid
                );
            
            switch($ret_val){
                case 0:
                    //sestaveni a odeslani mailu pro dokonceni registrace
                    $translator = new \Translator($this->getSession()->getSection(\App\Common\Common::getSelectionName())->language);

                    $mail = new Message;
                    $mail->setFrom(\App\Common\Common::getEmailFrom())
                        ->addTo($values['email'])
                        ->setSubject($translator->translate('Dokončení registrace - EdShop'))
                        ->setHtmlBody($translator->translate('Dobrý den,<br><br>Vaše registrace na stránky EdShop byla dokončena.
                        Pro aktivaci účtu klikněte zde : <a href="').\App\Common\Common::getEshopUrl()."registration/verification?uuid=$uuid\">".$translator->translate('Aktivace')."</a>
                        <br><br>");

                    $mailer = new SendmailMailer;
                    $mailer->send($mail);

                    //nastaveni priznaku odeslani registracniho emailu
                    $userObjArr = $this->em->getUserRepository()->findby(['username' => $values['username']]);
                    if(sizeof($userObjArr)==1){
                        $userObjArr[0]->setRegistrationMailSended(true);
                        $this->em->flush();
                    }

                    //presmerovani na stranku s potvrzenim uspesne registrace
                    $this->redirect('success');

                    break;
                case 1:
                    $translator = new \Translator($this->getSession()->getSection(\App\Common\Common::getSelectionName())->language);
                    $this->flashMessage(
                        $translator->translate('Zadané uživatelské jméno nebo email již existují ! Zadejte prosím jiné'),'error');
                    break;
            }

    }

    /**
     * Kontrola existence jmena uzivatelskeho uctu
     */
    public function actionCheckusername(){
        $username = $_POST['username'];

        if(strlen($username) > 2){
            $userObjArr = $this->em->getUserRepository()->findBy(['username' => $username]);
            if(sizeof($userObjArr) > 0){//uzivatelske jmeno jiz existuje
                echo '{"userNameExist" : 1}';
            }
            else{ //uzivatelske jmeno neexistuje
                echo '{"userNameExist" : 0}';
            }
        }
        die();
    }


}


?>