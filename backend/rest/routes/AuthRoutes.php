<?php

require_once __DIR__ . '/../services/UserService.class.php';

Flight::group('/auth', function () {
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
     *     summary="Login with email and password",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Password!"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful authentication",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="JWT_token_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request or missing parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email and password are required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized: invalid email or password",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid email or password")
     *         )
     *     )
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
