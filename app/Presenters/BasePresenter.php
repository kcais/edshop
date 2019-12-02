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
        $this->template->category2 = $categoryRepository->findAll();
    }

    public function injectCategoryManager(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
        $this->template->categories = $this->categoryManager->getAllCategories();
    }
}

?>