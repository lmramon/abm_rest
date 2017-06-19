<?php
// Routes
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


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

$app->get('/users/{id}', function (Request $request, Response $response, $args) {
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
});

$app->delete('/users{id}', function(Request $request, Response $response, $args)
{
    $user_id = (int)$args['id'];
    $mapper = new UserMapper($this->db);
    $user = $mapper->deleteUserById($user_id);
    if($user) { 
        $newResponse = $response->withStatus(204);
    }else {
        $newResponse = $response->withStatus(404);
    }
    return $newResponse;
});

$app->post('/user/new', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $user_data = [];
    $user_data['name'] = filter_var($data['title'], FILTER_SANITIZE_STRING);
    $user_data['email'] = filter_var($data['description'], FILTER_SANITIZE_EMAIl);
    
    $user = new UsertEntity($user_data);
    $user_mapper = new UserMapper($this->db);
    $user_mapper->save($user);

    $newResponse = $response->withRedirect("/users");
    return $newResponse;
});
