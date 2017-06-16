<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


#if (PHP_SAPI == 'cli-server') {
#    // To help the built-in PHP dev server, check if the request was actually for
#    // something which should probably be served as a static file
#    $url  = parse_url($_SERVER['REQUEST_URI']);
#    $file = __DIR__ . $url['path'];
#    if (is_file($file)) {
#        return false;
#    }
#}

require __DIR__ . '/../vendor/autoload.php';
spl_autoload_register(function ($classname) {
    require ("classes/" . $classname . ".php");
});
session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);
$container = $app->getContainer();

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};


$app->get('/users', function (Request $request, Response $response) {
    $this->logger->addInfo("Ticket list");
    $mapper = new UserMapper($this->db);
    $users = $mapper->getUsers();

    $data = array();

    foreach ($users as $user) {
        $data[] = array(
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'image' => $user->getImage(),
        );
    }
    $r = $response->withHeader("Content-type", "application/json");
    $r = $response->withJson($data,200);
    return $r;
});

$app->get('/user/{id}', function (Request $request, Response $response, $args) {
    $user_id = (int)$args['id'];
    $mapper = new UserMapper($this->db);
    $user = $mapper->getUserById($user_id);
    
    $data = array();

        $data = array(
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'image' => $user->getImage(),
        );
    
    $r = $response->withHeader("Content-type", "application/json");
    $r = $response->withJson($data,200);

    return $r;
})->setName('ticket-detail');

$app->delete('/user/{id}', function(Request $request, Response $response, $args)
{
    $user_id = (int)$args['id'];
    $mapper = new UserMapper($this->db);
    $user = $mapper->deleteUserById($user_id);
    if($user) { 
        $r = $response->withStatus(204);
    }else {
        $r = $response->withStatus(404);
    }
    return $r;
});






// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
#require __DIR__ . '/../src/routes.php';

// Run app
$app->run();
