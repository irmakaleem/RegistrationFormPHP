<?php

// Require the connection file
require './connection.php';

// Initialize variables
$name = $city = $pincode = $email = $password = '';
$errors = [];

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $name = $_POST['name'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate the data
    if (empty($name)) {
        $errors[] = "Full Name is required.";
    }
    if (!preg_match("/^[0-9]{6}$/", $pincode)) {
        $errors[] = "Pincode must be exactly 6 digits.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // If there are no errors, insert the data into the database
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("INSERT INTO user (name, city, pincode, email, password) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $name, $city, $pincode, $email, $hashed_password);
            $stmt->execute();
            $success_message = "Registration successful!";
        } catch (Exception $e) {
            $errors[] = "Error: ". $e->getMessage();
        }
    }
}

// Close the database connection
$conn->close();


include('../Views/register.php')

?>