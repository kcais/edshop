<?php

namespace App\Presenters;

use App\Model\CategoryManager;
use Nette;
use Nette\Application\UI\Form;


final class AdminPresenter extends BasePresenter//Nette\Application\UI\Presenter
{
    private $categoryManager;

    function __construct(CategoryManager $categoryManager)
    {
        parent::__construct();
        $this->categoryManager = $categoryManager;
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

    protected function createComponentAdminCatnewForm() : Form
    {
        $form = new Form;

        $form->addText('name','Jméno kategorie :')
            ->setRequired("Zadejte jméno kategorie");

        $form->addText('comment','Popis kategorie')
            ->setRequired('Zadejte popis kategorie');

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