<?php declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use LDL\Http\Core\Request\Request;
use LDL\Http\Core\Request\RequestInterface;

use LDL\Http\Core\Response\Response;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Handler\Exception\Collection\ExceptionHandlerCollection;
use LDL\Http\Router\Route\Dispatcher\RouteDispatcherInterface;
use LDL\Http\Router\Router;
use LDL\Http\Router\Route\Factory\RouteFactory;
use LDL\Http\Router\Route\Group\RouteGroup;
use LDL\Http\Router\Route\Config\Parser\RouteConfigParserInterface;
use LDL\Http\Router\Route\Config\Parser\RouteConfigParserCollection;
use LDL\Http\Router\Route\Route;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use LDL\Http\Router\Plugin\LDL\Log\Exception\LogExceptionHandler;

class Dispatcher implements RouteDispatcherInterface
{
    public function dispatch(
        RequestInterface $request,
        ResponseInterface $response
    )
    {
        return 'test';
    }
}

/**
 * Class ConfigParser
 *
 * Useful for plugin developers to implement a custom route configuration
 */
class ConfigParser implements RouteConfigParserInterface
{
    public function parse(
        array $data,
        Route $route,
        ContainerInterface $container = null,
        string $file=null
    ): void
    {
        if(!array_key_exists('customConfig', $data)){
            return;
        }

        $route->getConfig()->getPreDispatchMiddleware()->append(new PreDispatch());
        $route->getConfig()->getPostDispatchMiddleware()->append(new PostDispatch());
    }
}

$exceptionHandlerCollection = new ExceptionHandlerCollection();
$exceptionHandlerCollection->append(
    new LogExceptionHandler(new \Monolog\Logger('LogExceptionHandler', [new StreamHandler(__DIR__.'/test.log', Logger::DEBUG)]))
);

$parserCollection = new RouteConfigParserCollection();
$parserCollection->append(new ConfigParser());

$response = new Response();

$router = new Router(
    Request::createFromGlobals(),
    $response,
    $exceptionHandlerCollection
);

$routes = RouteFactory::fromJsonFile(
    './routes.json',
    $router,
    null,
    $parserCollection
);

$group = new RouteGroup('student', 'student', $routes);

$router->addGroup($group);

$router->dispatch()->send();
