<?php


?>
<?php include('layouts/header.php'); ?>
<body style="margin-top: 60px; background: #E4E6EB;">
    <?php include('navbar.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="thumbnail">
                    <div class="row">
                        <div class="col-xs-1">
                            <img src="user_profile_img/<?php echo $signin_user['img_name']; ?>" width="80px">
                        </div>
                        <div class="col-xs-10">
                            名前 <a href="profile.php" style="color: #7f7f7f;">
                                <?php echo $feed['name'];?></a>
                            <br>
                            
                        </div>
                    </div>
                    <div class="row feed_sub">
                        <div class="col-xs-12">
                            <span class="like-count">つぶやき数：10</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include('layouts/footer.php'); ?>
</html>
