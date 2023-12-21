<?php

$host = "database";
$username = "user";
$password = "password";
$dbname = "appDB";

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Ошибка подключения к базе данных: " . $mysqli->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET["category"])) {
        $category = intval($_GET["category"]);
        $result = $mysqli->query("SELECT items.id, items.name, items.description, items.price, categories.name AS category FROM items
            INNER JOIN categories ON items.category = categories.id
            WHERE items.category = $category");
    } else {
        $result = $mysqli->query("SELECT items.id, items.name, items.description, items.price, categories.name AS category FROM items
            INNER JOIN categories ON items.category = categories.id");
    }

    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    echo json_encode($products);

} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data["name"]) && isset($data["price"]) && isset($data["category"]) && isset($data["description"])) {
        $name = $mysqli->real_escape_string($data["name"]);
        $description = $mysqli->real_escape_string($data["description"]);
        $price = floatval($data["price"]);
        $category = intval($data["category"]);

        $insertQuery = "INSERT INTO items (name, description, price, category) VALUES ('$name', '$description', $price, $category)";
        if ($mysqli->query($insertQuery)) {
            echo json_encode(["message" => "Product was created successfully"]);
        } else {
            echo json_encode(["error" => "Error while creating a product: " . $mysqli->error]);
        }
    } else {
        echo json_encode(["error" => "Name, price and category needed!"]);
    }
}

$mysqli->close();

?>