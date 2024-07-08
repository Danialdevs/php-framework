<?php

use App\Services\UserService;
use Doctrine\DBAL\Connection;
use League\Container\Argument\Literal\ArrayArgument;
use League\Container\Argument\Literal\StringArgument;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Danial\Framework\Authentication\SessionAuthentication;
use Danial\Framework\Authentication\SessionAuthInterface;
use Danial\Framework\Console\Application;
use Danial\Framework\Console\Commands\MigrateCommand;
use Danial\Framework\Console\Kernel as ConsoleKernel;
use Danial\Framework\Controller\AbstractController;
use Danial\Framework\Dbal\ConnectionFactory;
use Danial\Framework\Event\EventDispatcher;
use Danial\Framework\Http\Kernel;
use Danial\Framework\Http\Middleware\ExtractRouteInfo;
use Danial\Framework\Http\Middleware\RequestHandler;
use Danial\Framework\Http\Middleware\RequestHandlerInterface;
use Danial\Framework\Http\Middleware\RouterDispatch;
use Danial\Framework\Routing\Router;
use Danial\Framework\Routing\RouterInterface;
use Danial\Framework\Session\Session;
use Danial\Framework\Session\SessionInterface;
use Danial\Framework\Template\TwigFactory;

$dotenv = new Dotenv();
$dotenv->load(dirname(__DIR__).'/.env');

// Application parameters
$basePath = dirname(__DIR__);
$routes = include $basePath.'/routes/web.php';
$appEnv = $_ENV['APP_ENV'] ?? 'local';
$viewsPath = $basePath.'/views';
$databaseUrl = 'pdo-mysql://lemp:lemp@database:3306/lemp?charset=utf8mb4';

// Application services

$container = new Container();

$container->add('base-path', new StringArgument($basePath));

$container->delegate(new ReflectionContainer(true));

$container->add('framework-commands-namespace', new StringArgument('Somecode\\Framework\\Console\\Commands\\'));

$container->add('APP_ENV', new StringArgument($appEnv));

$container->add(RouterInterface::class, Router::class);

$container->add(RequestHandlerInterface::class, RequestHandler::class)
    ->addArgument($container);

$container->addShared(EventDispatcher::class);

$container->add(Kernel::class)
    ->addArguments([
        $container,
        RequestHandlerInterface::class,
        EventDispatcher::class,
    ]);

$container->addShared(SessionInterface::class, Session::class);

$container->add('twig-factory', TwigFactory::class)
    ->addArguments([
        new StringArgument($viewsPath),
        SessionInterface::class,
        SessionAuthInterface::class,
    ]);

$container->addShared('twig', function () use ($container) {
    return $container->get('twig-factory')->create();
});

$container->inflector(AbstractController::class)
    ->invokeMethod('setContainer', [$container]);

$container->add(ConnectionFactory::class)
    ->addArgument(new StringArgument($databaseUrl));

$container->addShared(Connection::class, function () use ($container): Connection {
    return $container->get(ConnectionFactory::class)->create();
});

$container->add(Application::class)
    ->addArgument($container);

$container->add(ConsoleKernel::class)
    ->addArgument($container)
    ->addArgument(Application::class);

$container->add('console:migrate', MigrateCommand::class)
    ->addArgument(Connection::class)
    ->addArgument(new StringArgument($basePath.'/database/migrations'));

$container->add(RouterDispatch::class)
    ->addArguments([
        RouterInterface::class,
        $container,
    ]);

$container->add(SessionAuthInterface::class, SessionAuthentication::class)
    ->addArguments([
        UserService::class,
        SessionInterface::class,
    ]);

$container->add(ExtractRouteInfo::class)
    ->addArgument(new ArrayArgument($routes));

return $container;
