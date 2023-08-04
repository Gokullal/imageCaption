<?php
// Create connection
$conn = new mysqli("localhost", "root", "", "imageCaptioning");
if (!$conn) {
    die('Could not connect: ' . mysqli_connect_error());
}
