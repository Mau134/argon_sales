<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    echo "<script>alert('You must log in first.'); window.location.href='login.php';</script>";
    exit();
}

if ($_SESSION["user_role"] !== "boss") {
    echo "<script>alert('Access denied. Redirecting to Sales page.'); window.location.href='./pages/sales.php';</script>";
    exit();
}



include './includes/db.php';
// Fetch total sales
$totalSalesResult = $conn->query("SELECT SUM(total_amount) as total_sales FROM sales");
$totalSales = $totalSalesResult->fetch_assoc()['total_sales'] ?? 0;

// Fetch total inventory
$totalInventoryResult = $conn->query("SELECT SUM(quantity) as total_inventory FROM inventory");
$totalInventory = $totalInventoryResult->fetch_assoc()['total_inventory'] ?? 0;

// Fetch total profit (sales - ordering cost)
$profitResult = $conn->query("
    SELECT SUM(s.total_amount - (s.quantity_sold * i.ordering_price)) as total_profit
    FROM sales s
    JOIN inventory i ON s.product_id = i.id
");

$totalProfit = $profitResult->fetch_assoc()['total_profit'] ?? 0;

$currentDate = date('l, d M Y');

// Fetch inventory data for chart and table
$inventoryQuery = $conn->query("SELECT product_name, category, quantity, selling_price, ordering_price, updated_at FROM inventory ORDER BY updated_at DESC LIMIT 10");
$inventoryLabels = [];
$inventoryQuantities = [];
$inventoryColors = [];
$inventoryRows = [];

while ($row = $inventoryQuery->fetch_assoc()) {
  $inventoryLabels[] = $row['product_name'];
  $inventoryQuantities[] = $row['quantity'];
  $inventoryColors[] = $row['quantity'] < 10 ? 'rgba(255, 99, 132, 0.6)' : 'rgba(75, 192, 192, 0.6)'; // Red for low, green for high
  $inventoryRows[] = $row;
}

// Get sales data for current month
$currentMonth = date('Y-m');
$sql = "
  SELECT 
    i.product_name,
    SUM(s.total_amount) AS total_sales,
    SUM(s.quantity_sold * i.ordering_price) AS total_cost,
    SUM(s.total_amount - (s.quantity_sold * i.ordering_price)) AS profit
  FROM sales s
  JOIN inventory i ON s.product_id = i.id
  WHERE DATE_FORMAT(s.created_at, '%Y-%m') = ?
  GROUP BY s.product_id
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $currentMonth);
$stmt->execute();
$result = $stmt->get_result();

