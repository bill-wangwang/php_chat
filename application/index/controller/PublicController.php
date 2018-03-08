<?php

namespace app\index\controller;

use app\common\controller\BaseController;
use app\common\exception\ParamsException;
use app\common\service\UserService;

class PublicController extends BaseController {

    public function login() {
        if($this->isLogin()){
            return redirect('index/index');
        }
        return view();
    }

    public function doLogin(){
        $account = input('post.account', '');
        $pwd = input('post.pwd', '');
        if(empty($account) || empty($pwd)){
            throw new ParamsException();
        }
        UserService::checkLogin($account, $pwd);
        $this->responseSuccess(['redirect'=>'/']);
    }
}