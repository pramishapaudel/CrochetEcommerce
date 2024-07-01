<?php
require('./connection.php');
// include('./header.php');
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = $_SESSION['userID'];

    // File upload configuration
    $targetDir = "../uploads/";
    $citizenshipFile = $targetDir . basename($_FILES["citizenship"]["name"]);
    $licenseFile = $targetDir . basename($_FILES["license"]["name"]);
    $uploadOk = 1;
    $imageFileTypeCitizenship = strtolower(pathinfo($citizenshipFile, PATHINFO_EXTENSION));
    $imageFileTypeLicense = strtolower(pathinfo($licenseFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $checkCitizenship = getimagesize($_FILES["citizenship"]["tmp_name"]);
    $checkLicense = getimagesize($_FILES["license"]["tmp_name"]);
    if($checkCitizenship !== false && $checkLicense !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["citizenship"]["size"] > 500000 || $_FILES["license"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileTypeCitizenship != "jpg" && $imageFileTypeCitizenship != "png" && $imageFileTypeCitizenship != "jpeg"
    && $imageFileTypeCitizenship != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if($imageFileTypeLicense != "jpg" && $imageFileTypeLicense != "png" && $imageFileTypeLicense != "jpeg"
    && $imageFileTypeLicense != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // If everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["citizenship"]["tmp_name"], $citizenshipFile) && move_uploaded_file($_FILES["license"]["tmp_name"], $licenseFile)) {
            // Insert file name into database
            $stmt = $conn->prepare("UPDATE users SET CitizenshipImg = ?, LicenseImg = ? WHERE userID = ?");
            $stmt->bind_param('ssi', $citizenshipFile, $licenseFile, $userID);

            if ($stmt->execute()) {
                echo "The files have been uploaded and records updated.";
                header('Location: ../index.php');
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Verification</title>
    <style>
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form div {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="file"] {
            display: block;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Verification</h2>
        <form action="verify_acc.php" method="POST" enctype="multipart/form-data">
            <div>
                <label for="citizenship">Upload Citizenship Document:</label>
                <input type="file" name="citizenship" id="citizenship" required>
            </div>
            <div>
                <label for="license">Upload License Document:</label>
                <input type="file" name="license" id="license" required>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
