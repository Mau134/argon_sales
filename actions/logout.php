<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="refresh" content="2;url=../index.php" />
  <title>Logging Out...</title>
  <link href="../assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />
</head>
<body class="d-flex align-items-center justify-content-center bg-light" style="height: 100vh;">
  <div class="text-center">
    <h3 class="text-danger">You have been logged out</h3>
    <p class="text-muted">Redirecting to login page...</p>
  </div>
</body>
</html>
