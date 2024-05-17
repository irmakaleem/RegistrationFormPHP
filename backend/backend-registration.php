<?php
require('../Connection/connection.php');
// Initialize an empty array to store errors
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted data
$name = $_POST['fname'] . " " . $_POST['lname'];
$email = $_POST['email'];
$password = $_POST['password'];
$city = $_POST['city']; // assuming this is also posted

// Validate the data
if (empty($name)) {
    $errors[] = "Full Name is required.";
}
if (empty($email)) {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}
if (empty($password)) {
    $errors[] = "Password is required.";
}

// If there are no errors, insert the data into the database
if (empty($errors)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("INSERT INTO users (name, city, email, password) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $name, $city, $email, $hashed_password);
        $stmt->execute();
        $_SESSION['success_msg'] = "Registration successful!";
        $_SESSION['username'] = $name;
        $_SESSION['email'] = $email;
    } catch (Exception $e) {
        $errors[] = "Error: ". $e->getMessage();
    }
}
}

// Close the database connection
$conn->close();
