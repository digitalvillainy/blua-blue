<?php
/* Generated by neoan3-cli */

namespace Neoan3\Components;

use Neoan3\Apps\Db;
use Neoan3\Apps\Session;
use Neoan3\Apps\Stateless;
use Neoan3\Core\RouteException;
use Neoan3\Core\Unicore;
use Neoan3\Frame\Neoan;
use Neoan3\Model\ImageModel;
use Neoan3\Model\UserModel;

/**
 * Class Login
 *
 * @package Neoan3\Components
 */
class Login extends Neoan
{

    function init()
    {
        $uni = new Unicore();
        $uni->uni('neoan')->vueComponent('login')
            ->hook('main', 'login')
            ->output();
    }

    /**
     * @param $credentials
     *
     * @return array
     * @throws RouteException
     * @throws \Neoan3\Apps\DbException
     */
    function postLogin($credentials)
    {
        $user = UserModel::login($credentials);

        // prepare roles/scopes
        $scope = ['user'];
        if ($user['user_type'] !== 'user') {
            $scope[] = $user['user_type'];
        }

        // attach image
        if (!empty($user['image_id'])) {
            $user['image'] = ImageModel::byId($user['image_id']);
        }

        $jwt = Stateless::assign($user['id'], $scope, ['exp' => time() + (2 * 60 * 60 * 1000)]);
        // For hybrid auth
        Session::login($user['id'], $user['roles'], $user['user_type']);
        $redirect = false;
        if (isset($_COOKIE['redirect'])) {
            $redirect = $_COOKIE['redirect'];
            unset($_COOKIE['redirect']);
            setcookie('redirect', '', time() - 3600);
        }

        return ['token' => $jwt, 'user' => $user, 'redirect' => $redirect];
    }

    /**
     * void
     */
    function deleteLogin()
    {
        Session::logout();
    }

}