$chart_labels = [];
$chart_profits = [];
while ($row = $result->fetch_assoc()) {
  $chart_labels[] = $row['product_name'];
  $chart_profits[] = (float)$row['profit'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/cflogo.jpg">
  <link rel="icon" type="image/png" href="./assets/img/cflogo.jpg">
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
  <link id="pagestyle" href="./assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
  body {
    overflow-x: hidden;
  }

  .main-content,
  .container-fluid,
  .row,
  .col {
    max-width: 100%;
    overflow-x: hidden;
  }

  canvas {
    max-width: 100%;
    height: auto;
  }
</style>

</head>

<body class="g-sidenav-show   bg-gray-100">
  <div class="min-height-300 bg-dark position-absolute w-100"></div>
  <aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
<a class="navbar-brand m-0 d-flex align-items-center" href="dashboard.php" >
  <img src="./assets/img/cflogo.jpg" style="height: 150px; width: auto; max-height: none;" alt="main_logo">
</a>

    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto h-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item ">
          <a class="nav-link active" href="dashboard.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item mt-3">
          <a class="nav-link " href="./pages/sales.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-calendar-grid-58 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Sales</span>
          </a>
        </li>
        <li class="nav-item mt-3">
          <a class="nav-link " href="./pages/inventory.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-credit-card text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Inventory</span>
          </a>
        </li>
        <li class="nav-item mt-3">
          <a class="nav-link" href="./actions/logout.php">
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
          <h1 class="font-weight-bolder text-white mb-0">Dashboard</h1>
        </nav>
        </nav>  
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
   <div class="container-fluid py-4">
  <div class="row">
    <div class="col-lg-3 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="d-flex">
            <div class="icon icon-shape bg-gradient-primary text-white rounded-circle shadow">
              <i class="ni ni-money-coins"></i>
            </div>
            <div class="ms-3">
              <p class="text-sm mb-0 text-capitalize">Total Sales</p>
              <h5 class="mb-0">MWK <?= number_format($totalSales, 2) ?></h5>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="d-flex">
            <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
              <i class="ni ni-box-2"></i>
            </div>
            <div class="ms-3">
              <p class="text-sm mb-0 text-capitalize">Total Inventory</p>
              <h5 class="mb-0"><?= $totalInventory ?></h5>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="d-flex">
            <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
              <i class="ni ni-calendar-grid-58"></i>
            </div>
            <div class="ms-3">
              <p class="text-sm mb-0 text-capitalize">Current Date</p>
              <h5 class="mb-0"><?= $currentDate ?></h5>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="d-flex">
            <div class="icon icon-shape bg-gradient-warning text-white rounded-circle shadow">
              <i class="ni ni-chart-bar-32"></i>
            </div>
            <div class="ms-3">
              <p class="text-sm mb-0 text-capitalize">Total Profit</p>
              <h5 class="mb-0">MWK <?= number_format($totalProfit, 2) ?></h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
      <div class="row mt-4">
        <div class="col-lg-7 mb-lg-0 mb-4">
    <div class="card z-index-2 h-100">
      <div class="card-header pb-0 pt-3 bg-transparent">
        <h6 class="text-capitalize">Sales Overview (<?= date('F Y') ?>)</h6>
      </div>
      <div class="card-body p-3">
        <div class="chart">
           <canvas id="monthly-sales-chart"></canvas>
              <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
              <script>
                const ctx = document.getElementById('monthly-sales-chart').getContext('2d');
                const chart = new Chart(ctx, {
                  type: 'line',
                  data: {
                    labels: <?= json_encode($chart_labels) ?>,
                    datasets: [{ label: 'Profit', data: <?= json_encode($chart_profits) ?>, fill: false, borderColor: 'rgb(75, 192, 192)' }]
                  }
                });
              </script>
        </div>
      </div>
     </div>
    </div>
        <div class="col-lg-5">
          
          <div class="card card-carousel overflow-hidden h-100 p-0">
            <div id="carouselExampleCaptions" class="carousel slide h-100" data-bs-ride="carousel">
              <div class="carousel-inner border-radius-lg h-100">
                <div class="carousel-item h-100 active" style="background-image: url('assets/img/cfhero.jpg');
                   background-size: cover;">
                  <div class="carousel-caption d-none d-md-block bottom-0 text-start start-0 ms-5">
                    <div class="icon icon-shape icon-sm bg-white text-center border-radius-md mb-3">
                      <i class="ni ni-camera-compact text-dark opacity-60"></i>
                    </div>
                    <h5 class="text-white mb-4">Welcome to Classy Fashions</h5>
                    
                  </div>
                </div>
                
            </div>
          </div>
        </div>
      </div>
      <div class="container-fluid py-4">
        <div class="row">
          <div class="col-12">
          <div class="card">
            <div class="card-header">Recent Inventory Updates</div>
            <canvas id="inventoryChart"></canvas>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Selling Price</th>
                    <th>Ordering Price</th>
                    <th>Updated At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($inventoryRows as $row): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['product_name']) ?></td>
                      <td><?= htmlspecialchars($row['category']) ?></td>
                      <td><?= $row['quantity'] ?></td>
                      <td>MWK <?= number_format($row['selling_price'], 2) ?></td>
                      <td>MWK <?= number_format($row['ordering_price'], 2) ?></td>
                      <td><?= $row['updated_at'] ?></td>
                    </tr>
                  <?php endforeach; ?>
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
                Made by
                Maurice Estates.
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </main>
  
  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/chartjs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Sales chart
  new Chart(document.getElementById('monthly-sales-chart'), {
    type: 'line',
    data: {
      labels: <?= json_encode($chart_labels) ?>,
      datasets: [{
        label: 'Profit (MWK)',
        data: <?= json_encode($chart_profits) ?>,
        borderColor: 'rgb(75, 192, 192)',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      scales: { y: { beginAtZero: true } }
    }
  });

  // Inventory chart
  new Chart(document.getElementById('inventoryChart'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($inventoryLabels) ?>,
      datasets: [{
        label: 'Quantity in Stock',
        data: <?= json_encode($inventoryQuantities) ?>,
        backgroundColor: <?= json_encode($inventoryColors) ?>,
        borderColor: 'rgba(0, 0, 0, 0.1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
</script>

  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
 

</body>

</html>