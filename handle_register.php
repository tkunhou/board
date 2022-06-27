<?php
    session_start();
    require_once('./conn.php');

    $nickname=$_POST['nickname'];
    $username=$_POST['username'];
    $password=password_hash($_POST['password'],PASSWORD_DEFAULT);

    if(empty($nickname)||empty($username)||empty($password)){
        header('Location:./register.php?errCode=1');
        die('資料不齊全');
    }

    $sql="INSERT INTO user(nickname,username,password) VALUES(?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('sss',$nickname,$username,$password);

    $result=$stmt->execute();

    if(!$result){
        if(strpos($conn->error,"Duplicate entry")!==false){
            header('Location:register.php?errCode=2');
        }
        die ('error:'.$conn->errno);
    }
    $_SESSION['username']=$username;
    header('Location:./index.php');
?>