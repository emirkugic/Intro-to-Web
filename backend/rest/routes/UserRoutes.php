<?php

require_once __DIR__ . '/../services/UserService.class.php';

// Flight::route('/', function () {
//     echo 'Hello from User Service!';
// });

Flight::group('/users', function () {
    Flight::route('GET /', function () {
        $request = Flight::request();
        $order = isset($request->query['order']) ? $request->query['order'] : '-id';

        $user_service = new UserService();
        $users = $user_service->get_all_users($order);
        Flight::json($users);
    });

    Flight::route('GET /@id', function ($id) {
        $user_service = new UserService();
        $user = $user_service->get_user_by_id($id);
        if ($user) {
            Flight::json($user);
        } else {
            Flight::halt(404, 'User not found');
        }
    });

    // Register new user
    Flight::route('POST /add', function () {
        $data = Flight::request()->data->getData();
        $user_service = new UserService();
        $user = $user_service->add_user($data);
        if ($user) {
            Flight::json($user, 201);
        } else {
            Flight::halt(400, 'Failed to create user');
        }
    });

    Flight::route('PUT /update/@id', function ($id) {
        $data = Flight::request()->data->getData();
        $user_service = new UserService();
        $updated_user = $user_service->update_user($id, $data);
        if ($updated_user) {
            Flight::json($updated_user, 200);
        } else {
            Flight::halt(400, 'Failed to update user');
        }
    });

    Flight::route('DELETE /delete/@id', function ($id) {
        $user_service = new UserService();
        $success = $user_service->delete_user_by_id($id);
        if ($success) {
            Flight::json(['message' => "User successfully deleted"], 200);
        } else {
            Flight::halt(404, 'User not found or could not be deleted');
        }
    });
});
