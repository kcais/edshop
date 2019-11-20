<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\CategoryManager;
use Nette;


final class HomepagePresenter extends BasePresenter
{
    public function renderProducts($categoryId)
    {
        $this->template->id = $categoryId;
    }
}
