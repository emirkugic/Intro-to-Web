<?php

require_once __DIR__ . '/../dao/UserDao.class.php';

class ImageService
{
    private $user_dao;

    public function __construct()
    {
        $this->user_dao = new UserDao();
    }

    public function upload_to_imgur($image_path)
    {
        $clientId = "ab0ad10e479e687";
        $url = "https://api.imgur.com/3/image";
        $headers = ["Authorization: Client-ID $clientId"];
        $data = ['image' => base64_encode(file_get_contents($image_path))];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            error_log("cURL error: " . $curl_error);
            return ['success' => false, 'error' => $curl_error];
        }

        $response_data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error: " . json_last_error_msg());
            return ['success' => false, 'error' => 'Failed to decode JSON response'];
        }

        return $response_data;
    }

    public function update_user_profile_picture($user_id, $image_url)
    {
        $user = $this->user_dao->get_user_by_id($user_id);
        if (!$user) {
            return null;
        }
        $user['profile_picture_url'] = $image_url;
        $this->user_dao->update_user($user_id, $user);
        return $user;
    }
}
