<!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8" />
            <title><?php getTitle()?></title>
            <link rel="stylesheet" href="<?php echo $css; ?>bootstrap.min.css">
            <link rel="stylesheet" href="<?php echo $css; ?>jquery-ui.css">
            <link rel="stylesheet" href="<?php echo $css; ?>jquery.selectBoxIt.css">
            <link rel="stylesheet" href="<?php echo $css; ?>font-awesome.min.css">
            <link rel="stylesheet" href="<?php echo $css; ?>front.css">
        </head>
    <body>
    <div class="upper-bar">
        <div class="container">
        <?php
        if(isset($_SESSION['user'])){

            $getUser = $con->prepare("select * from users where Username = ?");
            $getUser->execute(array($sessionUser));
            $info = $getUser->fetch();
            echo "<img class='my-image img-thumbnail img-circle' src='admin/uploads/avatar/" . $info['Avatar'] . "' alt=''>";
            ?>

            <?= $sessionUser?>
            <div class="btn-group my-info pull-right">
                <span class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-list">&nbsp;</i>
                    <span class="caret"></span>
                </span>
                <ul class="dropdown-menu">
                    <li><a href="profile.php">My Profile</a></li>
                    <li><a href="newad.php">New Item</a></li>
                    <li><a href="profile.php#my-ads">My Item</a></li>
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </div>

            <?php
        } else {
        ?>
            <a href="login.php">
                <span class="pull-right">Log In | Sign Up</span>
            </a>
        <?php } ?>
        </div>
    </div>
    <nav class="navbar navbar-inverse">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-nav" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">HomePage</a>
            </div>
            <div class="collapse navbar-collapse" id="app-nav">
                <ul class="nav navbar-nav navbar-right">
                    <?php
                    $allCat = getAllFrom("*","categories","where Parent = 0","","ID","ASC");
                    foreach ($allCat as $cat){
                    echo '<li>
                               <a href="categories.php?pageid='.$cat['ID'].'">'.$cat['Name'].'</a>
                         </li>';
                    }
                    ?>
            </div>
        </div>
    </nav>