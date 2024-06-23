<?php 
    require('./connection.php');
    require('./header.php');
    if($_SERVER['REQUEST_METHOD']=='POST'){
        $phone = $_POST['phone'];
        //check passwords
        if(isset($phone) && isset($_SESSION['Password'])){
            $password=$_POST['password'];
            if (password_verify($password, $_SESSION['Password'])){
                $_SESSION['Username']=$_SESSION['tempUname'];
                header('Location: ./index.php');
            }else{
                unset($_SESSION['Password']);
                echo "<script>alert('Incorrect Password');</script>";
            }
        }else if(isset($phone) && isset($_SESSION['admPassword'])){
            $password=$_POST['password'];
            if($password == $_SESSION['admPassword']){
                $_SESSION['admUsername']=$_SESSION['tempadmUname'];
                header('Location: ./admin/index.php');
            }else{
                unset($_SESSION['admPassword']);
                echo "<script>alert('Incorrect Password');</script>";
            }
        }else{
            //check if number available in users table
            $sql="SELECT * FROM users WHERE Contact = $phone";
            $result = $conn->query($sql);
            if($result->num_rows==1){
                $row=$result->fetch_assoc();
                $_SESSION['tempUname']=$row['Name'];
                $_SESSION['Phone']=$row['Contact'];
                $_SESSION['Password']=$row['Password'];
                $_SESSION['userID']=$row['UserID'];
            }else{
                //check if number exists in admins table
                $sql1="SELECT * FROM admins WHERE adminNum = $phone";
                $result1 = $conn->query($sql1);
                if($result1->num_rows==1){
                    $row1=$result1->fetch_assoc();
                    $_SESSION['tempadmUname']=$row1['adminName'];
                    $_SESSION['admPhone']=$row1['adminNum'];
                    $_SESSION['admPassword']=$row1['password'];
                    $_SESSION['adminID']=$row1['adminID'];
                }else{
                    //if not available nowhere ask for register
                    echo "<script>
                    if(confirm('Email not registered! Wanna Register?')==true){
                        window.location.replace('register.php');
                    };</script>";
                }
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
        <input type="number" name="phone" id="phone" value="<?php if(isset($_SESSION['Phone'])){ echo @$_SESSION['Phone'];} else{echo @$_SESSION['admPhone'];}?>" maxlength="10" minlength="10"><br><br>
        <?php 
            if(isset($_SESSION['Password']) || isset($_SESSION['admPassword'])){
                echo '<label for="password">Password:</label>
                <input type="password" name="password" id="password" maxlength="255" minlength="8"><br><br>';
            }
        ?>
        <button id="login">Login</button>
    </form>
</body>
</html>