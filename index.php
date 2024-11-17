<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paid Project</title>
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> 
    <style>
        body {
            background-color: #c8e6c9; /* Light green background */
            color: #ffffff; /* White text for contrast */
            font-family: Arial, sans-serif; /* Font style */
        }
        .container {
            margin-top: 100px; /* Space from the top */
            padding: 40px; /* Padding inside the container */
            background-color: #388e3c; /* Darker green for the container */
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); /* Shadow effect */
        }
        h1 {
            margin-bottom: 30px; /* Space below the heading */
            font-weight: bold; /* Bold heading */
        }
        .btn-primary {
            background-color: #66bb6a; /* Light green button color */
            border-color: #4caf50; /* Border color for button */
        }
        .btn-primary:hover {
            background-color: #57a05c; /* Darker shade on hover */
            border-color: #4caf50;
        }
        .btn-secondary {
            background-color: #81c784; /* Lighter green for secondary button */
            border-color: #66bb6a; /* Border color */
        }
        .btn-secondary:hover {
            background-color: #75b367; /* Darker shade on hover */
            border-color: #66bb6a;
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <h1>Welcome to EcoFacilities</h1>
        <a href="../views/login.php" class="btn btn-primary">Login as Manager</a>
        <a href="../views/public.php" class="btn btn-secondary">Browse Facilities</a> 
    </div>

    <!-- Include Bootstrap JS (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> 
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>