<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reviews_db";

$conn = new mysqlI($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$table = "CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    review TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($table);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $review = htmlspecialchars($_POST['review']);

    if (empty($name) || empty($email) || empty($review)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("INSERT INTO reviews (name, email, review) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $review);

        if ($stmt->execute()) {
            $success = "Review submitted successfully.";
        } else {
            $error = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="Stylesheet" href="style.css">
    <title>Review Submission</title>
</head>
<body>
    <div class="form-container">
        <h2>Submit Review</h2>
        <?php if (isset($error)): ?>
            <div class="message"> <?php echo $error; ?> </div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="message success"> <?php echo $success; ?> </div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <textarea name="review" rows="5" placeholder="Review" required></textarea>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
