<?php
session_start();

if (!isset($_SESSION['products'])) {
    $_SESSION['products'] = [
        ["id" => 1, "name" => "Laptop", "description" => "Portable computer", "price" => 1200, "category" => "Electronics"],
        ["id" => 2, "name" => "Book", "description" => "Learn PHP basics", "price" => 25, "category" => "Books"],
    ];
}

$products = &$_SESSION['products'];
$errors = [];
$submittedData = ["name" => "", "description" => "", "price" => "", "category" => ""];
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['add_product'])) {
        $submittedData['name'] = $_POST['name'] ?? '';
        $submittedData['description'] = $_POST['description'] ?? '';
        $submittedData['price'] = $_POST['price'] ?? '';
        $submittedData['category'] = $_POST['category'] ?? '';

        if ($submittedData['name'] === '') $errors['name'] = "Name is required.";
        if ($submittedData['description'] === '') $errors['description'] = "Description is required.";
        if ($submittedData['price'] === '' || !is_numeric($submittedData['price']) || $submittedData['price'] <= 0) $errors['price'] = "Price must be a positive number.";
        if ($submittedData['category'] === '') $errors['category'] = "Category is required.";

        if (empty($errors)) {
            $newId = count($products) + 1;
            $products[] = [
                "id" => $newId,
                "name" => htmlspecialchars($submittedData['name']),
                "description" => htmlspecialchars($submittedData['description']),
                "price" => $submittedData['price'],
                "category" => htmlspecialchars($submittedData['category']),
            ];

            $successMessage = "Product added successfully!";
            $submittedData = ["name" => "", "description" => "", "price" => "", "category" => ""];
        }
    }

    if (isset($_POST['delete_id'])) {
        $deleteId = $_POST['delete_id'];
        foreach ($products as $index => $product) {
            if ($product['id'] == $deleteId) {
                unset($products[$index]);
                $products = array_values($products);
                $successMessage = "Product deleted successfully!";
                break;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Product Inventory</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="master.css">
</head>
<body>
<div class="container my-5">
    <h1 class="mb-4 text-center">Product Inventory</h1>

    <?php if ($successMessage): ?>
        <div class="alert alert-success text-center"><?= $successMessage ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">Please fix the errors below.</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped custom-table">
            <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Category</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= $p['name'] ?></td>
                        <td><?= $p['description'] ?></td>
                        <td>$<?= $p['price'] ?></td>
                        <td><?= $p['category'] ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No products available.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <h2 class="mt-5">Add New Product</h2>
    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($submittedData['name']) ?>">
            <?php if (isset($errors['name'])): ?>
                <div class="invalid-feedback"><?= $errors['name'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"><?= htmlspecialchars($submittedData['description']) ?></textarea>
            <?php if (isset($errors['description'])): ?>
                <div class="invalid-feedback"><?= $errors['description'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" name="price" class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($submittedData['price']) ?>">
            <?php if (isset($errors['price'])): ?>
                <div class="invalid-feedback"><?= $errors['price'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select <?= isset($errors['category']) ? 'is-invalid' : '' ?>">
                <option value="">-- Select --</option>
                <option value="Electronics" <?= $submittedData['category'] === 'Electronics' ? 'selected' : '' ?>>Electronics</option>
                <option value="Books" <?= $submittedData['category'] === 'Books' ? 'selected' : '' ?>>Books</option>
                <option value="Clothes" <?= $submittedData['category'] === 'Clothes' ? 'selected' : '' ?>>Clothes</option>
            </select>
            <?php if (isset($errors['category'])): ?>
                <div class="invalid-feedback"><?= $errors['category'] ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
    </form>
</div>
</body>
</html>
