<?php
    session_start();
    require_once('./conn.php');
    require_once('./utils.php');

    $content=$_POST['content'];
    $username=$_COOKIE['username'];

    if(empty($content)){
        header('Location:./index.php?errCode=1');
        die('資料不齊全');
    }

    $user=($_SESSION['username']);

    //$user_sql=sprintf("SELECT nickname FROM user WHERE username='%s'", $username);
    //$user_result=$conn->query($user_sql);
    //$row=$user_result->fetch_assoc();
    $username=$_SESSION['username'];


    $sql="INSERT INTO comments(username,content) VALUES(?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('ss',$username,$content);
    
    //$result=$conn->query($sql); 沒有防惡意字串的寫法
    $result=$stmt->execute(); //有防惡意字串
    if(!$result){
        die('error:'.$conn->error);
    }
    header('Location:./index.php');
?>