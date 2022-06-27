<?php
    session_start();
    require_once('./conn.php');
    require_once('utils.php');

    $id=$_GET['id'];
    
    $username=NULL;
    $user=NULL;
    if(!empty($_SESSION['username'])){
        $username=$_SESSION['username'];
        $user=getUserFromUsername($username);
    }

    $stmt=$conn->prepare("SELECT * FROM comments WHERE id=?");
    $stmt->bind_param('i',$id);
    $result=$stmt->execute();
    if(!$result){
        die('error:'.$conn->error);
    }
    $result=$stmt->get_result();
    $row=$result->fetch_assoc(); 
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>留言板</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <header class="warning">
        <strong>注意！本站為練習用網站，因教學用途而忽略資安的實作，註冊時請勿使用任何真實的帳號密碼。</strong>
    </header>
    <main class="board">
        <h1 class="board__title">編輯留言</h1>
        <?php
            if(!empty($_GET['errCode'])){
                $code=$_GET['errCode'];
                $msg='err';
                if($code==='1'){
                    $msg='錯誤：資料不齊全';
                };
                echo '<h2 class="error">'.$msg.'</h2>';
            }
        ?>
            <form class="board__new-comment-form" method="POST" action="handle_update_comment.php">
                <textarea name="content" cols="30" rows="10" value="請寫點什麼吧..."><?php echo $row['content']?></textarea>
                <input type="hidden" name="id" value="<?php echo $row['id']?>"/> 
                <input class="board__submit-btn" type="submit"/>
            </form>
    </main>
</body>
</html>