<?php

class TranslatorCz implements \Nette\Localization\ITranslator
{
    private $language='CZ';

    public function __construct(String $language)
    {
        $this->language = $language;
    }

    public function translate($message, ...$parameters): string
    {
        switch($message){
            //ublaboo datagrid
            case 'ublaboo_datagrid.perPage_submit':
                if($this->language == 'CZ') return 'Nastavit počet položek na stránku';
                break;
            case 'ublaboo_datagrid.items':
                if($this->language == 'CZ') return 'Zobrazené prodejní položky';
                break;
            case 'ublaboo_datagrid.from':
                if($this->language == 'CZ') return 'z celkových';
                break;
            case 'ublaboo_datagrid.reset_filter':
                if($this->language == 'CZ') return 'Reset filtru';
                break;
            case 'ublaboo_datagrid.all':
                if($this->language == 'CZ') return 'Všechny';
                break;
            case 'ublaboo_datagrid.no_item_found':
                if($this->language == 'CZ') return 'Žádná položka nenalezena';
                break;
            case 'ublaboo_datagrid.next':
                if($this->language == 'CZ') return 'Další';
                break;
            case 'ublaboo_datagrid.previous':
                if($this->language == 'CZ') return 'Předchozí';
                break;
            // datagrid s prodejnimi polozkami
            case 'objectsGrid.description':
                if($this->language == 'CZ') return 'Popis';
                break;
            case 'objectsGrid.category':
                if($this->language == 'CZ') return 'Kategorie';
                break;
            case 'objectsGrid.name':
                if($this->language == 'CZ') return 'Název';
                break;
            case 'objectsGrid.price':
                if($this->language == 'CZ') return 'Cena';
                break;
            case 'ublaboo_datagrid.action':
                if($this->language == 'CZ') return '';
                break;
            case 'objectsGrid.pricePerPcs':
                if($this->language == 'CZ') return 'Cena / ks';
                break;
            case 'objectsGrid.pcs':
                if($this->language == 'CZ') return 'Ks';
                break;
            case 'objectsGrid.totalPrice':
                if($this->language == 'CZ') return 'Cena celkem';
                break;
        }
        return $message;
    }
}

?>