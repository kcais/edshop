-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost
-- Vytvořeno: Úte 19. lis 2019, 11:08
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
-- Struktura tabulky `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL COMMENT 'Primarni auto_increment key',
  `name` varchar(255) NOT NULL COMMENT 'Nazev kategorie',
  `comment` varchar(1024) NOT NULL COMMENT 'Komentar kategorie',
  `order_id` int(11) NOT NULL COMMENT 'Cislo urcujici poradi kategorie',
  `parent_cat_id` int(11) DEFAULT NULL COMMENT 'Id nadrazene kategorie, jinak null',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum vytvoreni zaznamu',
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum posledni upravy kategorie',
  `deleted_on` timestamp NULL DEFAULT NULL COMMENT 'Datum smazani kategorie'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Vypisuji data pro tabulku `categories`
--

INSERT INTO `categories` (`id`, `name`, `comment`, `order_id`, `parent_cat_id`, `created_on`, `updated_on`, `deleted_on`) VALUES
(1, 'Vstupní zařízení', 'Ovládací periferie jako např. myši, klávesnice, joysticky ap.', 1, NULL, '2019-11-13 09:31:19', '2019-11-13 09:31:19', NULL),
(2, 'Grafické karty', 'Grafické karty a příslušenství', 2, NULL, '2019-11-13 09:32:55', '2019-11-13 09:32:55', NULL),
(3, 'Monitory', 'Zobrazovací zařízení', 3, NULL, '2019-11-13 09:34:15', '2019-11-13 09:34:15', NULL),
(4, 'Procesory', 'Výpočetní procesory, CPU, FPU, ASIC ...', 4, NULL, '2019-11-13 09:49:28', '2019-11-13 09:49:28', NULL),
(5, 'Televizory', 'Televizory LCD/LED/Plasma/OLED', 1, NULL, '2019-11-18 14:33:29', '2019-11-18 14:33:29', NULL),
(6, 'Diskety', 'Staré typy disket - 3,5 a 5,25 palce', 1, NULL, '2019-11-18 14:34:53', '2019-11-18 14:34:53', NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `objects`
--

CREATE TABLE `objects` (
  `id` int(11) NOT NULL COMMENT 'Primarni auto_increment key',
  `name` varchar(1024) NOT NULL COMMENT 'Nazev prodejniho artiklu',
  `description` text COMMENT 'Popis prodejniho artiklu',
  `is_available` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-prodejni artikl je dostupny',
  `is_visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-prodejni polozka je viditelna ',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum vytvoreni objektu',
  `deleted_on` timestamp NULL DEFAULT NULL COMMENT 'Datum smazani objektu',
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum posledni aktualizace objektu'
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
  `password_hash` varchar(2048) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Zaheshovane heslo uzivatele pro prihlaseni',
  `email` varchar(1024) NOT NULL COMMENT 'Emai',
  `language` varchar(16) NOT NULL DEFAULT 'CZ' COMMENT 'Jazyk uzivatele',
  `uuid_registration` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Vygenerovane uuid pro dokonceni registrace',
  `uuid_lost_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Vygenerovane uuid pro obnovu hesla',
  `is_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-ucet je aktivni',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-ucet systemoveho administratora',
  `registration_mail_sended` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-registracni email byl odeslan',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum vytvoreni uctu',
  `deleted_on` timestamp NULL DEFAULT NULL COMMENT 'Datum smazani uctu',
  `updated_on` timestamp NULL DEFAULT NULL COMMENT 'Datum zmeny uctu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`id`, `username`, `firstname`, `surname`, `password_hash`, `email`, `language`, `uuid_registration`, `uuid_lost_password`, `is_active`, `is_admin`, `registration_mail_sended`, `created_on`, `deleted_on`, `updated_on`) VALUES
(25, 'ppetr', 'Petr', 'Petrovci', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'p@petr.cz', 'CZ', '981f9116-1bf5-4296-ac1b-0f3a7c74e07d', NULL, 1, 0, 0, '2019-11-13 14:22:01', NULL, '2019-11-13 14:22:15'),
(26, 'plenkovic', 'Curila', 'Plenkovic', '56b1db8133d9eb398aabd376f07bf8ab5fc584ea0b8bd6a1770200cb613ca005', 'cplenk@ru.ru', 'CZ', 'ae8bea9c-c9e6-4f9c-9757-9d4b72ecafa3', NULL, 0, 0, 0, '2019-11-13 14:23:43', NULL, NULL),
(27, 'kcais', 'Karel', 'Cais', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5', 'kcais@volny.cz', 'CZ', '1e3e51a7-dccc-4083-b32b-b07505cc86a2', NULL, 1, 1, 0, '2019-11-18 16:02:05', NULL, '2019-11-18 16:08:27');

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_cat_id` (`parent_cat_id`);

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
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `uuid_registration` (`uuid_registration`,`uuid_lost_password`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primarni auto_increment key', AUTO_INCREMENT=7;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primarni auto_increment key', AUTO_INCREMENT=28;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_cat_id`) REFERENCES `categories` (`id`);

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
