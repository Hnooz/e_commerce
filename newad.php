<?php
    ob_start();
    session_start();
    $pageTitle ='Create New Item';
    include "init.php";
    if(isset($_SESSION['user'])) {

        if($_SERVER['REQUEST_METHOD'] == 'POST'){

         $formErrors = array();

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

            $name       = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
            $desc       = filter_var($_POST['description'],FILTER_SANITIZE_STRING);
            $price      = filter_var($_POST['price'],FILTER_SANITIZE_NUMBER_INT);
            $country    = filter_var($_POST['country'],FILTER_SANITIZE_STRING);
            $status     = filter_var($_POST['status'],FILTER_SANITIZE_NUMBER_INT);
            $category   = filter_var($_POST['category'],FILTER_SANITIZE_NUMBER_INT);
            $tags       = filter_var($_POST['tags'],FILTER_SANITIZE_STRING);

            if (strlen($name)<3){
                $formErrors[] = 'Item Title Must Be At Least 3 Characters';
            }
            if (strlen($desc)<10){
                $formErrors[] = 'Item Description Must Be At Least 10 Characters';
            }
            if (strlen($country)<2){
                $formErrors[] = 'Item Country Must Be At Least 2 Characters';
            }
            if (empty($price)){
                $formErrors[] = 'Item Price Must Be Not Empty';
            }
            if (empty($status)){
                $formErrors[] = 'Item Status Must Be Not Empty';
            }
            if (empty($category)){
                $formErrors[] = 'Item Category Must Be Not Empty';
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
            if(empty($formErrors)){

                $avatar = rand(0, 100000) . '_'. $avatarName;
                move_uploaded_file($avatarTmp,"admin\uploads\avatar\\".$avatar);

                // check if user exist in database

                // insert user info in database

                $stmt = $con->prepare("
                 INSERT INTO
                 items(Name, Description,Price,Country_Made, Status,Add_Date,Cat_ID, Member_ID,Avatar,Tags)
                 VALUES(:zname,:zdesc,:zprice,:zcountry,:zstatus,  now(), :zcat, :zmember,:zavatar,:ztags)");

                $stmt->execute(array(
                    'zname'     => $name,
                    'zdesc'     => $desc,
                    'zprice'    => $price,
                    'zcountry'  => $country,
                    'zstatus'   => $status,
                    'zmember'   => $_SESSION['uid'],
                    'zcat'      => $category,
                    'zavatar'   => $avatar,
                    'ztags'     => $tags


                ));
                if($stmt){
                    $succesMsg='Item Has Been Added';
                }
            }
        }
        ?>
        <h1 class="text-center"><?=$pageTitle?></h1>
        <div class="create block">
            <div class="container">
                <div class="panel panel-primary">
                    <div class="panel-heading"><?=$pageTitle?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                    <form class="form-horizontal main-form" action="<?= $_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
                                        <!--Name-->
                                        <div class="form-group form-group-lg">
                                            <label class="col-sm-3 control-label">Name</label>
                                            <div class="col-sm-10 col-md-9">
                                                <input
                                                       pattern=".{4,}"
                                                       title="This Field Require At Least 4 Chars"
                                                       type="text" name="name"
                                                       class="form-control live"
                                                       placeholder="Name OF The Item"
                                                       data-class=".live-title"
                                                        required>
                                            </div>
                                        </div>
                                        <!--Description-->
                                        <div class="form-group form-group-lg">
                                            <label class="col-sm-3 control-label">Description</label>
                                            <div class="col-sm-10 col-md-9">
                                                <input
                                                        pattern=".{10,}"
                                                        title="This Field Require At Least 10 Chars"
                                                       type="text" name="description"
                                                       class="form-control live"
                                                       placeholder="Description OF The Item"
                                                       data-class=".live-desc"
                                                       required >
                                            </div>
                                        </div>
                                        <!--Price-->
                                        <div class="form-group form-group-lg">
                                            <label class="col-sm-3 control-label">Price</label>
                                            <div class="col-sm-10 col-md-9">
                                                <input type="text" name="price"
                                                       class="form-control live"
                                                       placeholder="Price OF The Item"
                                                       data-class=".live-price"
                                                       required >
                                            </div>
                                        </div>
                                        <!--Country-->
                                        <div class="form-group form-group-lg">
                                            <label class="col-sm-3 control-label">Country</label>
                                            <div class="col-sm-10 col-md-9">
                                                <input type="text" name="country"
                                                       class="form-control"
                                                       placeholder="Country OF Made"
                                                       required >
                                            </div>
                                        </div>
                                        <!--Status-->
                                        <div class="form-group form-group-lg">
                                            <label class="col-sm-3 control-label">Status</label>
                                            <div class="col-sm-10 col-md-9">
                                                <select name="status" required>
                                                    <option value="">...</option>
                                                    <option value="1">New</option>
                                                    <option value="2">Like New</option>
                                                    <option value="3">Used</option>
                                                    <option value="4">Very old</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--Categories-->
                                        <div class="form-group form-group-lg">
                                            <label class="col-sm-3 control-label">Categories</label>
                                            <div class="col-sm-10 col-md-9">
                                                <select name="category" required>
                                                    <option value="">...</option>
                                                    <?php
                                                    $cats = getAllFrom('*','categories','','','ID');
                                                    foreach ($cats as $cat){
                                                        echo "<option value='".$cat['ID']."'>". $cat['Name']."</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <!--                profile img-->
                                        <div class="form-group form-group-lg">
                                            <label class="col-sm-3 control-label">Item Photo</label>
                                            <div class="col-sm-10 col-md-9">
                                                <input type="file" name="avatar" class="form-control live"
                                                       data-class=".live-img"
                                                       required>
                                            </div>
                                        </div>
                                        <!--Tags-->
                                        <div class="form-group form-group-lg">
                                            <label class="col-sm-3 control-label">Tags</label>
                                            <div class="col-sm-10 col-md-9">
                                                <input type="text" name="tags"
                                                       class="form-control"
                                                       placeholder="Separate Tags With Coma (,)"
                                                       >
                                            </div>
                                        </div>
                                        <!--submit-->
                                        <div class="form-group form-group-lg">
                                            <div class="col-sm-offset-3 col-sm-9">
                                                <input type="submit" value="Add Item" class="btn btn-primary btn-lg">
                                            </div>
                                        </div>
                                    </form>
                            </div>
                            <div class="col-md-4">
                                <div class="thumbnail item-box live-preview">
                                    <span class="price-tag">
                                        $<span class="live-price"></span>
                                    </span>
                                    <img class="img-responsive" src="images%20(6).jpg">
                                    <div class="caption">
                                        <h3 class="live-title">Title</h3>
                                        <p class="live-desc">Description</p>
                                    </div>
                                  </div>
                            </div>
                        </div>
                        <!-- start looping through errors -->
                        <?php
                            if (!empty($formErrors)){
                                foreach ($formErrors as $error){
                                    echo '<div class="alert alert-danger">' .$error .'</div>';
                                }
                            }
                            if (isset($succesMsg)){
                                echo '<div class="alert alert-success">'.$succesMsg.'</div>';
                            }
                        ?>
                        <!-- end looping through errors -->
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
