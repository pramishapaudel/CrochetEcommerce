<?php 
    require('./connection.php');
    require('./header.php');
    if($_SERVER['REQUEST_METHOD']=='POST'){
        $phone = $_POST['phone'];
        if(isset($phone) && isset($_SESSION['Password'])){
            $password=$_POST['password'];
            if (password_verify($password, $_SESSION['Password'])){
                $_SESSION['Username']=$_SESSION['tempUname'];
                header('Location: ./index.php');
            }else{
                echo "<script>alert('Incorrect Password');</script>";
            }
        }else{
            $sql="SELECT * FROM users WHERE Contact = $phone";
            $result = $conn->query($sql);
            if($result->num_rows==1){
                $row=$result->fetch_assoc();
                $_SESSION['tempUname']=$row['Name'];
                $_SESSION['Phone']=$row['Contact'];
                $_SESSION['Password']=$row['Password'];
            }else{
                echo "<script>
                if(confirm('Email not registered! Wanna Register?')==true){
                    window.location.replace('register.php');
                };</script>";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to RideReady Rentals</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="loginform">
        <h1>Welcome Riders!</h1>
        <h2>Log in!</h2>
        <label for="phone">Phone:</label>
        <input type="number" name="phone" id="phone" value="<?php echo @$_SESSION['Phone'];?>" maxlength="10" minlength="10"><br><br>
        <?php 
            if(isset($_SESSION['Password'])){
                echo '<label for="password">Password:</label>
                <input type="password" name="password" id="password" maxlength="255" minlength="8"><br><br>';
            }
        ?>
        <button id="login">Login</button>
    </form>
</body>
</html>