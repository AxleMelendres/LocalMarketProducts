<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market Alchemy</title>
    <link rel="stylesheet" href="../CSS/customerInterface.css">
    <script src="https://kit.fontawesome.com/89e47c0436.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php  require "../HEADER/customerHeader.html" ?>
    <section class="product-section">
    <?php
    require_once "../PHP/dbConnection.php";

    $database = new Database();
    $conn = $database->getConnection();

    require_once "../DB/productsTB.php";
    $product = new Product($conn);
    $product->displayAll();
    ?>
    </section>
</body>
</html>
