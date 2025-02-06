<?php
session_start();
include('db.php');  // Include the database connection

// Dummy admin credentials (you can retrieve these from the database)
$admin_user = 'admin';
$admin_pass = 'password';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check login credentials
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    if ($input_username === $admin_user && $input_password === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        echo "Invalid credentials!";
        exit;
    }
}

if (!isset($_SESSION['admin_logged_in'])) {
    echo "Please log in first.";
    exit;
}

// Fetch appointments from the database
$sql = "SELECT id, name, email, phone, appointment_date, reason, gender FROM appointments ORDER BY appointment_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Admin Dashboard</h1>
    <h2>User Appointments</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Appointment ID</th>
                <th>User Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Appointment Date</th>
                <th>Reason</th>
                <th>Gender</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['phone']}</td>
                            <td>{$row['appointment_date']}</td>
                            <td>{$row['reason']}</td>
                            <td>{$row['gender']}</td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No appointments found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
