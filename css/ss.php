<?php
session_start();
include('db.php');  // Include the database connection

// Define database connection parameters
$servername = "localhost";
$username = "root"; // default username for XAMPP
$password = ""; // default password for XAMPP (empty)
$dbname = "appointments_db"; // the name of the database you created

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['number'];
    $appointment_date = $_POST['date'];
    $reason = $_POST['reason'];

    // Check if the exact combination of details already exists in the database
    $sql_check = "SELECT * FROM appointments WHERE name = ? AND email = ? AND phone = ? AND appointment_date = ? AND reason = ? AND gender = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("ssssss", $name, $email, $phone, $appointment_date, $reason, $gender);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Appointment already exists
        echo "<p style='
            background-color: red;
            color: white;
            padding:20px;
            margin-left: 450px;
            font-weight: 200;
            display: inline-block;
            border-radius:15px;
}
        '>Your appointment with the exact same details already exists. Please check your details and try again.</p>";
    } else {
        // No match found, insert the new appointment into the database
        $sql_insert = "INSERT INTO appointments (name, email, phone, appointment_date, reason, gender) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("ssssss", $name, $email, $phone, $appointment_date, $reason, $gender);

        if ($stmt->execute()) {
            echo "<p style=' background-color: green;
            color: white;
            padding:20px;
            margin-left: 550px;
            font-weight: 200;
            display: inline-block;
            border-radius:15px;'>Appointment request submitted successfully!</p>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
    }

    $stmt->close();
}

// Fetch and display existing appointments
$sql_select = "SELECT id, name, email, phone, appointment_date, reason, gender FROM appointments";
$result = $conn->query($sql_select);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmation</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Appointment Confirmation</h1>
        <nav>
        <a href="index.html">Home</a>
        <a href="appointment.html">Make appointment</a>
        </nav>
    </header>
    
    <section>
        <h2>Your appointment request has been received!</h2>
        <p>Thank you for scheduling an appointment. Our team will contact you shortly to confirm the details.</p>
    </section>

    <!-- New Section to Retrieve and Display Appointment Data -->
    <section>
        <h2>Existing Appointments:</h2>
        <?php
        if ($result->num_rows > 0) {
            echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Appointment Date</th><th>Reason</th><th>Gender</th></tr>";
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row["id"] . "</td>
                        <td>" . $row["name"] . "</td>
                        <td>" . $row["email"] . "</td>
                        <td>" . $row["phone"] . "</td>
                        <td>" . $row["appointment_date"] . "</td>
                        <td>" . $row["reason"] . "</td>
                        <td>" . $row["gender"] . "</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "No appointments found.";
        }

        // Close the connection
        $conn->close();
        ?>
    </section>
</body>
</html>


<!---------------------------------code 2 --------------------->


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

// Handle Accept and Reject actions
if (isset($_POST['action'])) {
    $appointment_id = $_POST['appointment_id'];
    $action = $_POST['action'];

    // Update the appointment status
    $status = $action === 'accept' ? 'Accepted' : 'Rejected';
    $update_sql = "UPDATE appointments SET status = '$status' WHERE id = $appointment_id";
    if ($conn->query($update_sql) === TRUE) {
        echo "Appointment status updated to $status.";
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
                                <form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' name='appointment_id' value='{$row['id']}'>
                                    <button type='submit' name='action' value='accept'>Accept</button>
                                </form>
                                <form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' name='appointment_id' value='{$row['id']}'>
                                    <button type='submit' name='action' value='reject'>Reject</button>
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

<!---code 4 -->
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

// Handle Accept and Reject actions
if (isset($_POST['action'])) {
    $appointment_id = $_POST['appointment_id'];
    $action = $_POST['action'];

    // Update the appointment status
    $status = $action === 'accept' ? 'Accepted' : 'Rejected';
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
                            <td >{$row['status']}</td>
                            <td>
                                <form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' name='appointment_id' value='{$row['id']}'>
                                    <button style='
                                    background-color: rgb(47, 206, 47);
                                    'type='submit' name='action' value='accept'>Accept</button>
                                </form>
                                <form action='' method='POST' style='display:inline'>
                                    <input type='hidden'  name='appointment_id' value='{$row['id']}'>
                                    <button style='
                                    background-color:  #df3636;'
                                     type='submit' class='ss' name='action' value='reject'>Reject</button>
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