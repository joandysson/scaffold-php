<?php
declare(strict_types=1);

CONST LANG_PT = 'pt';
CONST LANG_EN = 'en';
CONST LANG_FR = 'fr';
CONST LANG_ES = 'es';


function emailView(string $view, array $data = []): mixed
{
    $fileDir = 'public' . DIRECTORY_SEPARATOR . 'email' . DIRECTORY_SEPARATOR;

    if (str_contains($view, '..')) {
        throw new RuntimeException('invalid view path');
    }

    $filePath = $fileDir . $view . '.php';
    $baseDir = realpath($fileDir) ?: $fileDir;
    $realPath = realpath($filePath);

    if ($realPath !== false && !str_starts_with($realPath, $baseDir)) {
        throw new RuntimeException('invalid view path');
    }

    if (!is_file($filePath)) {
        throw new RuntimeException('view not found');
    }

    extract($data);

    ob_start();
    include $realPath ?: $filePath;

    return ob_get_clean();
}

/**
 * @param string $file
 * @return string
 */
function asset(string $file): string
{
    return getenv('APP_URL') . '/' . "public" . '/' . "assets" . '/' . "{$file}";
}

function dd(mixed $data): void
{
    if (getenv('APP_DEBUG') === 'true') {
        echo '<pre>';
        var_dump($data);
        die;
    }

    \App\Config\Log\Log::error(is_string($data) ? $data : var_export($data, true));
}

/**
 * @param mixed $data
 * @return void
 */
function pd(mixed $data): void
{
    if (getenv('APP_DEBUG') === 'true') {
        echo '<pre>', print_r($data, true);
        die;
    }

    \App\Config\Log\Log::error(is_string($data) ? $data : var_export($data, true));
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
    $data = file_get_contents(
        'public' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . getPrefixLang() . '.json'
    );

    return json_decode($data, true) ?? [];
}

function getPrefixLang(): string
{
    $prefixLang = prefixLang();

    if(empty($prefixLang) || $prefixLang === LANG_PT || !in_array($prefixLang, languages())) {
        return LANG_PT;
    }

    return $prefixLang;
}

function prefixLang(): string
{
    $url = $_SERVER['REDIRECT_URL'] ?? ($_SERVER['REQUEST_URI'] ?? '/');
    $data = explode('/', $url);
    return $data[1] ?? '';
}

function getUri(string $uri): string
{
   return '/' . getPrefixLang() . ($uri === '/' ? '': $uri);
}

function changeLang(string $lang): string
{
    $langPrefix = array_map(fn ($value) => '/'.$value, languages());
    return match ($lang) {
        LANG_PT => str_replace($langPrefix, '/' . LANG_PT, $_SERVER['REQUEST_URI']),
        LANG_EN => str_replace($langPrefix, '/' . LANG_EN, $_SERVER['REQUEST_URI']),
        LANG_FR => str_replace($langPrefix, '/' . LANG_FR, $_SERVER['REQUEST_URI']),
        LANG_ES => str_replace($langPrefix, '/' . LANG_ES, $_SERVER['REQUEST_URI']),
        default => '/' . LANG_PT
    };
}

function languages(): array
{
    return [LANG_EN, LANG_PT, LANG_FR, LANG_ES];
}

function __(string $text): string
{
    $texts = getTextLang();

    return $texts[$text] ?? $text;
}

function getHtmlLang(): string
{
    return match (getPrefixLang()) {
        LANG_EN => 'en-US',
        LANG_FR => 'fr-FR',
        LANG_ES => 'es-ES',
        default => 'pt-BR'
    };
}

function intlFormatDate(string $date): string
{
    $date = date_create($date);
    $fmt = new IntlDateFormatter(
        'pt_br',
        IntlDateFormatter::LONG,
        IntlDateFormatter::NONE
    );

    return $fmt->format($date);

}

function section(Closure $fun) {
    ob_start();

    $fun();

    return ob_get_clean();
}

/**
 * Resolve and instantiate class dependencies for a callable or a class name.
 */
function make(callable|string $callable, array $routeParams = []): array|object
{
    if (is_string($callable) && class_exists($callable)) {
        return \App\Config\Container\Container::get($callable);
    }

    $parameters = [];

    if (is_array($callable)) {
        $ref = new \ReflectionMethod($callable[0], $callable[1]);
    } else {
        $ref = new \ReflectionFunction($callable);
    }

    foreach ($ref->getParameters() as $param) {
        $type = $param->getType();
        if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
            $className = $type->getName();
            if (class_exists($className)) {
                $object = \App\Config\Container\Container::get($className);
                if ($object instanceof \App\Config\Request\Request) {
                    $object->setRouteParams($routeParams);
                }
                $parameters[] = $object;
                continue;
            }
        }

        $name = $param->getName();
        if (array_key_exists($name, $routeParams)) {
            $parameters[] = $routeParams[$name];
        } elseif ($param->isDefaultValueAvailable()) {
            $parameters[] = $param->getDefaultValue();
        } else {
            $parameters[] = null;
        }
    }

    return $parameters;
}
