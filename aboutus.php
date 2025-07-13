<?php
require('./includes/header.php');
require('includes/connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Yarn-Joy</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <!-- Assuming your header.php includes the navigation -->
        </nav>
    </header>

    <div class="about-container">
        <div class="content">
            <h1>Welcome to <span>Yarn-Joy</span></h1>
            <p>
                At <span>Yarn-Joy</span>, we weave creativity and passion into every stitch. Our platform is dedicated to fulfilling your love for crochet with a delightful range of handmade items, including keychains, bags, flowers, and so much more. Dive into the world of yarn and let your joy unravel with every unique piece!
            </p>
        </div>

        <div class="content">
            <h2>Our Mission</h2>
            <p>
                We aim to bring the timeless art of crochet to life, offering you beautifully crafted products that spark joy and inspiration. Whether you're a crochet enthusiast or simply looking for a special gift, <span>Yarn-Joy</span> is here to make every moment colorful and cozy.
            </p>
        </div>

        <div class="contact">
            <h2>Get in Touch</h2>
            <p>
                Have questions or want to collaborate? We’d love to hear from you! Reach out to us through:
            </p>
            <p>
                <strong>Email:</strong> 
                <a href="mailto:ppramisha16@gmail.com">ppramisha16@gmail.com</a> 
        <br>
                <strong>Phone:</strong> 
                <a href="tel:+977 9828714530">9828714530</a> 
            </p>
            <button onclick="window.location.href='mailto:ppramisha16@gmail.com';">
                Contact Us Now
            </button>
        </div>
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-links">
                <a href="#">Home</a>
                <a href="#">Shop</a>
                <a href="#">About Us</a>
                <a href="#">Contact</a>
            </div>
            <p>&copy; 2025 E-Commerce Crochet</p>
        </div>
    </footer>
</body>
</html>