<?php

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$errors = [];

$title = '';
$price = '';
$description = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $image = "";
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $create_date = Date('Y-m-d H:i:s');
    if(!$title) {
        array_push($errors, 'Title is not entered');
    }
    if(!$price) {
        array_push($errors, 'Price is not entered');
    }
    if(!is_dir('images')) {
        mkdir('images');
    }

    if(empty($errors)) {
        $image = $_FILES['image'] ?? null;
        if($image && $image['tmp_name']) {
            $imagePath = 'images/'.randomString(8).'/'.$image['name'];
            mkdir(dirname($imagePath));
            move_uploaded_file($image['tmp_name'], $imagePath);
        }
        $statement = $pdo->prepare("INSERT INTO products (title, image, description, price, create_date) VALUES (:title, :image, :description, :price, :date)");
        $statement->bindValue(':title', $title);
        $statement->bindValue(':image', $imagePath);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':date', $create_date);
        $statement->execute();
        header('Location: index.php');
    }
}
function randomString($n) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = "";
    for($i=0;$i<$n;++$i) {
        $index = rand(0, strlen($characters)-1);
        $str .= $characters[$index];
    }
    return $str;
} 

?>


<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="app.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">

    <title>PRODUCTS CRUD</title>
</head>

<body>
    <h1>Create New Product</h1>
    <?php if(count($errors) > 0) { ?>
    <div class="alert alert-danger">
    <?php
        foreach ($errors as $key => $error) {
        # code...
        echo $error;
    } ?></div>
    <?php } ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Product Image</label>
            <br>
            <input type="file" class="" name="image">
        </div>
        <div class="mb-3">
            <label class="form-label">Product Title</label>
            <input type="text" class="form-control" name="title">
        </div>
        <div class="mb-3">
            <label class="form-label">Product Description</label>
            <br>
            <textarea class="form-control" name="description"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Product Price</label>
            <input type="number" step="0.01" class="form-control" name="price">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</body>

</html>