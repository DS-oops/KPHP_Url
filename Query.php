<?php


namespace KPurl;


/**
 * Query represents the part of a Url after the question mark (?).
 */
class Query
{
    /** @var bool */
    protected $initialized = false;
    /** @var string|null The original query string. */
    private $query;
    /**
     * @var mixed
     */
    private $data;

    /**
     * @param string|null $query
     */

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    /**
     * @return string
     */

    public function getQuery(): string
    {
        $this->initialize();

        return http_build_query($this->data);
    }

    /**
     * @param string $query
     * @return void
     */

    public function setQuery($query): void
    {
        $this->initialized = false;
        $this->query = $query;
    }

    /**
     * @return string
     */

    public function __toString(): string
    {
        return $this->getQuery();
    }

    /**
     * @param string $add
     * @return void
     */

    public function addQuery($add): void
    {
        $this->initialized = false;
        $this->query = $this->query . $add;
    }

    /**
     * @return void
     */
    protected function doInitialize(): void
    {

        parse_str((string)$this->query, $this->data);
        //$this->data = $data;
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
}
