<?php

namespace app\index\controller;

use app\common\controller\BaseController;
use think\facade\Config;
use think\facade\Session;


class CommonController extends BaseController {

    protected $user = null;

    public function initialize(){
        parent::initialize();
        if(!$this->isLogin()){
            $this->redirect('Public/login');
        }

        $this->user = Session::get(Config::get('login_session_key'));
    }
}