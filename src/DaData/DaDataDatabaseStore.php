<?php
namespace DaData;

use MongoDB\Database;

/**
 * Class DaDataDatabaseStore
 * @package DaData
 */
class DaDataDatabaseStore
{
    /**
     * @var DaDataDatabaseStore
     */
    protected static $_instance = null;

    /**
     * @var Database|null
     */
    protected $database = null;

    /**
     * DaDataDatabaseStore constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @param Database|null $database
     */
    public function setDatabase(?Database $database): void
    {
        $this->database = $database;
    }

    /**
     * @return Database|null
     */
    public function getDatabase(): ?Database
    {
        return $this->database;
    }

    /**
     * Retrieve an instance of the given class.
     *
     * @return DaDataDatabaseStore
     */
    public static function getInstance()
    {
        if (!isset(static::$_instance)) {
            static::$_instance = new static;
        }
        return static::$_instance;
    }

    /**
     * Protected to prevent clone.
     */
    protected function __clone()
    {
    }
}
