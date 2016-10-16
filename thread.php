<?php

require 'Platform.php';

class q extends Worker
{
    protected $url;
    protected $dbh;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function run ()
    {
        while($url = $this->url->next()) {
            $data = file_get_contents($url);
            $data = (array) json_decode($data)->data;
            echo $this->getThreadId().$url.PHP_EOL;
            $data = $this->url->filter($data);
            
            $dbh = new PDO('mysql:host=localhost;dbname=fs', 'root', 'root');
            $sql = "insert into live (id, name, online) values ";
            foreach($data as $t) {
                $sql .= "('$t[id]', '$t[name]', $t[online]),";
            }
            $sql = substr($sql, 0, -1);
            $dbh->exec($sql);
        };
    }
}
$data = new Threaded;
$douyu = new Douyu;
$q = new q($douyu);
$t = microtime(true);
$pool = new Pool(18, q::class, [$douyu]);
foreach(range(0,200) as $i) {
    try{
        $pool->submit(new q($douyu));
    } catch (RuntimeException $e) {
        //who care
    }
}

try{
    $pool->shutdown();
} catch (RuntimeException $e) {
    //who care
    print_r($e);
}

echo microtime(true) - $t;