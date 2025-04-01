<?php
   $conn = new mysqli('localhost', 'root', '', 'yarn_joy', 3307); 

    if($conn->connect_error) {
        die("Connetion Failed: " . $conn->connect_error);
    }
