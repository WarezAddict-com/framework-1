<?php declare(strict_types=1);

namespace Igni\Tests\Functional\Http\Controller;

use Igni\Http\Controller\ControllerAggregate;
use Igni\Http\Router\Route;
use Igni\Http\Router\Router;
use Igni\Tests\Fixtures\HttpController;
use Mockery;
use PHPUnit\Framework\TestCase;

class ControllerAggregateTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(
            ControllerAggregate::class,
            new ControllerAggregate(Mockery::mock(Router::class))
        );
    }

    public function testAddCallableController(): void
    {
        $controller = function() {};
        $route = Mockery::mock(Route::class);
        $route->shouldReceive('withController')
            ->withArgs([$controller]);

        $router = Mockery::mock(Router::class);
        $router->shouldReceive('addRoute')
            ->withArgs(function($route) {
                self::assertInstanceOf(\Igni\Http\Route::class, $route);
                return true;
            });
        $aggregate = new ControllerAggregate($router);

        self::assertNull($aggregate->add($controller, $route));
    }

    public function testAddControllerClass(): void
    {
        $controller = HttpController::class;

        $router = Mockery::mock(Router::class);
        $router->shouldReceive('addRoute')
            ->withArgs(function(Route $route) {
                self::assertSame(HttpController::URI, $route->getPath());
                return true;
            });
        $aggregate = new ControllerAggregate($router);

        self::assertNull($aggregate->add($controller));
    }

    public function testAddControllerObject(): void
    {
        $controller = new HttpController();

        $router = Mockery::mock(Router::class);
        $router->shouldReceive('addRoute')
            ->withArgs(function(Route $route) {
                self::assertInstanceOf(Route::class, $route);
                self::assertSame(HttpController::URI, $route->getPath());
                return true;
            });
        $aggregate = new ControllerAggregate($router);

        self::assertNull($aggregate->add($controller));
    }
}
