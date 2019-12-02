<?php

namespace App\Presenters;

use App\Model\CategoryManager;
use App\Model\Database\Entity\Category;
use App\Model\ObjectManager;
use Nette;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Ublaboo\DataGrid\DataGrid;


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
        if (!$this->user->isInRole("admin")) {
            $this->flashMessage("Přihlášený uživatel nemá roli admin !", 'warning');
            $this->redirect("Homepage:");
        }
    }

    /** Formular ro zadani noveho produktu
     * @return Form
     */
    protected function createComponentAdminProdnewForm(): Form
    {
        $form = new Form;
        $categories = [];

        $selections = $this->categoryManager->getAllCategories();
        foreach ($selections as $selection) {
            $categories[$selection['id']] = $selection['name'];
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

        $form->addSubmit('add', 'Přidat');

        $form->onSuccess[] = [$this, 'adminProdnewFormSucceeded'];

        return $form;
    }

    /** Ulozeni noveho produktu
     * @param Form $form
     * @param array $values
     * @throws Nette\Application\AbortException
     */
    public function adminProdnewFormSucceeded(Form $form, array $values): void
    {
        if ($this->objectManager->createNewObject($values['category_id'], $values['name'], $values['description'], $values['price'])) {
            $this->redirect("Admin:newsuccess");
        } else {
            $this->redirect("Admin:newerror");
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
        $grid->addColumnText('name', 'objectsGrid.name')->setSortable();
        $grid->addColumnText('description', 'objectsGrid.description');

        $grid->addAction('markDelCat','Set deleted','MarkDelCat!')
            ->setClass('btn btn-primary')
        ;

        $grid->addAction('delCat','Del DB','DelCat!')
            ->setClass('btn btn-primary')
        ;


        $grid->setTranslator(new \TranslatorCz('CZ'));

        return $grid;
    }

    /** Handle udalosti smazani kategorie
     * @param int $id
     */
    public function handleDelCat(int $id)
    {
        $catObj = $this->em->getCategoryRepository()->find($id);
        $this->em->remove($catObj);
        $this->em->flush();
    }

    /** Handle udalosti oznaceni kategorie jako smazane
     * @param int $id Id kategorie
     */
    public function handleMarkDelCat(int $id)
    {
        $catObj = $this->em->getCategoryRepository()->find($id);
        $catObj->setDeletedOn(new \DateTime('now'));
        $this->em->merge($catObj);
        $this->em->flush();
    }

}

?>