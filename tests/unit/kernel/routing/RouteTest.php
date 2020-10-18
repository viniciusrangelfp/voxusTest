<?php

use PHPUnit\Framework\TestCase;
use Voxus\Core\routing\Route;

class RouteTest extends TestCase
{

    public function testControllerExist(){
        $this->expectError();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage( 'Controller doest not exist');
        new Route('/api/user', \Voxus\App\controller\DataController::class,'getMethod');
    }

    public function testIfItIsAController()
    {
        $this->expectError();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('This object it is not a instance of controller');

        new Route('/api/user', \Voxus\App\controller\Controller::class,'getMethod');
    }

    public function testMethodNotExist()
    {
        $this->expectError();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Method');

        new Route('/api/user', \Voxus\App\controller\UserController::class,'getMethod');
    }

    public function testRouteCreation()
    {
        $route = new Route('/api/user', \Voxus\App\controller\UserController::class,'get');
        $this->assertSame(true,$route->isValidRoute());
    }

}


