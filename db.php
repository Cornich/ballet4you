<?php
$host = 'localhost';
$user = 'root';
$password = '';

$conn = new mysqli($host, $user, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$dbname = 'ballet4you';
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";

if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists.";
} else {
    echo "Error creating database: " . $conn->error;
}

$conn2 = new mysqli($host, $user, $password, $dbname);
$sql2 = "CREATE TABLE IF NOT EXISTS cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);
CREATE TABLE IF NOT EXISTS planning (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cours_id INT NOT NULL,
    jour VARCHAR(20) NOT NULL,
    heure TIME NOT NULL,
    FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE
);";

if ($conn2->multi_query($sql2) === TRUE) {
    echo "Tables created successfully or already exist.";
} else {
    echo "Error creating tables: " . $conn2->error;
}

$conn->close();
$conn2->close();

?>