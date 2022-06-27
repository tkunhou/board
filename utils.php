<?php 
    require_once('./conn.php');

    function generateToken(){
        $s='';
        for($i=1; $i<=16; $i++){
            $s.=chr(rand(65,90));
        }
        return $s;
    }

    function getUserFromUsername($username){
        global $conn;
        $sql=sprintf("SELECT * FROM user WHERE username='%s'",$username);
        $result=$conn->query($sql);
        $row=$result->fetch_assoc();
        $username=$row['username'];
        return $row; //username , id , nickname
    }

    //將使用者所輸入的內容轉為純文字，避免 XSS (cross site scripting)
    function escape($str){
        return htmlspecialchars($str,ENT_QUOTES);
    }
?>