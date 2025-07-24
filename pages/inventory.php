<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    echo "<script>alert('You must log in first.'); window.location.href='login.php';</script>";
    exit();
}

if ($_SESSION["user_role"] !== "boss") {
    echo "<script>alert('Access denied. Redirecting to Sales page.'); window.location.href='sales.php';</script>";
    exit();
}
include '../includes/db.php';

// Date filter
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Build WHERE clause
$where = "";
$params = [];

if ($from && $to) {
    $where = "WHERE updated_at BETWEEN ? AND ?";
    $params[] = $from . " 00:00:00";
    $params[] = $to . " 23:59:59";
}

$sql = "SELECT * FROM inventory $where ORDER BY updated_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat("s", count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Get total count for pagination
$countSql = "SELECT COUNT(*) as total FROM inventory $where";
$countStmt = $conn->prepare($countSql);
if ($where !== "") {
    $countStmt->bind_param("ss", $params[0], $params[1]);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalItems = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="/assets/img/favicon.png">
  <title>
    CF Stores
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />
  
</head>

<body class="g-sidenav-show   bg-gray-100">
<div class="min-height-300 bg-dark position-absolute w-100"></div>
  <aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0 d-flex align-items-center" href="../dashboard.php">
  <img src="../assets/img/cflogo.jpg" style="height: 150px; width: auto; max-height: none;" alt="main_logo">
</a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto h-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item ">
          <a class="nav-link " href="../dashboard.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item mt-3">
          <a class="nav-link " href="./sales.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-calendar-grid-58 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Sales</span>
          </a>
        </li>
        <li class="nav-item mt-3">
              <a class="nav-link active" href="./inventory.php">
              <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-credit-card text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Inventory</span>
          </a>
        </li>
        <li class="nav-item mt-3">
          <a class="nav-link" href="../actions/logout.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-user-run text-danger text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Logout</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>
  <main class="main-content position-relative border-radius-lg ">
        <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur" data-scroll="false">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <h1 class="font-weight-bolder text-white mb-0">Inventory</h1>
        </nav>
        </nav>  
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6>Add New Item</h6>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-4">
            <form action="../actions/insert_inventory.php" method="POST">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Product Name</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Category</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Quantity</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Selling Price (MWK)</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ordering Price (MWK)</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <input type="text" name="product_name" class="form-control form-control-sm" required>
                    </td>
                    <td>
                      <input type="text" name="category" class="form-control form-control-sm" required>
                    </td>
                    <td>
                      <input type="number" name="quantity" class="form-control form-control-sm" required>
                    </td>
                    <td>
                      <input type="number" step="0.01" name="selling_price" class="form-control form-control-sm" required>
                    </td>
                    <td>
                      <input type="number" step="0.01" name="ordering_price" class="form-control form-control-sm" required>
                    </td>
                    <td>
                      <button type="submit" class="btn btn-sm btn-success">Add Item</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center pb-0">
          
          <h6>Inventory List</h6>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-4" style="overflow-x: auto;">
            <table class="table align-items-center mb-0" style="min-width: 1000px;">
              <thead>
                <tr>
                  <th>Product Name</th>
                  <th>Category</th>
                  <th>Quantity</th>
                  <th>Selling Price</th>
                  <th>Ordering Price</th>
                  <th>Updated At</th>
                  <th>Update Stock</th>
                </tr>
              </thead>
              <tbody>
  <?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <form action="../actions/update_inventory.php" method="POST" class="align-middle">
          <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
          
          <!-- Product Name -->
          <td>
            <input type="text" name="product_name" class="form-control form-control-sm" value="<?= htmlspecialchars($row['product_name']) ?>">
          </td>

          <!-- Category (read-only) -->
          <td>
            <p class="text-sm mb-0"><?= htmlspecialchars($row['category']) ?></p>
          </td>

          <!-- Current Quantity (read-only) -->
          <td>
            <p class="text-sm mb-0"><?= $row['quantity'] ?></p>
          </td>

          <!-- Selling Price -->
          <td>
            <input type="number" name="selling_price" class="form-control form-control-sm" step="0.01" value="<?= $row['selling_price'] ?>">
          </td>

          <!-- Ordering Price -->
          <td>
            <input type="number" name="ordering_price" class="form-control form-control-sm" step="0.01" value="<?= $row['ordering_price'] ?>">
          </td>

          <!-- Last Updated -->
          <td>
            <p class="text-sm mb-0"><?= $row['updated_at'] ?></p>
          </td>

          <!-- Add/Remove Quantity + Update Button -->
          <td>
            <div class="d-flex flex-column flex-md-row gap-1">
              <input type="number" name="add_quantity" class="form-control form-control-sm" placeholder="+Qty" min="1" style="width: 70px;">
              <input type="number" name="reduce_quantity" class="form-control form-control-sm" placeholder="-Qty" min="1" style="width: 70px;">
              <button type="submit" class="btn btn-sm btn-primary">Update</button>
            </div>
          </td>
        </form>
      </tr>
    <?php endwhile; ?>
  <?php else: ?>
    <tr>
      <td colspan="7" class="text-center text-secondary">No inventory items found.</td>
    </tr>
  <?php endif; ?>
</tbody>

            </table>
<!-- Inventory Filter and Download Section -->
<div class="card shadow-sm border-0 mb-4">
  <div class="card-header pb-0 d-flex justify-content-between align-items-center">
    <h6 class="mb-0">📦 Filter & Download Inventory Data</h6>
  </div>
  <div class="card-body">
    <form method="GET">
      <div class="row g-3">
        <div class="col-md-4">
          <label for="from" class="form-label">From Date</label>
          <input type="date" name="from" id="from" class="form-control" value="<?= htmlspecialchars($from) ?>" required>
        </div>
        <div class="col-md-4">
          <label for="to" class="form-label">To Date</label>
          <input type="date" name="to" id="to" class="form-control" value="<?= htmlspecialchars($to) ?>" required>
        </div>
        <div class="col-md-4 d-flex align-items-end gap-2">
          <button type="submit" class="btn btn-primary w-50">🔍 Filter</button>
          <?php if ($from && $to): ?>
            <a href="../actions/download_inventory.php?from=<?= $from ?>&to=<?= $to ?>" 
               class="btn btn-success w-50">⬇️ Download CSV</a>
          <?php endif; ?>
        </div>
      </div>
    </form>
  </div>
</div>

            <!-- Pagination -->
            <nav aria-label="Inventory pagination">
              <ul class="pagination justify-content-center mt-4">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                  <a class="page-link" href="?page=<?= $page - 1 ?>&from=<?= $from ?>&to=<?= $to ?>" aria-label="Previous">‹</a>
                </li>
                <?php
                $visiblePages = 5;
                $startPage = max(1, $page - floor($visiblePages / 2));
                $endPage = min($totalPages, $startPage + $visiblePages - 1);
                $startPage = max(1, $endPage - $visiblePages + 1);
                for ($i = $startPage; $i <= $endPage; $i++):
                ?>
                  <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&from=<?= $from ?>&to=<?= $to ?>"><?= $i ?></a>
                  </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                  <a class="page-link" href="?page=<?= $page + 1 ?>&from=<?= $from ?>&to=<?= $to ?>" aria-label="Next">›</a>
                </li>
              </ul>
            </nav>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

      <footer class="footer pt-3  ">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted text-lg-start">
                © <script>
                  document.write(new Date().getFullYear())
                </script>,
                made<i class="fa fa-heart"></i> by
                Maurice Estates.
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </main>
  
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/argon-dashboard.min.js?v=2.1.0"></script>
</body>

</html>