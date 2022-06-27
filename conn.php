<?php

    $conn=new mysqli('localhost','tony','0219','tony');

    if ($conn->error) {
        die('資料庫連線錯誤:' . $conn->error);
      }

    $conn->query('SET NAMES UTF8');
    $conn->query('SET time_zone="+8:00"');
?>