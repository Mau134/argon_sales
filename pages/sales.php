<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    echo "<script>alert('You must log in first.'); window.location.href='login.php';</script>";
    exit();
}
include '../includes/db.php';

// Inventory for dropdown
$inventoryResult = $conn->query("SELECT id, product_name, selling_price FROM inventory");

// Sales for today
$salesSql = "
  SELECT 
    s.id, 
    i.product_name, 
    s.total_amount,
    s.quantity_sold,
    s.selling_price,
    s.created_at 
  FROM sales s
  JOIN inventory i ON s.product_id = i.id
  WHERE DATE(s.created_at) = CURDATE()
";

$salesResult = $conn->query($salesSql);

if (!$salesResult) {
    die("Sales query failed: " . $conn->error);
}
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
          <a class="nav-link active " href="./sales.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-calendar-grid-58 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Sales</span>
          </a>
        </li>
        <li class="nav-item mt-3">
          <a class="nav-link " href="./inventory.php">
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
          <h1 class="font-weight-bolder text-white mb-0">Sales</h1>
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
        <?php if (isset($_GET['error'])): ?>
  <div class="alert alert-danger">
    <?php
      if ($_GET['error'] === 'out_of_stock') echo "Sale failed: Product is out of stock.";
      elseif ($_GET['error'] === 'not_enough_stock') echo "Sale failed: Not enough quantity in inventory.";
    ?>
  </div>
<?php endif; ?>
        <div class="card-header pb-0">
          <h6>Make a Sale</h6>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-4">
            <form action="../actions/insert_sale.php" method="POST">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Product</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Price Sold (MWK)</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Quantity Sold</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Item Price (MWK)</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <select name="product_id" id="productSelect" class="form-control form-control-sm" required>
                      <option value="" disabled selected>Select Product</option>
                      <?php while ($row = $inventoryResult->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>" data-price="<?= $row['selling_price'] ?>">
                          <?= htmlspecialchars($row['product_name']) ?>
                        </option>
                      <?php endwhile; ?>
                      <?php if (isset($_GET['error'])): ?>
  <div class="alert alert-danger">
    <?php
      switch ($_GET['error']) {
        case 'out_of_stock':
          echo "Sale failed: Product is out of stock.";
          break;
        case 'not_enough_stock':
          echo "Sale failed: Not enough quantity in inventory.";
          break;
        case 'price_too_high':
          echo "Sale failed: Entered price is higher than the system price.";
          break;
      }
    ?>
  </div>
<?php endif; ?>
                      </select>
                    </td>
                    <td>
                        <!-- Price Sold (user inputs actual selling price) -->
                        <input type="number" step="0.01" name="price" class="form-control form-control-sm" required>
                    </td>
                    <td>
                      <input type="number" name="quantity_sold" class="form-control form-control-sm" required>
                    </td>
                    <td>
                    <!-- Item Price from inventory (read-only for reference only) -->
                    <input type="number" step="0.01" id="priceInput" class="form-control form-control-sm" readonly>
                    </td>
                    <td>
                      <button type="submit" class="btn btn-sm btn-success">Record Sale</button>
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
    <div class="col-12"> <!-- Same col width as Make a Sale -->
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6>Today's Sales (<?= date('d M Y') ?>)</h6>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-4"> <!-- Matches Make a Sale padding -->
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Product</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Price</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Quantity</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total (MWK)</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Time</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $salesResult->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td>MWK <?= number_format($row['selling_price'], 2) ?></td>
                    <td><?= $row['quantity_sold'] ?></td>
                    <td>MWK <?= number_format($row['total_amount'], 2) ?></td>
                    <td><?= date('H:i', strtotime($row['created_at'])) ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>

            </table>
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
  <script>
  document.getElementById('productSelect').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const price = selectedOption.getAttribute('data-price');
    document.getElementById('priceInput').value = price;
  });
</script>
<script>
document.querySelector('form').addEventListener('submit', function(e) {
  const systemPrice = parseFloat(document.getElementById('priceInput').value);
  const userPrice = parseFloat(document.querySelector('input[name="price"]').value);

  if (userPrice > systemPrice) {
    alert("The entered price is higher than the system price. Sale not allowed.");
    e.preventDefault();
  }
});
</script>


</body>

</html>