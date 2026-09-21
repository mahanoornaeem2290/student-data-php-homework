<?php
// Database Configuration
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');        // Change to your MySQL username
define('DB_PASS', '');            // Change to your MySQL password
define('DB_NAME', 'student_db');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("<div style='color:red; padding:20px; font-family:sans-serif;'>
        ❌ Connection Failed: " . $conn->connect_error . "
        <br><br>Please check your MySQL credentials in <strong>db_connect.php</strong>
    </div>");
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
$conn->select_db(DB_NAME);

// Create students table if not exists
$sql = "CREATE TABLE IF NOT EXISTS students (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    first_name  VARCHAR(50)  NOT NULL,
    last_name   VARCHAR(50)  NOT NULL,
    roll_number VARCHAR(20)  NOT NULL UNIQUE,
    class       VARCHAR(30)  NOT NULL,
    section     VARCHAR(5)   NOT NULL,
    gender      ENUM('Male','Female','Other') NOT NULL,
    dob         DATE         NOT NULL,
    email       VARCHAR(100) NOT NULL,
    phone       VARCHAR(15)  NOT NULL,
    address     TEXT         NOT NULL,
    father_name VARCHAR(100) NOT NULL,
    mother_name VARCHAR(100) NOT NULL,
    guardian_phone VARCHAR(15) NOT NULL,
    blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    nationality VARCHAR(50)  NOT NULL DEFAULT 'Pakistani',
    religion    VARCHAR(30)  NOT NULL,
    admission_date DATE      NOT NULL,
    fee_status  ENUM('Paid','Unpaid','Partial') NOT NULL DEFAULT 'Unpaid',
    photo_url   VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($sql)) {
    die("<div style='color:red; padding:20px;'>❌ Table creation failed: " . $conn->error . "</div>");
}
?>
