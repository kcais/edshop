<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\CategoryManager;
use Nette;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{
    private $categoryManager;

    function __construct(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    /** Defaultni renderer, volano pri zobrazeni sablony Homepage
     *
     */
    public function renderDefault(): void
    {
        $this->template->categories = $this->categoryManager->getAllCategory();
    }

}
