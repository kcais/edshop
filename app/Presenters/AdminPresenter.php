<?php

namespace App\Presenters;

use App\Model\Database\Entity\Category;
use App\Model\Database\Entity\Image;
use App\Model\Database\Entity\Product;
use App\Model\Database\Entity\User;
use Nette;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use RuntimeException;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;


final class AdminPresenter extends BasePresenter//Nette\Application\UI\Presenter
{

    /** Metoda volana pri vytvoreni presenteru, slouzi k overeni zda prihlaseny uzivatel ma admin roli
     * @throws Nette\Application\AbortException
     */
    protected function startup()
    {
        //volani metody startup predka
        parent::startup();

        //overeni , zda je prihlaseny uzivatel prislusny roli admin
        if (!$this->user->isInRole("admin")) {
            $this->flashMessage("Přihlášený uživatel nemá roli admin !", 'warning');
            $this->redirect("Homepage:");
        }
    }

    /** Formular pro zadani noveho produktu
     * @return Form
     */
    protected function createComponentAdminProdnewForm(): Form
    {
        $form = new Form;
        $categories = [];

        $catObjArr = $this->em->getCategoryRepository()->findBy(['deleted_on' => null]);

        foreach ($catObjArr as $catObj) {
            $categories[$catObj->getId()] = $catObj->getName();
        }
        $form->addSelect('category_id', 'Kategorie', $categories);

        $form->addText('name', 'Název')
            ->setRequired('Zadejte jméno produktu')
            ->setMaxLength(1024)
            ->addRule(Form::MIN_LENGTH, 'Název musí obsahovat nejméně 3 znaky', 3);

        $form->addTextArea('description', 'Popis')
            ->setRequired('Zadejte popis produktu')
            ->setMaxLength(1024);

        $form->addText('price', 'Cena(Kč)')
            ->setEmptyValue('0.00')
            ->setHtmlType('number')
            ->setRequired('Zadejte cenu produktu');

        $form->addUpload('imageFile','Obrázek');

        $form->addSubmit('add', 'Přidat');

        $form->onSuccess[] = [$this, 'adminProdnewFormSucceeded'];

        return $form;
    }

