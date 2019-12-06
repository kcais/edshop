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

            //nacti obrazek z db pokud existuje
            if(isset($imageObj[0])){
                switch($type){
                    case 'mini':
                        $imageRes = $imageObj[0]->getImageMini();
                        break;
                    case 'normal':
                        $imageRes = $imageObj[0]->getImageNormal();
                        break;
                    case 'icon':
                        $imageRes = $imageObj[0]->getImageIcon();
                        break;
                }

                header("Content-type: image/jpeg");
                echo stream_get_contents($imageRes);
            }
            else{ //obrazek neni ulozen v db, pouzij NA image

                switch($type) {
                    case 'mini':
                        $imageFile = './img/na-mini.jpg';
                        break;
                    case 'normal':
                        $imageFile = './img/na-normal.jpg';
                        break;
                    case 'icon':
                        $imageFile = './img/na-icon.jpg';
                        break;
                }

                $fh = fopen($imageFile, "rb");
                $imageData = fread($fh, filesize($imageFile));
                fclose($fh);

                header("Content-type: image/jpeg");
                echo $imageData;
            }

        }
        die();
    }
}