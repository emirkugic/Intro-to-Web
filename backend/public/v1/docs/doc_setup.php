<?php

/**
 * @OA\Info(
 *   title="API",
 *   description="Intro to web API",
 *   version="1.0",
 *   @OA\Contact(
 *     email="emir.kugic@stu.ibu.edu.ba",
 *     name="Emir Kugic"
 *   )
 * ),
 * @OA\OpenApi(
 *   @OA\Server(
 *       url=BASE_URL
 *   )
 * )
 * @OA\SecurityScheme(
 *     securityScheme="ApiKeyAuth",
 *     type="apiKey",
 *     in="header",
 *     name="Authentication"
 * )
 */
