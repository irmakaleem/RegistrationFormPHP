<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registeration";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}
$name = $city = $pincode = $email = $password = "";
$errors = array();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$name = $_POST['name'];
$city = $_POST['city'];
$pincode = $_POST['pincode'];
$email = $_POST['email'];
$password = $_POST['password'];
if (empty($name)) {
$errors[] = "Full Name is required.";
}
if (!preg_match("/^[0-9]{6}$/", $pincode)) {
$errors[] = "Pincode must be exactly 6 digits.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
$errors[] = "Invalid email format.";
}
if (empty($errors)) {
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO user (name, city, pincode, email, password) VALUES (?, ?, ?, ?, ?)");
if ($stmt === false) {
$errors[] = "Error preparing the statement: " . $conn->error;
} else {
if (!$stmt->bind_param("sssss", $name, $city, $pincode, $email, $hashed_password)) {
$errors[] = "Error binding parameters: " . $stmt->error;
} else {
if ($stmt->execute()) {
$success_message = "Registration successful!";
} else {
$errors[] = "Error: " . $stmt->error;
}
}
$stmt->close();
}
}
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register User</title>
</head>
<body>
<h2>Register User</h2>
<?php
if (!empty($errors)) {
echo "<h3>Errors:</h3>";
foreach ($errors as $error) {
echo "<p>$error</p>";
}
}
if (isset($success_message)) {
echo "<h3>$success_message</h3>";
}
?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
<label for="name">Full Name:</label>
<input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
<br><br>
<label for="city">City:</label>
<input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>" required>
<br><br>
<label for="pincode">Pincode:</label>
<input type="text" id="pincode" name="pincode" pattern="[0-9]{6}" title="Pincode must be 6 digits" value="<?php echo
htmlspecialchars($pincode); ?>" required>
<br><br>
<label for="email">Email:</label>
<input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
<br><br>
<label for="password">Password:</label>
<input type="password" id="password" name="password" required>
<br><br>
<input type="submit" value="Register">
</form>
</body>
</html>