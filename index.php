<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - CF Stores</title>
  <link rel="apple-touch-icon" href="./assets/img/cflogo.jpg" sizes="180x180">
  <link rel="icon" type="image/png" href="./assets/img/cflogo.jpg" sizes="192x192">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <link href="./assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />
<style>
  body {
    background: linear-gradient(to right, #e0f2ff, #cce4ff);
    min-height: 100vh;
  }

  .logo-wrapper {
    display: inline-block;
    padding: 20px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(8px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
    width: 240px;
    height: 240px;
  }

  .login-logo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transform: scale(1.2); /* This zooms the logo */
    transition: transform 0.3s ease;
  }

  .logo-wrapper:hover .login-logo {
    transform: scale(1.3);
  }

  .card {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    border-radius: 1rem;
    padding: 2rem;
  }

  .btn-primary {
    background-color: #1e88e5;
  }

  .form-wrapper {
    max-width: 480px;
    width: 100%;
    margin: 0 auto;
  }
</style>

</head>

<body class="bg-light d-flex align-items-center justify-content-center">
  <main class="main-content w-100 mt-0">
    <div class="page-header min-vh-100 d-flex flex-column align-items-center justify-content-center px-3">
      <div class="text-center mb-4">
        <div class="logo-wrapper">
          <img src="./assets/img/cflogo.jpg" alt="CF Stores Logo" class="login-logo">
        </div>
        <h4 class="text-dark font-weight-bold">Welcome to CF Stores</h4>
      </div>

      <div class="form-wrapper">
        <div class="card">
          <div class="card-header text-center bg-transparent pt-3 pb-2">
            <h5 class="text-dark">Login</h5>
          </div>
          <div class="card-body">
            <form action="./actions/login_process.php" method="POST">
              <div class="mb-3">
                <input type="email" class="form-control" name="email" placeholder="Email" required>
              </div>
              <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-primary w-100">Login</button>
              </div>
            </form>
          </div>
          
        </div>
      </div>
    </div>
  </main>
</body>

</html>
