<?php
/**
 * Created by PhpStorm.
 * User: Shuai
 * Date: 2020/6/12
 * Time: 16:46
 */

namespace app\library;

class Help
{

    public static function hasInstallLock()
    {
        $lockFile = '/app/runtime/install.lock';
        if (is_file($lockFile)) {
            return true;
        }
        return false;
    }

    public static function makeInstallLock()
    {
        $lockFile = '/app/runtime/install.lock';
        if (is_file($lockFile)) {
            return true;
        }
        file_put_contents($lockFile, 'install.lock');
        return false;
    }

    public static function setPassword($password)
    {
        $passwordFile = '/app/runtime/password';
        file_put_contents($passwordFile, $password);
    }

    public static function getPassword()
    {
        $passwordFile = '/app/runtime/password';
        if (is_file($passwordFile)) {
            return file_get_contents($passwordFile);
        }
    }

    public static function makeAuthKey()
    {
        $authFile = '/app/runtime/auth';
        return md5(microtime(true) . rand(10000, 999999));
    }

    public static function getAuthKey()
    {
        $authFile = '/app/runtime/auth';
        return file_get_contents($authFile);
    }

    public static function setAuthKey($key = '')
    {
        $authFile = '/app/runtime/auth';
        file_put_contents($authFile, $key);
    }

    public static function makeToken()
    {
        return md5(self::getAuthKey() . self::getPassword());
    }

    public static function setTokenToCookie($exp = 86400)
    {
        cookie('token', self::makeToken(), $exp);
    }

    public static function isLogin()
    {
        $token = cookie('token');
        if ($token === self::makeToken()) {
            return true;
        }
        return false;
    }

    public static function makeApiToken()
    {
        return md5(microtime(true) . rand(10000, 999999));
    }

    public static function getApiToken()
    {
        $tokenFile = '/app/runtime/api_token';
        return file_get_contents($tokenFile);
    }

    public static function setApiToken($token)
    {
        $tokenFile = '/app/runtime/api_token';
        file_put_contents($tokenFile, $token);
    }
}