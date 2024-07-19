<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Hurley Restaurant</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Amatic+SC:wght@400;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">
</head>

<body class="index-page">

    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container position-relative d-flex align-items-center justify-content-between">

            <a href="index.php" class="logo d-flex align-items-center me-auto me-xl-0">
                <!-- Uncomment the line below if you also wish to use an image logo -->
                <img src="assets/img/logo.png" alt="">
                <h1 class="sitename">Hurley</h1>
                <span>.</span>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="index.php" class="active">Home<br></a></li>

                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <a class="btn-getstarted" href="login.php">Login</a>

        </div>
    </header>
    <section class="background-radial-gradient overflow-hidden">
        <style>
            .background-radial-gradient {
                background-color: hsl(218, 41%, 15%);
                background-image: radial-gradient(650px circle at 0% 0%,
                        hsl(218, 41%, 35%) 15%,
                        hsl(218, 41%, 30%) 35%,
                        hsl(218, 41%, 20%) 75%,
                        hsl(218, 41%, 19%) 80%,
                        transparent 100%),
                    radial-gradient(1250px circle at 100% 100%,
                        hsl(218, 41%, 45%) 15%,
                        hsl(218, 41%, 30%) 35%,
                        hsl(218, 41%, 20%) 75%,
                        hsl(218, 41%, 19%) 80%,
                        transparent 100%);
            }

            #radius-shape-1 {
                height: 220px;
                width: 220px;
                top: -60px;
                left: -130px;
                background: radial-gradient(#44006b, #ad1fff);
                overflow: hidden;
            }

            #radius-shape-2 {
                border-radius: 38% 62% 63% 37% / 70% 33% 67% 30%;
                bottom: -60px;
                right: -110px;
                width: 300px;
                height: 300px;
                background: radial-gradient(#44006b, #ad1fff);
                overflow: hidden;
            }

            .bg-glass {
                background-color: hsla(0, 0%, 100%, 0.9) !important;
                backdrop-filter: saturate(200%) blur(25px);
            }
        </style>
        <div class="container">
            <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
                <div class="row gx-lg-5 align-items-center mb-5">
                    <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
                        <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%)">
                            The best restaurant <br />
                            <span style="color: hsl(218, 81%, 75%)">for your taste</span>
                        </h1>
                        <p class="mb-4 opacity-70" style="color: hsl(218, 81%, 85%)">
                            Sign in with Hurley's Restaurant
                        </p>
                    </div>

                    <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                        <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                        <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                        <div class="card bg-glass">
                            <div class="card-body px-4 py-5 px-md-5">
                            <form method="POST" action="objects/process_reg.php">
    <!-- 2 column grid layout with text inputs for the first and last names -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div data-mdb-input-init class="form-outline">
                <input type="text" id="firstName" name="firstName" class="form-control" required />
                <label class="form-label" for="firstName">First name</label>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div data-mdb-input-init class="form-outline">
                <input type="text" id="lastName" name="lastName" class="form-control" required />
                <label class="form-label" for="lastName">Last name</label>
            </div>
        </div>
    </div>

    <!-- Email input -->
    <div data-mdb-input-init class="form-outline mb-4">
        <input type="email" id="email" name="email" class="form-control" required />
        <label class="form-label" for="email">Email address</label>
    </div>

    <!-- Phone Number input -->
    <div data-mdb-input-init class="form-outline mb-4">
        <input type="text" id="phoneNumber" name="phoneNumber" class="form-control" />
        <label class="form-label" for="phoneNumber">Phone number</label>
    </div>

    <!-- Address input -->
    <div data-mdb-input-init class="form-outline mb-4">
        <input type="text" id="address" name="address" class="form-control" />
        <label class="form-label" for="address">Address</label>
    </div>

    <!-- City input -->
    <div data-mdb-input-init class="form-outline mb-4">
        <input type="text" id="city" name="city" class="form-control" />
        <label class="form-label" for="city">City</label>
    </div>

    <!-- State input -->
    <div data-mdb-input-init class="form-outline mb-4">
        <input type="text" id="state" name="state" class="form-control" />
        <label class="form-label" for="state">State</label>
    </div>

    <!-- Zip Code input -->
    <div data-mdb-input-init class="form-outline mb-4">
        <input type="text" id="zipCode" name="zipCode" class="form-control" />
        <label class="form-label" for="zipCode">Zip Code</label>
    </div>

    <!-- Password input -->
    <div data-mdb-input-init class="form-outline mb-4">
        <input type="password" id="password" name="password" class="form-control" required />
        <label class="form-label" for="password">Password</label>
    </div>

    <!-- Role selection (default to Customer) -->
    <div class="form-outline mb-4">
        <select id="role" name="role" class="form-select" required>
            <option value="Customer" selected>Customer</option>
        </select>
        <label class="form-label" for="role">Role</label>
    </div>

    <!-- Checkbox -->
    <div class="form-check d-flex justify-content-center mb-4">
        <input class="form-check-input me-2" type="checkbox" value="" id="newsletter" name="newsletter" />
        <label class="form-check-label" for="newsletter">
            Subscribe to our newsletter
        </label>
    </div>

    <!-- Submit button -->
    <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4">
        Sign up
    </button>

    <!-- Register buttons -->
    <div class="text-center">
        <p>or sign up with:</p>
        <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
            <i class="fab fa-facebook-f"></i>
        </button>

        <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
            <i class="fab fa-google"></i>
        </button>

        <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
            <i class="fab fa-twitter"></i>
        </button>

        <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
            <i class="fab fa-github"></i>
        </button>
    </div>
</form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
    include("objects/footer.php"); ?>