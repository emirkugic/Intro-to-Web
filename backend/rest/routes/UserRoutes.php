<?php

require_once __DIR__ . '/../services/UserService.class.php';

Flight::route('/', function () {
    echo 'Hello from User Service!';
});

Flight::group('/users', function () {
    Flight::route('GET /', function () {
        $offset = Flight::query('offset', 0);
        $limit = Flight::query('limit', 25);
        $order = Flight::query('order', '-id');

        $user_service = new UserService();
        $users = $user_service->get_all_users($offset, $limit, $order);
        Flight::json($users);
    });

    Flight::route('GET /@id', function ($id) {
        $user_service = new UserService();
        $user = $user_service->get_user_by_id($id);
        Flight::json($user);
    });

    Flight::route('POST /add', function () {
        $data = Flight::request()->data->getData();
        $user_service = new UserService();
        $user = $user_service->add_user($data);
        Flight::json($user);
    });

    Flight::route('PUT /update/@id', function ($id) {
        $data = Flight::request()->data->getData();
        $user_service = new UserService();
        $updated_user = $user_service->update_user($id, $data);
        Flight::json($updated_user);
    });

    Flight::route('DELETE /delete/@id', function ($id) {
        $user_service = new UserService();
        $user_service->delete_user_by_id($id);
        Flight::json(['message' => "User successfully deleted"]);
    });
});
