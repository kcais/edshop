-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost
-- Vytvořeno: Stř 13. lis 2019, 08:26
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
-- Struktura tabulky `objects`
--

CREATE TABLE `objects` (
  `id` int(11) NOT NULL COMMENT 'Primarni auto_increment key',
  `name` varchar(1024) NOT NULL COMMENT 'Nazev prodejniho artiklu',
  `description` text COMMENT 'Popis prodejniho artiklu',
  `is_available` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-prodejni artikl je dostupny'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL COMMENT 'Primarni auto_increment key',
  `user_id` int(11) NOT NULL COMMENT 'Cizi klic z tabulky users',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Datum vytvoreni objednavky',
  `updated_on` timestamp NULL DEFAULT NULL COMMENT 'Datum upravy objednavky',
  `deleted_on` timestamp NULL DEFAULT NULL COMMENT 'Datum smazani objednavky'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `order_objects`
--

CREATE TABLE `order_objects` (
  `id` int(11) NOT NULL COMMENT 'Primarni auto_increment key',
  `order_id` int(11) NOT NULL COMMENT 'Cizi klic z orders',
  `object_id` int(11) NOT NULL COMMENT 'Cizi klic z objects',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum vytvoreni objednaneho objektu',
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum upravy objednaneho objektu',
  `deleted_on` timestamp NULL DEFAULT NULL COMMENT 'Datum smazani objednaneho objektu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT 'Primarni auto_increment key',
  `username` varchar(255) NOT NULL COMMENT 'Prihlasovaci jmeno uzivatele',
  `firstname` varchar(255) DEFAULT NULL COMMENT 'Jmeno uzivatele',
  `surname` varchar(255) DEFAULT NULL COMMENT 'Prijmeni uzivatele',
  `password_hash` varchar(2048) NOT NULL COMMENT 'Zaheshovane heslo uzivatele pro prihlaseni',
  `email` varchar(1024) NOT NULL COMMENT 'Emai',
  `language` varchar(16) NOT NULL DEFAULT 'CZ' COMMENT 'Jazyk uzivatele',
  `uuid_registration` varchar(20) DEFAULT NULL COMMENT 'Vygenerovane uuid pro dokonceni registrace',
  `uuid_lost_password` varchar(20) DEFAULT NULL COMMENT 'Vygenerovane uuid pro obnovu hesla',
  `is_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-ucet je aktivni',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-ucet systemoveho administratora',
  `registration_mail_sended` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-registracni email byl odeslan',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum vytvoreni uctu',
  `deleted_on` timestamp NULL DEFAULT NULL COMMENT 'Datum smazani uctu',
  `updated_on` timestamp NULL DEFAULT NULL COMMENT 'Datum zmeny uctu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `objects`
--
ALTER TABLE `objects`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Klíče pro tabulku `order_objects`
--
ALTER TABLE `order_objects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `object_id` (`object_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Klíče pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `objects`
--
ALTER TABLE `objects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primarni auto_increment key';

--
-- AUTO_INCREMENT pro tabulku `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primarni auto_increment key';

--
-- AUTO_INCREMENT pro tabulku `order_objects`
--
ALTER TABLE `order_objects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primarni auto_increment key';

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primarni auto_increment key';

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Omezení pro tabulku `order_objects`
--
ALTER TABLE `order_objects`
  ADD CONSTRAINT `order_objects_ibfk_1` FOREIGN KEY (`object_id`) REFERENCES `objects` (`id`),
  ADD CONSTRAINT `order_objects_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
