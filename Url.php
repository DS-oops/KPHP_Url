<?php


namespace KPurl;
require_once "Query.php";
require_once "Parser.php";
require_once "Path.php";
require_once "Fragment.php";


/**
 * Url is a simple OO class for manipulating Urls in PHP.
 *
 * @property string $scheme
 * @property string $host
 * @property int $port
 * @property string $user
 * @property string $pass
 * @property Path|string $path
 * @property Query|string $query
 * @property Fragment|string $fragment
 * @property string $canonical
 * @property string $resource
 */
class Url
{
    /** @var bool */
    protected $initialized = false;
    /** @var string|null The original url string. */
    private $url;

    /** @var Parser|null */
    private $parser;
    /**
     * @var Path
     */
    private $path;
    /**
     * @var Query
     */
    private $query;
    /**
     * @var Fragment
     */
    public $fragment;
    /** @var string [] */
    public $data = [
        'scheme' => '',
        'host' => '',
        'port' => '',
        'user' => '',
        'pass' => '',
        'publicSuffix' => '',
        'registerableDomain' => '',
        'subdomain' => '',
        'canonical' => '',
        'resource' => '',
    ];

    /**
     * @param string|null $url
     * @param Parser|null $parser
     */
    public function __construct($url = null, $parser = null)
    {
        $this->url = $url;
        $this->parser = $parser;
    }

    /**
     * @param string $url
     * @return Url
     */

    public static function parse($url): Url
    {
        return new self($url);
    }

    /**
     * @param string $string
     * @return Url[] $urls
     */
    public static function extract($string): array
    {
        $regex = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,}(\/\S*)?/';

        preg_match_all($regex, $string, $matches);
        $urls = [];
        foreach ($matches[0] as $url) {
            $urls[] = self::parse(strval($url));
        }

