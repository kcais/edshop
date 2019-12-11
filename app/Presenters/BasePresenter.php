<?php

namespace App\Presenters;

use App\Model\Database\EntityManagerDecorator;
use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var EntityManagerDecorator @inject */
    public $em;

    protected function startup()
    {
        parent::startup();
        $this->template->basketPrice = $this->getSession()->getSection(\App\Common\Common::getSelectionName())->basketPrice;

        $categoryRepository = $this->em->getCategoryRepository();
        $catObjArr = $categoryRepository->findby(['deleted_on' => null],['name' => 'ASC']);
        $category = null;
        $parCategory = null;
        $childParCat = null;
        foreach ($catObjArr as $catObj){
            $catObj->getParentCat()?$parCatId = $catObj->getParentCat()->getId():$parCatId = null;

            if($parCatId){
                $parCategory[$parCatId][] = $this->em->getCategoryRepository()->find($catObj->getId());
                $childParCat[$catObj->getId()]=$catObj->getParentCat();
            }
            else {
                $category[] = ['id' => $catObj->getId(), 'name' => $catObj->getName(), 'description' => $catObj->getDescription(), 'parent_cat_id' => $parCatId];
            }

        }

        $this->template->category2 = $category;
        $this->template->categories = $category;
        $this->template->par_category = $parCategory;
        $this->template->child_par_category = $childParCat;
    }

}

?>