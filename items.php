<?php
    ob_start();
    session_start();
    $pageTitle ='Show Item';
    include "init.php";

    // CHECK IF GET REQUEST ITEM NUMERIC & GET ITS INTEGER VALUE
    $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0 ;

    $stmt = $con->prepare("SELECT
                                        items.*,categories.Name as category_name,
                                        users.Username
                                    from
                                        items
                                    inner join
                                        categories
                                    on
                                        categories.ID = items.Cat_ID
                                    inner join
                                        users
                                    on
                                        users.UserID = items.Member_ID
                                    WHERE
                                        Item_ID = ?
                                        and
                                        Approve = 1");
    $stmt->execute(array($itemid));
    $count =$stmt->rowCount();
    if ($count > 0) {

        $item = $stmt->Fetch();
        ?>
        <h1 class="text-center"><?= $item['Name'] ?></h1>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <?php
                    if (empty($item['Avatar'])) {
                        echo '<img class="img-responsive img-thumbnail center-block" src="images%20(6).jpg">';
                    }else{
                        echo "<img class='img-responsive img-thumbnail center-block' src='admin/uploads/avatar/" . $item['Avatar'] . "' alt=''>";
                    }
                    ?>
<!--                    <img class="img-responsive img-thumbnail center-block" src="admin/uploads/avatar/--><?//=$item['Avatar']?><!--" alt="">-->
                </div>
                <div class="col-md-9 item-info">
                    <h2><?= $item['Name']?></h2>
                    <p><?= $item['Description']?></p>
                    <ul class="list-unstyled">
                        <li>
                            <i class="fa fa-calendar fa-fw"></i>
                            <span>Add Date</span> : <?= $item['Add_Date']?>
                        </li>
                        <li>
                            <i class="fa fa-money fa-fw"></i>
                            <span>Price</span> : $<?= $item['Price']?>
                        </li>
                        <li>
                            <i class="fa fa-building fa-fw"></i>
                            <span>Made In</span> : <?= $item['Country_Made']?>
                        </li>
                        <li>
                            <i class="fa fa-tags fa-fw"></i>
                            <span>Category</span> : <a href="categories.php?pageid=<?= $item['Cat_ID']?>"><?= $item['category_name']?></a>
                        </li>
                        <li>
                            <i class="fa fa-user fa-fw"></i>
                            <span>Add By</span> : <a href="profile.php"><?= $item['Username']?></a>
                        </li>
                        <li class="tags-items">
                            <i class="fa fa-user fa-fw"></i>
                            <span>Tags</span> :
                            <?php
                            $allTags = explode(",",$item['Tags']);
                            foreach ($allTags as $tag) {
                                $tag = str_replace(' ', '', $tag);
                                $lowertag = strtolower($tag);
                                if (!empty($tag)) {
                                    echo "<a href='tags.php?name={$lowertag}'>" . $tag . "</a>";
                                }
                            }
                            ?>
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="custom-hr">
           <?php if(isset($_SESSION['user'])) {?>
<!--            start add comment-->
            <div class="row">
                    <div class="col-sm-offset-3">
                        <div class="add-comment">
                           <h3>Add Your Comment</h3>
                            <form action="<?= $_SERVER['PHP_SELF'] .'?itemid='.$item['Item_ID']?>" method="POST">
                                <textarea name="comment" required></textarea>
                                <input class="btn btn-primary" type="submit" value="Add Comment">
                            </form>
                            <?php
                            if($_SERVER['REQUEST_METHOD']== 'POST'){

                                $comment = filter_var($_POST['comment'],FILTER_SANITIZE_STRING);
                                $userid  = $_SESSION['uid'];
                                $itemid  = $item['Item_ID'];

                                if (!empty($comment)){
                                    $stmt = $con->prepare("insert into
                                                        comments(Comment,Status,Comment_Date,Item_id,User_id)
                                                        values(:zcomment,0,now(),:zitemid,:zuserid)");
                                    $stmt->execute(array(

                                        'zcomment' => $comment,
                                        'zitemid'   => $itemid,
                                        'zuserid'   =>$userid
                                    ));
                                    if ($stmt){
                                        echo '<div class="alert alert-success">Comment Add</div>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
            </div>
<!--            end add comment-->
            <?php }else {
               echo '<a href="login.php">Log in</a> or <a href="login.php">Register</a> To Add Comment';
            }?>
            <hr class="custom-hr">
            <?php
            // select all users except admin
            $stmt =$con->prepare("SELECT
                    comments.*,users.Username as Member ,users.Avatar
                    FROM
                    comments
                    inner join
                    users
                    on
                    users.UserID = comments.User_id
                    where
                    item_id = ?
                    and
                    Status = 1
                    order by
                    C_id DESC");

            $stmt->execute(array($item['Item_ID']));
            $comments =$stmt->fetchAll();
            ?>
            <?php
            foreach ($comments as $comment){?>
                <div class="comment-box">
                    <div class="row">
                        <div class="col-sm-2 text-center">
                            <?php
                            if (empty($comment['Avatar'])) {
                                echo '<img class="img-responsive img-thumbnail img-circle center-block" src="images%20(6).jpg">';
                            }else{
                                echo "<img class='img-responsive img-thumbnail img-circle center-block' src='admin/uploads/avatar/" . $comment['Avatar'] . "' alt=''>";
                            }
                            ?>
<!--                            <img class="img-responsive img-thumbnail img-circle center-block" src="admin/uploads/avatar/--><?//= $comment['Avatar']?><!--" alt="">-->
                            <?=$comment['Member']?>
                        </div>
                        <div class="col-sm-10">
                            <p class="lead"><?=$comment['Comment']?></p>
                        </div>
                    </div>
                </div>
                <hr class="custom-hr">
           <?php } ?>
        </div>
        <?php
    } else {
        echo '<div class="container">';
        echo '<div class="alert alert-danger">There\'s No Such Id Or This Item Waiting To Approval</div>';
        echo '</div>';
    }
    include $tpl . 'footer.php';
    ob_end_flush();
?>
