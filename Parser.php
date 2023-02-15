<?php


namespace KPurl;

require_once "Query.php";
require_once "Url.php";
require_once "Path.php";
require_once "Fragment.php";

use InvalidArgumentException;


/**
 * Parser class.
 */
class Parser
{

    /**
     * @param string|null $url
     * @return string[]
     * @var $parsedUrl string []
     */
    public function parseUrl($url): array
    {
        $parsedUrl = [];
        $parsedUrl ['scheme'] = strval(parse_url($url, PHP_URL_SCHEME));
        $parsedUrl ['host'] = strval(parse_url($url, PHP_URL_HOST));
        $parsedUrl ['port'] = strval(parse_url($url, PHP_URL_PORT));
        $parsedUrl ['user'] = strval(parse_url($url, PHP_URL_USER));
        $parsedUrl ['pass'] = strval(parse_url($url, PHP_URL_PASS));
        $parsedUrl ['path'] = strval(parse_url($url, PHP_URL_PATH));
        $parsedUrl ['query'] = strval(parse_url($url, PHP_URL_QUERY));
        $parsedUrl ['fragment'] = strval(parse_url($url, PHP_URL_FRAGMENT));

        if (empty($parsedUrl)) {
            throw new InvalidArgumentException(sprintf('Invalid url %s', $url));
        }


        if (!empty($parsedUrl['host'])) {

            $parsedUrl['canonical'] = implode('.', array_reverse(explode('.', $parsedUrl['host']))) . ($parsedUrl['path'] ?? '') . (isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '');

            $parsedUrl['resource'] = $parsedUrl['path'] ?? '';

            if (isset($parsedUrl['query'])) {
                $parsedUrl['resource'] .= '?' . $parsedUrl['query'];
            }
        }


        return $parsedUrl;
    }


    /**
     * @param string $url
     * @return mixed
     */
    protected function doParseUrl($url): mixed
    {
        $parsedUrl = parse_url($url);

        return $parsedUrl !== false ? $parsedUrl : [];
    }
}
