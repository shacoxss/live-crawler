<?php

class LiveCrawler extends Worker
{
    protected $live;

    public function __construct($live)
    {
        $this->live = $live;
    }
	
	
	public function init(){

		run();
	}

    public function run ()
    {
        $dbh = new PDO('mysql:host=localhost;dbname=fs', 'root', 'root');
        while($url = $this->live->nextUrl()) {

            try {
                $data = file_get_contents($url);
                $data = json_decode($data);
                $data = $this->live->dataFormat($data);
                echo $this->getThreadId().$url.PHP_EOL;
            } catch (Exception $e) {
                continue;
            }
            foreach($data as $t) {
                
                // live_category
                $sql_c = "insert into live_categories (name, live_category_id,
                    live_id, url) values ";
                $sql_c .= "('$t[category_name]', '$t[live_category_id]', $t[live_id], '$t[category_url]')";

                // anchor
                $sql = "insert into anchors (name,
                    live_user_id, live_id, url) values ";
                
                $sql .= "('$t[name]', 
                    '$t[live_user_id]', $t[live_id], '$t[url]')";

                //history
                $his = "insert into anchor_histories (live_user_id, live_category_id, room_name, online)
                 values ('$t[live_user_id]', '$t[live_category_id]', '$t[room_name]', $t[online])";



                $up = "UPDATE anchors SET status=1,live_category_id='$t[live_category_id]', cover='$t[cover]',
                    avatar='$t[avatar]', room_name='$t[room_name]',online=$t[online]
                    WHERE `live_user_id`='$t[live_user_id]'";
                    
                //$sql_c = substr($sql_c, 0, -1);
                //$sql = substr($sql, 0, -1);
                $dbh->exec($sql_c);
                $dbh->exec($sql);
                $dbh->exec($his);
                $dbh->exec($up);
            }
            //\DB::insert($sql_c);
            //\DB::insert($sql);
        };
    }
}
