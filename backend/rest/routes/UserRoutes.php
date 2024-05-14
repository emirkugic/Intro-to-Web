<?php

require_once __DIR__ . '/../services/UserService.class.php';
require_once __DIR__ . '/../../middleware.php';

/**
 * @OA\Schema(
 *     schema="User",
 *     required={"id", "email", "first_name", "last_name"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Unique identifier for the user"
 *     ),
 *     @OA\Property(
 *         property="first_name",
 *         type="string",
 *         description="First name of the user"
 *     ),
 *     @OA\Property(
 *         property="last_name",
 *         type="string",
 *         description="Last name of the user"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Email address of the user"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         description="Password for the user account"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Phone number of the user"
 *     ),
 *     @OA\Property(
 *         property="profile_picture_url",
 *         type="string",
 *         description="URL of the user's profile picture"
 *     ),
 *     @OA\Property(
 *         property="role",
 *         type="string",
 *         description="Role of the user within the system"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Status of the user's account"
 *     )
 * )
 */
Flight::group('/users', function () {


    /**
     * @OA\Get(
     *     path="/users/all",
     *     summary="List all users",
     *     tags={"Users"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         required=false,
     *         description="Ordering of returned users",
     *         @OA\Schema(type="string", example="-id")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of user list",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
    Flight::route('GET /all', function () {
        authorize("ADMIN");
        $request = Flight::request();
        $order = isset($request->query['order']) ? $request->query['order'] : '-id';

        $user_service = new UserService();
        $users = $user_service->get_all_users($order);
        Flight::json($users);
    });


    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Get a single user by ID",
     *     tags={"Users"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of user",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
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



    /**
     * @OA\Post(
     *     path="/users/add",
     *     summary="Add a new user",
     *     tags={"Users"},
     *     security={{"ApiKey": {}}},
     *     @OA\RequestBody(
     *         description="User data to add",
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "email", "password"},
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="profile_picture_url", type="string"),
     *             @OA\Property(property="phone", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully created",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to create user"
     *     )
     * )
     */
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


    /**
     * @OA\Put(
     *     path="/users/update/{id}",
     *     summary="Update an existing user",
     *     tags={"Users"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         description="Data for updating the user",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="profile_picture_url", type="string"),
     *             @OA\Property(property="phone", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully updated",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to update user"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/users/delete/{id}",
     *     summary="Delete a user by ID",
     *     tags={"Users"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="User successfully deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found or could not be deleted"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
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
