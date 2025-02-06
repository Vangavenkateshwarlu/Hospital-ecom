

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

    // Prepare the SQL query to insert data
    $sql = "INSERT INTO appointments (name, email, phone, appointment_date, reason, gender) 
            VALUES ('$name', '$email', '$phone', '$appointment_date', '$reason', '$gender')";

    if ($conn->query($sql) === TRUE) {
        echo "Appointment request submitted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

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
        // Fetch data from the appointments table
        $sql_select = "SELECT id, name, email, phone, appointment_date, reason, gender FROM appointments";
        $result = $conn->query($sql_select);

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
