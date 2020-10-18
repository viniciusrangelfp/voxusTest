<?php

namespace Voxus\Core\connection;

/**
 * Interface DBInterface
 *
 * @package Voxus\Core\connection
 */
interface DBInterface {

    /**
     * @return mixed Requires to every connection implement the method conn
     */
    public function conn();
}
