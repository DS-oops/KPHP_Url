<?php


namespace KPurl;



/**
 * Path represents the part of a Url after the domain suffix and before the hashmark (#).
 */
class Path
{
    /** @var bool */
    protected $initialized = false;
    /** @var string|null The original path string. */
    private $path;
    /**
     * @var string[] $data
     */
    public $data = [];

    /**
     * @param $path string|null
     */
    public function __construct($path = null)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */

    public function getPath(): string
    {
        $this->initialize();

        return implode('/', array_map(static function ($value) {
            return str_replace(' ', '%20', $value);
        }, $this->data));
    }

    /**
     * @param string $path
     * @return void
     */

    public function setPath( $path): void
    {
        $this->initialized = false;
        $this->path = $path;
    }

    /**
     * @param string $add
     * @return void
     */

    public function addPath( $add): void
    {
        $this->initialized = false;
        $this->path = $this->path . $add;
    }

    /**
     * @return  string[]
     */
    public function getSegments(): array
    {
        $this->initialize();

        return $this->data;
    }

    /**
     * @return string
     */

    public function __toString(): string
    {
        return $this->getPath();
    }

    /**
     * @return void
     */

    protected function doInitialize(): void
    {
        $this->data = explode('/', (string)$this->path);
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
