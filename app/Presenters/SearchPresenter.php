<?php

namespace App\Presenters;

use Nette\Utils\Json;

final class SearchPresenter extends BasePresenter
{
    /**
     * @throws \Nette\Utils\JsonException
     */
    public function renderDefault()
    {
        $vars = $this->request->getParameters();

        //neni zadan vyhledavaci retezec
        if(!isset($vars['search']))die('Neni definovana promenna search');

        //zadany retezec je kratsi nez xx znaky
        if(strlen($vars['search'])<2)die('Promenna search je mensi nez 2 znaky');

        //
        $searchStr = $vars['search'];

        //kategorie
        $q = $this->em->createQuery("select cat from \App\Model\Database\Entity\Category cat where cat.name like :searchterm")
            ->setParameter('searchterm', '%'.$searchStr.'%')
        ;
        $catObjArr = $q->execute();

        $searchRes = null;
        $searchResFinal = null;

        if(sizeof($catObjArr) > 0) {

            foreach ($catObjArr as $catObj) {
                $searchRes[] = ['id' => "/homepage/products?categoryId=" . $catObj->getId(), 'text' => $catObj->getName()];
            }
            $searchResFinal[] = ['text' => 'Kategorie', 'children' => $searchRes];
        }



        //produkty
        $q = $this->em->createQuery("select prod from \App\Model\Database\Entity\Product prod where prod.name like :searchterm or prod.description like :searchterm")
            ->setParameter('searchterm', '%'.$searchStr.'%')
        ;
        $prodObjArr = $q->execute();

        if(sizeof($prodObjArr) > 0) {
            $searchRes = null;

            foreach ($prodObjArr as $prodObj) {
                $searchRes[] = ['id' => "/homepage/products?id=" . $prodObj->getId(), 'text' => $prodObj->getName()];
            }
            $searchResFinal[] = ['text' => 'Produkty', 'children' => $searchRes];
        }

        echo Json::Encode(['results'=>$searchResFinal]);
        die();
    }
}