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
        $lang =  $this->language;
        switch($message){
            //ublaboo datagrid
            case 'ublaboo_datagrid.perPage_submit':
                if($lang == 'CZ') return 'Nastavit počet položek na stránku';
                break;
            case 'ublaboo_datagrid.items':
                if($lang == 'CZ') return 'Zobrazené prodejní položky';
                break;
            case 'ublaboo_datagrid.from':
                if($lang == 'CZ') return 'z celkových';
                break;
            case 'ublaboo_datagrid.reset_filter':
                if($lang == 'CZ') return 'Reset filtru';
                break;
            case 'ublaboo_datagrid.all':
                if($lang == 'CZ') return 'Všechny';
                break;
            case 'ublaboo_datagrid.no_item_found':
                if($lang == 'CZ') return 'Žádná položka nenalezena';
                break;
            case 'ublaboo_datagrid.next':
                if($lang == 'CZ') return 'Další';
                break;
            case 'ublaboo_datagrid.previous':
                if($lang == 'CZ') return 'Předchozí';
                break;
            // datagrid s prodejnimi polozkami
            case 'objectsGrid.description':
                if($lang == 'CZ') return 'Popis';
                break;
            case 'objectsGrid.category':
                if($lang == 'CZ') return 'Kategorie';
                break;
            case 'objectsGrid.name':
                if($lang == 'CZ') return 'Název';
                break;
            case 'objectsGrid.price':
                if($lang == 'CZ') return 'Cena';
                break;
            case 'ublaboo_datagrid.action':
                if($lang == 'CZ') return '';
                break;
            case 'objectsGrid.pricePerPcs':
                if($lang == 'CZ') return 'Cena / ks';
                break;
            case 'objectsGrid.pcs':
                if($lang == 'CZ') return 'Ks';
                break;
            case 'objectsGrid.totalPrice':
                if($lang == 'CZ') return 'Cena celkem';
                break;
            case 'objectsGrid.category_change':
                if($lang == 'CZ') return 'Změna kategorie';
                break;
            //datagrid s uzivateli
            case 'userGrid.username':
                if($lang == 'CZ') return 'Uživatelské jméno';
                break;
            case 'userGrid.firstname':
                if($lang == 'CZ') return 'Jméno';
                break;
            case 'userGrid.surname':
                if($lang == 'CZ') return 'Příjmení';
                break;
            case 'userGrid.email':
                if($lang == 'CZ') return 'E-mail';
                break;
            case 'userGrid.language':
                if($lang == 'CZ') return 'Jazyk';
                break;
            case 'userGrid.isAdmin':
                if($lang == 'CZ') return 'Je admin';
                break;
            case 'userGrid.isActive':
                if($lang == 'CZ') return 'Je aktivní';
                break;
            case 'userGrid.registrationMailSended':
                if($lang == 'CZ') return 'Registrační email odeslán';
                break;
            case 'userGrid.createdOn':
                if($lang == 'CZ') return 'Vytvořen';
                break;
            case 'userGrid.updatedOn':
                if($lang == 'CZ') return 'Naposledy upraven';
                break;
            case 'userGrid.deletedOn':
                if($lang == 'CZ') return 'Smazán';
                break;
            //datagrid se seznamem objednavek
            case 'orderGrid.orderId':
                if($lang == 'CZ') return 'ID objednávky';
                break;
            case 'orderGrid.username':
                if($lang == 'CZ') return 'Zákazník';
                break;
            case 'orderGrid.isClosed':
                if($lang == 'CZ') return 'Dokončená';
                break;
            case 'orderGrid.createdOn':
                if($lang == 'CZ') return 'Vytvořena';
                break;
            case 'orderGrid.updatedOn':
                if($lang == 'CZ') return 'Naposledy upravena';
                break;
            case 'orderGrid.deletedOn':
                if($lang == 'CZ') return 'Smazána';
                break;
            case 'orderGrid.orderPriceWithDeleted':
                if($lang == 'CZ') return 'Celková cena(včetně smazaných)';
                break;
            case 'orderGrid.orderPriceWithoutDeleted':
                if($lang == 'CZ') return 'Celková cena(bez smazaných)';
                break;
            //datagrid s editaci objednavky
            case 'orderEditGrid.name':
                if($lang == 'CZ') return 'Jméno';
                break;
            case 'orderEditGrid.description':
                if($lang == 'CZ') return 'Popis';
                break;
            case 'orderEditGrid.pcs':
                if($lang == 'CZ') return 'Kusů';
                break;
            case 'orderEditGrid.createdOn':
                if($lang == 'CZ') return 'Vytvořeno';
                break;
            case 'orderEditGrid.updatedOn':
                if($lang == 'CZ') return 'Naposledy upraveno';
                break;
            case 'orderEditGrid.deletedOn':
                if($lang == 'CZ') return 'Smazáno';
                break;
            case 'ublaboo_datagrid.no_item_found_reset':
                if($lang == 'CZ') return 'Žádná položka nenalezna';
                break;
            case 'ublaboo_datagrid.here':
                if($lang == 'CZ') return 'Smazat filtr';
                break;
        }
        return $message;
    }
}

?>