        return $urls;
    }

    /**
     * @return Url
     */

    public static function fromCurrent(): Url
    {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === 443 ? 'https' : 'http';

        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = sprintf('%s://%s', $scheme, $host);

        $url = new self($baseUrl);

        if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']) {
            if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
                [$path, $query] = explode('?', $_SERVER['REQUEST_URI'], 2);
            } else {
                $path = $_SERVER['REQUEST_URI'];
                $query = '';
            }

            $url->setPathFromString(strval($path));
            $url->setQueryFromString(strval($query));
        }

        // Only set port if different from default (80 or 443)
        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT']) {
            $port = $_SERVER['SERVER_PORT'];
            if (($scheme === 'http' && $port !== 80) ||
                ($scheme === 'https' && $port !== 443)) {
                $url->set('port', strval($port));
            }
        }

        // Authentication
        if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER']) {
            $url->set('user', strval($_SERVER['PHP_AUTH_USER']));
            if (isset($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_PW']) {
                $url->set('pass', strval($_SERVER['PHP_AUTH_PW']));
            }
        }

        return $url;
    }

    /**
     * @return Parser
     */
    public function getParser(): Parser
    {
        if ($this->parser === null) {
            $this->parser = self::createDefaultParser();
        }

        return $this->parser;
    }

    /**
     * @param Parser $parser
     * @return void
     */
    public function setParser($parser): void
    {
        $this->parser = $parser;
    }

    /**
     * @param Url $url
     * @return Url
     */
    public function join($url): Url
    {
        $this->initialize();
        $parts = $this->getParser()->parseUrl($url->__toString());

        if ($this->data['scheme'] !== null) {
            $parts['scheme'] = $this->data['scheme'];
        }

        foreach ($parts as $k => $v) {
            if (empty($v)) {
                continue;
            }

            $this->fillData($k, $v);
        }


        return $this;
    }

    /**
     * @param string $value
     * @param string $key
     * @return void
     */
    public function set($key, $value): void
    {
        $this->initialize();

        $this->data[$key] = $value;


    }

    /**
     * @param Path $path
     * @return void
     */

    public function setPath($path): void
    {
        $this->initialize();
        $this->path = $path;
        $this->data['path'] = $this->path->getPath();


    }

    /**
     * @param string $path
     * @return void
     */
    public function setPathFromString($path): void
    {
        $this->initialize();
        $this->path = new Path($path);
        $this->data['path'] = $this->path->getPath();


    }

    /**
     * @param string $add
     * @return void
     */

    public function addPathFromString($add): void
    {
        $this->path->addPath($add);
    }

    /**
     * @param Path $add
     * @return void
     */

    public function addPath($add): void
    {
        $this->initialize();
        $this->path->addPath($add->getPath());
    }

    /**
     * @return string
     */

    public function getPath(): string
    {
        $this->initialize();

        return $this->path->getPath();


    }

    /**
     * @param string $key
     * @return string
     */
    public function getData($key)
    {
        $this->initialize();
        return $this->data[$key];
    }

    /**
     * @param Query $query
     * @return void
     */
    public function setQuery($query): void
    {
        //$this->query = new Query($query);
        $this->initialize();
        $this->query = $query;
        $this->data['query'] = $this->query->getQuery();


    }

    /**
     * @param string $query
     * @return void
     */

    public function setQueryFromString($query): void
    {
        $this->initialize();
        $this->query = new Query($query);
        $this->data['query'] = $this->query->getQuery();


    }

    /**
     * @param string $add
     * @return void
     */

    public function addQueryFromString($add): void
    {
        $this->query->addQuery($add);
    }

    /**
     * @param Query $add
     * @return void
     */
    public function addQuery($add): void
    {
        $this->query->addQuery($add->getQuery());
    }

    /**
     * @return string
     */

    public function getQuery(): string
    {
        $this->initialize();

        return $this->query->getQuery();
    }

    /**
     * @param Fragment $fragment
     * @return void
     */
    public function setFragment($fragment): void
    {
        $this->fragment = $fragment;
        $this->data['fragment'] = $this->fragment->getFragment();


    }

    /**
     * @return Fragment
     */

    public function getFragment(): Fragment
    {
        $this->initialize();

        return $this->fragment;
    }

    /**
     * @return string
     */

    public function getNetloc(): string
    {
        $this->initialize();

        return ($this->data['user'] !== null && $this->data['pass'] !== null ? $this->data['user'] . ($this->data['pass'] !== null ? ':' . $this->data['pass'] : '') . '@' : '') . $this->data['host'] . ($this->data['port'] !== null ? ':' . $this->data['port'] : '');
    }

    /**
     * @return string
     */

    public function getUrl(): string
    {
        $this->initialize();
        $this->data['path'] = $this->path->getPath();
        $this->data['query'] = $this->query->getQuery();
        $this->data['fragment'] = $this->fragment->getFragment();
        $parts = array_map('strval', $this->data);

        if (!$this->isAbsolute()) {
            return self::httpBuildRelativeUrl($parts);
        }

        return self::httpBuildUrl($parts);
    }

    /**
     * @param string $url
     * @return void
     */

    public function setUrl($url): void
    {
        $this->initialized = false;
        $this->data = [];
        $this->url = $url;
    }

    /**
     * @return bool
     */

    public function isAbsolute(): bool
    {
        $this->initialize();

        return !empty($this->data['scheme']) && !empty($this->data['host']);
    }

    /**
     * @return string
     */

    public function __toString(): string
    {
        return $this->getUrl();
    }

    /**
     * @return void
     */

    protected function doInitialize(): void
    {
        $parts = $this->getParser()->parseUrl($this->url);

        foreach ($parts as $k => $v) {
            $this->fillData($k, $v);

        }

    }

    /**
     * @param string[] $parts
     * @return string
     */
    private static function httpBuildUrl($parts): string
    {
        $relative = self::httpBuildRelativeUrl($parts);

        $pass = !empty($parts['pass']) ? sprintf(':%s', $parts['pass']) : '';
        $auth = !empty($parts['user']) ? sprintf('%s%s@', $parts['user'], $pass) : '';
        $port = !empty($parts['port']) ? sprintf(':%d', $parts['port']) : '';

        return sprintf(
            '%s://%s%s%s%s',
            $parts['scheme'],
            $auth,
            $parts['host'],
            $port,
            $relative
        );
    }

    /**
     * @param string[] $parts
     * @return string
     */
    private static function httpBuildRelativeUrl($parts): string
    {
        if (!empty($parts['path'])) {
            $parts['path'] = ltrim($parts['path'], '/');
        }
        return sprintf(
            '/%s%s%s',
            $parts['path'],
            !empty($parts['query']) ? '?' . $parts['query'] : '',
            !empty($parts['fragment']) ? '#' . $parts['fragment'] : ''
        );


    }

    /**
     * @return Parser
     */

    private static function createDefaultParser(): Parser
    {
        return new Parser();
    }

    /**
     * @return void
     */
    protected function initialize(): void
    {
        if ($this->initialized === true) {
            return;
        }

        $this->initialized = true;

        $this->doInitialize();
    }

    /**
     * @param int|string $k
     * @param string $v
     * @return void
     */
    public function fillData($k, $v): void
    {
        if ($k == 'path') {
            $this->setPath(new Path(($v)));
        } elseif ($k == 'query') {
            $this->setQuery(new Query(($v)));
        } elseif ($k == 'fragment') {
            $this->setFragment(Fragment::from_string($v));
        } else {
            $this->data[$k] = $v;
        }
    }
}
