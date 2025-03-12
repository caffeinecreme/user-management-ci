<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('make_api_request')) {
    function make_api_request($url, $method = 'GET', $data = null)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        switch (strtoupper($method)) {
            case 'GET':
                break;

            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;

            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;

            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;

            default:
                return false;
        }

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($error) {
            log_message('error', 'API Request Error: ' . $error);
            return false;
        }

        if ($http_code >= 400) {
            log_message('error', 'API HTTP Error: ' . $http_code . ' for URL ' . $url);
            return false;
        }

        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'API JSON Parse Error: ' . json_last_error_msg());
            return false;
        }

        return $result;
    }
}
