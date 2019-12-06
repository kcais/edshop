<?php

namespace App\Presenters;

use App\Model\Database\Entity\Category;
use App\Model\Database\Entity\Image;
use App\Model\Database\Entity\Product;
use Nette;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
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

    /** Formular ro zadani noveho produktu
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

            $imageFile = $values['imageFile'];

            //nahrani obrazku pokud byl pridan
            if ($imageFile->isOk() && filesize ($imageFile) > 0) { //kdyz je obrazek skutecne poslan z formulare

                $imageFile = $values['imageFile'];

                $imageObj = \Nette\Utils\Image::fromFile($imageFile);
                //vytvoreni ikony z normal velikosti
                $imageIconObj = clone $imageObj;
                $imageIconObj->resize(120,null);

                //vytvoreni mini z normal velikosti
                $imageMiniObj = clone $imageObj;
                $imageMiniObj->resize(320,null);

                //ulozeni dat obrazku do db
                $imageDbObj = new Image();
                $imageDbObj->setImageIcon((string)$imageIconObj);
                $imageDbObj->setImageMini((string)$imageMiniObj);
                $imageDbObj->setImageNormal((string)$imageObj);

                $imageDbObj->setProduct($product);

                $this->em->persist($imageDbObj);
            }

            $this->em->flush();
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

    protected function createComponentAdminProductGrid($name) : DataGrid
    {
        $prodArr = null;

        $prodObjArr = $this->em->getProductRepository()->findBy(['deleted_on' => null]);

        foreach($prodObjArr as $prodObj){
            $prodArr[] = ['id' => $prodObj->getId(),'category' => $prodObj->getCategory()->getName(),'name' => $prodObj->getName(), 'description' => $prodObj->getDescription()];
        }

        $grid = new DataGrid($this, $name);


        $grid->setDataSource($prodArr);

        $grid->addColumnText('category', 'objectsGrid.category')
            ->setSortable()
        ;

        $grid->addColumnText('name', 'objectsGrid.name')
            ->setSortable()
            ->setEditableCallback(function($id, $value): void {

            });
        ;

        $grid->addColumnText('description', 'objectsGrid.description')
            ->setEditableCallback(function($id, $value): void {

            });
        ;

        $grid->addAction('markDelCat','Set deleted','MarkDelProd!')
            ->setClass('btn btn-primary')
            ->setConfirmation(
                new StringConfirmation('Skutečně označit product %s jako deleted_on ?', 'name') // Second parameter is optional
            );
        ;

        $grid->addAction('delCat','Del DB','DelProd!')
            ->setClass('btn btn-primary')
            ->setConfirmation(
                new StringConfirmation('Skutečně smazat product %s z DB ?', 'name') // Second parameter is optional
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

}

?>