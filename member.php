<?php

    /*
     ======================================
     == manange members page
     == you can add | edit | delete members from here
     ======================================
    */
    ob_start();

    session_start();

    $pageTitle = 'Members';

    if (isset($_SESSION['user'])){


    include 'init.php';

    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
  if($do == 'Edit'){//edit page

        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0 ;

        $stmt = $con->prepare("SELECT * FROM users WHERE UserID = ? LIMIT 1");
        $stmt->execute(array($userid));
        $row = $stmt->Fetch();
        $count = $stmt->rowCount();

        if( $count > 0) { ?>

            <h1 class="text-center">Edit Profile</h1>

            <div class="container">
                <form class="form-horizontal main-form" action="?do=Update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="userid" value="<?= $userid?>">
                    <!--                username-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Username</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="username" class="form-control" value="<?= $row['Username']?>" autocomplete="off" required="required">
                        </div>
                    </div>
                    <!--                password-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Password</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="hidden" name="oldpassword" value="<?= $row['Password']?>" >
                            <input type="password" name="newpassword" class="form-control" autocomplete="new-password">
                        </div>
                    </div>
                    <!--                email-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="email" name="email" class="form-control" value="<?= $row['Email']?>" required="required">
                        </div>
                    </div>
                    <!--                full name-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Full Name</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="full" class="form-control" value="<?= $row['FullName']?>" required="required">
                        </div>
                    </div>
                    <!--                profile img-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">User Photo</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="file" name="avatar" class="form-control" required>
                        </div>
                    </div>
                    <!--                submit-->
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

        echo "<h1 class='text-center'>Update Members</h1>";
        echo "<div class='container'>";

        if($_SERVER['REQUEST_METHOD'] == 'POST'){

            $avatarName = $_FILES['avatar']['name'];
            $avatarSize = $_FILES['avatar']['size'];
            $avatarTmp  = $_FILES['avatar']['tmp_name'];
            $avatarType = $_FILES['avatar']['type'];

            //type of allowed file type to upload

            $avatarAllowedExtension = array("jpeg","jpg","png","gif");

            //get avatar extension
            $ex_avatar = explode('.',$avatarName);
            $avatarExtension =strtolower(end($ex_avatar));

            $id = $_POST['userid'];
            $user = $_POST['username'];
            $email = $_POST['email'];
            $name = $_POST['full'];

            //pass trick
            $pass =empty($_POST['newpassword']) ? $_POST['oldpassword'] : sha1($_POST['newpassword']) ;

            // validate the form
            $formErrors = array();

            if(strlen($user)<4){
                $formErrors[] = 'username cant be less than <strong>4 characters</strong>';
            }

            if(strlen($user)>20){
                $formErrors[] = 'username cant be more than <strong>20 characters</strong>';
            }

            if(empty($user)){
                $formErrors[] = 'username <strong>cant be empty</strong>';
            }

            if(empty($name)){
                $formErrors[] = 'full name <strong>cant be empty</strong>';
            }

            if(empty($email)){
                $formErrors[] = 'email <strong>cant be empty</strong>';
            }
            if (!empty($avatarName) && ! in_array($avatarExtension,$avatarAllowedExtension)){
                $formErrors[] = 'This Extension Is Not <strong>Allowed</strong>';
            }

            if (empty($avatarName)){
                $formErrors[] = 'Avatar Is <strong>Required</strong>';
            }

            if ($avatarSize > 6291456){
                $formErrors[] = 'Avatar Cant Be Larger Than <strong>6MB</strong>';
            }

            foreach ($formErrors as $error){
                echo '<div class="alert alert-danger">'.$error.'</div>';
            }
            //check if on error
            if(empty($formErrors)){

                $stmt2 =$con->prepare("select
                                                  *
                                                 from
                                                  users
                                                 where
                                                  Username = ?
                                                 and
                                                  UserID != ?");

                $stmt2->execute(array($user,$id));
                $count = $stmt2->rowCount();
                if($count == 1){
                    $theMsg = '<div class="alert alert-danger">sorry this user is exist</div>';

                    redirectHome($theMsg,'back');
                }else{

                    $avatar = rand(0, 100000) . '_'. $avatarName;
                    move_uploaded_file($avatarTmp,"admin\uploads\avatar\\".$avatar);

                    $stmt =$con->prepare("UPDATE users SET Username = ?, Email = ?, FullName = ?, Password = ?,Avatar = ? WHERE UserID = ?");
                    $stmt->execute(array($user, $email, $name, $pass,$avatar, $id));

                    $theMsg = "<div class='alert alert-success'>".$stmt->rowCount() . ' record update</div>';

                    redirectHome($theMsg, 'back');

                }
            }
            //update the database

        } else {

            $theMsg = '<div class="alert alert-danger">sorry you cant browse this page</div>';
            redirectHome($theMsg);

        }
        echo "</div>";
    }
    include $tpl . 'footer.php';

} else {

    header('location: index.php');

    exit();
}

ob_end_flush();
 ?>
