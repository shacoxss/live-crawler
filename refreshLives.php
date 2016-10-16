<?php
require 'LiveCrawler.php';
require 'Douyu.php';

$dbh = new PDO('mysql:host=localhost;dbname=fs', 'root', 'root');
$up = "UPDATE anchors SET status=0,online=0";
$dbh->exec($up);
//$pool = new \Pool(18, \Worker::class, []);
//dd(serialize(new LiveCrawler($douyu)));
// $pool->submit((new Threaded));
$platform = [new Panda, new Douyu];
$t = microtime(true);
$pool = new \Pool(15, Worker::class, []);
$thread = (int) 15 / count($platform);
//dd(serialize(new LiveCrawler($douyu)));
// $pool->submit((new \Threaded));
foreach($platform as $p) {
    foreach(range(0,20) as $i) {
        try{
            $pool->submit(new LiveCrawler($p));
        } catch (RuntimeException $e) {
            //who care
        }
    }
}


try{
    $pool->shutdown();
} catch (RuntimeException $e) {
    //who care
    print_r($e);
}

echo microtime(true) - $t;

