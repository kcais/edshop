<?php

namespace App\Presenters;

use App\Model\CategoryManager;
use App\Model\Database\EntityManagerDecorator;
use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var EntityManagerDecorator @inject */
    public $em;

    private $categoryManager;

    protected function startup()
    {
        parent::startup();
        $this->template->basketPrice = $this->getSession()->getSection(\App\Common\Common::getSelectionName())->basketPrice;

        $categoryRepository = $this->em->getCategoryRepository();
        $catObjArr = $categoryRepository->findby([],['name' => 'ASC']);
        $category2 = null;
        foreach ($catObjArr as $catObj){
            $category2[]=['id' => $catObj->getId(), 'name' => $catObj->getName(), 'description' => $catObj->getDescription()];
        }

        $this->template->category2 = $category2;
        $this->template->categories = $category2;
    }

    public function injectCategoryManager(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }
}

?>