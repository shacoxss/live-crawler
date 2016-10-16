<?php


class Douyu extends Threaded
{
    const BASE_URL = 'http://open.douyucdn.cn/api/RoomApi/live';
    private $offset = 0;
    private $done = false;
    public function __construct()
    {
    }
    public function nextUrl() : string
    {
        if($this->done) {
            return '';
        } else {
            $offset = $this->offset * 100;
            $this->offset++;
            return self::BASE_URL."?offset={$offset}&limit=100";
        }
    }
    public function dataFormat($data) : array
    {
        if(isset($data->data)) {
            $data = (array) $data->data;
        } else return [];
        $r = [];
        foreach($data as $i) {
            $i = (array)$i;
            $t['name'] = $i['nickname'];
            $t['room_name'] = $i['room_name'];
            $t['online'] = $i['online'];
            $t['live_user_id'] = 'd_'.$i['owner_uid'];
            $t['live_id'] = 1;
            $t['url'] = $i['url'];
            $t['cover'] = $i['room_src'];
            $t['avatar'] = $i['avatar'];
            $t['live_category_id'] = 'd_'.$i['cate_id'];
            $t['category_name'] = $i['game_name'];
            $t['category_url'] = $i['game_url'];
            $r[] = $t;
        }
        if(empty($r)) $this->done = true;
        return $r;
    }
}

class Panda extends Threaded
{
    const BASE_URL = 'http://www.panda.tv/live_lists?status=2&order=person_num';
    private $offset = 0;
    private $page_count = 0;
    public function __construct()
    {
    }
    public function nextUrl() : string
    {
        if(!$this->page_count == 0 && $this->offset > $this->page_count) {
            return '';
        } else {
            $this->offset++;
            return self::BASE_URL."&pageno=$this->offset&pagenum=120";
        }
    }
    public function dataFormat($data) : array
    {
        if($this->page_count == 0) $this->page_count = $data->data->total / 120;
        $data = (array) $data->data->items;
        $r = [];
        foreach($data as $i) {
            $i = (array)$i;
            $t['name'] = $i['userinfo']->nickName;
            $t['room_name'] = $i['name'];
            $t['online'] = $i['person_num'];
            $t['live_user_id'] = 'p_'.$i['userinfo']->rid;
            $t['live_id'] = 2;
            $t['url'] = "http://www.panda.tv/".$i['id'];
            $t['cover'] = $i['pictures']->img;
            $t['avatar'] = $i['userinfo']->avatar;
            $t['live_category_id'] = 'p_'.$i['classification']->ename;
            $t['category_name'] = $i['classification']->cname;
            $t['category_url'] = "http://www.panda.tv/cate/".$i['classification']->ename;
            $r[] = $t;
        }
        return $r;
    }
}