<?php

namespace app\library;

class Docker
{

    const DOMAIN = 'http://localhost';
    const SOCK = '/var/run/docker.sock';

    public static function get($url)
    {
        return self::curl('GET', $url);
    }

    public static function post($url, $params = [])
    {
        return self::curl('POST', $url, $params);
    }

    public static function put($url, $params = [])
    {
        return self::curl('PUT', $url, $params);
    }

    public static function delete($url)
    {
        return self::curl('DELETE', $url);
    }

    public static function curl($method = '', $url = '', $params = [])
    {
        $ch = curl_init(self::DOMAIN . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_UNIX_SOCKET_PATH, self::SOCK);

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        $result = [
            'data' => json_decode($result, true),
            'info' => $info
        ];
        return $result;
    }
}