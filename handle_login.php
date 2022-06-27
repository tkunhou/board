<?php
    session_start();
    require_once('./conn.php');
    require_once('./utils.php');

    $username=$_POST['username'];
    $password=$_POST['password'];

    if(empty($username)||empty($password)){
        header('Location:./login.php?errCode=1');
        die('資料不齊全');
    }

    $sql="SELECT * FROM user WHERE username=?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('s',$username);
    $result=$stmt->execute();

    $result=$stmt->get_result();
    if(!$result){
        die ('error:'.$conn->errno);
    }

    //查無使用者
    if($result->num_rows===0){
        header('Location:./login.php?errCode=2');
        exit();
    }

    //有查到使用者
    $row=$result->fetch_assoc();
    if(password_verify($password,$row['password'])){
        //echo ("登入成功");
        /*
            1. 產生 session id (token)
            2. 把 username 寫入檔案
            3. set-cookie: session-id
        */
        $_SESSION['username']=$username;
        header("Location:./index.php");
    }else{
        header('Location:./login.php?errCode=2');
    }

?>