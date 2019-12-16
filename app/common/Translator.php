<?php

class Translator implements \Nette\Localization\ITranslator
{
    private $language='CZ';

    /**
     * Translator constructor.
     * @param String|null $language
     */
    public function __construct(?String $language)
    {
        if(isset($language)) {
            $this->language = strtoupper($language);
        }
        else{
            $this->language ='CZ';
        }
        return $this;
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
                if($lang == 'CZ') return 'Zobrazené položky';
                if($lang == 'ENG') return 'Displayed items';
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
                if($lang == 'ENG') return 'Description';
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
            case 'Vytvoření nové kategorie':
                if($lang == 'ENG') return 'Create a new category';
                break;
            case 'Jméno kategorie :':
                if($lang == 'ENG') return 'Category name :';
                break;
            case 'Popis kategorie :':
                if($lang == 'ENG') return 'Category description :';
                break;
            case 'Pořadí kategorie :':
                if($lang == 'ENG') return 'Category order :';
                break;
            case 'Nadřazená kategorie :':
                if($lang == 'ENG') return 'Parent category :';
                break;
            case 'Vytvořit':
                if($lang == 'ENG') return 'Create';
                break;
            case 'Vytvoření proběhlo v pořádku.':
                if($lang == 'ENG') return 'Creation was OK.';
                break;
            case 'Pokračovat zde':
                if($lang == 'ENG') return 'Continue here';
                break;
            case 'Editace kategorií':
                if($lang == 'ENG') return 'Editing categories';
                break;
            case 'Vytvoření nového produktu':
                if($lang == 'ENG') return 'Create a new product';
                break;
            case 'Kategorie :':
                if($lang == 'ENG') return 'Category :';
                break;
            case 'Název :':
                if($lang == 'ENG') return 'Name :';
                break;
            case 'Popis :':
                if($lang == 'ENG') return 'Description :';
                break;
            case 'Cena(Kč) :':
                if($lang == 'ENG') return 'Price(Kč) :';
                break;
            case 'Obrázek :':
                if($lang == 'ENG') return 'Image :';
                break;
            case 'Editace produktů':
                if($lang == 'ENG') return 'Products edit';
                break;
            case 'Nový uživatel':
                if($lang == 'ENG') return 'New user';
                break;
            case 'Uživatelské jméno :':
                if($lang == 'ENG') return 'Username :';
                break;
            case 'Heslo :':
                if($lang == 'ENG') return 'Password :';
                break;
            case 'Heslo znovu :':
                if($lang == 'ENG') return 'Password again :';
                break;
            case 'Přidat':
                if($lang == 'ENG') return 'Add';
                break;
            case 'Editace uživatelů':
                if($lang == 'ENG') return 'Users edit';
                break;
            case 'Editace objednávek':
                if($lang == 'ENG') return 'Orders edit';
                break;
            case 'Při vytvoření došlo k chybě !':
                if($lang == 'ENG') return 'Error creating!';
                break;
            case 'Registrace':
                if($lang == 'ENG') return 'Registration';
                break;
            case 'Uživatel není přihlášen':
                if($lang == 'ENG') return 'User is not logged in';
                break;
            case 'Registrace nového uživatele':
                if($lang == 'ENG') return 'New user registration';
                break;
            case 'Vaše jméno :':
                if($lang == 'ENG') return 'Your name :';
                break;
            case 'Vaše příjmení :':
                if($lang == 'ENG') return 'Your surname :';
                break;
            case 'Registrovat':
                if($lang == 'ENG') return 'Register';
                break;
            case 'Zapomenuté heslo':
                if($lang == 'ENG') return 'Forgotten password';
                break;
            case 'Přihlášení uživatele':
                if($lang == 'ENG') return 'User login';
                break;
            case 'Přihlásit':
                if($lang == 'ENG') return 'Login';
                break;
            case 'Zadejte přihlašovací jméno':
                if($lang == 'ENG') return 'Enter your login name';
                break;
            case 'Zadejte přihlašovací heslo':
                if($lang == 'ENG') return 'Enter your login password';
                break;
            case 'Zadejte uživatelské jméno':
                if($lang == 'ENG') return 'Enter your username';
                break;
            case 'Zadejte Vaše jméno':
                if($lang == 'ENG') return 'Enter your name';
                break;
            case 'Zadejte Vaše příjmení':
                if($lang == 'ENG') return 'Enter your surname';
                break;
            case 'Zadejte registrační email':
                if($lang == 'ENG') return 'Enter your registration email';
                break;
            case 'Zadejte heslo':
                if($lang == 'ENG') return 'Enter your password';
                break;
            case 'Zadejte heslo pro potvrzení':
                if($lang == 'ENG') return 'Enter your password to confirm';
                break;
            case 'Registrace nového uživatele proběhla v pořádku.':
                if($lang == 'ENG') return 'New user registration was OK.';
                break;
            case 'Na Vámi zadanou emailovou adresu byl odeslán potvrzovací email pro dokončení registrace.':
                if($lang == 'ENG') return 'A confirmation email has been sent to the email address you entered to complete the registration.';
                break;
            case 'Uživatelské jméno musí mít alespoň tři znaky.':
                if($lang == 'ENG') return 'Username must have at least three characters.';
                break;
            case 'Uživatelské jméno je volné':
                if($lang == 'ENG') return 'Username is free';
                break;
            case 'Uživatelské jméno je již použito, zvolte prosím jiné':
                if($lang == 'ENG') return 'Username already in use, please choose another';
                break;
            case 'Při zjišťování dostupnosti uživatelského jména došlo k chybě':
                if($lang == 'ENG') return 'Error checking username availability';
                break;
            case 'Zjišťuji dostupnost uživatelského jména ...':
                if($lang == 'ENG') return 'Checking for username availability ...';
                break;
            case 'Zapomenuté heslo uživatele':
                if($lang == 'ENG') return 'Forgotten user password';
                break;
            case 'Email použitý při registraci :':
                if($lang == 'ENG') return 'Email used for registration:';
                break;
            case 'Zadejte email použitý při registraci':
                if($lang == 'ENG') return 'Enter the email used to register';
                break;
            case 'Odeslat':
                if($lang == 'ENG') return 'Send';
                break;
            case 'Zadané uživatelské jméno nebo email již existují ! Zadejte prosím jiné':
                if($lang == 'ENG') return 'The username or email you entered already exists! Please enter a different one';
                break;
            case 'Přihlášený uživatel nemá roli admin !':
                if($lang == 'ENG') return 'The logged in user has no role admin!';
                break;
            case 'Uživatelský účet ještě nebyl přes email s odkazem aktivován.':
                if($lang == 'ENG') return 'Uživatelský účet ještě nebyl přes email s odkazem aktivován.';
                break;
            case 'Uživatelský účet neexistuje nebo bylo zadáno chybné heslo.':
                if($lang == 'ENG') return 'The user account does not exist or an incorrect password was entered.';
                break;
            case 'Heslo bylo změněno. Nyní se můžete přihlásit pomocí nového hesla.':
                if($lang == 'ENG') return 'The password has been changed. You can now sign in with your new password.';
                break;
            case 'Na Vaší emailovou adresu byly odeslány instrukce pro obnovu hesla.':
                if($lang == 'ENG') return 'Password recovery instructions have been sent to your email address.';
                break;
            case 'Vámi zadaný registrační email neexistuje. Zkuste to prosím znovu.':
                if($lang == 'ENG') return 'The registration email you entered does not exist. Please try again.';
                break;
            case 'Zadejte jméno kategorie':
                if($lang == 'ENG') return 'Enter a category name';
                break;
            case 'Zadejte popis kategorie':
                if($lang == 'ENG') return 'Enter a category description';
                break;
            case 'Skutečně označit kategorii %s jako deleted_on ?':
                if($lang == 'ENG') return 'Really mark category %s as deleted_on?';
                break;
            case 'Skutečně smazat kategorii %s z DB ?':
                if($lang == 'ENG') return 'Really delete category %s from DB?';
                break;
            case 'Zadejte jméno produktu':
                if($lang == 'ENG') return 'Enter product name';
                break;
            case 'Zadejte popis produktu':
                if($lang == 'ENG') return 'Enter product description';
                break;
            case 'Zadejte cenu produktu':
                if($lang == 'ENG') return 'Enter product price';
                break;
            case 'Skutečně označit product %s jako deleted_on ?':
                if($lang == 'ENG') return 'Really mark product %s as deleted_on?';
                break;
            case 'Skutečně smazat product %s z DB ?':
                if($lang == 'ENG') return 'Really delete product %s from DB?';
                break;
            case 'Skutečně označit uživatele %s jako deleted_on ?':
                if($lang == 'ENG') return 'Really mark user %s as deleted_on?';
                break;
            case 'Skutečně smazat uživatele %s z DB ?':
                if($lang == 'ENG') return 'Really delete user %s from DB?';
                break;
            case 'Skutečně označit objednávku %s jako deleted_on ?':
                if($lang == 'ENG') return 'Really mark order %s as deleted_on?';
                break;
            case 'Skutečně označit produkt v objednávce %s jako deleted_on ?':
                if($lang == 'ENG') return 'Really mark product in order %s as deleted_on?';
                break;
            case 'Skutečně smazat product v objednávce %s z DB ?':
                if($lang == 'ENG') return 'Really delete product in order %s from DB?';
                break;
            case 'Odhlášení uživatele proběhlo úspěšně':
                if($lang == 'ENG') return 'User logout succeeded';
                break;
            case 'Pro nové přihlášení klikněte':
                if($lang == 'ENG') return 'Click for new login';
                break;
            case 'zde':
                if($lang == 'ENG') return 'here';
                break;
            case 'Zadejte doručovací adresu':
                if($lang == 'ENG') return 'Enter your shipping address';
                break;
            case 'Objednávka byla dokončena.':
                if($lang == 'ENG') return 'Order completed.';
                break;
            case 'Pro další nákup klikněte zde':
                if($lang == 'ENG') return 'For next purchase click here';
                break;
            case 'Ověření emailu a dokončení registrace proběhlo v pořádku':
                if($lang == 'ENG') return 'Email verification and registration complete';
                break;
            case 'Nyní se můžete':
                if($lang == 'ENG') return 'You can now';
                break;
            case 'přihlásit':
                if($lang == 'ENG') return 'login';
                break;
            case 'Dokončení registrace - EdShop':
                if($lang == 'ENG') return 'Complete registration - EdShop';
                break;
            case 'Dobrý den,<br><br>Vaše registrace na stránky EdShop byla dokončena.
            Pro aktivaci účtu klikněte zde : <a href="':
                if($lang == 'ENG') return 'Hello, <br><br>Your registration to EdShop has been completed.
             Click here to activate your account: <a href="';
                break;
            case 'Aktivace':
                if($lang == 'ENG') return 'Activate';
                break;


        }

        return $message;
    }
}



?>