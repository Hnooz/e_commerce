<?php
ob_start();
session_start();
$noNavbar ='';
$pageTitle ='login';
if(isset($_SESSION['Username'])){
    header('location: dashboard.php');
}
include "init.php";

    // check if user coming from http post request
    if($_SERVER['REQUEST_METHOD']=='POST'){
        $username = $_POST['user'];
        $password = $_POST['pass'];
        $hashedPass = sha1($password);
    // check if the user exist in database
        $stmt = $con->prepare("SELECT
                                              UserID, Username, Password 
                                         FROM 
                                              users 
                                         WHERE 
                                              Username = ? 
                                         AND 
                                              Password = ? 
                                         AND 
                                              GroupID = 1
                                         LIMIT 1");
        $stmt->execute(array($username,$hashedPass));
        $row = $stmt->Fetch();
        $count = $stmt->rowCount();
        // if count > 0
        if($count > 0){
           $_SESSION['Username'] = $username;
           $_SESSION['ID'] = $row['UserID'];
           header('location: dashboard.php');
           exit();
        }
    }
?>

 <form class="login" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
     <h4 class="text-center">Admin Login</h4>
     <input class="form-control" type="text" name="user" placeholder="Username" autocomplete="off">
     <input class="form-control" type="password" name="pass" placeholder="Password" autocomplete="new-password">
     <input class="btn btn-primary btn-block" type="submit" value="login">
 </form>
<?php include $tpl . 'footer.php';
ob_end_flush();
?>
