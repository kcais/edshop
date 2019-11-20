<?php

namespace App\Presenters;

use App\Model\CategoryManager;
use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    private $categoryManager;

    public function injectCategoryManager(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
        $this->template->categories = $this->categoryManager->getAllCategories();
    }
}

?>