<?php

namespace Voxus\Core\connection;

/**
 * Class DB invoke the current database connection
 */
class DB {
    /**
     * hold the connection in a singleton way
     */
    private static $conn;

    /**
     * @param DBInterface $database
     */
    public static function init(\Voxus\Core\connection\DBInterface $database){
        if(!self::$conn){
            self::$conn = $database->conn();
        }
    }

    /**
     * @return \Voxus\Core\connection\DBInterface
     */
    public static function conn(){
        return self::$conn;
    }
}
