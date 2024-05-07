<?php

require_once __DIR__ . '/../services/UserService.class.php';

Flight::group('/auth', function () {


    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"first_name", "last_name", "email", "password", "profile_picture_url", "phone"},
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                     example="John"
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                     example="Doe"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     example="user@example.com"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     example="yourpassword"
     *                 ),
     *                 @OA\Property(
     *                     property="profile_picture_url",
     *                     type="string",
     *                     example="https://example.com/profile.jpg"
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     example="1234567890"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 property="first_name",
     *                 type="string",
     *                 example="John"
     *             ),
     *             @OA\Property(
     *                 property="last_name",
     *                 type="string",
     *                 example="Doe"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 example="user@example.com"
     *             ),
     *             @OA\Property(
     *                 property="profile_picture_url",
     *                 type="string",
     *                 example="https://example.com/profile.jpg"
     *             ),
     *             @OA\Property(
     *                 property="phone",
     *                 type="string",
     *                 example="1234567890"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Missing or invalid parameter: {parameter_name}"
     *             )
     *         )
     *     )
     * )
     */

    Flight::route('POST /register', function () {
        $data = Flight::request()->data->getData();

        $data = array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $data);


        foreach ($data as $key => $value) {
            if (empty($value) && $key != 'profile_picture_url') {
                Flight::halt(400, "Missing or invalid parameter: $key");
                return;
            }
        }

        $user_service = new UserService();
        $user = $user_service->add_user($data);
        if ($user) {
            Flight::json($user, 201);
        } else {
            Flight::halt(400, "User could not be registered");
        }
    });


    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Login user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email", "password"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     example="user@example.com"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     example="yourpassword"
     *                 )
     *             )
     *         )
     *     ),
     * )
     */
    Flight::route('POST /login', function () {
        $data = Flight::request()->data->getData();
        $email = trim($data['email']);
        $password = $data['password'];

        if (empty($email) || empty($password)) {
            Flight::halt(400, "Email and password are required");
            return;
        }

        $user_service = new UserService();
        $user = $user_service->authenticate_user($email, $password);
        if ($user) {
            $jwt = $user['token'];
            Flight::json(['token' => $jwt]);
        } else {
            Flight::halt(401, "Invalid email or password");
        }
    });
});
