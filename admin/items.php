<?php

/*
   ======================================
   == Items PAGE
   ======================================
  */
    ob_start();

    session_start();

    $pageTitle = 'Items';

    if (isset($_SESSION['Username'])) {


    include 'init.php';

    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
    if($do == 'Manage'){


    $stmt =$con->prepare("select
                                        items.*,
                                        categories.Name as Category_Name,
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
                                      order by 
                                      Item_ID DESC");

        // execute the statement

        $stmt->execute();

        //assign to variable

        $items =$stmt->fetchAll();
        if(!empty($items)){

        ?>

        <h1 class="text-center">Manage Item</h1>

        <div class="container">
            <div class="table-responsive">
                <table  class="main-table manage-members text-center table table-bordered">
                    <tr>
                        <td>#ID</td>
                        <td>Image</td>
                        <td>Name</td>
                        <td>Description</td>
                        <td>Price</td>
                        <td>Adding Date</td>
                        <td>Category</td>
                        <td>User Name</td>
                        <td>Tags</td>
                        <td>Control</td>
                    </tr>

                    <?php

                    foreach ($items as $item){

                        echo "<tr>";
                            echo "<td>" .$item['Item_ID'] ."</td>";
                            echo "<td>";
                            if (empty($item['Avatar'])) {
                                echo '<img src="../images%20(6).jpg">';
                            }else{
                                echo "<img src='uploads/avatar/" . $item['Avatar'] . "' alt=''>";
                            }
                            echo "</td>";
                            echo "<td>" .$item['Name'] ."</td>";
                            echo "<td>" .$item['Description'] ."</td>";
                            echo "<td>" .$item['Price'] ."</td>";
                            echo "<td>". $item['Add_Date']."</td>";
                            echo "<td>" .$item['Category_Name'] ."</td>";
                            echo "<td>". $item['Username']."</td>";
                            echo "<td>". $item['Tags']."</td>";
                            echo "<td>
                               <a href='items.php?do=Edit&itemid=". $item['Item_ID']."' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
                               <a href='items.php?do=Delete&itemid=". $item['Item_ID']."' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete </a>";

                        if($item['Approve']==0){
                            echo "<a href='items.php?do=Approve&itemid=". $item['Item_ID']."' class='btn btn-info activate'>
                            <i class='fa fa-check'></i> Approve</a>";
                        }
                            echo "</td>";
                        echo "</tr>";
                    }

                    ?>
                </table>
            </div>
            <a href="items.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> New Item</a>
        </div>
        <?php } else{
            echo '<div class="container">';
            echo '<div class="nice-message">there\'s no items to show</div>';
            echo ' <a href="items.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> New Item</a>';
            echo '</div>';
        } ?>
    <?php
    } elseif($do =='Add'){ ?>

        <h1 class="text-center">Add New Item</h1>

            <div class="container">
                <form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">
                    <!--Name-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="name"
                                   class="form-control"
                                   placeholder="Name OF The Item">
                        </div>
                    </div>
                    <!--Description-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="description"
                                   class="form-control"
                                   placeholder="Description OF The Item">
                        </div>
                    </div>
                    <!--Price-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Price</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="price"
                                   class="form-control"
                                   placeholder="Price OF The Item">
                        </div>
                    </div>
                    <!--Country-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Country</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="country"
                                   class="form-control"
                                   placeholder="Country OF Made">
                        </div>
                    </div>
                    <!--Status-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10 col-md-6">
                           <select name="status">
                               <option value="0">...</option>
                               <option value="1">New</option>
                               <option value="2">Like New</option>
                               <option value="3">Used</option>
                               <option value="4">Very old</option>
                           </select>
                        </div>
                    </div>
                    <!--Members-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Members</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="member">
                                <option value="0">...</option>
                                <?php
                                $allMember = getAllFrom("*","users","","","UserID");
                                    foreach ($allMember as $user){
                                        echo "<option value='".$user['UserID']."'>". $user['Username']."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!--Categories-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Categories</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="category">
                                <option value="0">...</option>
                                <?php
                                $allCats = getAllFrom("*","categories","where parent = 0","","ID");
                                foreach ($allCats as $cat){
                                    echo "<option value='".$cat['ID']."'>". $cat['Name']."</option>";
                                    $childCats = getAllFrom("*","categories","where parent = {$cat['ID']}","","ID");
                                    foreach ($childCats as $child){
                                        echo "<option value='".$child['ID']."'>". $child['Name']."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!--                profile img-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Item Photo</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="file" name="avatar" class="form-control"
                                   required>
                        </div>
                    </div>
                    <!--Tags-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Tags</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="tags"
                                   class="form-control"
                                   placeholder="Separate Tags With Coma (,)">
                        </div>
                    </div>
                    <!--submit-->
                    <div class="form-group form-group-lg">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" value="Add Item" class="btn btn-primary btn-lg">
                        </div>
                    </div>
                </form>
            </div>

            <?php

    }elseif($do =='Insert'){

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            echo "<h1 class='text-center'>Insert Items</h1>";
            echo "<div class='container'>";

            $avatarName = $_FILES['avatar']['name'];
            $avatarSize = $_FILES['avatar']['size'];
            $avatarTmp  = $_FILES['avatar']['tmp_name'];
            $avatarType = $_FILES['avatar']['type'];

            //type of allowed file type to upload

            $avatarAllowedExtension = array("jpeg","jpg","png","gif");

            //get avatar extension
            $ex_avatar = explode('.',$avatarName);
            $avatarExtension =strtolower(end($ex_avatar));


            $name       = $_POST['name'];
            $desc       = $_POST['description'];
            $price      = $_POST['price'];
            $country    = $_POST['country'];
            $status     = $_POST['status'];
            $member     = $_POST['member'];
            $cat        = $_POST['category'];
            $tags        = $_POST['tags'];


            // validate the form
            $formErrors = array();

            if(empty($name)){
                $formErrors[] = 'Name Can\'t Be <strong>Empty</strong>';
            }

            if(empty($desc)){
                $formErrors[] = 'Description Can\'t Be <strong>Empty</strong>';
            }

            if(empty($price)){
                $formErrors[] = 'Price Can\'t Be <strong>Empty</strong>';
            }

            if(empty($country)){
                $formErrors[] = 'Country Can\'t Be <strong>Empty</strong>';
            }

            if($status == 0){
                $formErrors[] = 'You Must Choose The <strong>Status</strong>';
            }

            if($member == 0){
                $formErrors[] = 'You Must Choose The <strong>Member</strong>';
            }

            if($cat == 0){
                $formErrors[] = 'You Must Choose The <strong>Category</strong>';
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

                    // insert user info in database

                    $stmt = $con->prepare("
                 INSERT INTO
                 items(Name, Description,Price,Country_Made, Status,Add_Date,Cat_ID, Member_ID,Avatar,Tags)
                 VALUES(:zname,:zdesc,:zprice,:zcountry,:zstatus,  now(), :zcat, :zmember,:zavatar, :ztags)");

                    $stmt->execute(array(
                        'zname'     => $name,
                        'zdesc'     => $desc,
                        'zprice'    => $price,
                        'zcountry'  => $country,
                        'zstatus'   => $status,
                        'zmember'   => $member,
                        'zcat'      => $cat,
                        'zavatar'      => $avatar,
                        'ztags'     => $tags

                    ));
                    $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . 'record Inserted</div>';

                    redirectHome($theMsg, 'back');

            }
            //update the database

        } else {
            echo "<div class='container'>";
            $theMsg = '<div class="alert alert-danger">sorry you cant browse this page</div>';
            redirectHome($theMsg);
            echo "</div>";
        }
        echo "</div>";


    }elseif($do =='Edit'){

        // CHECK IF GET REQUEST ITEM NUMERIC & GET ITS INTEGER VALUE

        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0 ;

        $stmt = $con->prepare("SELECT * FROM items WHERE Item_ID = ?");
        $stmt->execute(array($itemid));
        $item = $stmt->Fetch();
        $count = $stmt->rowCount();

        if($count > 0) { ?>

            <h1 class="text-center">Edit Item</h1>

            <div class="container">
                <form class="form-horizontal" action="?do=Update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="itemid" value="<?= $itemid?>">
                    <!--Name-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="name"
                                   class="form-control"
                                   placeholder="Name OF The Item"
                                   value="<?= $item['Name']?>">
                        </div>
                    </div>
                    <!--Description-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="description"
                                   class="form-control"
                                   placeholder="Description OF The Item"
                                   value="<?= $item['Description']?>">
                        </div>
                    </div>
                    <!--Price-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Price</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="price"
                                   class="form-control"
                                   placeholder="Price OF The Item"
                                   value="<?= $item['Price']?>">
                        </div>
                    </div>
                    <!--Country-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Country</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="country"
                                   class="form-control"
                                   placeholder="Country OF Made"
                                   value="<?= $item['Country_Made']?>">
                        </div>
                    </div>
                    <!--Status-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="status">
                                <option value="1" <?php if($item['Status'] == 1){echo 'selected';}?>>New</option>
                                <option value="2" <?php if($item['Status'] == 2){echo 'selected';}?>>Like New</option>
                                <option value="3" <?php if($item['Status'] == 3){echo 'selected';}?>>Used</option>
                                <option value="4" <?php if($item['Status'] == 4){echo 'selected';}?>>Very old</option>
                            </select>
                        </div>
                    </div>
                    <!--Members-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Members</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="member">
                                <?php
                                $stmt = $con->prepare("SELECT * FROM users");
                                $stmt->execute();
                                $users = $stmt->fetchAll();
                                foreach ($users as $user){
                                    echo "<option value='".$user['UserID']."'";
                                    if($item['Member_ID'] == $user['UserID']){echo 'selected';}
                                    echo">". $user['Username']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!--Categories-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Categories</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="category">
                                <?php
                                $stmt2 = $con->prepare("SELECT * FROM categories");
                                $stmt2->execute();
                                $cats = $stmt2->fetchAll();
                                foreach ($cats as $cat){
                                    echo "<option value='".$cat['ID']."'";
                                    if($item['Cat_ID'] == $cat['ID']){echo 'selected';}
                                    echo">". $cat['Name']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!--                profile img-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Item Photo</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="file" name="avatar" class="form-control"
                                   value="<?= $item['Avatar']?>" aria-selected="true">
                        </div>
                    </div>
                    <!--Tags-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Tags</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="tags"
                                   class="form-control"
                                   placeholder="Separate Tags With Coma (,)"
                                   value="<?= $item['Tags']?>">
                        </div>
                    </div>
                    <!--submit-->
                    <div class="form-group form-group-lg">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" value="Add Item" class="btn btn-primary btn-lg">
                        </div>
                    </div>
                </form>

                <?php
                // select all users except admin
                $stmt =$con->prepare("SELECT 
                                              comments.*,users.Username as Member
                                            FROM 
                                              comments
                                           inner join 
                                              users
                                            on 
                                              users.UserID = comments.User_id
                                              where Item_id=?");

                // execute the statement

                $stmt->execute(array($itemid));

                //assign to variable

                $rows =$stmt->fetchAll();

                if(!empty($rows)){

                ?>

                <h1 class="text-center">Manage [ <?= $item['Name']?> ] Comments</h1>
                    <div class="table-responsive">
                        <table  class="main-table text-center table table-bordered">
                            <tr>
                                <td>Comment</td>
                                <td>User Name</td>
                                <td>Add Date</td>
                                <td>Control</td>
                            </tr>

                            <?php

                            foreach ($rows as $row){

                                echo "<tr>";
                                echo "<td>" .$row['Comment'] ."</td>";
                                echo "<td>" .$row['Member'] ."</td>";
                                echo "<td>". $row['Comment_Date']."</td>";
                                echo "<td>
                           <a href='comments.php?do=Edit&comid=". $row['C_id']."
                           ' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
                           <a href='comments.php?do=Delete&comid=". $row['C_id']."
                           ' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete </a>";

                                if($row['Status']==0){
                                    echo "<a href='comments.php?do=Approve&comid=". $row['C_id']."
                                ' class='btn btn-info activate'>
                                <i class='fa fa-check'></i> Approve</a>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </table>
                    </div>
                    <?php }?>
            </div>

            <?php
        } else {
            echo "<div class='container'>";

            $theMsg = '<div class="alert alert-danger">theres no such id</div>';

            redirectHome($theMsg);

            echo "</div>";
        }

    }elseif($do =='Update'){

        echo "<h1 class='text-center'>Update Item</h1>";
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


            $id         = $_POST['itemid'];
            $name       = $_POST['name'];
            $desc       = $_POST['description'];
            $price      = $_POST['price'];
            $country    = $_POST['country'];
            $status     = $_POST['status'];
            $member     = $_POST['member'];
            $cat        = $_POST['category'];
            $tags        = $_POST['tags'];

            $formErrors = array();

            if(empty($name)){
                $formErrors[] = 'Name Can\'t Be <strong>Empty</strong>';
            }

            if(empty($desc)){
                $formErrors[] = 'Description Can\'t Be <strong>Empty</strong>';
            }

            if(empty($price)){
                $formErrors[] = 'Price Can\'t Be <strong>Empty</strong>';
            }

            if(empty($country)){
                $formErrors[] = 'Country Can\'t Be <strong>Empty</strong>';
            }

            if($status == 0){
                $formErrors[] = 'You Must Choose The <strong>Status</strong>';
            }

            if($member == 0){
                $formErrors[] = 'You Must Choose The <strong>Member</strong>';
            }

            if($cat == 0){
                $formErrors[] = 'You Must Choose The <strong>Category</strong>';
            }
            if (!empty($avatarName) && ! in_array($avatarExtension,$avatarAllowedExtension)){
                $formErrors[] = 'This Extension Is Not <strong>Allowed</strong>';
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

                $stmt =$con->prepare("UPDATE 
                                                      items
                                                SET 
                                                      Name = ?,
                                                      Description = ?,
                                                      Price = ?,
                                                      Country_Made = ?,
                                                      Status = ?,
                                                      Cat_ID = ?,
                                                      Member_ID = ?,
                                                      Avatar = ?,
                                                      Tags = ? 
                                                WHERE
                                                      Item_ID = ?");
                $stmt->execute(array($name, $desc, $price, $country, $status, $cat, $member,$avatar,$tags, $id));

                $theMsg = "<div class='alert alert-success'>".$stmt->rowCount() . ' record update</div>';

                redirectHome($theMsg, 'back');
            }
            //update the database

        } else {

            $theMsg = '<div class="alert alert-danger">sorry you cant browse this page</div>';
            redirectHome($theMsg);

        }
        echo "</div>";

    }elseif($do =='Delete'){

        echo "<h1 class='text-center'>Delete Item</h1>";
        echo "<div class='container'>";

        // check if get request itemid is numeric & get the integer value of it

        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0 ;

        // select all data depend on this id

        $check = checkItem('Item_ID', 'items', $itemid);

        //if there's such id show the form

        if( $check > 0) {

            $stmt =$con->prepare("DELETE FROM items WHERE Item_ID = :zid");

            $stmt->bindParam(":zid", $itemid);

            $stmt->execute();

            $theMsg = "<div class='alert alert-success'>".$stmt->rowCount() . 'record Deleted</div>';

            redirectHome($theMsg,'back');
        } else {
            $theMsg = '<div class="alert alert-danger">This id is not exist</div>';

            redirectHome($theMsg);
        }

        echo '</div>';

    }elseif($do =='Approve') {

        echo "<h1 class='text-center'>Approve Item</h1>";
        echo "<div class='container'>";

        // check if get request item id is numeric & get the integer value of it

        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0 ;

        // select all data depend on this id

        $check = checkItem('Item_ID', 'items', $itemid);

        //if there's such id show the form

        if( $check > 0) {

            $stmt =$con->prepare("UPDATE items SET Approve = 1 WHERE Item_ID = ?");

            $stmt->execute(array($itemid));

            $theMsg = "<div class='alert alert-success'>".$stmt->rowCount() . 'record Updated</div>';

            redirectHome($theMsg,'back');
        } else {
            $theMsg = '<div class="alert alert-danger">This id is not exist</div>';

            redirectHome($theMsg);
        }

        echo '</div>';
    }
    include $tpl .'footer.php';
    } else {

        header('location: index.php');

        exit();
    }

ob_end_flush();
?>