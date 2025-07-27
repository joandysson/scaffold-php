<?php
declare(strict_types=1);

namespace App\Config\Router;

class URL
{
    private static array $url = [];
    public static array $labelPages = [];

    public static function getLabelPage(string|int $url): mixed
    {
        return self::$labelPages[$url] ?? null;
    }

    public static function get(int|string ...$positions): string
    {
        self::checkURL();

        if (! count($positions)) {
            return implode('/', self::$url);
        }

        $urlReturn = [];
        foreach ($positions as $position) {
            if (! array_key_exists($position, self::$url)) {
                continue;
            }
            $urlReturn[] = self::$url[$position];
        }

        return implode('/', $urlReturn);
    }

    private static function checkURL(): void
    {
        if (count(self::$url)) {
            return;
        }

        $uri = explode('?', $_SERVER['REQUEST_URI']);
        $url = explode('/', strip_tags(rawurldecode($uri[0])));

        if (empty($url[0])) {
            array_shift($url);
        }
        self::$url = $url;
    }
}
