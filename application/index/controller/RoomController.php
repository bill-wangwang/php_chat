<?php

namespace app\index\controller;

use GatewayClient\Gateway;

class RoomController extends CommonController {
    public function index() {
        $roomId = input('param.roomId', 1, 'intval'); //默认房间号为1
        $this->assign('roomId', $roomId);
        $this->assign('user', $this->user);
        return view();
    }

    public function bind() {
        $user = $this->user;
        $userId = $user['id'];
        $clientId = input('post.clientId', '');
        $roomId = input('post.roomId', 1, 'intval'); //默认房间号为1
        Gateway::$registerAddress='127.0.0.1:1238';
        Gateway::bindUid($clientId, $userId);
        Gateway::joinGroup($clientId,$roomId);
        $this->responseSuccess(['userId'=>$userId]);
    }

    public function talk() {
        Gateway::$registerAddress='127.0.0.1:1238';
        $user = $this->user;
        $userId = $user['id'];
        $roomId = input('post.roomId', 1, 'intval'); //默认房间号为1
        $message = input('post.message', '', 'trim');
        $data = json_encode([
            'message'=>$message,
            'userId'=>$userId,
            'date'=>date('H:i:s')
        ]);
        Gateway::sendToGroup($roomId, $data);
        $this->responseSuccess();
    }
}