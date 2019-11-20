<?php

namespace App\Presenters;

use App\Model\CategoryManager;
use App\Model\ObjectManager;
use Nette;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;


final class AdminPresenter extends BasePresenter//Nette\Application\UI\Presenter
{
    private $categoryManager;
    private $objectManager;

    function __construct(CategoryManager $categoryManager, ObjectManager $objectManager)
    {
        parent::__construct();
        $this->categoryManager = $categoryManager;
        $this->objectManager = $objectManager;
    }

    /** Metoda volana pri vytvoreni presenteru, slouzi k overeni zda prihlaseny uzivatel ma admin roli
     * @throws Nette\Application\AbortException
     */
    protected function startup()
    {
        //volani metody startup predka
        parent::startup();

        //overeni , zda je prihlaseny uzivatel prislusny roli admin
        if(!$this->user->isInRole("admin"))
        {
            $this->flashMessage("Přihlášený uživatel nemá roli admin !",'warning');
            $this->redirect("Homepage:");
        }
    }

    protected function createComponentAdminProdnewForm() : Form
    {
       $form = new Form;
       $categories=[];

       $selections = $this->categoryManager->getAllCategories();
       foreach($selections as $selection){
            $categories[$selection['id']]=$selection['name'];
       }
        $form->addSelect('category_id','Kategorie', $categories);

       $form->addText('name','Název')
           ->setRequired('Zadejte jméno produktu')
           ->setMaxLength(255)
           ->addRule(Form::MIN_LENGTH,'Název musí obsahovat nejméně 3 znaky',3)
       ;

       $form->addTextArea('description','Popis')
            ->setRequired('Zadejte popis produktu')
            ->setMaxLength(255)
       ;

       $form->addText('price','Cena(Kč)')
            ->setEmptyValue('0.00')
            ->setHtmlType('number')
            ->setRequired('Zadejte cenu produktu');

       $form->addSubmit('add','Přidat');

        $form->onSuccess[] = [$this, 'adminProdnewFormSucceeded'];

       return $form;
    }

    public function adminProdnewFormSucceeded(Form $form, array $values) : void
    {
        if($this->objectManager->createNewObject($values['category_id'],$values['name'],$values['description'], $values['price'])){
            $this->redirect("Admin:newsuccess");
        }
        else{
            $this->redirect("Admin:newerror");
        }
    }

    protected function createComponentAdminCatnewForm() : Form
    {
        $form = new Form;

        $form->addText('name','Jméno kategorie :')
            ->setMaxLength(255)
            ->addRule(Form::MIN_LENGTH,'Název musí obsahovat nejméně 3 znaky',3)
            ->setRequired("Zadejte jméno kategorie");

        $form->addText('description','Popis kategorie')
            ->setRequired('Zadejte popis kategorie')
            ->setMaxLength(255)
        ;

        $form->addText('order','Pořadí kategorie')->setMaxLength(3);

        $form->addText('parent_cat_id','ID nadřazené kategorie');

        $form->addSubmit('create','Vytvořit');

        $form->onSuccess[] = [$this, 'adminCatnewFormSucceeded'];

        return $form;
    }

    public function adminCatnewFormSucceeded(Form $form, array $values) : void
    {
        if(!$values['order'])$values['order']=1;
        if(!$values['parent_cat_id'])$values['parent_cat_id']=null;

        if($this->categoryManager->createNewCategory($values["name"],$values["comment"],$values["order"], $values["parent_cat_id"])){
            $this->redirect("Admin:newsuccess");
        }
        else{
            $this->redirect("Admin:newerror");
        }
    }
}

?>