    /** Ulozeni noveho produktu
     * @param Form $form
     * @param array $values
     * @throws \Exception
     */
    public function adminProdnewFormSucceeded(Form $form, array $values): void
    {
        try{
            $category = $this->em->getCategoryRepository()->find($values['category_id']);
            $product = new Product($category, $values['name'], $values['description'], $values['price']);
            $this->em->persist($product);
            $this->em->flush();

            $imageFile = $values['imageFile'];

            //nahrani obrazku pokud byl pridan
            if ($imageFile->isOk() && filesize ($imageFile) > 0) { //kdyz je obrazek skutecne poslan z formulare
                $this->em->saveImageFromFile($product, $imageFile);
            }

            $this->redirect("Admin:newsuccess");
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /** Formular pro zadani nove kategorie
     * @return Form
     */
    protected function createComponentAdminCatnewForm(): Form
    {
        $form = new Form;

        $form->addText('name', 'Jméno kategorie :')
            ->setMaxLength(255)
            ->addRule(Form::MIN_LENGTH, 'Název musí obsahovat nejméně 3 znaky', 3)
            ->setRequired("Zadejte jméno kategorie");

        $form->addText('description', 'Popis kategorie')
            ->setRequired('Zadejte popis kategorie')
            ->setMaxLength(255);

        $form->addText('order', 'Pořadí kategorie')->setMaxLength(3);

        $form->addText('parent_cat_id', 'ID nadřazené kategorie');

        $form->addSubmit('create', 'Vytvořit');

        $form->onSuccess[] = [$this, 'adminCatnewFormSucceeded'];

        return $form;
    }

    /** Ulozeni nove kategorie zadane ve formulari
     * @param Form $form
     * @param array $values
     * @throws \Exception
     */
    public function adminCatnewFormSucceeded(Form $form, array $values): void
    {
        if (!$values['order']) $values['order'] = 1;
        if (!$values['parent_cat_id']) $values['parent_cat_id'] = null;

        try {
            $category = new Category($values["name"], $values["description"], $values["order"], $values["parent_cat_id"]);
            $this->em->persist($category);
            $this->em->flush();
            $this->redirect("Admin:newsuccess");
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /** Vypis kategorii v gridu
     * @param $name
     * @return DataGrid
     */
    protected function createComponentAdminCategoryGrid($name) : DataGrid
    {
        $catArr = null;

        $catObjArr = $this->em->getCategoryRepository()->findBy(['deleted_on' => null]);

        foreach($catObjArr as $catObj){
            $catArr[] = ['id' => $catObj->getId(),'name' => $catObj->getName(), 'description' => $catObj->getDescription()];
        }

        $grid = new DataGrid($this, $name);


        $grid->setDataSource($catArr);

        $grid->addColumnText('name', 'objectsGrid.name')
            ->setSortable()
            ->setEditableCallback(function($id, $value): void {
                $catObj = $this->em->getCategoryRepository()->find($id);
                $catObj->setName($value);
                $this->em->merge($catObj);
                $this->em->flush();
            });
        ;

        $grid->addColumnText('description', 'objectsGrid.description')
            ->setEditableCallback(function($id, $value): void {
                $catObj = $this->em->getCategoryRepository()->find($id);
                $catObj->setDescription($value);
                $this->em->merge($catObj);
                $this->em->flush();
            });
        ;


        $grid->addAction('markDelCat','Set deleted','MarkDelCat!')
            ->setClass('btn btn-primary')
            ->setConfirmation(
                new StringConfirmation('Skutečně označit kategorii %s jako deleted_on ?', 'name') // Second parameter is optional
            );
        ;

        $grid->addAction('delCat','Del DB','DelCat!')
            ->setClass('btn btn-primary')
            ->setConfirmation(
                new StringConfirmation('Skutečně smazat kategorii %s z DB ?', 'name') // Second parameter is optional
            );
        ;

        $grid->setTranslator(new \TranslatorCz('CZ'));

        return $grid;
    }

    /** Datagrid s uzivateli
     * @param $name
     * @return DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    protected function createComponentAdminUserGrid($name) : DataGrid
    {
        $userArr = null;

        $userObjArr = $this->em->getUserRepository()->findAll();

        foreach($userObjArr as $userObj){

            $userObj->getCreatedOn()?$createdOn = $userObj->getCreatedOn()->format('Y-m-d H:i:s'):$createdOn = null;
            $userObj->getUpdatedOn()?$updatedOn = $userObj->getUpdatedOn()->format('Y-m-d H:i:s'):$updatedOn = null;
            $userObj->getDeletedOn()?$deletedOn = $userObj->getDeletedOn()->format('Y-m-d H:i:s'):$deletedOn = null;


            $userArr[] = [
                'id' => $userObj->getId(),
                'username' => $userObj->getUsername(),
                'firstname' => $userObj->getFirstname(),
                'surname' => $userObj->getSurname(),
                'email' => $userObj->getEmail(),
                'language' => $userObj->getLanguage(),
                'isAdmin' => $userObj->isAdmin(),
                'isActive' => $userObj->isActive(),
                'registrationMailSended' => $userObj->isRegistrationMailSended(),
                'createdOn' => $createdOn,
                'updatedOn' => $updatedOn,
                'deletedOn' => $deletedOn,
            ];
        }

        $grid = new DataGrid($this, $name);

        $grid->setDataSource($userArr);

        $grid->addColumnText('username', 'userGrid.username')
            ->setSortable()
        ;

        $grid->addColumnText('firstname', 'userGrid.firstname')
            ->setSortable()
            ->setEditableCallback(function($id, $value): void {
                $userObj = $this->em->getUserRepository()->find($id);
                $userObj->setFirstname($value);
                $this->em->merge($userObj);
                $this->em->flush();
            })
        ;

        $grid->addColumnText('surname', 'userGrid.surname')
            ->setSortable()
            ->setEditableCallback(function($id, $value): void {
                $userObj = $this->em->getUserRepository()->find($id);
                $userObj->setSurname($value);
                $this->em->merge($userObj);
                $this->em->flush();
            })
        ;

        $grid->addColumnText('email', 'userGrid.email')
            ->setSortable()
            ->setEditableCallback(function($id, $value): void {
                $userObj = $this->em->getUserRepository()->find($id);
                $userObj->setEmail($value);
                $this->em->merge($userObj);
                $this->em->flush();
            })
        ;

        $grid->addColumnText('language', 'userGrid.language')
        ;

        $grid->addColumnText('isAdmin', 'userGrid.isAdmin')
            ->setSortable()
            ->setEditableCallback(function($id, $value): void {
                $userObj = $this->em->getUserRepository()->find($id);
                $userObj->setIsAdmin($value);
                $this->em->merge($userObj);
                $this->em->flush();
            })
        ;

        $grid->addColumnText('isActive', 'userGrid.isActive')
            ->setSortable()
            ->setEditableCallback(function($id, $value): void {
                $userObj = $this->em->getUserRepository()->find($id);
                $userObj->setIsActive($value);
                $this->em->merge($userObj);
                $this->em->flush();
            })
        ;

        $grid->addColumnText('registrationMailSended', 'userGrid.registrationMailSended')
            ->setSortable()
            ->setEditableCallback(function($id, $value): void {
                $userObj = $this->em->getUserRepository()->find($id);
                $userObj->setRegistrationMailSended($value);
                $this->em->merge($userObj);
                $this->em->flush();
            })
        ;

        $grid->addColumnText('createdOn', 'userGrid.createdOn')
            ->setSortable()
        ;

        $grid->addColumnText('updatedOn', 'userGrid.updatedOn')
            ->setSortable()
        ;

        $grid->addColumnText('deletedOn', 'userGrid.deletedOn')
            ->setSortable()
        ;

        $grid->addAction('markDelUser','Set deleted','MarkDelUser!')
            ->setClass('btn btn-primary')
            ->setConfirmation(
                new StringConfirmation('Skutečně označit uživatele %s jako deleted_on ?', 'username')
            );
        ;

        $grid->addAction('delUser','Del DB','DelUser!')
            ->setClass('btn btn-primary')
            ->setConfirmation(
                new StringConfirmation('Skutečně smazat product %s z DB ?', 'username')
            );
        ;

        $grid->setTranslator(new \TranslatorCz('CZ'));

        return $grid;
    }

    /** Datagrid objednavek
     * @param $name
     * @return DataGrid
     */
    protected function createComponentAdminOrderGrid($name) : DataGrid
    {
        $ordArr = null;

        $ordObjArr = $this->em->getOrderRepository()->findAll();

        foreach ($ordObjArr as $ordObj) {
            $ordObj->getCreatedOn() ? $createdOn = $ordObj->getCreatedOn()->format('Y-m-d H:i:s') : $createdOn = null;
            $ordObj->getUpdatedOn() ? $updatedOn = $ordObj->getUpdatedOn()->format('Y-m-d H:i:s') : $updatedOn = null;
            $ordObj->getDeletedOn() ? $deletedOn = $ordObj->getDeletedOn()->format('Y-m-d H:i:s') : $deletedOn = null;


            $userArr[] = [
                'id' => $ordObj->getId(),
                'username' => $ordObj->getUser()->getUsername(),
                'isClosed' => $ordObj->isClosed(),
                'createdOn' => $createdOn,
                'updatedOn' => $updatedOn,
                'deletedOn' => $deletedOn,
                'orderPrice' => $this->em->getOrderPrice($ordObj->getId(),true),
            ];
        }

        $grid = new DataGrid($this, $name);

        $grid->setDataSource($userArr);

        $grid->addColumnText('id', 'orderGrid.orderId')
            ->setSortable()
        ;
        $grid->addColumnText('username', 'orderGrid.username')
            ->setSortable()
        ;
        $grid->addColumnText('isClosed', 'orderGrid.isClosed')
            ->setSortable()
        ;
        $grid->addColumnText('createdOn', 'orderGrid.createdOn')
            ->setSortable()
        ;
        $grid->addColumnText('updatedOn', 'orderGrid.updatedOn')
            ->setSortable()
        ;
        $grid->addColumnText('deletedOn', 'orderGrid.deletedOn')
            ->setSortable()
        ;
        $grid->addColumnText('orderPrice', 'orderGrid.orderPrice')
            ->setRenderer(function ($row):String{return "$row[orderPrice] Kč";})
            ->setSortable()
        ;

        $grid->addAction('markDelOrd','Set deleted','MarkDelOrd!')
            ->setClass('btn btn-primary')
            ->setConfirmation(
                new StringConfirmation('Skutečně označit objednávku %s jako deleted_on ?', 'id')
            );
        ;

        $grid->addAction('delOrd','Del DB','DelOrd!')
            ->setClass('btn btn-primary')
            ->setConfirmation(
                new StringConfirmation('Skutečně smazat objednávku %s z DB ?', 'id')
            );
        ;


        $grid->setTranslator(new \TranslatorCz('CZ'));

        return $grid;
    }

    /** datagrid s produkty
     * @param $name
     * @return DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    protected function createComponentAdminProductGrid($name) : DataGrid
    {
        $prodArr = null;

        $prodObjArr = $this->em->getProductRepository()->findBy(['deleted_on' => null]);

        foreach($prodObjArr as $prodObj){
            $prodArr[] = [
                'id' => $prodObj->getId(),
                'category' => $prodObj->getCategory()->getName(),
                'name' => $prodObj->getName(),
                'description' => $prodObj->getDescription(),
                'price' => $prodObj->getPrice(),
            ];
        }

        $grid = new DataGrid($this, $name);

        $grid->setDataSource($prodArr);

        $grid->addColumnText('image', '')
            ->setTemplate(__DIR__ . '/templates/components/datagrid/grid.img-new.latte')
            ->setAlign('center')
        ;

        $grid->addColumnText('category', 'objectsGrid.category')
            ->setSortable()
        ;

        $grid->addColumnText('name', 'objectsGrid.name')
            ->setSortable()
            ->setEditableCallback(function($id, $value): void {
                $prodObj = $this->em->getProductRepository()->find($id);
                $prodObj->setName($value);
                $this->em->merge($prodObj);
                $this->em->flush();
            });
        ;

        $grid->addColumnText('description', 'objectsGrid.description')
            ->setEditableCallback(function($id, $value): void {
                $prodObj = $this->em->getProductRepository()->find($id);
                $prodObj->setDescription($value);
                $this->em->merge($prodObj);
                $this->em->flush();
            });
        ;

        $grid->addColumnText('price', 'objectsGrid.price')
            ->setEditableCallback(function($id, $value): void {
                $prodObj = $this->em->getProductRepository()->find($id);
                $prodObj->setPrice($value);
                $this->em->merge($prodObj);
                $this->em->flush();
            });
        ;

        $grid->addAction('markDelCat','Set deleted','MarkDelProd!')
            ->setClass('btn btn-primary')
            ->setConfirmation(
                new StringConfirmation('Skutečně označit product %s jako deleted_on ?', 'name')
            );
        ;

        $grid->addAction('delCat','Del DB','DelProd!')
            ->setClass('btn btn-primary')
            ->setConfirmation(
                new StringConfirmation('Skutečně smazat product %s z DB ?', 'name')
            );
        ;

        $grid->addFilterText('category', 'objectsGrid.category');
        $grid->addFilterText('name', 'objectsGrid.name')->setSplitWordsSearch(FALSE);
        $grid->addFilterText('description', 'objectsGrid.description');

        $grid->setTranslator(new \TranslatorCz('CZ'));

        return $grid;
    }

    /** Handle udalosti smazani produktu
     * @param int $id
     */
    public function handleDelProd(int $id)
    {
        $this->em->deleteProduct($id, true);
    }

    /** Handle udalosti oznaceni produktu jako smazany
     * @param int $id Id kategorie
     */
    public function handleMarkDelProd(int $id)
    {
        $this->em->deleteProduct($id, false);
    }

    /** Handle udalosti smazani objednávky
     * @param int $id
     */
    public function handleDelOrd(int $id)
    {
        $this->em->deleteOrder($id, true);
    }

    /** Handle udalosti oznaceni objednávky jako smazané
     * @param int $id Id kategorie
     */
    public function handleMarkDelOrd(int $id)
    {
        $this->em->deleteOrder($id, false);
    }

    /** Handle udalosti smazani kategorie
     * @param int $id
     */
    public function handleDelCat(int $id)
    {
        $this->em->deleteCategory($id, true);
    }

    /** Handle udalosti oznaceni kategorie jako smazane
     * @param int $id Id kategorie
     */
    public function handleMarkDelCat(int $id)
    {
        $this->em->deleteCategory($id,false);
    }

    /** Handle udalosti smazani uzivatele
     * @param int $id
     */
    public function handleDelUser(int $id)
    {
        $this->em->deleteUser($id, true);
    }

    /** Handle udalosti oznaceni uzivatele jako smazane
     * @param int $id Id kategorie
     */
    public function handleMarkDelUser(int $id)
    {
        $this->em->deleteUser($id,false);
    }

    /** Nahrani noveho obrazku pro produkt
     * @throws Nette\Utils\UnknownImageFileException
     */
    public function actionUpload(){
        if (
            !isset($_FILES['new_image_file']['error']) ||
            is_array($_FILES['new_image_file']['error'])
        ) {
            throw new RuntimeException('Invalid parameters.');
        }

        switch ($_FILES['new_image_file']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }

        if ($_FILES['new_image_file']['size'] > 1000000) {
            throw new RuntimeException('Exceeded filesize limit.');
        }

        $imageName = $_FILES['new_image_file']['tmp_name'];

        $idProd = $_POST['id'];

        $prodObj = $this->em->getProductRepository()->find($idProd);

        //produkt s id existuje
        if($prodObj){
            $this->em->saveImageFromFile($prodObj, $imageName);
        }

        die();
    }

    /** Smazani obrazku produktu
     * @param int $id Product id
     * @throws Nette\Application\AbortException
     */
    public function actionImagedel(int $id){

        if(!isset($id) && isset($_GET['id']))$id=$_GET['id'];

        $imageObjArr = $this->em->getImageRepository()->findBy(['product'=>$id]);

        if(sizeof($imageObjArr) > 0){
            $this->em->remove($imageObjArr[0]);
            $this->em->flush();
        }

        $this->redirect('Admin:prodedit');
    }

    /** Pridani noveho uzivatele z admin sekce
     * @return Form
     */
    public function createComponentAdminUsernewForm() : Form
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

        $form->addSubmit('add','Přidat');

        $form->onSuccess[] = [$this, 'userNewFormSucceeded'];

        return $form;
    }

    /** Ulozeni noveho uzivatele pres admin sekci
     * @param Form $form
     * @param array $values
     */
    public function userNewFormSucceeded(Form $form, array $values): void
    {
        $newUserObj = new User();
        $newUserObj->setUsername($values['username']);
        $newUserObj->setFirstname($values['firstname']);
        $newUserObj->setSurname($values['lastname']);
        $newUserObj->setEmail($values['email']);
        $newUserObj->setPasswordHash(hash('sha256',$values['pass1']));
        $newUserObj->setIsAdmin(0);
        $newUserObj->setIsActive(1);
        $newUserObj->setLanguage('CZ');
        $newUserObj->setRegistrationMailSended(0);

        $this->em->persist($newUserObj);
        $this->em->flush();

        $this->redirect('Admin:newsuccess');
    }

}

?>