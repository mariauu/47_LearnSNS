<?php
require('dbconnect.php');
//1..どの投稿を削除するか
$feed_id = $_GET['feed_id'];

// echo "<pre>";
// var_dump($feed_id);
// echo "</pre>";
// die();

//2.Deleteの処理
$sql = 'DELETE FROM `feeds` WHERE `id` = ?';
$data = [$feed_id];
$stmt = $dbh->prepare($sql);
$stmt->execute($data);


//3.timeline.phpphpに遷移

header("Location: timeline.php");
exit();