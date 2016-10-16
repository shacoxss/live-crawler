<?php
        $pool = new \Pool(18, \Worker::class, []);
        //dd(serialize(new LiveCrawler($douyu)));
        $pool->submit((new \Threaded));