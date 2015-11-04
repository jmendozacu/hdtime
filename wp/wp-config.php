<?php
/**
 * Основные параметры WordPress.
 *
 * Этот файл содержит следующие параметры: настройки MySQL, префикс таблиц,
 * секретные ключи и ABSPATH. Дополнительную информацию можно найти на странице
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Кодекса. Настройки MySQL можно узнать у хостинг-провайдера.
 *
 * Этот файл используется скриптом для создания wp-config.php в процессе установки.
 * Необязательно использовать веб-интерфейс, можно скопировать этот файл
 * с именем "wp-config.php" и заполнить значения вручную.
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'beckup_21102015');

/** Имя пользователя MySQL */
define('DB_USER', 'root');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', 'cTmliCNbuU');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8mb4');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'm:vKp8lK%a`Ef!sEWZ~77eyKJ]@i,VDb]Tu/|GAz10{h<[d8e[wrr?h,,7+Z8vHW');
define('SECURE_AUTH_KEY',  'qpP8p;1XsVf>@}C-2a,gB/It/DV5j8I4*p@]{pXk)ZI}#~jjn<]^86ZCH~s+ng>p');
define('LOGGED_IN_KEY',    '3eq6^y=Q(S92b,i53WR+r$&owHJ#*!wkc*K{,(xkeWabm|-oF:0Rh;64@*ki>x??');
define('NONCE_KEY',        'O0Iz,=XJ*W^`u$uFLy^`-L}]AjU8<-|.ByICKreT:ly*a%lS+!$Np#l!%{^,c8{y');
define('AUTH_SALT',        'jw1(-H?Lf?x^(3*!FR*sL!S2gy2>b7$R#McWD?aqfelcY|bjKL>QFzB}WPc}@I<@');
define('SECURE_AUTH_SALT', '%(fa|U+c#sD;tntW#WE)s(R,*{S@0AhU8=zTNz <y>Z[.E#4RNa<K+P$!$S;avWO');
define('LOGGED_IN_SALT',   'CTh-PI~yEB434+s63}9UaTy+4BtEUI@IIZB{1E`6l2^4DEUzXt2Cyo`k70]<|#`#');
define('NONCE_SALT',       '#wo8WT^oPJe>D;-}_#C2-1Y9% KdwzA4oP52O<*c{!0e3ey#09ZT+N;Nh47Sc .B');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
