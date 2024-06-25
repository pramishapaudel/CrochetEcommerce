<?php
    require('../../connection.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $vehicleName = $_POST['vehicleName'];
        $vehicleDes = $_POST['vehicleDes'];
        $price = $_POST['price'];
        $vehicleImg = $_FILES['vehicleImg'];

        // Directory where images will be uploaded
        $targetDir = "../../img";
        $targetFile = "img/" . basename($vehicleImg['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if image file is an actual image or fake image
        $check = getimagesize($vehicleImg['tmp_name']);
        if ($check === false) {
            echo "File is not an image.";
            exit;
        }

        // Check if file already exists
        if (file_exists($targetFile)) {
            echo "Sorry, file already exists.";
            exit;
        }

        // Check file size (5MB max)
        if ($vehicleImg['size'] > 5000000) {
            echo "Sorry, your file is too large.";
            exit;
        }

        // Allow certain file formats
        $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedFormats)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            exit;
        }

        // Try to upload file
        if (!move_uploaded_file($vehicleImg['tmp_name'], "../../".$targetFile)) {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }

        // Insert product data into database
        $stmt = $conn->prepare("INSERT INTO products (vehicleName, vehicleDes, price, vehicleImg) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $vehicleName, $vehicleDes, $price, $targetFile);

        if ($stmt->execute()) {
            echo "Product added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
?>
