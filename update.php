<?php

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$errors = [];

$id = $_GET['id'] ?? null;
if(!$id) {
    header('Location: index.php');
    exit;
}

$statement = $pdo->prepare('SELECT * FROM products WHERE id=:id');
$statement->bindValue(':id', $id);
$statement->execute();
$product = $statement->fetch(PDO::FETCH_ASSOC);

$title = $product['title'];
$price = $product['price'];
$description = $product['description'];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $image = '';
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];

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
        $imagePath = '';
        if($image && $image['tmp_name']) {
            if($product['image']) {
                unlink($product['image']);
            }
            $imagePath = 'images/'.randomString(8).'/'.$image['name'];
            mkdir(dirname($imagePath));
            move_uploaded_file($image['tmp_name'], $imagePath);
        }
        if ($image['tmp_name']) {
            $statement = $pdo->prepare("UPDATE products SET title=:title, image=:image, description=:description, price=:price
        WHERE id=:id");
            $statement->bindValue(':image', $imagePath);
        } else {
            $statement = $pdo->prepare("UPDATE products SET title=:title, description=:description, price=:price
        WHERE id=:id");
        }
        $statement->bindValue(':id', $id);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':price', $price);
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
    <a href="index.php" class="btn btn-secondary">Go back to products</a>
    <?php if(count($errors) > 0) { ?>
    <div class="alert alert-danger">
    <?php //for errors
        foreach ($errors as $key => $error) {
        # code...
        echo $error;
    } ?></div>
    <?php } ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <?php if($product['image']) { ?>
            <img src="<?php echo $product['image']?>"alt="" style="width:500px;height: 400px;margin-top:20px">
        <?php } ?>
        <div class="mb-3">
            <label class="form-label">Product Image</label>
            <br>
            <input type="file" class="" name="image">
        </div>
        <div class="mb-3">
            <label class="form-label">Product Title</label>
            <input type="text" class="form-control" name="title" value=<?php echo $title;?>>
        </div>
        <div class="mb-3">
            <label class="form-label">Product Description</label>
            <br>
            <textarea class="form-control" name="description"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Product Price</label>
            <input type="number" step="0.01" class="form-control" name="price" value=<?php echo $price;?>>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</body>

</html>