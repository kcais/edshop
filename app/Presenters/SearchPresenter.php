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


        $searchRes[] = ['id' => 'categories', 'text' => 'Kategorie_________________________________________________', 'disabled' => true];

        foreach($catObjArr as $catObj){
            $searchRes[] =  ['id' => "/homepage/products?categoryId=".$catObj->getId(), 'text' => $catObj->getName()];
        }

        //produkty
        $q = $this->em->createQuery("select prod from \App\Model\Database\Entity\Product prod where prod.name like :searchterm or prod.description like :searchterm")
            ->setParameter('searchterm', '%'.$searchStr.'%')
        ;
        $prodObjArr = $q->execute();

        $searchRes[] = ['id' => 'products', 'text' => 'Produkty', 'disabled' => true];
        foreach($prodObjArr as $prodObj){
            $searchRes[] =  ['id' => "/homepage/products?id=".$prodObj->getId(), 'text' => $prodObj->getName()];
        }


        //echo Json::Encode(['results'=>[['id' => 'AK', 'text' => 'hodnota']]]);
        echo Json::Encode(['results'=>$searchRes]);
        //echo '{"results" : {{"id" : 1 , "text" : "hodnota"}}}';
        die();
    }
}