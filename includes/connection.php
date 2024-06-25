<?php
    $conn = new mysqli("localhost","root","","RideReady");
    if($conn->connect_error) {
        die("Connetion Failed: " . $conn->connect_error);
    }
