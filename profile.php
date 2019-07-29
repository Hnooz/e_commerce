<?php
    ob_start();
    session_start();
    $pageTitle ='Profile';
    include "init.php";

if(isset($_SESSION['user'])) {


        $getUser = $con->prepare("select * from users where Username = ?");
        $getUser->execute(array($sessionUser));
        $info = $getUser->fetch();
        $userid = $info['UserID'];
        ?>
        <h1 class="text-center">My Profile</h1>
        <div class="information block">
            <div class="container">
                <div class="panel panel-primary">
                    <div class="panel-heading">My Information</div>
                    <div class="panel-body">
                        <ul class="list-unstyled">
                            <li>
                                <i class="fa fa-unlock-alt fa-fw"></i>
                                <span>Name</span> : <?= $info['Username']?>
                            </li>
                            <li>
                                <i class="fa fa-envelope-o fa-fw"></i>
                                <span>Email</span> : <?= $info['Email']?>
                            </li>
                            <li>
                                <i class="fa fa-user fa-fw"></i>
                                <span>Full Name</span> : <?= $info['FullName']?>
                            </li>
                            <li>
                                <i class="fa fa-calendar fa-fw"></i>
                                <span>Register Date </span>: <?= $info['Date']?>
                            </li>
                            <li>
                                <i class="fa fa-tags fa-fw"></i>
                                <span>Fav Category</span> :
                            </li>

                        </ul>
                        <a href="member.php?do=Edit&userid=<?= $info['UserID']?>" class="btn btn-default">Edit Information</a>
                    </div>
                </div>
            </div>
        </div>

        <div id="my-ads" class="my-ads block">
            <div class="container">
                <div class="panel panel-primary">
                    <div class="panel-heading">My Items</div>
                    <div class="panel-body">
                            <?php
                            $allItems = getAllFrom("*","items","where Member_ID =$userid ","","Item_ID");
                            if(!empty ($allItems)) {
                                echo '<div class="row">';
                                foreach ($allItems as $item) {
                                    echo '<div class="col-sm-6 col-md-3">';
                                    echo '<div class="thumbnail item-box">';
                                    if ($item['Approve'] == 0){
                                        echo '<span class="approve-status">Waiting Approval</span>';
                                    }
                                    echo '<span class="price-tag">$' . $item['Price'] . '</span>';

//                                    echo '<img class="img-responsive" src="images%20(6).jpg" alt="">';
                                    if (empty($item['Avatar'])) {
                                        echo '<img src="images%20(6).jpg">';
                                    }else{
                                        echo "<img class='img-responsive' src='admin/uploads/avatar/" . $item['Avatar'] . "' alt=''>";
                                    }
                                    echo '<div class="caption">';
                                    echo '<h3><a href="items.php?itemid='.$item['Item_ID'].'">' . $item['Name'] . '</a></h3>';
                                    echo '<p>' . $item['Description'] . '</p>';
                                    echo '<div class="date">' . $item['Add_Date'] . '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                                echo '</div>';
                            }else{
                                echo 'Sorry There\'s No Ads To Show, Create <a href="newad.php"> New Ad</a>';
                            }
                            ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="my-comments block">
            <div class="container">
                <div class="panel panel-primary">
                    <div class="panel-heading">Latest Comments</div>
                    <div class="panel-body">
                        <?php
                        $mycomment = getAllFrom("comment","comments","where User_id =$userid ","","C_id");
//                        $stmt =$con->prepare("SELECT
//                                              comment
//                                            FROM
//                                              comments
//                                              where User_id=?");
//
//                        // execute the statement
//
//                        $stmt->execute(array($info['UserID']));
//
//                        //assign to variable
//
//                        $comments =$stmt->fetchAll();

                        if(!empty($mycomment)){
                            foreach ($mycomment as $comment){
                               echo '<p>'.$comment['comment'].'</p>';
                            }
                        }else{
                            echo 'there\'s no comments to show';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }else {
        header('location:login.php');
        exit();
    }
    include $tpl . 'footer.php';
    ob_end_flush();
?>
