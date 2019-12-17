-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost
-- Vytvořeno: Úte 17. pro 2019, 09:20
-- Verze serveru: 8.0.17
-- Verze PHP: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `edshop`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL COMMENT 'Primarni auto_increment key',
  `name` varchar(255) NOT NULL COMMENT 'Nazev kategorie',
  `description` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Komentar kategorie',
  `order_id` int(11) NOT NULL COMMENT 'Cislo urcujici poradi kategorie',
  `parent_cat_id` int(11) DEFAULT NULL COMMENT 'Id nadrazene kategorie, jinak null',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum vytvoreni zaznamu',
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum posledni upravy kategorie',
  `deleted_on` timestamp NULL DEFAULT NULL COMMENT 'Datum smazani kategorie'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `image`
--

CREATE TABLE `image` (
  `id` int(11) NOT NULL COMMENT 'Id obrazku',
  `product_id` int(11) DEFAULT NULL COMMENT 'Cizi klic z produktu',
  `image_icon` longblob COMMENT 'Ikonka obrazku',
  `image_mini` longblob COMMENT 'Miniatura obrazku',
  `image_normal` longblob NOT NULL COMMENT 'Original obrazku',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum a cas vytvoreni',
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum a cas posledni editace',
  `deleted_on` timestamp NULL DEFAULT NULL COMMENT 'Datum a cas smazani'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `ord`
--

CREATE TABLE `ord` (
  `id` int(11) NOT NULL COMMENT 'Primarni auto_increment key',
  `user_id` int(11) NOT NULL COMMENT 'Cizi klic z tabulky users',
  `is_closed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-objednavka jiz dokoncena',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Datum vytvoreni objednavky',
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum upravy objednavky',
  `deleted_on` timestamp NULL DEFAULT NULL COMMENT 'Datum smazani objednavky'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `ord_product`
--

CREATE TABLE `ord_product` (
  `id` int(11) NOT NULL COMMENT 'Primarni auto_increment key',
  `ord_id` int(11) NOT NULL COMMENT 'Cizi klic z orders',
  `product_id` int(11) NOT NULL COMMENT 'Cizi klic z objects',
  `pcs` float NOT NULL COMMENT 'Pocet kusu objednaneho zbozi',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum vytvoreni objednaneho objektu',
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum upravy objednaneho objektu',
  `deleted_on` timestamp NULL DEFAULT NULL COMMENT 'Datum smazani objednaneho objektu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL COMMENT 'Primarni auto_increment key',
  `name` varchar(1024) CHARACTER SET utf8 NOT NULL COMMENT 'Nazev prodejniho artiklu',
  `description` text CHARACTER SET utf8 COMMENT 'Popis prodejniho artiklu',
  `category_id` int(11) NOT NULL COMMENT 'Cizi klic do tabulky categories',
  `price` float NOT NULL COMMENT 'Cena prodejniho artiklu',
  `is_available` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-prodejni artikl je dostupny',
  `is_visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-prodejni polozka je viditelna ',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum vytvoreni objektu',
  `deleted_on` timestamp NULL DEFAULT NULL COMMENT 'Datum smazani objektu',
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum posledni aktualizace objektu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL COMMENT 'Primarni auto_increment key',
  `username` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'Prihlasovaci jmeno uzivatele',
  `firstname` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Jmeno uzivatele',
  `surname` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Prijmeni uzivatele',
  `password_hash` varchar(2048) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Zaheshovane heslo uzivatele pro prihlaseni',
  `email` varchar(1024) CHARACTER SET utf8 NOT NULL COMMENT 'Emai',
  `language` varchar(16) CHARACTER SET utf8 NOT NULL DEFAULT 'CZ' COMMENT 'Jazyk uzivatele',
  `uuid_registration` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Vygenerovane uuid pro dokonceni registrace',
  `uuid_lost_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Vygenerovane uuid pro obnovu hesla',
  `is_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-ucet je aktivni',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-ucet systemoveho administratora',
  `registration_mail_sended` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-registracni email byl odeslan',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum vytvoreni uctu',
  `deleted_on` timestamp NULL DEFAULT NULL COMMENT 'Datum smazani uctu',
  `updated_on` timestamp NULL DEFAULT NULL COMMENT 'Datum zmeny uctu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_cat_id` (`parent_cat_id`);

--
-- Klíče pro tabulku `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Klíče pro tabulku `ord`
--
ALTER TABLE `ord`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Klíče pro tabulku `ord_product`
--
ALTER TABLE `ord_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ord_id` (`ord_id`) USING BTREE,
  ADD KEY `product_id` (`product_id`) USING BTREE;

--
-- Klíče pro tabulku `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Klíče pro tabulku `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `uuid_registration` (`uuid_registration`,`uuid_lost_password`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primarni auto_increment key';

--
-- AUTO_INCREMENT pro tabulku `image`
--
ALTER TABLE `image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id obrazku';

--
-- AUTO_INCREMENT pro tabulku `ord`
--
ALTER TABLE `ord`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primarni auto_increment key';

--
-- AUTO_INCREMENT pro tabulku `ord_product`
--
ALTER TABLE `ord_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primarni auto_increment key';

--
-- AUTO_INCREMENT pro tabulku `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primarni auto_increment key';

--
-- AUTO_INCREMENT pro tabulku `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primarni auto_increment key';

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `category_ibfk_1` FOREIGN KEY (`parent_cat_id`) REFERENCES `category` (`id`);

--
-- Omezení pro tabulku `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `image_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Omezení pro tabulku `ord`
--
ALTER TABLE `ord`
  ADD CONSTRAINT `ord_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Omezení pro tabulku `ord_product`
--
ALTER TABLE `ord_product`
  ADD CONSTRAINT `ord_product_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `ord_product_ibfk_2` FOREIGN KEY (`ord_id`) REFERENCES `ord` (`id`);

--
-- Omezení pro tabulku `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
