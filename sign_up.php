<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign Up - CF Stores</title>
  <link rel="icon" type="image/png" href="./assets/img/favicon.png">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <link href="./assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(to right, #e0f2ff, #cce4ff);
      min-height: 100vh;
    }
    .form-wrapper {
      max-width: 480px;
      margin: 0 auto;
    }
  </style>
</head>
<body class="bg-light d-flex align-items-center justify-content-center">
  <main class="main-content w-100 mt-0">
    <div class="page-header min-vh-100 d-flex flex-column align-items-center justify-content-center px-3">
      <div class="text-center mb-4">
        <img src="./assets/img/cflogo.jpg" alt="CF Stores Logo" style="height: 100px; opacity: 0.9;" />
        <h4 class="text-dark font-weight-bold">Register an Account</h4>
      </div>
      <div class="form-wrapper">
        <div class="card">
          <div class="card-header text-center pt-3 pb-2">
            <h5>Sign Up</h5>
          </div>
          <div class="card-body">
            <form action="actions/signup_process.php" method="POST">
              <div class="mb-3">
                <input type="text" class="form-control" name="name" placeholder="Full Name" required>
              </div>
              <div class="mb-3">
                <input type="email" class="form-control" name="email" placeholder="Email" required>
              </div>
              <div class="mb-3">
                <select name="role" class="form-control" required>
                  <option value="">Select Role</option>
                  <option value="boss">Boss</option>
                  <option value="shopkeeper">Shopkeeper</option>
                </select>
              </div>
              <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-primary w-100">Create Account</button>
              </div>
            </form>
          </div>
          <div class="card-footer text-center pb-2">
            <small>Already have an account? <a href="login.php" class="text-primary">Login here</a></small>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
