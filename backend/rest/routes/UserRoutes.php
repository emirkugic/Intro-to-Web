<?php

require_once __DIR__ . '/../services/UserService.class.php';
require_once __DIR__ . '/../../middleware.php';

Flight::group('/users', function () {

    Flight::route('GET /', function () {
        authorize("ADMIN");
        $request = Flight::request();
        $order = isset($request->query['order']) ? $request->query['order'] : '-id';

        $user_service = new UserService();
        $users = $user_service->get_all_users($order);
        Flight::json($users);
    });

    Flight::route('GET /@id', function ($id) {
        $user = Flight::get('user');
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $id) {
            Flight::halt(403, 'Access Denied');
        }
        $user_service = new UserService();
        $user = $user_service->get_user_by_id($id);
        if ($user) {
            Flight::json($user);
        } else {
            Flight::halt(404, 'User not found');
        }
    });

    Flight::route('POST /add', function () {
        authorize("ADMIN");
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
        $user = Flight::get('user');
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $id) {
            Flight::halt(403, 'Access Denied');
        }
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
        $user = Flight::get('user');
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $id) {
            Flight::halt(403, 'Access Denied');
        }
        $user_service = new UserService();
        $success = $user_service->delete_user_by_id($id);
        if ($success) {
            Flight::json(['message' => "User successfully deleted"], 200);
        } else {
            Flight::halt(404, 'User not found or could not be deleted');
        }
    });
});
