<?php
// session_start();
// if (empty($_SESSION['admin_session'])) {
//     header('Location: login.php');
//     exit();
// }

include 'dbconnection.php';
$todayMonthDay = date("m-d");

$sql = "SELECT * FROM `client`";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $email = $row['email'];
        $name = $row['name'];
        $birthdate = str_replace("<br>", "", $row['birthdate']);
        $anniversarydate = str_replace("<br>", "", $row['anniversarydate']);

        // Extract month and day from the dates
        $clientBirthMonthDay = date("m-d", strtotime($birthdate));
        $clientAnniversaryMonthDay = date("m-d", strtotime($anniversarydate));

        if ($todayMonthDay === $clientBirthMonthDay) {
            $messageID = 3; // ID for birthday message
            $subject = "Birthday Greetings";
        } elseif ($todayMonthDay === $clientAnniversaryMonthDay) {
            $messageID = 4; // ID for anniversary message
            $subject = "Anniversary Greetings";
        } else {
            continue; // Skip to the next client if it's not their special day
        }

        $categorynm = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `message` FROM `message` WHERE id = $messageID"))['message'];

        // Construct the notification message
        $notificationMessage = "Dear $name,\n\n$categorynm\n\nBest regards,\nYour Company";

        // Sending notification using PHP's mail function
        if (mail($email, $subject, $notificationMessage)) {
            echo "<script>alert('Notification sent to $name for $subject');location.replace('index.php')</script>";
        } else {
            echo "<script>alert('Failed to send notification to $name for $subject');</script>";
        }
    }
} else {
    echo "No clients found.";
}

$conn->close();
