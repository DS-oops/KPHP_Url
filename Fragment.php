<?php


namespace KPurl;

require_once "Query.php";

require_once "Path.php";


class Fragment
{
    /** @var bool */
    protected $initialized = false;
    /** @var string|null The original fragment string. */
    private $fragment;
    /** @var Path */
    public $path;
    /** @var Query */
    public $query;


    /**
     * @param string|Path|null $fragment
     * @param Query|null|string $query
     */

    public function __construct($fragment = null, $query = null)
    {
    }

    /**
     * @param $fragment string
     * @return Fragment
     */
    public static function from_string($fragment)
    {
        $Fragment = new self ();
        $Fragment->setFragment($fragment);
        return $Fragment;
    }

    /**
     * @param $path Path
     * @return Fragment
     */
    public static function from_path($path)
    {
        $Fragment = new self ();
        $Fragment->setPath($path);
        return $Fragment;
    }

    /**
     * @return string
     */

    public function getFragment(): string
    {
        $this->initialize();

        return sprintf(
            '%s%s',
            (string)$this->path,
            (string)$this->query !== '' ? '?' . (string)$this->query : ''
        );
    }

    /**
     * @param $fragment string
     * @return void
     */

    public function setFragment($fragment): void
    {
        $this->initialized = false;
        //$this->data = [];
        $this->fragment = $fragment;


    }

    /**
     * @param $path Path
     * @return void
     */
    public function setPath(Path $path): void
    {

        $this->path = $path;

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
     * @param Query $query
     * @return void
     */
    public function setQuery($query): void
    {
        $this->query = $query;
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
     * @return string
     */
    public function __toString(): string
    {
        return $this->getFragment();
    }

    /**
     * @return void
     * @var $key string
     * @var $value Query|Path|string
     */
    protected function doInitialize(): void
    {
        if ($this->fragment !== null && !$this->hasPath()) {
            //$parsed = parse_url($this->fragment);
            $this->path = new Path(strval(parse_url($this->fragment, PHP_URL_PATH)));
        }
        if ($this->fragment !== null && !$this->hasQuery()) {
            $this->query = new Query(strval(parse_url($this->fragment, PHP_URL_QUERY)));

        }
    }

    protected function initialize(): void
    {
        if ($this->initialized === true) {
            return;
        }

        $this->initialized = true;

        $this->doInitialize();
    }


    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    public function hasQuery(): bool
    {
        $this->initialize();

        return isset($this->query);
    }

    public function hasPath(): bool
    {
        $this->initialize();

        return isset($this->path);
    }

    public function removeQuery(): void
    {
        $this->initialize();

        unset($this->query);


    }

    public function removePath(): void
    {
        $this->initialize();

        unset($this->path);


    }

}
