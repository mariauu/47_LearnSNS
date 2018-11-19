<?php
session_start();
require('dbconnect.php');

//1ページあたりの表示件数
const CONTENT_PER_PAGE = 5;

if (!isset($_SESSION['47_LearnSNS']['id'])) {
    header('Location: signin.php');
    exit();
}


$sql = 'SELECT * FROM `users` WHERE `id` = ?';
$data = [$_SESSION['47_LearnSNS']['id']];
$stmt = $dbh->prepare($sql);
$stmt->execute($data);

$signin_user = $stmt->fetch(PDO::FETCH_ASSOC);

//エラー内容を保持する
$errors = [];

//投稿ボタンが押された時(POST送信された時)
if (!empty($_POST)) {
    //投稿データを取得
    $feed = $_POST['feed'];

    //投稿の空チェック
    if ($feed !='') {
        //投稿処理
        //宿題
        //feedsテーブルに値を登録しよう
        //登録する値はfeed,user_id,createdの3つ
            $sql = 'INSERT INTO `feeds`(`feed`,`user_id`,`created`)VALUES(?,?,NOW())';


            $data = [$feed,$signin_user['id']];

            //3.SQL文をセットする
            $stmt = $dbh->prepare($sql);
            //4.SQL文を実行する
            $stmt->execute($data);

            header('Location: timeline.php');
            exit();

            }else{
                //バリデーション処理
                $errors['feed'] = 'blank';

    }
}

if (isset($_GET['page'])) {
    $page = $_GET['page'];
}else{
    //ページの指定がない場合(初期値)
    $page = 1;
}


//-1などの不正な値を渡された際の対策
$page = max ($page,1);

//feedsテーブルのレコードを取得する
//COUNT()何レコードあるか集計するSQLの関数
$sql = 'SELECT COUNT(*) AS`cnt` FROM `feeds`';
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$cnt = $result['cnt'];

//最後のページ数を取得
//最後のページ = 取得したページ数 + 1ページあたりのページ数
$last_page = ceil($cnt / CONTENT_PER_PAGE);


//最後のページより大きい値を渡された際の対策
$page = min($page, $last_page);

//スキップするレコード数 = (指定ページ-1)* 表示件数


$start = ($page-1) * CONTENT_PER_PAGE;





//1.投稿情報全てを取得
$sql = 'SELECT `f`.*,`u`.`name`,`u`.`img_name`FROM`feeds` AS`f`
LEFT JOIN`users` AS `u`
ON`f`.`user_id` = `u`.`id`
ORDER BY`f`.`created` DESC LIMIT '.CONTENT_PER_PAGE.' OFFSET '.$start;


$stmt = $dbh->prepare($sql);
$stmt->execute();

//投稿情報全てを入れる配列定義
$feeds = [];
while (true) {
    //$recordは要するにfeed一件の情報
    //fetchとは一つの行を
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($record == false) {
        //レコードが取れなくなったらおしまい。
        break;//←breakでも無限ループが終わる
    }
    $feeds[] = $record;
 // echo "<pre>";
 // var_dump($feeds);
 // echo "</pre>";
}


//宿題
//$feedをもとにHTML内に投稿内容、投稿日時、ユーザー名、ユーザー画像を表示



?>
<?php include('layouts/header.php'); ?>
<body style="margin-top: 60px; background: #E4E6EB;">
    <!--
         incliude(ファイル名);
         指定したファイルを組み込んで表示
         共通部分を切り出して使いたいページから読み込む
     -->
    <?php include('navbar.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-3">
                <ul class="nav nav-pills nav-stacked">
                    <li class="active"><a href="timeline.php?feed_select=news">新着順</a></li>
                    <li><a href="timeline.php?feed_select=likes">いいね！済み</a></li>
                </ul>
            </div>
            <div class="col-xs-9">
                <div class="feed_form thumbnail">
                    <form method="POST" action="">
                        <div class="form-group">
                            <textarea name="feed" class="form-control" rows="3" placeholder="Happy Hacking!" style="font-size: 24px;"></textarea><br>
                        <input type="submit" value="投稿する" class="btn btn-primary">


                        <?php if (isset($errors['feed'])&& $errors
                        ['feed'] == 'blank'):?>
                            <p class="text-danger">文字を入力してください</p>
                        <?php endif; ?> 

                        </div>
                    </form>
                </div>
                <?php foreach($feeds as $feed):?>
                <div class="thumbnail">
                    <div class="row">
                        <div class="col-xs-1">
                            <img src="user_profile_img/<?php echo $feed['img_name']; ?>" width="40px">
                       </div>
                        <div class="col-xs-11">
                            <a href="profile.php" style="color: #7f7f7f;">
                                <?php echo $feed['name']; ?>
                            </a>
                            <?php echo $feed['created']; ?>
                        </div>
                    </div>
                    <div class="row feed_content">
                        <div class="col-xs-12">
                            <span style="font-size: 24px;">
                                <?php echo $feed['feed']; ?>
                            </span>
                        </div>
                    </div>
                    <div class="row feed_sub">
                        <div class="col-xs-12">
                            <button class="btn btn-default">いいね！</button>
                            いいね数：
                            <span class="like-count">10</span>
                            <a href="#collapseComment" data-toggle="collapse" aria-expanded="false"><span>コメントする</span></a>
                            <span class="comment-count">コメント数：5</span>

                            <?php if ($signin_user['id'] == $feed['user_id']): ?>
                                <a href="edit.php?feed_id=<?php echo $feed['id']; ?>" class="btn btn-success btn-xs">編集</a>
                                <a onclick="return confirm('ほんまに消すん？');" href="delete.php?feed_id=<?php echo $feed['id'];?> "class="btn btn-danger btn-xs">削除</a>
                            <?php endif;?>
                                </div>
                            <?php include('comment_view.php'); ?>
                    </div>

                </div>
            <?php endforeach; ?>
                <div aria-label="Page navigation">
                    <ul class="pager">
                        <?php if($page ==1): ?>

                        <li class="previous disabled">
                            <a><span aria-hidden="true">&larr;</span> Newer</a></li>
                            <?php else: ?>
                                <li class="previous">
                                    <a href="timeline.php?page=<?php echo $page -1; ?>"><span aria-hidden= "true">&larr;</span>Newer</a></li>

                                <?php endif;?>

                                <?php if ($page == $last_page): ?> 
                                    <li class="next disabled">
                                    <a>Older <span aria-hidden="true">&rarr;</span></a></li>
                       <?php else:?>
                        <li class="next ">
                            <a href="timeline.php?page=<?php echo$page + 1;?>"><span aria-hidden="true">&rarr;</span>Older</a></li>
                            <?php endif;?>                    </ul>
                </div>

            </div>
        </div>
    </div>
</body>
<?php include('layouts/footer.php'); ?>
</html>













