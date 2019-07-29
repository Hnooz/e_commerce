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

    if (isset($_SESSION['Username'])){


    include 'init.php';

    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

    // start Manage page
    if($do == 'Manage') {//Manage member page

        $query ='';

        if(isset($_GET['page'])&& $_GET['page']=='Pending'){
           $query ='And RegStatus = 0';
        }
        // select all users except admin
        $stmt =$con->prepare("SELECT * FROM users WHERE GroupID !=1 $query order by UserID DESC");

        // execute the statement

        $stmt->execute();

        //assign to variable

        $rows =$stmt->fetchAll();

        if(!empty($rows)){

        ?>

        <h1 class="text-center">Manage Members</h1>

        <div class="container">
            <div class="table-responsive">
                <table  class="main-table manage-members text-center table table-bordered">
                    <tr>
                        <td>#ID</td>
                        <td>Avatar</td>
                        <td>Username</td>
                        <td>Email</td>
                        <td>Full Name</td>
                        <td>Registered Date</td>
                        <td>Control</td>
                    </tr>

                    <?php

                    foreach ($rows as $row){

                        echo "<tr>";
                            echo "<td>" .$row['UserID'] ."</td>";
                            echo "<td>";
                            if (empty($row['Avatar'])) {
                                echo '<img src="../images%20(6).jpg">';
                            }else{
                                echo "<img src='uploads/avatar/" . $row['Avatar'] . "' alt=''>";
                            }
                            echo "</td>";
                            echo "<td>" .$row['Username'] ."</td>";
                            echo "<td>" .$row['Email'] ."</td>";
                            echo "<td>" .$row['FullName'] ."</td>";
                            echo "<td>". $row['Date']."</td>";
                            echo "<td>
                           <a href='members.php?do=Edit&userid=". $row['UserID']."' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
                           <a href='members.php?do=Delete&userid=". $row['UserID']."' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete </a>";

                            if($row['RegStatus']==0){
                                echo "<a href='members.php?do=Activate&userid=". $row['UserID']."' class='btn btn-info activate'>
                                <i class='fa fa-check'></i> active</a>";
                            }
                            echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
            <a href="members.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> New Members</a>
        </div>
            <?php } else{
            echo '<div class="container">';
                echo '<div class="nice-message">there\'s no members to show</div>';
                echo'<a href="members.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> New Members</a>';
            echo '</div>';
        } ?>

   <?php } elseif ($do== 'Add') { //add members page?>

        <h1 class="text-center">Add New Members</h1>

        <div class="container">
            <form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">
                <!--                username-->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Username</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="username" class="form-control" autocomplete="off" required="required"
                               placeholder=" User name to login">
                    </div>
                </div>
                <!--                password-->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="password" name="password" class="password form-control" autocomplete="new-password"
                               required="required" placeholder="password most be hard & complexes">
                        <i class="show-pass fa fa-eye fa-2x"></i>
                    </div>
                </div>
                <!--                email-->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="email" name="email" class="form-control" required="required"
                               placeholder="Enter valid email">
                    </div>
                </div>
                <!--                full name-->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Full Name</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="full" class="form-control" required="required"
                               placeholder="Full name will show in top">
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
                        <input type="submit" value="Add Member" class="btn btn-primary btn-lg">
                    </div>
                </div>
            </form>
        </div>


        <?php
    } elseif ($do == 'Insert'){



        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            echo "<h1 class='text-center'>Insert Members</h1>";
            echo "<div class='container'>";
            // upload var

            $avatarName = $_FILES['avatar']['name'];
            $avatarSize = $_FILES['avatar']['size'];
            $avatarTmp  = $_FILES['avatar']['tmp_name'];
            $avatarType = $_FILES['avatar']['type'];

            //type of allowed file type to upload

            $avatarAllowedExtension = array("jpeg","jpg","png","gif");

            //get avatar extension
            $ex_avatar = explode('.',$avatarName);
            $avatarExtension =strtolower(end($ex_avatar));



            //get var from the form
            $user = $_POST['username'];
            $pass = $_POST['password'];
            $email = $_POST['email'];
            $name = $_POST['full'];

            $hashPass = sha1($_POST['password']);

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

            if(empty($pass)){
                $formErrors[] = 'password <strong>cant be empty</strong>';
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
                $avatar = rand(0, 100000) . '_'. $avatarName;
                move_uploaded_file($avatarTmp,"uploads\avatar\\".$avatar);

                    // check if user exist in database

                    $check = checkItem("Username", "users", $user);

                    if ($check == 1){

                        $theMsg = '<div class="alert alert-danger"> Sorry This User Is Exist</div>';

                        redirectHome($theMsg,'back');
                    } else {


                        // insert user info in database

                        $stmt = $con->prepare("
                     INSERT INTO
                     users(Username, Password,Email,FullName, RegStatus,Date,Avatar)
                     VALUES(:zuser,:zpass,:zmail,:zname, 1, now(),:zavatar)");

                        $stmt->execute(array(
                            'zuser'     => $user,
                            'zpass'     => $hashPass,
                            'zmail'     => $email,
                            'zname'     => $name,
                            'zavatar'   => $avatar
                        ));
                        $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . 'record Inserted</div>';

                        redirectHome($theMsg, 'back');
                    }
                }
            //update the database

        } else {
            echo "<div class='container'>";
            $theMsg = '<div class="alert alert-danger">sorry you cant browse this page</div>';
            redirectHome($theMsg);
            echo "</div>";
        }
        echo "</div>";


    } elseif ($do == 'Edit'){//edit page

        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0 ;

        $stmt = $con->prepare("SELECT * FROM users WHERE UserID = ? LIMIT 1");
        $stmt->execute(array($userid));
        $row = $stmt->Fetch();
        $count = $stmt->rowCount();

        if( $count > 0) { ?>

            <h1 class="text-center">Edit Members</h1>

            <div class="container">
                <form class="form-horizontal" action="?do=Update" method="POST" enctype="multipart/form-data">
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
                    move_uploaded_file($avatarTmp,"uploads\avatar\\".$avatar);

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
    } elseif ($do == 'Delete'){

        echo "<h1 class='text-center'>Delete Members</h1>";
        echo "<div class='container'>";

            // check if get request userid is numeric & get the integer value of it

            $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0 ;

            // select all data depend on this id

            $check = checkItem('userid', 'users', $userid);

            //if there's such id show the form

            if( $check > 0) {

                $stmt =$con->prepare("DELETE FROM users WHERE UserID = :zuser");

                $stmt->bindParam(":zuser", $userid);

                $stmt->execute();

                $theMsg = "<div class='alert alert-success'>".$stmt->rowCount() . 'record Deleted</div>';

                redirectHome($theMsg ,'back');
            } else {
                $theMsg = '<div class="alert alert-danger">This id is not exist</div>';

                redirectHome($theMsg);
            }

            echo '</div>';
    } elseif ( $do == 'Activate'){
        echo "<h1 class='text-center'>Activate Members</h1>";
        echo "<div class='container'>";

        // check if get request userid is numeric & get the integer value of it

        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0 ;

        // select all data depend on this id

        $check = checkItem('userid', 'users', $userid);

        //if there's such id show the form

        if( $check > 0) {

            $stmt =$con->prepare("UPDATE users SET RegStatus = 1 WHERE UserID = ?");

            $stmt->execute(array($userid));

            $theMsg = "<div class='alert alert-success'>".$stmt->rowCount() . 'record Updated</div>';

            redirectHome($theMsg);
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