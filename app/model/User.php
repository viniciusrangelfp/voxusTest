<?php

namespace Voxus\App\Model;

use Voxus\Core\ORM\Model;

/**
 * Class User
 */
class User extends Model
{

    /**
     * @var string
     */
    protected string $tableName = 'user';

    /**
     * User id
     */
    public $id;

    /**
     * user name
     */
    public $name;

    /**
     * last latitude position
     */
    public $lat;

    /**
     * ast longitude position
     */
    public $long;

}
