<?php

namespace App\Presenters;

use App\Model\CategoryManager;
use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    private $categoryManager;

    protected function startup()
    {
        parent::startup();
        $this->template->basketPrice = $this->getSession()->getSection(\App\Common\Common::getSelectionName())->basketPrice;
    }

    public function injectCategoryManager(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
        $this->template->categories = $this->categoryManager->getAllCategories();
    }
}

?>