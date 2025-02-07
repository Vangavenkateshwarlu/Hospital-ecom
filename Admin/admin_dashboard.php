


<?php
session_start();
include('db.php');  // Include the database connection

// Dummy admin credentials (you can retrieve these from the database)
$admin_user = 'admin';
$admin_pass = 'password';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
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

// Handle Accept, Reject, and Complete actions
if (isset($_POST['action'])) {
    $appointment_id = $_POST['appointment_id'];
    $action = $_POST['action'];

    // Update the appointment status based on the action
    if ($action === 'accept') {
        $status = 'Accepted';
    } elseif ($action === 'reject') {
        $status = 'Rejected';
    } elseif ($action === 'completed') {
        $status = 'Completed';
    }

    $update_sql = "UPDATE appointments SET status = '$status' WHERE id = $appointment_id";
    if ($conn->query($update_sql) === TRUE) {
        // Redirect to refresh the page
        header("Location: " . $_SERVER['PHP_SELF']);
        exit; // Make sure no further code is executed after the redirect
    } else {
        echo "Error updating status: " . $conn->error;
    }
}

// Fetch appointments from the database
$sql = "SELECT id, name, email, phone, appointment_date, reason, gender, status FROM appointments ORDER BY appointment_date DESC";
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
<header>
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="../User/index.html">logout</a>  
        </nav>
</header>
   
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
            <th>Status</th>
            <th>Action</th>
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
                        <td>{$row['status']}</td>
                        <td>
                            <form action='' method='POST' style='
                                background-color: transparent;
                                color: white;
                                padding:0px;
                                border-radius:5px
                                box-shadow: 0 0 10px white);
                                width:fixed;
                                
                            '>
                                <input type='hidden' name='appointment_id' value='{$row['id']}'>
                                <button style='background-color: rgb(47, 206, 47);width:90px;
                                margin-left:40px' type='submit' name='action' value='accept'>Accept</button>
                            
                                <input type='hidden' name='appointment_id' value='{$row['id']}'>
                                <button style='background-color: #df3636;width:90px;' type='submit' name='action' value='reject'>Reject</button>
                            
                                <input type='hidden' name='appointment_id' value='{$row['id']}'>
                                <button style='background-color: #ffa500;
                                width:90px;
                            ' type='submit' name='action' value='completed'>Completed</button>
                            </form>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No appointments found.</td></tr>";
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
