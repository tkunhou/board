<?php
    session_start();
    require_once('./conn.php');
    require_once('utils.php');

    //print_r($_COOKIE);

    /*
        1. 從 cookie 中讀取 PHPSESSID(token)
        2. 從檔中讀取 PHPSESSID 的內容，以這邊來說就是 username
        3. 放到 $_SESSION
    */
    
    $username=NULL;
    $user=NULL;
    if(!empty($_SESSION['username'])){
        $username=$_SESSION['username'];
        $user=getUserFromUsername($username);
    }

    $page=1;
    if(!empty($_GET['page'])){
        $page=intval($_GET['page']);
    }
    $items_per_page=5;
    $offset=($page-1)*$items_per_page;

    $stmt=$conn->prepare("SELECT C.id AS id, C.content AS content, C.created_at AS created_at, U.nickname AS nickname, U.username AS username FROM comments AS C LEFT JOIN user AS U ON C.username=U.username WHERE C.is_deleted IS NULL ORDER BY C.id DESC LIMIT ? OFFSET ?");
    $stmt->bind_param('ii',$items_per_page, $offset);
    $result=$stmt->execute();
    if(!$result){
        die('error:'.$conn->error);
    }
    $result=$stmt->get_result(); 
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
        <?php if(!$username){?>
            <a class="board__btn" href="./register.php">註冊</a>
            <a class="board__btn" href="./login.php">登入</a>
        <?php } else { ?>
            <a class="board__btn" href="./logout.php">登出</a> 
            <span class="board__btn update-nickname">編輯暱稱</span> 
            <form class="hide board__nickname-form board__new-comment-form" method="POST" action="update_user.php">
                <div class="board__nickname">
                    <span>新的暱稱：</span>
                    <input type="text" name="nickname"/>
                    <input class="board__submit-btn" type="submit" >                         
                </div>
            </form>
            <h3>你好！<?php echo $user['nickname']?></h3>
        <?php }?>
        <h1 class="board__title">Comments</h1>
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
            <form class="board__new-comment-form" method="POST" action="handle_add_comment.php">
                <textarea name="content" cols="30" rows="10" value="請寫點什麼吧..."></textarea>
                <?php if($username){?>
                    <input class="board__submit-btn" type="submit"/>
                <?php } else {?>
                    <h3>請登入後發布留言</h3> 
                <?php } ?>
            </form>
            <div class="board__hr"></div>
        <section>
            <?php 
                while($row=$result->fetch_assoc()){          
            ?>

            <div class="card">
                <div class="card__avatar">
                    
                </div>
                <div class="card__body">
                    
                    <div class="card__info">
                        <sapn class="card__author">
                            <?php echo escape($row['nickname']);?>
                            (@<?php echo escape($row['username']);?>)
                        </sapn>
                        <span class="card__time"><?php echo $row['created_at'];?></span>
                        <?php if($row['username']===$username){?>
                            <a href="update_comment.php?id=<?php echo $row['id']?>">編輯</a>
                            <a href="delete_comment.php?id=<?php echo $row['id']?>">刪除</a>
                        <?php } ?>
                    </div>
                    <p class="card__content"><?php echo escape($row['content']);?></p>
                </div>
            </div>
            <?php } ?>
        </section>
        <div class="board__hr"></div>
        <?php 
            $stmt=$conn->prepare("SELECT count(id) AS count FROM comments WHERE is_deleted IS NULL");
            $result=$stmt->execute();
            $result=$stmt->get_result();
            $row=$result->fetch_assoc();
            $count=$row['count'];
            $total_page=ceil($count/$items_per_page);
        ?>
        <div class="page-info">
            <span>總共有 <?php echo $count?> 筆資料，頁數</span>
            <span><?php echo $page ?>/<?php echo $total_page ?></span>
        </div>
        <div class="paginator">           
            <?php if($page!=1){?>
                <a href="index.php?page=1">首頁</a>
                <a href="index.php?page=<?php echo $page-1?>">上一頁</a>
            <?php } ?>
            <?php if($page!=$total_page){?>
                <a href="index.php?page=<?php echo $page+1?>">下一頁</a>
                <a href="index.php?page=<?php echo $total_page ?>">最終頁</a>
            <?php } ?>
            
        </div>
    </main>
    <script>
        var btn=document.querySelector('.update-nickname');
        btn.addEventListener('click',function(){
            var form=document.querySelector('.board__nickname-form');
            form.classList.toggle('hide')
        })
    </script>
</body>
</html>