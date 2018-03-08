<?php

namespace app\index\controller;

use app\common\service\UserService;


class IndexController extends CommonController {

    public function index() {
        $this->assign('user', $this->user);
        return view();
    }

    public function logout() {
        UserService::setUserLogout();
        $this->responseSuccess(['redirect'=>url('Public/login')]);
    }
}