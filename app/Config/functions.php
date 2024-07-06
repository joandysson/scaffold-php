<?php

CONST LANG_PT = 'pt';
CONST LANG_EN = 'en';
CONST LANG_FR = 'fr';
CONST LANG_ES = 'es';


/**
 * @return bool
 */
function isMobileDevice(): bool
{
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

/**
 * @param string $view
 * @param array $data
 * @param boolean $isMail
 * @return mixed
 */
function view(string $view, array $data = [], bool $isMail = false): mixed
{
    extract($data);

    $filename = $isMail ? "public" . DIRECTORY_SEPARATOR . "template" . DIRECTORY_SEPARATOR . "email" . DIRECTORY_SEPARATOR . $view . ".php" : "public" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . $view . ".php";

    if (!is_file($filename)) {
        exit('view not found');
    }

    ob_start();
    include $filename;

    if ($isMail) return ob_get_clean();

    exit(ob_get_clean());
}

/**
 * @param string $file
 * @return string
 */
function asset(string $file): string
{
    return getenv('APP_URL') . '/' . "public" . '/' . "assets" . '/' . "{$file}";
}

/**
 * @param mixed $data
 * @return void
 */
function dd(mixed $data): void
{
    echo '<pre>', var_dump($data);
    die;
}

/**
 * @param mixed $data
 * @return void
 */
function pd(mixed $data): void
{
    echo '<pre>', print_r($data, true);
    die;
}

/**
 * @param string $file
 * @return void
 */
function loadCSS(string $file): void
{
    $filePath = dirname(__DIR__) . '/public/assets/css/' . $file;
    $version = filemtime($filePath);
    $style = "<link rel='stylesheet' href='" . asset("css/$file") . "?v=$version' type='text/css'>";
    echo $style;
}

/**
 * @param bool $enable
 * @return void
 */
function errorReporting(bool $enable): void
{
    error_reporting(E_ALL);
    ini_set('display_errors', $enable);
}

function getTextLang(): array
{
    $data = file_get_contents('public' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . prefixLang() . '.json');

    return json_decode($data, true);
}

function getPrefixLang(): string
{
    $prefixLang = prefixLang();

    if(empty($prefixLang) || $prefixLang === LANG_PT || !in_array($prefixLang, langs())) {
        return LANG_PT;
    }

    return $prefixLang;
}

function prefixLang() {
    $data = explode('/', $_SERVER['REDIRECT_URL']);
    return $data[1];
}

function getUri(string $uri)
{
   return '/' . getPrefixLang() . ($uri === '/' ? '': $uri);
}

function isMultiLanguage(): true
{
    return true;
}

function changeLang(string $lang): string
{
    return match ($lang) {
        LANG_PT => str_replace(langs(), LANG_PT, $_SERVER['REQUEST_URI']),
        LANG_EN => str_replace(langs(), LANG_EN, $_SERVER['REQUEST_URI']),
        LANG_FR => str_replace(langs(), LANG_FR, $_SERVER['REQUEST_URI']),
        LANG_ES => str_replace(langs(), LANG_ES, $_SERVER['REQUEST_URI']),
        default => '/' . LANG_PT
    };
}

function langs(): array
{
    return [LANG_EN, LANG_PT, LANG_FR, LANG_ES, LANG_ES];
}
