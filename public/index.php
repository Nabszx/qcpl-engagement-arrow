<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>
    <?php 
        require_once("../app/includes/header.php");
        if(!isset($_SESSION['user_id'])){
            $error_message = '';
            $username_value = '';

            if(isset($_POST['login'])) {
                $username = mysqli_real_escape_string($conn, $_POST['username']);
                $password = mysqli_real_escape_string($conn, $_POST['password']);
                
                $query = "SELECT users.*, user_role.user_role_id FROM users INNER JOIN user_role USING (user_role_id) WHERE username = '$username';";
                $result = mysqli_query($conn, $query);
                $count = mysqli_num_rows($result);

                if($count === 1) {
                    while($row = mysqli_fetch_assoc($result)){
                        $username_value = $row['username'];
                        $error_message = '';

                        $hashed_password = $row['password'];
                        $verify_password = password_verify($password, $hashed_password);

                        if($verify_password === false){
                            $error_message = ' Invalid password';
                        }else{
                            $is_active = $row['is_active'];
                            if($is_active == 0){
                                $error_message = 'Your account is deactivated.';
                                $username_success = '';
                            }else{
                                $_SESSION['user_id'] = $row['user_id'];
                                $_SESSION['username'] = $row['username'];
                                $_SESSION['user_role_id'] = $row['user_role_id'];

                                $query = "UPDATE users SET last_login = NOW() WHERE user_id = '" .$_SESSION['user_id']. "' AND username = '$username_value';";
                                $result = mysqli_query($conn, $query);
                                echo '<script>
                                    $(document).ready(function(){
                                        swal({
                                            title: "", 
                                            text: "",
                                            icon: "./assets/images/loader/loader.gif",
                                            buttons: false,      
                                            closeOnClickOutside: false,
                                            timer: 3000,
                                        }).then(function(){
                                            window.location.href = "portal.php";
                                        });
                                    });
                                </script>';
                                exit(); // Terminate script execution after echoing JavaScript
                            }
                        }
                    }
                }else{
                    $error_message = 'Invalid username.';
                }
            }
        }else{
            // Redirect to portal.php if user is already logged in
            header("location: portal.php");
            exit(); // Terminate script execution after redirection
        }
    ?>
    <section id="swup" class="transtion-fade">
        <div class="logo">
            <img src="assets/images/qclogo.jpg" alt="">
            <div class="title">
                <p>Quezon City Public Library</p>
                <p>Quezon City Government</p>
            </div>
            <img src="assets/images/qcplLogo.png" alt="">
        </div>
        <div class="container">
            <div class="promotext">
                <h3>E-Government</h3>
                <h3>Services</h3>
                <p>Offers free assistance to all individuals <br>
                 who wish to access the online government services <br>
                 offered by different department agencies of the Philippines.</p>
            </div>
            <div class="login">
                <h4>Welcome Back!</h4>
                <p>Login to continue</p>
                <div class="logbox">
                    <form id="loginForm" action="" method="post">
                        <span class="text-danger"><?= $error_message; ?></span>
                        <div class="inputbox">
                            <input type="text" name="username" id="username" value="<?= $username_value ?>" required>
                            <span>Username</span>
                        </div>
                        <div class="inputbox">
                            <input type="password" name="password" id="pass" required>
                            <span>Password</span>
                        </div>
                        <button type="submit" name="login" class="button" style="text-decoration: none;">Log in</button>
                    </form>
                </div>
            </div>
            <div class="bcgimage"></div>
        </div>
    </section>
    <script src="https://unpkg.com/swup@4"></script>
    <script src="script.js"></script>
</body>
</html>
