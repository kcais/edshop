<?php
namespace App\Presenters;

use App\Common\Common;
use App\Model\UserManager;
use Nette;
use Nette\Application\UI\Form;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

final class RegistrationPresenter extends BasePresenter//Nette\Application\UI\Presenter
{

    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function renderVerification(){
        $validation = $this->userManager->activateUser($_GET['uuid']);
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
            ->setRequired("Zadejte uživatelské jméno");

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
                if(!$this->userManager->existUserUUID($uuid)){
                   break;
                }
            }

            //vytvoreni noveho uzivatelskeho uctu pres model
            $ret_val = $this->userManager->createUser(
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
                    $mail = new Message;
                    $mail->setFrom(\App\Common\Common::getEmailFrom())
                        ->addTo($values['email'])
                        ->setSubject('Dokončení registrace - EdShop')
                        ->setHtmlBody("Dobrý den,<br><br>Vaše registrace na stránky EdShop byla dokončena.
                        Pro aktivaci účtu klikněte zde : <a href=\"".\App\Common\Common::getEshopUrl()."registration/verification?uuid=$uuid\">Aktivace</a>
                        <br><br>");

                    $mailer = new SendmailMailer;
                    $mailer->send($mail);

                    $this->redirect('success');


                    //presmerovani na stranku s potvrzenim uspesne registrace
                    break;
                case 1:
                    $this->flashMessage('Zadané uživatelské jméno nebo email již existují ! Zadejte prosím jiné','error');
                    break;
            }

    }


}


?>