<?php

require_once __DIR__ . '/../services/ImageService.class.php';

/**
 * @OA\Post(
 *     path="/images/upload",
 *     summary="Upload a user's profile picture",
 *     tags={"Images"},
 *     security={{"ApiKey": {}}},
 *     @OA\RequestBody(
 *         description="Image upload data",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="binary",
 *                     description="The image file to upload"
 *                 ),
 *                 @OA\Property(
 *                     property="user_id",
 *                     type="integer",
 *                     description="ID of the user uploading the image"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful image upload and user profile update",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="user", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Failed to upload image"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to upload image or update user profile picture"
 *     )
 * )
 */
Flight::route('POST /images/upload', function () {
    $user_id = Flight::request()->data->user_id;
    if (!isset($_FILES['image'])) {
        Flight::halt(400, 'Image file is required');
    }

    $file = $_FILES['image'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
        ];
        $error_message = isset($error_messages[$file['error']]) ? $error_messages[$file['error']] : 'Unknown error';
        Flight::halt(400, 'Failed to upload image: ' . $error_message);
    }

    $image_path = $file['tmp_name'];

    $image_service = new ImageService();
    $imgur_response = $image_service->upload_to_imgur($image_path);

    if (isset($imgur_response['success']) && $imgur_response['success']) {
        $updated_user = $image_service->update_user_profile_picture($user_id, $imgur_response['data']['link']);

        if ($updated_user) {
            Flight::json(['success' => true, 'user' => $updated_user]);
        } else {
            Flight::halt(500, 'Failed to update user profile picture');
        }
    } else {
        $error_message = isset($imgur_response['error']) ? json_encode($imgur_response['error']) : 'Unknown error';
        Flight::halt(500, 'Failed to upload image to Imgur: ' . $error_message);
    }
});
