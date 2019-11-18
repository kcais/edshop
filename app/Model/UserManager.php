<?php

namespace App\Model;

use Nette;

class UserManager
{
    use Nette\SmartObject;

    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    /** Vytvoreni noveho uzivatelskeho uctu
     * @param $username Uzivatelske jmeno zakaznika
     * @param $firstname Jmeno zakaznika
     * @param $surname Prijmeni zakaznika
     * @param $password Heslo v ciste forme
     * @param $email Email zakaznika
     * @return int Pocet vlozenych radku
     */
    public function createUser($username,$firstname,$surname,$password, $email,$uuid_registration=null)
    {
        try {
            $this->database->table('users')->insert(
                [
                    "username" => $username,
                    "firstname" => $firstname,
                    "surname" => $surname,
                    "password_hash" => hash('sha256', $password),
                    "email" => $email,
                    "uuid_registration" => $uuid_registration,
                ]
            );
            return 0;
        }
        catch(Nette\Database\UniqueConstraintViolationException $e){
            return 1; //zadane uzivatelske jmeno jiz existuje
        }
    }

    /** Oznaci uzivatele jako aktivniho na zaklade UUID
     * @param $uuid
     * @return int Vraci pocet zmenenych radku
     */
    public function activateUser($uuid)
    {
        return $this->database->table('users')
            ->where('uuid_registration = ',$uuid)
            ->where('is_active = ',0)
            ->update(['is_active'=>1,'updated_on'=>new Nette\Utils\DateTime()]);
    }

    /** Vraci stav aktivace uzivatele
     * @param $username
     * @return int 0 - uzivatel nebyl aktivovan , 1 - uzivatel byl aktivovan
     */
    public function isUserActivated(String $username) : int
    {
        $selections = $this->database->table('users')
            ->where('username',$username)
            ->where('deleted_on', null)
            ->where('is_active',1);

        if(count($selections)){
            return 1;
        }
        else{
            return 0;
        }
    }

    /** Testuje existenci username v db
     * @param String $username
     * @return int Vraci 1 pokud username jiz existuje(i pokud je deleted_on), pokud ne 0
     */
    public function usernameExist(String $username):int
    {
        if(count($this->database->table('users')->select('username')->where('username',$username))){
            return 1;
        }
        else{
            return 0;
        }
    }

    /**Kontrola existence UUID v tabulce users v db
     * @param $uuid Hledane uuid
     * @param string $cell Kde hledat uuid
     * @return int Vraci 0 pokud nenaslo uuid, jinak 1
     */
    public function existUserUUID(String $uuid, $cell='uuid_registration') : int
    {
        if(count($this->database->table('users')->select('id')->where($cell,$uuid))){
            return 1;
        }
        else{
            return 0;
        }
    }

}