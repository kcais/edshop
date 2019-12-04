<?php

namespace App\Common;
use App\Model\Database\EntityManagerDecorator;
use Nette;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;

/**
 * Class Common
 * Spolecne funkce
 * @package App\Common
 */
 class Common{
      /** Funkce generujici UUID v4
       * @return string
       */
    static public function generateUUID():string
    {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

     /** Vrati nazev selection v session - pro pouziti na vice mistech s pripadnou zmenou na jednom
      * @return String
      */
    static public function getSelectionName() : String
    {
        return 'edshop';
    }

     /** Vrati email, z ktereho jsou odesilany registracni / potvrzovaci / objednavkove maily
      * @return String
      */
    static public function getEmailFrom() : String
    {
        return 'edshop@edshop.cz';
    }

     /** Vrati url eshopu
      * @return string
      */
    static public function getEshopUrl()
    {
        return 'https://edshop.php5.cz/www/';
    }
}

/** Trida resici autentifikaci uzivatele
 * Class Authenticator
 * @package App\Common
 */
class Authenticator implements Nette\Security\IAuthenticator
{
    //private $database;
    private $em;

    /*public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }*/
    public function __construct(EntityManagerDecorator $em)
    {
        $this->em = $em;
    }

    /** Autentifikace uzivatele
     * @param array $credentials
     * @return Nette\Security\IIdentity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials): Nette\Security\IIdentity
    {
        [$username, $password] = $credentials;

        $userObjArr = $this->em->getUserRepository()->findBy(['username' => $username]);

        if (!sizeof($userObjArr)) {
            throw new Nette\Security\AuthenticationException("Uživatel $username nenalezen");
        }

        if (hash('sha256', $password) != $userObjArr[0]->getPasswordHash()) {
            throw new Nette\Security\AuthenticationException('Chybné heslo');
        }

        $userObjArr[0]->isAdmin()?$role="admin":$role=null;

        return new Nette\Security\Identity($userObjArr[0]->getId(), [$role], ['username' => $username]);
    }

}


?>