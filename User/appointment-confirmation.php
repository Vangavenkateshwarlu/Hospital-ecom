

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
        '>Your appointment with the exact same details already exists. Please check your details and try again.</p>";
    } else {
        // No match found, insert the new appointment into the database
        $sql_insert = "INSERT INTO appointments (name, email, phone, appointment_date, reason, gender, status) 
                       VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
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
$sql_select = "SELECT id, name, email, phone, appointment_date, reason, gender, status FROM appointments";
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
            echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Appointment Date</th><th>Reason</th><th>Gender</th><th>Status</th></tr>";
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row["id"] . "</td>
                        <td>" . $row["name"] . "</td>
                        <td>" . $row["email"] . "</td>
                        <td>" . $row["phone"] . "</td>
                        <td>" . $row["appointment_date"] . "</td>
                        <td>" . $row["reason"] . "</td>
                        <td>" . $row["gender"] . "</td>
                        <td>" . $row["status"] . "</td>
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

