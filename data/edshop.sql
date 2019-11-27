-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost
-- Vytvořeno: Stř 27. lis 2019, 15:36
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
(6, 'Diskety', 'Staré typy disket - 3,5 a 5,25 palce', 1, NULL, '2019-11-18 14:34:53', '2019-11-18 14:34:53', NULL),
(7, 'Tiskárny', 'Tiskárny a spotřební materiál', 1, NULL, '2019-11-20 10:27:39', '2019-11-20 10:27:39', NULL),
(8, 'Mobilní telefony', 'Mobilní telefony, pouzdra, příslušenství ...', 1, NULL, '2019-11-20 10:53:07', '2019-11-20 10:53:07', NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `objects`
--

CREATE TABLE `objects` (
  `id` int(11) NOT NULL COMMENT 'Primarni auto_increment key',
  `name` varchar(1024) NOT NULL COMMENT 'Nazev prodejniho artiklu',
  `description` text COMMENT 'Popis prodejniho artiklu',
  `category_id` int(11) NOT NULL COMMENT 'Cizi klic do tabulky categories',
  `price` float NOT NULL COMMENT 'Cena prodejniho artiklu',
  `is_available` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-prodejni artikl je dostupny',
  `is_visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-prodejni polozka je viditelna ',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum vytvoreni objektu',
  `deleted_on` timestamp NULL DEFAULT NULL COMMENT 'Datum smazani objektu',
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum posledni aktualizace objektu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `objects`
--

INSERT INTO `objects` (`id`, `name`, `description`, `category_id`, `price`, `is_available`, `is_visible`, `created_on`, `deleted_on`, `updated_on`) VALUES
(3, 'MSI Radeon RX 580 ARMOR 8G OC, 8GB GDDR5', 'Výkonná herní grafická karta společnosti MSI, PCIe 3.0, frekvence 1366 MHz, 8 GB GDDR5 paměti s frekvencí 8000 MHz, 256-bit sběrnice, 1x DVI-D, 2x HDMI, 2x DisplayPort, OpenGL 4.5, DirectX 12, AMD Eyefinity, AMD CrossFire, MSI Afterburner.', 2, 5800, 1, 1, '2019-11-20 12:13:30', NULL, '2019-11-20 12:13:30'),
(4, 'Zotac GeForce RTX 2080 GAMING, 8GB GDDR6', 'Extrémně výkonná herní grafická karta v podání Zotac, rozhraní PCIe 3.0 x16, architektura Turing, frekvence 1710 MHz (boost), 8 GB GDDR6 paměti, 256-bit sběrnice, 1x HDMI, 3x DisplayPort, 1x USB typ-C, OpenGL 4.5, DirectX 12, VR Ready, NVIDIA: Ansel, NVLi', 2, 21500, 1, 1, '2019-11-20 13:27:23', NULL, '2019-11-20 13:27:23'),
(5, 'MSI GeForce RTX 2070 SUPER GAMING X TRIO, 8GB GDDR6', 'Extrémně výkonná herní grafická karta v podání MSI, rozhraní PCIe 3.0 x16, architektura Turing, frekvence 1800 MHz (boost), 8 GB GDDR6 paměti, 256-bit sběrnice, 1x HDMI, 3x DisplayPort, OpenGL 4.5, DirectX 12, VR Ready, MSI Afterburner, NVIDIA: Ansel, NVL', 2, 15490, 1, 1, '2019-11-20 15:04:07', NULL, '2019-11-20 15:04:07'),
(6, 'ASUS GeForce GTX 1660 Ti DUAL-GTX1660TI-O6G, 6GB GDDR6', 'Vysoce výkonná herní grafická karta v podání ASUS, rozhraní PCIe 3.0 x16, architektura Turing, frekvence až 1830 MHz (boost v režimu OC), 6 GB GDDR6 paměti, 192-bit sběrnice, 2x HDMI, 1x DisplayPort, 1x DVI-D, OpenGL 4.5, DirectX 12, VR Ready, NVIDIA: Ans', 2, 7490, 1, 1, '2019-11-20 15:04:38', NULL, '2019-11-20 15:04:38'),
(7, 'Sapphire Radeon NITRO+ RX 580, 8GB GDDR5', 'Výkonná herní grafická karta společnosti Sapphire, PCIe 3.0, frekvence 1411 MHz (boost), 8 GB GDDR5 paměti s frekvencí 8000 MHz, 256-bit sběrnice, 1x DVI-D, 2x HDMI, 2x DisplayPort, OpenGL 4.5, DirectX 12, AMD Eyefinity, AMD CrossFire, HDR Ready. ', 2, 5490, 1, 1, '2019-11-20 15:05:41', NULL, '2019-11-20 15:05:41'),
(8, 'Disketa 1', 'Disketa 1 popis ', 6, 1, 1, 1, '2019-11-20 15:05:56', NULL, '2019-11-20 15:05:56'),
(9, 'Disketa 2', 'Disketa 2 popis', 6, 2, 1, 1, '2019-11-20 15:06:18', NULL, '2019-11-20 15:06:18'),
(10, 'Disketa 3', 'Disketa 3 popis', 6, 3, 1, 1, '2019-11-20 15:06:29', NULL, '2019-11-20 15:06:29'),
(11, 'Disketa 4', 'Disketa 4 popis', 6, 4, 1, 1, '2019-11-20 15:06:42', NULL, '2019-11-20 15:06:42'),
(12, 'Disketa 5', 'Disketa 5 popis', 6, 5, 1, 1, '2019-11-20 15:06:52', NULL, '2019-11-20 15:06:52'),
(13, 'Disketa 6', 'Disketa 6 popis', 6, 6, 1, 1, '2019-11-20 15:07:02', NULL, '2019-11-20 15:07:02'),
(14, 'Disketa 7', 'Disketa 7 popis', 6, 7, 1, 1, '2019-11-20 15:07:19', NULL, '2019-11-20 15:07:19'),
(15, 'Disketa 8', 'Disketa 8 popis', 6, 8, 1, 1, '2019-11-20 15:07:30', NULL, '2019-11-20 15:07:30'),
(16, 'Disketa 9', 'Disketa 9 popis', 6, 9, 1, 1, '2019-11-20 15:07:39', NULL, '2019-11-20 15:07:39'),
(17, 'Disketa 10', 'Disketa 10 popis', 6, 10, 1, 1, '2019-11-20 15:07:54', NULL, '2019-11-20 15:07:54'),
(18, 'Disketa 11', 'Disketa 11 popis', 6, 11, 1, 1, '2019-11-20 15:08:05', NULL, '2019-11-20 15:08:05');

-- --------------------------------------------------------

--
-- Struktura tabulky `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL COMMENT 'Primarni auto_increment key',
  `user_id` int(11) NOT NULL COMMENT 'Cizi klic z tabulky users',
  `is_closed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-objednavka jiz dokoncena',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Datum vytvoreni objednavky',
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datum upravy objednavky',
  `deleted_on` timestamp NULL DEFAULT NULL COMMENT 'Datum smazani objednavky'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `is_closed`, `updated_on`, `deleted_on`) VALUES
(15, 27, 0, '2019-11-27 15:20:44', NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `order_objects`
--

CREATE TABLE `order_objects` (
  `id` int(11) NOT NULL COMMENT 'Primarni auto_increment key',
  `order_id` int(11) NOT NULL COMMENT 'Cizi klic z orders',
  `object_id` int(11) NOT NULL COMMENT 'Cizi klic z objects',
  `pcs` float NOT NULL COMMENT 'Pocet kusu objednaneho zbozi',
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
(26, 'plenkovic', 'Curila', 'Plenkovic', '56b1db8133d9eb398aabd376f07bf8ab5fc584ea0b8bd6a1770200cb613ca005', 'cplenk@ru.ru', 'CZ', 'ae8bea9c-c9e6-4f9c-9757-9d4b72ecafa3', NULL, 0, 0, 0, '2019-11-13 14:23:43', NULL, NULL),
(27, 'kcais', 'Karel', 'Cais', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'kcais@volny.cz', 'CZ', '1e3e51a7-dccc-4083-b32b-b07505cc86a2', NULL, 1, 1, 0, '2019-11-18 16:02:05', NULL, '2019-11-19 12:02:11');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

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
  ADD KEY `order_id` (`order_id`),
  ADD KEY `object_id` (`object_id`) USING BTREE;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primarni auto_increment key', AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pro tabulku `objects`
--
ALTER TABLE `objects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primarni auto_increment key', AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pro tabulku `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primarni auto_increment key', AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pro tabulku `order_objects`
--
ALTER TABLE `order_objects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primarni auto_increment key', AUTO_INCREMENT=27;

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
-- Omezení pro tabulku `objects`
--
ALTER TABLE `objects`
  ADD CONSTRAINT `objects_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

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
