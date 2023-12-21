<?php

$host = "database";
$username = "user";
$password = "password";
$dbname = "appDB";

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $result = $mysqli->query("SELECT * FROM categories");
    $categories = [];

    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    echo json_encode($categories);

} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["name"])) {
        $name = $mysqli->real_escape_string($data["name"]);
        if ($mysqli->query("INSERT INTO categories (name) VALUES ('$name')")) {
            echo json_encode(["message" => "Category was created successfully"]);
        } else {
            echo json_encode(["error" => "Error while creating category: " . $mysqli->error]);
        }
    } else {
        echo json_encode(["error" => "You should enter the name!"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["id"])) {
        $id = intval($data["id"]);
        $deleteQuery = "DELETE FROM categories WHERE id = $id";
        if ($mysqli->query($deleteQuery)) {
            echo json_encode(["message" => "Category was deleted successfully"]);
        } else {
            echo json_encode(["error" => "Error while deleting a category: " . $mysqli->error]);
        }
    } else {
        echo json_encode(["error" => "ID is needed!"]);
    }
}

$mysqli->close();
?>