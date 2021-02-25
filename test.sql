-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Фев 23 2021 г., 11:52
-- Версия сервера: 5.7.29
-- Версия PHP: 7.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `test`
--

-- --------------------------------------------------------

--
-- Структура таблицы `note_data`
--

CREATE TABLE `note_data` (
  `note_id` int(255) NOT NULL,
  `user_login` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note_char` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activate` tinyint(1) DEFAULT '0',
  `del` tinyint(1) NOT NULL DEFAULT '0',
  `comment_admin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `note_data`
--

INSERT INTO `note_data` (`note_id`, `user_login`, `note_name`, `note_char`, `activate`, `del`, `comment_admin`) VALUES
(6, 'root@root.root', 'заметка', 'важная информация', 1, 0, NULL),
(7, 'root@root.root', 'пароль', '4321вс1213', 1, 0, NULL),
(9, 'string@netu.ru', '34214', '43125', 0, 0, NULL),
(10, 'string@netu.ru', 'пароль', '00909', 0, 0, NULL),
(11, 'nerealname@bfdfk.ru', '453215', '5312`', 1, 0, NULL),
(12, 'string@netu.ru', 'f', 'f', 1, 0, NULL),
(13, 'string@netu.ru', 'выфафыафвы', 'авыфавфыа', 0, 0, NULL),
(14, 'nerealname@bfdfk.ru', '123', '432143214321432143214321432143214321432143214321432143214321432143214321432143214321432143214321432143214321432143214321432143214321', 1, 0, NULL),
(15, 'string@netu.ru', 'ffffffffffffffffffffff43214321fd', '432', 0, 0, NULL),
(20, 'string@netu.ru', 'очень важно [b]1234[/b]', '[b]1234[/b]  [img]https://4.bp.blogspot.com/-YgiIDQ_el78/WvrCjkXwQaI/AAAAAAAAFcM/WMYm-mOZPMAzUp8-KjzWSlIHHni7HuPJgCLcBGAs/s1600/44a25591d21054646b745b79f879a48d.jpg[/img]', 1, 0, NULL),
(16, 'root@root.root', 'птица', '[img]http://www.youloveit.ru/uploads/posts/2019-08/1565284946_red4.jpg[/img] ', 1, 0, NULL),
(17, 'string@netu.ru', 'птица', 'так [img]http://www.youloveit.ru/uploads/posts/2019-08/1565284946_red4.jpg[/img] так', 1, 0, NULL),
(18, 'string@netu.ru', 'цифры', '5413', 0, 0, NULL),
(19, 'nerealname@bfdfk.ru', 'афвы', '123', 1, 0, NULL),
(21, 'string@netu.ru', 'Test', 'test1', 1, 0, NULL),
(22, 'nerealname@bfdfk.ru', 'Test1', '_)_)_)_)1', 1, 0, NULL),
(23, 'string@netu.ru', '125', '123', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `user_login` varchar(30) CHARACTER SET cp1251 NOT NULL,
  `role` varchar(255) CHARACTER SET cp1251 NOT NULL,
  `user_password` varchar(32) CHARACTER SET cp1251 NOT NULL,
  `user_hash` varchar(32) CHARACTER SET cp1251 NOT NULL DEFAULT '',
  `activate` tinyint(1) DEFAULT '0',
  `del` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_id`, `user_login`, `role`, `user_password`, `user_hash`, `activate`, `del`) VALUES
(57, 'root@root.root', 'root', 'root', 'ec603a1a1002f7fb29400f7c58caaa53', 1, 0),
(61, 'string@netu.ru', 'root', '123', '601f56ec7e0431132fa97907bca4e4f3', 1, 0),
(73, 'nerealname@bfdfk.ru', 'user', '123', '', 1, 0),
(74, 'm.suhanow@fdas.fdasf', 'user', '', '', 0, 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `note_data`
--
ALTER TABLE `note_data`
  ADD PRIMARY KEY (`note_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `note_data`
--
ALTER TABLE `note_data`
  MODIFY `note_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
