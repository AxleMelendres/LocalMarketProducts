<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../CSS/main.css">
</head>
<body>

    <?php  require "../HEADER/header.html" ?>

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
    <?php  require "../HEADER/footer.html" ?>
</body>
</html>