<?php

    /*
     ======================================
     == manange comments page
     == you can edit | delete | approve comments from here
     ======================================
    */
    ob_start();

    session_start();

    $pageTitle = 'Comments';

    if (isset($_SESSION['Username'])){


        include 'init.php';

        $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

        // start Manage page
        if($do == 'Manage') {//Manage member page

            // select all users except admin
            $stmt =$con->prepare("SELECT 
                                              comments.*,items.Name as Item_Name,users.Username as Member
                                            FROM 
                                              comments
                                            inner join 
                                              items
                                            on 
                                              items.Item_ID = comments.Item_id
                                            inner join 
                                              users
                                            on 
                                              users.UserID = comments.User_id
                                              order by 
                                              C_id DESC");

            // execute the statement

            $stmt->execute();

            //assign to variable

            $comments =$stmt->fetchAll();

            if(!empty($comments)){

            ?>

            <h1 class="text-center">Manage Comments</h1>

            <div class="container">
                <div class="table-responsive">
                    <table  class="main-table text-center table table-bordered">
                        <tr>
                            <td>#ID</td>
                            <td>Comment</td>
                            <td>Item Name</td>
                            <td>User Name</td>
                            <td>Add Date</td>
                            <td>Control</td>
                        </tr>

                        <?php

                        foreach ($comments as $comment){

                            echo "<tr>";
                            echo "<td>" .$comment['C_id'] ."</td>";
                            echo "<td>" .$comment['Comment'] ."</td>";
                            echo "<td>" .$comment['Item_Name'] ."</td>";
                            echo "<td>" .$comment['Member'] ."</td>";
                            echo "<td>". $comment['Comment_Date']."</td>";
                            echo "<td>
                           <a href='comments.php?do=Edit&comid=". $comment['C_id']."
                           ' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
                           <a href='comments.php?do=Delete&comid=". $comment['C_id']."
                           ' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete </a>";

                            if($comment['Status']==0){
                                echo "<a href='comments.php?do=Approve&comid=". $comment['C_id']."
                                ' class='btn btn-info activate'>
                                <i class='fa fa-check'></i> Approve</a>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
            <?php } else{
                echo '<div class="container">';
                echo '<div class="nice-message">there\'s no comments to show</div>';
                echo '</div>';
            } ?>
        <?php

        } elseif ($do == 'Edit'){//edit page

            $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0 ;

            $stmt = $con->prepare("SELECT * FROM comments WHERE C_id = ?");
            $stmt->execute(array($comid));
            $row = $stmt->Fetch();
            $count = $stmt->rowCount();

            if( $count > 0) { ?>

                <h1 class="text-center">Edit Comments</h1>

                <div class="container">
                    <form class="form-horizontal" action="?do=Update" method="POST">
                        <input type="hidden" name="comid" value="<?= $comid?>">
                        <!--Comment-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Comment</label>
                            <div class="col-sm-10 col-md-6">
                                <textarea class="form-control" name="comment"><?php echo $row['Comment']?></textarea>
                            </div>
                        </div>
                        <!--submit-->
                        <div class="form-group form-group-lg">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="submit" value="Save" class="btn btn-primary btn-lg">
                            </div>
                        </div>
                    </form>
                </div>

                <?php
            } else {
                echo "<div class='container'>";

                $theMsg = '<div class="alert alert-danger">theres no such id</div>';

                redirectHome($theMsg);

                echo "</div>";
            }
        } elseif ($do == 'Update'){

            echo "<h1 class='text-center'>Update Comment</h1>";
            echo "<div class='container'>";

            if($_SERVER['REQUEST_METHOD'] == 'POST'){

                $comid = $_POST['comid'];
                $comment = $_POST['comment'];

                //check if on error

                    $stmt =$con->prepare("UPDATE comments SET Comment = ? WHERE C_id = ?");
                    $stmt->execute(array($comment, $comid));

                    $theMsg = "<div class='alert alert-success'>".$stmt->rowCount() . ' record update</div>';

                    redirectHome($theMsg, 'back');
                //update the database

            } else {

                $theMsg = '<div class="alert alert-danger">sorry you cant browse this page</div>';
                redirectHome($theMsg);

            }
            echo "</div>";
        } elseif ($do == 'Delete'){

            echo "<h1 class='text-center'>Delete Comment</h1>";
            echo "<div class='container'>";

            // check if get request userid is numeric & get the integer value of it

            $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0 ;

            // select all data depend on this id

            $check = checkItem('C_id', 'comments', $comid);

            //if there's such id show the form

            if( $check > 0) {

                $stmt =$con->prepare("DELETE FROM comments WHERE C_id = :zid");

                $stmt->bindParam(":zid", $comid);

                $stmt->execute();

                $theMsg = "<div class='alert alert-success'>".$stmt->rowCount() . 'record Deleted</div>';

                redirectHome($theMsg,'back');
            } else {
                $theMsg = '<div class="alert alert-danger">This id is not exist</div>';

                redirectHome($theMsg);
            }

            echo '</div>';
        } elseif ( $do == 'Approve'){
            echo "<h1 class='text-center'>Approve Comment</h1>";
            echo "<div class='container'>";

            // check if get request userid is numeric & get the integer value of it

            $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0 ;

            // select all data depend on this id

            $check = checkItem('C_id', 'comments', $comid);

            //if there's such id show the form

            if( $check > 0) {

                $stmt =$con->prepare("UPDATE comments SET Status = 1 WHERE C_id = ?");

                $stmt->execute(array($comid));

                $theMsg = "<div class='alert alert-success'>".$stmt->rowCount() . 'Comment Approved</div>';

                redirectHome($theMsg,'back');
            } else {
                $theMsg = '<div class="alert alert-danger">This id is not exist</div>';

                redirectHome($theMsg);
            }

            echo '</div>';
        }

        include $tpl . 'footer.php';

    } else {

        header('location: index.php');

        exit();
    }

ob_end_flush();
?>