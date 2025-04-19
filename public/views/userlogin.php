<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login as User</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/user.css">
</head>
<body>
    <div class="background-image"></div>
    <div class="background-overlay"></div>

    <div class="login-container">
        <h2>
            <i class="fas fa-user-circle text-success mb-3"></i>
            Login as User
        </h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="userlogin_process.php" method="POST">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user text-success mr-2"></i>Username
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-success text-white">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock text-success mr-2"></i>Password
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-success text-white">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
            </div>

            <!-- Hidden input for facility_id -->
            <input type="hidden" name="facility_id" id="facility_id">

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-sign-in-alt mr-2"></i>Login
            </button>
        </form>
    </div>

    <script>
        // Retrieve facility ID from session storage
        const facilityId = sessionStorage.getItem('facilityId');
        if (facilityId) {
            document.getElementById('facility_id').value = facilityId;
        }
    </script>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>