<?php
namespace app\common\controller;

use think\Controller;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;

class BaseController extends Controller {

    public function isLogin() {
        $user = Session::get(Config::get('login_session_key'));
        if($user && isset($user['id']) && $user['id']>0){
            return true;
        } else {
            return false;
        }
    }
    private function _response($code = 0, $message, $data = []) {
        $request = Request::instance();
        if ( $request->isAjax() ) {
            exit(json_encode(['code' => $code, 'message' => $message, 'data' => $data]));
        } else {
            if ($code == 0) {
                $this->success($message);
            } else {
                $this->error($message ?: '请求错误');
            }
        }
    }

    public function responseSuccess($data=[], $message='success') {
        return $this->_response(0, $message, $data);
    }

    public function responseError($code, $message) {
        return $this->_response($code, $message, []);
    }

    public function responseParamsError($code=10001, $message='参数错误!') {
        return $this->_response($code, $message, []);
    }
}
