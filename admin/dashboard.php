<?php

    ob_start(); // OUTPUT BUFFERING START

    session_start();

    if (isset($_SESSION['Username'])){

    $pageTitle ='Dashboard';

    include 'init.php';

    /* start dashboard page*/

     $numUsers=4; //
     // get latest record register
    $LatestUsers =getLatest("*", "users", "UserID",$numUsers);

        $numItems=5; //
        // get latest record register
        $LatestItems = getLatest("*", "items", "Item_ID",$numItems);

        $numComment = 4;


        ?>

    <div class="container home-stats text-center">
        <h1>DashBoard</h1>
        <div class="row">
            <div class="col-md-3">
                <div class="stat st-members">
                    <i class="fa fa-users"></i>
                    <div class="info">
                        Total Members
                        <span><a href="members.php">
                                <?= countItems('UserID', 'users')?>
                         </a></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat st-pending">
                    <i class="fa fa-user-plus"></i>
                    <div class="info">
                        Pending Members
                        <span><a href="members.php?do=Manage&page=Pending">
                    <?= checkItem("RegStatus", "users", 0) ?>
                            </a></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat st-items">
                    <i class="fa fa-tags"></i>
                    <div class="info">
                        Total Items
                        <span><a href="items.php"><?= countItems('Item_ID', 'items')?></a></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat st-comments">
                    <i class="fa fa-comments"></i>
                    <div class="info">
                        Total Comments
                        <span><a href="comments.php"><?= countItems('C_id', 'comments')?></a></span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="container latest">
        <div class="row">
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-users"></i>
                        Latest <?php echo $numUsers;?> Registered Users
                        <span class="toggle-info pull-right">
                            <i class="fa fa-plus fa-lg"></i>
                        </span>
                    </div>
                    <div class="panel-body">
                        <ul class="list-unstyled latest-users">
                            <?php
                            if (!empty($LatestUsers)) {
                                foreach ($LatestUsers as $user) {
                                    echo '<li>' . $user['Username'] . '
                                            <a href="members.php?do=Edit&userid=' . $user['UserID'] . '" >
                                                <span class="btn btn-success pull-right">
                                                    <i class="fa fa-edit"></i>Edit';
                                    if ($user['RegStatus'] == 0) {
                                        echo "<a href='members.php?do=Activate&userid=" . $user['UserID'] . "
                                                                ' class='btn btn-info pull-right activate'>
                                                                <i class='fa fa-check'></i> Active</a>";
                                    }
                                    echo '  </span>
                                            </a>
                                          </li>';
                                }
                            } else {
                                echo 'There\'s No users To Show';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-tags"></i> Latest <?= $numItems?> Items
                        <span class="toggle-info pull-right">
                            <i class="fa fa-plus fa-lg"></i>
                        </span>
                    </div>
                    <div class="panel-body">
                        <ul class="list-unstyled latest-users">
                            <?php
                            if(!empty($LatestItems)) {
                                foreach ($LatestItems as $item) {
                                    echo '<li>' . $item['Name'] . '
                                            <a href="items.php?do=Edit&itemid=' . $item['Item_ID'] . '" >
                                                <span class="btn btn-success pull-right">
                                                    <i class="fa fa-edit"></i>Edit';
                                    if ($item['Approve'] == 0) {
                                        echo "<a href='items.php?do=Approve&itemid=" . $item['Item_ID'] . "
                                                    ' class='btn btn-info pull-right activate'>
                                                    <i class='fa fa-check'></i> Approve</a>";
                                    }
                                    echo '  </span>
                                            </a>
                                    </li>';
                                }
                            }else {
                                echo 'there\'s no items to show';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
<!--        latest comments-->

        <div class="row">
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-comments-o"></i>
                        Latest <?= $numComment?> Comments
                        <span class="toggle-info pull-right">
                            <i class="fa fa-plus fa-lg"></i>
                        </span>
                    </div>
                    <div class="panel-body">
                        <?php

                            $stmt =$con->prepare("SELECT 
                                                  comments.*,users.Username as Member
                                                FROM 
                                                  comments
                                               inner join 
                                                  users
                                                on 
                                                  users.UserID = comments.User_id
                                                  order by 
                                                  C_id DESC
                                                  limit $numComment");
                            $stmt->execute();
                            $comments =$stmt->fetchAll();
                            if(!empty($comments)) {
                                foreach ($comments as $comment) {
                                    echo '<div class="comment-box">';
                                    echo '<span class="member-n">' . $comment['Member'] . '</span>';
                                    echo '<p class="member-c">' . $comment['Comment'] . '</p>';
                                    echo '</div>';
                                }
                            }else {
                                echo 'there\'s no comments to show';
                            }
                        ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <?php
            /* end dashboard page */

            include $tpl . 'footer.php';

        } else {

            header('location: index.php');

            exit();
        }

        ob_end_flush();
    ?>