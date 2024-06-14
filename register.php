<?php require('./header.php');?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Account</title>
    <link rel="stylesheet" href="./assets/css/regform.css">
</head>
<body>
    <form action="" method="POST" id="regform">
        <span>RIDEREADY RENTALS</span><br>
        <h1>Register</h1>
        <p><label for="name">Name:</label>
        <input type="text" name="name" id="name" required></p>

        <p><label for="dob">Birth Date:</label>
        <input type="date" name="dob" id="dob" required></p>

        <p><label for="Gender">Gender:</label>
        <input type="radio" name="Gender" id="Gender" value="Male">Male
        <input type="radio" name="Gender" id="Gender" value="Female">Female </p>

        <p><label for="contact">Contact:</label>
        <input type="number" name="contact" id="contact" required></p>
        <p><label for="citizen">Citizenship No:</label>
        <input type="number" name="citizen" id="citizen" required></p>
        <p><label for="license">License No:</label>
        <input type="number" name="license" id="license" required></p>
        <p><label for="password">Password:</label>
        <input type="password" name="password" id="password" required ></p><br>
        <button>Register</button>
    </form>
</body>
</html>