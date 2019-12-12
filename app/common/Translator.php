<?php

class Translator implements \Nette\Localization\ITranslator
{
    private $language='CZ';

    public function __construct(String $language)
    {
        $this->language = strtoupper($language);
    }

    public function translate($message, ...$parameters): string
    {
        $lang =  $this->language;
        switch($message){
            //ublaboo datagrid
            case 'ublaboo_datagrid.perPage_submit':
                if($lang == 'CZ') return 'Nastavit počet položek na stránku';
                if($lang == 'ENG') return 'Set the number of items per page';
                break;
            case 'ublaboo_datagrid.items':
                if($lang == 'CZ') return 'Zobrazené prodejní položky';
                if($lang == 'ENG') return 'Displayed sales items';
                break;
            case 'ublaboo_datagrid.from':
                if($lang == 'CZ') return 'z celkových';
                if($lang == 'ENG') return 'of total';
                break;
            case 'ublaboo_datagrid.reset_filter':
                if($lang == 'CZ') return 'Reset filtru';
                if($lang == 'ENG') return 'Filter reset';
                break;
            case 'ublaboo_datagrid.all':
                if($lang == 'CZ') return 'Všechny';
                if($lang == 'ENG') return 'All';
                break;
            case 'ublaboo_datagrid.no_item_found':
                if($lang == 'CZ') return 'Žádná položka nenalezena';
                if($lang == 'ENG') return 'No item found';
                break;
            case 'ublaboo_datagrid.next':
                if($lang == 'CZ') return 'Další';
                if($lang == 'ENG') return 'Next';
                break;
            case 'ublaboo_datagrid.previous':
                if($lang == 'CZ') return 'Předchozí';
                if($lang == 'ENG') return 'Previous';
                break;
            // datagrid s prodejnimi polozkami
            case 'objectsGrid.description':
                if($lang == 'CZ') return 'Popis';
                if($lang == 'ENG') return 'Description';
                break;
            case 'objectsGrid.category':
                if($lang == 'CZ') return 'Kategorie';
                if($lang == 'ENG') return 'Category';
                break;
            case 'objectsGrid.name':
                if($lang == 'CZ') return 'Název';
                if($lang == 'ENG') return 'Name';
                break;
            case 'objectsGrid.price':
                if($lang == 'CZ') return 'Cena';
                if($lang == 'ENG') return 'Price';
                break;
            case 'ublaboo_datagrid.action':
                if($lang == 'CZ') return '';
                if($lang == 'ENG') return '';
                break;
            case 'objectsGrid.pricePerPcs':
                if($lang == 'CZ') return 'Cena / ks';
                if($lang == 'ENG') return 'Price / pcs';
                break;
            case 'objectsGrid.pcs':
                if($lang == 'CZ') return 'Ks';
                if($lang == 'ENG') return 'Pcs';
                break;
            case 'objectsGrid.totalPrice':
                if($lang == 'CZ') return 'Cena celkem';
                if($lang == 'ENG') return 'Total price';
                break;
            case 'objectsGrid.category_change':
                if($lang == 'CZ') return 'Změna kategorie';
                if($lang == 'ENG') return 'Category change';
                break;
            case 'Do košíku':
                if($lang == 'ENG') return 'To basket';
                break;
            //datagrid s uzivateli
            case 'userGrid.username':
                if($lang == 'CZ') return 'Uživatelské jméno';
                if($lang == 'ENG') return 'Username';
                break;
            case 'userGrid.firstname':
                if($lang == 'CZ') return 'Jméno';
                if($lang == 'ENG') return 'Firstname';
                break;
            case 'userGrid.surname':
                if($lang == 'CZ') return 'Příjmení';
                if($lang == 'ENG') return 'Surname';
                break;
            case 'userGrid.email':
                if($lang == 'CZ') return 'E-mail';
                if($lang == 'ENG') return 'E-mail';
                break;
            case 'userGrid.language':
                if($lang == 'CZ') return 'Jazyk';
                if($lang == 'ENG') return 'Language';
                break;
            case 'userGrid.isAdmin':
                if($lang == 'CZ') return 'Je admin';
                if($lang == 'ENG') return 'Is admin';
                break;
            case 'userGrid.isActive':
                if($lang == 'CZ') return 'Je aktivní';
                if($lang == 'ENG') return 'Is active';
                break;
            case 'userGrid.registrationMailSended':
                if($lang == 'CZ') return 'Registrační email odeslán';
                if($lang == 'ENG') return 'Registration mail sended';
                break;
            case 'userGrid.createdOn':
                if($lang == 'CZ') return 'Vytvořen';
                if($lang == 'ENG') return 'Created on';
                break;
            case 'userGrid.updatedOn':
                if($lang == 'CZ') return 'Naposledy upraven';
                if($lang == 'ENG') return 'Updated on';
                break;
            case 'userGrid.deletedOn':
                if($lang == 'CZ') return 'Smazán';
                if($lang == 'ENG') return 'Deleted on';
                break;
            //datagrid se seznamem objednavek
            case 'orderGrid.orderId':
                if($lang == 'CZ') return 'ID objednávky';
                if($lang == 'ENG') return 'ID order';
                break;
            case 'orderGrid.username':
                if($lang == 'CZ') return 'Zákazník';
                if($lang == 'ENG') return 'Customer';
                break;
            case 'orderGrid.isClosed':
                if($lang == 'CZ') return 'Dokončená';
                if($lang == 'ENG') return 'Closed';
                break;
            case 'orderGrid.createdOn':
                if($lang == 'CZ') return 'Vytvořena';
                if($lang == 'ENG') return 'Created on';
                break;
            case 'orderGrid.updatedOn':
                if($lang == 'CZ') return 'Naposledy upravena';
                if($lang == 'ENG') return 'Updated on';
                break;
            case 'orderGrid.deletedOn':
                if($lang == 'CZ') return 'Smazána';
                if($lang == 'ENG') return 'Deleted on';
                break;
            case 'orderGrid.orderPriceWithDeleted':
                if($lang == 'CZ') return 'Celková cena(včetně smazaných)';
                if($lang == 'ENG') return 'Total price(include deleted)';
                break;
            case 'orderGrid.orderPriceWithoutDeleted':
                if($lang == 'CZ') return 'Celková cena(bez smazaných)';
                if($lang == 'ENG') return 'Total price(w/o deleted)';
                break;
            //datagrid s editaci objednavky
            case 'orderEditGrid.name':
                if($lang == 'CZ') return 'Jméno';
                if($lang == 'ENG') return 'Name';
                break;
            case 'orderEditGrid.description':
                if($lang == 'CZ') return 'Popis';
                if($lang == 'ENG') return 'Popis';
                break;
            case 'orderEditGrid.pcs':
                if($lang == 'CZ') return 'Kusů';
                if($lang == 'ENG') return 'Pcs';
                break;
            case 'orderEditGrid.createdOn':
                if($lang == 'CZ') return 'Vytvořeno';
                if($lang == 'ENG') return 'Created on';
                break;
            case 'orderEditGrid.updatedOn':
                if($lang == 'CZ') return 'Naposledy upraveno';
                if($lang == 'ENG') return 'Updated on';
                break;
            case 'orderEditGrid.deletedOn':
                if($lang == 'CZ') return 'Smazáno';
                if($lang == 'ENG') return 'Deleted on';
                break;
            case 'ublaboo_datagrid.no_item_found_reset':
                if($lang == 'CZ') return 'Žádná položka nenalezna';
                if($lang == 'ENG') return 'No item found';
                break;
            case 'ublaboo_datagrid.here':
                if($lang == 'CZ') return 'Smazat filtr';
                if($lang == 'ENG') return 'Clear filter';
                break;

             //preklad sablon
            case 'Přihlášen':
                if($lang == 'ENG') return 'Signed in';
                break;
            case 'Odhlásit':
                if($lang == 'ENG') return 'Log out';
                break;
            case 'Administrátorské rozhraní':
                if($lang == 'ENG') return 'Admin interface';
                break;
            case 'Košík':
                if($lang == 'ENG') return 'Basket';
                break;
            case 'Vyhledat':
                if($lang == 'ENG') return 'Search';
                break;
            case 'Kategorie':
                if($lang == 'ENG') return 'Category';
                break;
            case 'Objednávkový košík':
                if($lang == 'ENG') return 'Order basket';
                break;
            case 'Vyprázdnit košík':
                if($lang == 'ENG') return 'Empty basket';
                break;
            case 'Opravdu si přejete vyprázdnit košík ?':
                if($lang == 'ENG') return 'Are you sure you want to empty your basket ?';
                break;
            case 'Pokračovat v objednávce':
                if($lang == 'ENG') return 'Continue in the order';
                break;
            case 'Odebrat':
                if($lang == 'ENG') return 'Remove';
                break;
            case 'Dokončení objednávky':
                if($lang == 'ENG') return 'Order completion';
                break;
            case 'Objednané zboží':
                if($lang == 'ENG') return 'Ordered goods';
                break;
            case 'Název':
                if($lang == 'ENG') return 'Name';
                break;
            case 'Popis':
                if($lang == 'ENG') return 'Description';
                break;
            case 'Cena/ks':
                if($lang == 'ENG') return 'Price/pcs';
                break;
            case 'Ks':
                if($lang == 'ENG') return 'Pcs';
                break;
            case 'Cena celkem':
                if($lang == 'ENG') return 'Total price';
                break;
            case 'Celková cena':
                if($lang == 'ENG') return 'Total price';
                break;
            case 'Zpět na košík':
                if($lang == 'ENG') return 'Back to basket';
                break;
            case 'Dokončit objednávku':
                if($lang == 'ENG') return 'Finish the order';
                break;
            case 'Doručovací údaje':
                if($lang == 'ENG') return 'Delivery information';
                break;
            // preklady formularu
            case 'Jméno :':
                if($lang == 'ENG') return 'Name :';
                break;
            case 'Příjmení :':
                if($lang == 'ENG') return 'Surname :';
                break;
            case 'Adresa :':
                if($lang == 'ENG') return 'Address :';
                break;
            case 'Nová':
            case 'Nový':
                if($lang == 'ENG') return 'New';
                break;
            case 'Editovat':
            case 'Editace':
                if($lang == 'ENG') return 'Edit';
                break;
            case 'Seznam':
                if($lang == 'ENG') return 'List';
                break;
            case 'Odhlásit se':
                if($lang == 'ENG') return 'Log out';
                break;
            case 'Přegenerovat statické obrázky produktů':
                if($lang == 'ENG') return 'Regenerate still images of products';
                break;
            case 'Zvolte pro přegenerování statických obrázku všech produktů':
                if($lang == 'ENG') return 'Select to regenerate still images of all products';
                break;
            case 'Při generování statických obrázků produktů došlo k chybě.':
                if($lang == 'ENG') return 'There was an error generating product still images.';
                break;
            case 'Generování statických obrázků produktů bylo dokončeno. (Celkem vygenerováno':
                if($lang == 'ENG') return 'Product still image generation complete. (Total generated';
                break;
            case 'obrázků':
                if($lang == 'ENG') return 'images';
                break;
            case 'Probíhá generování statických obrázků produktů ...':
                if($lang == 'ENG') return 'Generating product still images ...';
                break;
            case 'Prodejní položky':
                if($lang == 'ENG') return 'Products';
                break;
            case 'Uživatelé':
                if($lang == 'ENG') return 'Users';
                break;
            case 'Objednávky':
                if($lang == 'ENG') return 'Orders';
                break;
        }
        return $message;
    }
}

?>