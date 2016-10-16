<?php

class Douyu extends Threaded {
    const BASE_URL = 'http://open.douyucdn.cn/api/RoomApi/live';
    private $offset = 0;
    private $done = false;
    public function __construct()
    {
    }
    public function next()
    {
        if($this->done) {
            return null;
        } else {
            $offset = $this->offset * 100;
            $this->offset++;
            return self::BASE_URL."?offset={$offset}&limit=100";
        }
    }
    public function filter($data)
    {
        $r = [];
        foreach($data as $i) {
            $i = (array)$i;
            $t['id'] = 'd_'.$i['room_id'];
            $t['name'] = $i['room_name'];
            $t['online'] = $i['online'];
            $r[] = $t;
        }
        if(empty($r)) $this->done = true;
        return $r;
    }
}