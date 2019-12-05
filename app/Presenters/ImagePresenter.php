<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\Responses\FileResponse;
use Ublaboo\DataGrid\DataGrid;

final class ImagePresenter extends BasePresenter
{
    /** Vykresleni obrazku k produktu podle patametru (icon, mini, normal)
     * @param $id Product id
     */
    public function renderShow($id)
    {

        $type=$this->request->getParameter('type');

        //pokud neni definovan typ, pouzije se mini velikost
        if(!isset($type) || $type!= 'icon' && $type!= 'mini' && $type!='normal'){
            $type = 'mini';
        }

        if(isset($id)){
            $imageObj = $this->em->getImageRepository()->findby(['product' => $id, 'deleted_on' => null]);
            if(isset($imageObj[0])){
                switch($type){
                    case 'mini':
                        $imageData = $imageObj[0]->getImageMini();
                        break;
                    case 'normal':
                        $imageData = $imageObj[0]->getImageNormal();
                        break;
                    case 'icon':
                        $imageData = $imageObj[0]->getImageIcon();
                        break;
                }

                header("Content-type: image/jpeg");
                echo stream_get_contents($imageData);

            }
        }
        die();
    }
}