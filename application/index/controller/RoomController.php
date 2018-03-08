<?php

namespace app\index\controller;

class RoomController extends CommonController {
    public function index() {
        $this->assign('user', $this->user);
        return view();
    }
}