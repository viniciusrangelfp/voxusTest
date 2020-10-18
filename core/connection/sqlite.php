<?php

namespace Voxus\Core\connection;

/**
 * Class sqlite
 *
 * @package Voxus\Core\connection
 */
class sqlite implements DBInterface
{
    /**
     * @var \PDO hold sqlite connection
     */
    private \PDO $conn;

    /**
     * sqlite constructor.
     *
     * @param $SQLitePath
     * @return $this
     */
    public function __construct($SQLitePath)
    {
        $this->conn = new \PDO('sqlite:' . $SQLitePath);

        return $this;
    }

    /**
     * @return mixed|\PDO
     */
    public function conn()
    {
        return $this->conn;
    }


}
