<?php

namespace app\common\service;

use app\common\model\User;
use think\facade\Config;
use think\facade\Session;

class UserService extends CommonService {

    const SESSION_FIELD = ['id', 'account', 'status'];

    /**
     * @TODO 登录错误次数限制，根据混合后的密码验证
     * 根据用户名和密码 验证登录
     * @param $account string 用户名
     * @param $password string 密码
     * @return mixed array
     * @throws \Exception
     */
    public static function checkLogin($account, $password) {
        $user = User::get(['account' => $account]);
        if ($user && self::checkPassword($password, $user)) {
            if ($user['status'] != 1) {
                throw new \Exception('账号被禁用了', 10003);
            }
            //设置登录会话
            self::setUserLogin($user);
            return $user;
        } else {
            throw new \Exception('账号密码不匹配', 10002);
        }
    }


    /**
     * 设置用户登录
     * @param $user array
     * @return mixed
     */
    public static function setUserLogin($user) {
        $sessionData = [];
        foreach (self::SESSION_FIELD as $value) {
            $sessionData[$value] = $user[$value];
        }
        return Session::set(config('login_session_key'), $sessionData);
    }

    /**
     * 设置用户退出
     * @return bool
     */
    public static function setUserLogout() {
        session(config('login_session_key'), null);
        return true;
    }

    /**
     * 修改密码
     * @param $id int 用户id
     * @param $newPassword string 原始密码
     * @return bool
     * @throws \Exception
     */
    public static function modifyPassword($id, $newPassword) {
        $user = User::get($id);
        if (!$user) {
            throw new \Exception("记录不存在", 10001);
        }
        User::where(['id' => $id])->update(['password' => self::makePassword($newPassword, $user['salt'])]);
        return true;
    }

    /**
     * 判断用户是否已经登录
     * @return bool
     */
    public static function isLogin() {
        $user = Session::get(Config::get('login_session_key'));
        if($user && isset($user['id']) && $user['id']>0){
            return true;
        } else {
            return false;
        }

    }

    /**
     * 获取密码盐
     * @return string
     */
    public static function getSalt() {
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $len = strlen($str);
        $res = '';
        for ($i = 0; $i < 10; $i++) {
            $rand = rand(0, $len - 1);
            $res .= $str{$rand};
        }
        return $res;
    }

    /**
     * 根据明文密码和盐值生成对应的密码
     * @param $password string 明文密码
     * @param $salt string 盐值
     * @return string 加密后的密码
     */
    public static function makePassword($password, $salt) {
        return md5(md5($password) . $salt);
    }

    /**
     * 根据用户明文密码和用户对象校验密码
     * @param $password string 明文密码
     * @param $object array 用户对应
     * @return bool
     */
    public static function checkPassword($password, $object) {
        if (!$object) {
            return false;
        }
        return self::makePassword($password, $object['salt']) == $object['password'];
    }

    /**
     * 更改状态
     * @param $id
     * @param $status
     * @return bool
     * @throws \think\exception\DbException
     */
    public static function modifyStatus($id, $status) {
        $user = User::get($id);
        if ($user) {
            $user->status = $status;
            if (false === $user->save()) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}