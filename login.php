<?php
    session_start();
    $pageTitle ='log In';
    if(isset($_SESSION['user'])){
        header('location: index.php');
    }
    include 'init.php';
    // check if user coming from http post request
    if($_SERVER['REQUEST_METHOD']=='POST'){
        if(isset($_POST['login'])) {
            $user = $_POST['username'];
            $pass = $_POST['password'];
            $hashedPass = sha1($pass);

            // check if the user exist in database

            $stmt = $con->prepare("SELECT
                                                 UserID,Username, Password 
                                             FROM 
                                                  users 
                                             WHERE 
                                                  Username = ? 
                                             AND 
                                                  Password = ? 
                                             ");
            $stmt->execute(array($user, $hashedPass));
            $get = $stmt->fetch();
            $count = $stmt->rowCount();
            // if count > 0
            if ($count > 0) {
                $_SESSION['user'] = $user;

                $_SESSION['uid'] = $get['UserID'];

                header('location: index.php');

                exit();
            }
        } else {
            $formErrors = array();

            $username   = $_POST['username'];
            $password   = $_POST['password'];
            $password2  = $_POST['password2'];
            $email      = $_POST['email'];
            if(isset($username)){
                $filterdUser = Filter_var($username, FILTER_SANITIZE_STRING);

                if(strlen($filterdUser) < 4){
                    $formErrors[] = 'USER NAME MUST BE LARGER THAN 4 CHARACTER';
                }
            }
            if(isset($password) && isset($password2)){

                if(empty($password)){
                    $formErrors[] = 'SORRY PASSWORD CANT BE EMPTY';
                }

                if(sha1($password) !== sha1($password2)){
                    $formErrors[]= 'SORRY PASSWORD IS NOT MATCH';
                }
            }
            if(isset($email)){
                $filterdEmail = Filter_var($email, FILTER_SANITIZE_EMAIL);
                if(filter_var($filterdEmail, FILTER_VALIDATE_EMAIL) != true){
                    $formErrors[] = 'THIS EMAIL IS NOT VALID';
                }
            }

            if(empty($formErrors)){

                // check if user exist in database

                $check = checkItem("Username", "users", $username);

                if ($check == 1){

                    $formErrors[] = 'SORRY THIS USER IS EXISTS';

                } else {


                    // insert user info in database

                    $stmt = $con->prepare("
                 INSERT INTO
                 users(Username, Password,Email, RegStatus,Date)
                 VALUES(:zuser,:zpass,:zmail, 0, now())");

                    $stmt->execute(array(
                        'zuser' => $username,
                        'zpass' => sha1($password),
                        'zmail' => $email
                    ));
                    $succesMsg = 'Congrats You Are Now Registered User';
                }
            }
        }
    }
?>
    <div class="container login-page">
        <h1 class="text-center">
            <span class="selected" data-class="login">LogIn</span> |
            <span data-class="signup">SignUp</span>
        </h1>
<!--        login form-->
        <form class="login" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
            <div class="input-container">
                <input
                    class="form-control" type="text"
                    name="username" autocomplete="off"
                    placeholder="TYPE USER NAME" required="required">
            </div>
            <div class="input-container">
                <input
                    class="form-control" type="password"
                    name="password" autocomplete="new-password"
                    placeholder="TYPE PASSWORD" required="required">
                <input class="btn btn-primary btn-block" name="login" type="submit" value="Log In">
            </div>
        </form>
<!--        sign up form-->
        <form class="signup" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
            <div class="input-container">
                <input
                    pattern=".{4,}"
                    title="User Name Must Be Between 4 Chars"
                    class="form-control" type="text"
                    name="username" autocomplete="off"
                    placeholder="TYPE USER NAME"
                    required>
            </div>
            <div class="input-container">
                <input
                    minlength="4"
                    class="form-control" type="password"
                    name="password" autocomplete="new-password"
                    placeholder="TYPE PASSWORD"
                    required>
            </div>
            <div class="input-container">
                <input
                    minlength="4"
                    class="form-control" type="password"
                    name="password2" autocomplete="new-password"
                    placeholder="TYPE PASSWORD AGAIN"
                    required>
            </div>
            <div class="input-container">
                <input
                class="form-control" type="email"
                name="email"
                placeholder="TYPE A VALID EMAIL">
            </div>
            <input class="btn btn-success btn-block" name="signup" type="submit" value="Sign UP">
        </form>
        <div class="text-center the-errors">
        <?php
        if(!empty($formErrors)){
            foreach ($formErrors as $error){
                echo '<div class="msg error">'. $error .'</div>';
            }
        }
        if(isset($succesMsg)){
            echo '<div class="msg success">'. $succesMsg .'</div>';
        }
        ?>
        </div>
    </div>
<?php
    include $tpl.'footer.php';
?>
