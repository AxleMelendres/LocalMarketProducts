<?php

if (isset($_GET['query']) || isset($_GET['district']) || isset($_GET['category'])) {
    $query = $_GET['query'] ?? '';
    $district = $_GET['district'] ?? '';
    $category = $_GET['category'] ?? '';

    // SQL query (Assuming a MySQL database)
    $sql = "SELECT * FROM products WHERE name LIKE '%$query%'";

    if ($district) {
        $sql .= " AND district = '$district'";
    }
    if ($category) {
        $sql .= " AND category = '$category'";
    }

    // Database connection and query execution
    $conn = new mysqli("localhost", "username", "password", "database_name");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<p>" . $row['name'] . " - " . $row['price'] . "</p>";
        }
    } else {
        echo "No results found.";
    }
    $conn->close();
}
?>
