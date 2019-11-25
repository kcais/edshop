<?php

declare(strict_types=1);

namespace App\Presenters;

final class BasketPresenter extends BasePresenter
{
    private $objectManager;


    /**
     *  Renderer prodejniho kosiku
     */
    function renderDefault()
    {
        $session = $this->getSession();
        $section = $session->getSection('edshop');
        if (isset($section->basket)) {
            $this->template->basket = $section->basket;
        } else {
            $this->template->basket = "";
        }

    }
}