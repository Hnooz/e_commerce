<?php
/*
   ======================================
   == CATEGORIES PAGE
   ======================================
  */
    ob_start();

    session_start();

    $pageTitle = 'Categories';

    if (isset($_SESSION['Username'])) {


        include 'init.php';

        $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
        if($do == 'Manage'){

            $sort = 'ASC';
            $sort_array = array('ASC','DESC');
            if(isset($_GET['sort']) && in_array($_GET['sort'], $sort_array)){
                $sort = $_GET['sort'];
            }
            $stmt2 =$con->prepare("SELECT *FROM categories where parent = 0 ORDER BY Ordering $sort");
            $stmt2->execute();
            $cats =$stmt2->fetchAll();
            if(!empty($cats)){
            ?>

            <h1 class="text-center">Manage Categories</h1>
            <div class="container categories">
                <div class="panel panel-default">
                    <div class="panel-heading">
                       <i class="fa fa-edit"></i> Manage Categories
                        <div class="option pull-right">
                           <i class="fa fa-sort"></i> Ordering : [
                            <a class="<?php if ($sort =='ASC'){ echo 'active';}?>" href="?sort=ASC">Asc</a> |
                            <a class="<?php if ($sort =='DESC'){ echo 'active';}?>" href="?sort=DESC">Desc</a> ]
                           <i class="fa fa-eye"></i> View : [
                            <span class="active" data-view="full">Full</span> |
                            <span data-view="classic">Classic</span> ]
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php
                        foreach ($cats as $cat) {
                            echo "<div class='cat'>";
                            echo "<div class='hidden-buttons'>";
                            echo "<a href='categories.php?do=Edit&catid=" . $cat['ID'] . "' class='btn btn-xs btn-primary'><i class='fa fa-edit'></i>Edit</a>";
                            echo "<a href='categories.php?do=Delete&catid=" . $cat['ID'] . "' class='confirm btn btn-xs btn-danger'><i class='fa fa-close'></i>Delete</a>";
                            echo "</div>";
                            echo "<h3>" . $cat['Name'] . "</h3>";
                            echo "<div class='full-view'>";
                            echo "<p>";
                            if ($cat['Description'] == '') {
                                echo 'This category has no description';
                            } else {
                                echo $cat['Description'];
                            }
                            echo "</p>";
                            if ($cat['Visibility'] == 1) {
                                echo '<span class="visibility"><i class="fa fa-eye"></i> Hidden</span>';
                            }
                            if ($cat['Allow_Comment'] == 1) {
                                echo '<span class="commenting"><i class="fa fa-close"></i> Comment Disabled</span>';
                            }
                            if ($cat['Allow_Ads'] == 1) {
                                echo '<span class="advertises"><i class="fa fa-close"></i> Ads Disabled</span>';
                            }

                            // GET CHILD CATEGORY
                            $childCats = getAllFrom("*", "categories", "where Parent = {$cat['ID']}", "", "ID", "ASC");
                            if (!empty($childCats)) {
                                echo "<h4 class='child-head'>Child Categories</h4>";
                                echo "<ul class='list-unstyled child-cats'>";
                                foreach ($childCats as $child) {
                                    echo "<li class='child-link'>
                                                <a href='categories.php?do=Edit&catid=" . $child['ID'] . "'>" . $child['Name'] . "</a>
                                                <a href='categories.php?do=Delete&catid=" . $child['ID'] . "' class='show-delete confirm'>Delete</a>
                                        </li>";
                                }
                                echo "</ul>";
                            }
                            echo "</div>";
                            echo "</div>";
                            echo "<hr>";
                        }
                        ?>
                    </div>
                </div>
                <a class=" add-category btn btn-primary" href="categories.php?do=Add"><i class="fa fa-plus"></i>Add New Category</a>
            </div>
            <?php } else{
                echo '<div class="container">';
                echo '<div class="nice-message">there\'s no category to show</div>';
                echo ' <a href="categories.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Category</a>';
                echo '</div>';
            } ?>
<?php
        } elseif($do =='Add'){ ?>

            <h1 class="text-center">Add New Category</h1>

            <div class="container">
                <form class="form-horizontal" action="?do=Insert" method="POST">
                    <!--Name-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="name" class="form-control" autocomplete="off" required="required"
                                   placeholder="Name OF The Category">
                        </div>
                    </div>
                    <!--Description-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="description" class="form-control"
                                   placeholder="Describe The Category">
                        </div>
                    </div>
                    <!--Ordering-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Ordering</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="ordering" class="form-control"
                                   placeholder="Number To Arrange The Category">
                        </div>
                    </div>
                    <!--category type-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Parent ?</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="parent">
                                <option value="0">None</option>
                                <?php
                                $allCats = getAllFrom("*","categories","where Parent = 0","","ID","ASC");
                                foreach($allCats as $cat){
                                    echo "<option value='".$cat['ID']."'>".$cat['Name']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!--Visibility-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Visible</label>
                        <div class="col-sm-10 col-md-6">
                           <div>
                               <input id="vis-yes" type="radio" name="visibility" value="0" checked>
                               <label for="vis-yes">Yes</label>
                           </div>
                            <div>
                                <input id="vis-no" type="radio" name="visibility" value="1">
                                <label for="vis-no">No</label>
                            </div>
                        </div>
                    </div>
                    <!--Commenting-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Allow Commenting</label>
                        <div class="col-sm-10 col-md-6">
                            <div>
                                <input id="com-yes" type="radio" name="commenting" value="0" checked>
                                <label for="com-yes">Yes</label>
                            </div>
                            <div>
                                <input id="com-no" type="radio" name="commenting" value="1">
                                <label for="com-no">No</label>
                            </div>
                        </div>
                    </div>
                    <!--Ads-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Allow Ads</label>
                        <div class="col-sm-10 col-md-6">
                            <div>
                                <input id="ads-yes" type="radio" name="ads" value="0" checked>
                                <label for="ads-yes">Yes</label>
                            </div>
                            <div>
                                <input id="ads-no" type="radio" name="ads" value="1">
                                <label for="ads-no">No</label>
                            </div>
                        </div>
                    </div>
                    <!--                submit-->
                    <div class="form-group form-group-lg">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" value="Add Category" class="btn btn-primary btn-lg">
                        </div>
                    </div>
                </form>
            </div>

            <?php
        }elseif($do =='Insert'){

            if($_SERVER['REQUEST_METHOD'] == 'POST'){

                echo "<h1 class='text-center'>Insert Category</h1>";
                echo "<div class='container'>";


                $name       = $_POST['name'];
                $desc       = $_POST['description'];
                $parent     = $_POST['parent'];
                $order      = $_POST['ordering'];
                $visible    = $_POST['visibility'];
                $comment    = $_POST['commenting'];
                $ads        = $_POST['ads'];

                //check if on error

                    // check if category exist in database

                    $check = checkItem("Name", "categories", $name);

                    if ($check == 1){

                        $theMsg = '<div class="alert alert-danger"> Sorry This Category Is Exist</div>';

                        redirectHome($theMsg,'back');
                    } else {


                        // insert category info in database

                        $stmt = $con->prepare("
                 INSERT INTO
                 categories(Name, Description,Parent,Ordering,Visibility, Allow_Comment,Allow_Ads)
                 VALUES(:zname,:zdesc,:zparent,:zorder,:zvisible, :zcomment, :zads)");

                        $stmt->execute(array(
                            'zname'     => $name,
                            'zdesc'     => $desc,
                            'zparent'   => $parent,
                            'zorder'    => $order,
                            'zvisible'  => $visible,
                            'zcomment'  => $comment,
                            'zads'      => $ads
                        ));
                        $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . 'record Inserted</div>';

                        redirectHome($theMsg, 'back');
                    }

                //update the database

            } else {
                echo "<div class='container'>";
                $theMsg = '<div class="alert alert-danger">sorry you cant browse this page</div>';
                redirectHome($theMsg, 'back');
                echo "</div>";
            }
            echo "</div>";

        }elseif($do =='Edit'){
            // check if get request catid is numeric $ get its integer value

            $catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0 ;
            // select all data depend on this id
            $stmt = $con->prepare("SELECT * FROM categories WHERE ID = ?");
            // execute query
            $stmt->execute(array($catid));
            // fetch the data
            $cat = $stmt->Fetch();
            // the row count
            $count = $stmt->rowCount();
            // if
            if( $count > 0) { ?>
                <h1 class="text-center">Edit Category</h1>

                <div class="container">
                    <form class="form-horizontal" action="?do=Update" method="POST">
                        <input type="hidden" name="catid" value="<?= $catid?>">
                        <!--Name-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-10 col-md-6">
                                <input type="text" name="name" class="form-control" required="required" value="<?php echo $cat['Name']?>">
                            </div>
                        </div>
                        <!--Description-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Description</label>
                            <div class="col-sm-10 col-md-6">
                                <input type="text" name="description" class="form-control" value= "<?php echo $cat['Description']?>">
                            </div>
                        </div>
                        <!--Ordering-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Ordering</label>
                            <div class="col-sm-10 col-md-6">
                                <input type="text" name="ordering" class="form-control"
                                       value= "<?php echo $cat['Ordering']?>">
                            </div>
                        </div>
                        <!--category type-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Parent ?</label>
                            <div class="col-sm-10 col-md-6">
                                <select name="parent">
                                    <option value="0">None</option>
                                    <?php
                                    $allCats = getAllFrom("*","categories","where Parent = 0","","ID","ASC");
                                    foreach($allCats as $child){
                                        echo "<option value='".$child['ID']."'";
                                        if ($cat['Parent'] == $child['ID']){echo 'selected';}
                                        echo ">".$child['Name']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!--Visibility-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Visible</label>
                            <div class="col-sm-10 col-md-6">
                                <div>
                                    <input id="vis-yes" type="radio" name="visibility" value="0" <?php if ($cat['Visibility'] == 0){ echo 'checked';}?>>
                                    <label for="vis-yes">Yes</label>
                                </div>
                                <div>
                                    <input id="vis-no" type="radio" name="visibility" value="1" <?php if ($cat['Visibility'] == 1){ echo 'checked';}?>>
                                    <label for="vis-no">No</label>
                                </div>
                            </div>
                        </div>
                        <!--Commenting-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Allow Commenting</label>
                            <div class="col-sm-10 col-md-6">
                                <div>
                                    <input id="com-yes" type="radio" name="commenting" value="0" <?php if ($cat['Allow_Comment'] == 0){ echo 'checked';}?>>
                                    <label for="com-yes">Yes</label>
                                </div>
                                <div>
                                    <input id="com-no" type="radio" name="commenting" value="1" <?php if ($cat['Allow_Comment'] == 1){ echo 'checked';}?>>
                                    <label for="com-no">No</label>
                                </div>
                            </div>
                        </div>
                        <!--Ads-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Allow Ads</label>
                            <div class="col-sm-10 col-md-6">
                                <div>
                                    <input id="ads-yes" type="radio" name="ads" value="0" <?php if ($cat['Allow_Ads'] == 0){ echo 'checked';}?>>
                                    <label for="ads-yes">Yes</label>
                                </div>
                                <div>
                                    <input id="ads-no" type="radio" name="ads" value="1" <?php if ($cat['Allow_Ads'] == 1){ echo 'checked';}?>>
                                    <label for="ads-no">No</label>
                                </div>
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

        }elseif($do =='Update'){

            echo "<h1 class='text-center'>Update Category</h1>";
            echo "<div class='container'>";

            if($_SERVER['REQUEST_METHOD'] == 'POST'){

                $id         = $_POST['catid'];
                $name       = $_POST['name'];
                $desc       = $_POST['description'];
                $order      = $_POST['ordering'];
                $parent      = $_POST['parent'];

                $visible    = $_POST['visibility'];
                $comment    = $_POST['commenting'];
                $ads        = $_POST['ads'];

                //check if on error


                    $stmt =$con->prepare("UPDATE
                                                    categories
                                                    SET
                                                    Name = ?,
                                                   Description = ?, 
                                                   Ordering = ?,
                                                   Parent = ?, 
                                                   Visibility = ?, 
                                                   Allow_comment = ?,
                                                   Allow_Ads = ? 
                                                   WHERE ID = ?");
                    $stmt->execute(array($name, $desc, $order, $parent, $visible, $comment, $ads, $id));

                    $theMsg = "<div class='alert alert-success'>".$stmt->rowCount() . ' record update</div>';

                    redirectHome($theMsg, 'back');


            } else {

                $theMsg = '<div class="alert alert-danger">sorry you cant browse this page</div>';
                redirectHome($theMsg);

            }
            echo "</div>";

        }elseif($do =='Delete'){

            echo "<h1 class='text-center'>Delete Category</h1>";
            echo "<div class='container'>";

            // check if get request catid is numeric & get the integer value of it

            $catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0 ;

            // select all data depend on this id

            $check = checkItem('ID', 'categories', $catid);

            //if there's such id show the form

            if( $check > 0) {

                $stmt =$con->prepare("DELETE FROM categories WHERE ID = :zid");

                $stmt->bindParam(":zid", $catid);

                $stmt->execute();

                $theMsg = "<div class='alert alert-success'>".$stmt->rowCount() . 'record Deleted</div>';

                redirectHome($theMsg, 'back');
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
