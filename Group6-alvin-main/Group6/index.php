<!DOCTYPE html>
<html>

<head>
    <?php session_start(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Lab</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/cf223ee5eb.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <link rel="icon" href="images/ctu.png" type="image/x-icon">
    <!-- meta tag that disables caching for the page it is included on and it prevents the browser from storing a local copy. 
    This causes the browser to fetch a fresh copy of the page from the server each time the user visits. -->
    <meta http-equiv="Cache-control" content="no-cache">
    <!-- bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

</head>

<body>
    <div class="ctn">
        <div class="slider">
            <div id="slides" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="images/ctu1.jpg" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="images/ctu2.jpg" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="images/ctu3.jpg" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="images/ctu4.jpg" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="images/ctu6.jpg" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="images/ctu6.jpg" class="d-block w-100" alt="...">
                    </div>
                </div>
            </div>
        </div>
        <div class="text">
            <h1>Welcome to the<br>Computer Lab <br>Reservation System</h1>
            <div class="bttn">
                <a class="login-button" data-bs-toggle="modal" data-bs-target="#login-modal"
                    data-bs-whatever="login-modal">
                    <div>Login</div>
                </a>
                <a class="reg-button" data-bs-toggle="modal" data-bs-target="#reg-modal" data-bs-whatever="reg-modal">
                    <div>Register</div>
                </a>
            </div>
        </div>
    </div>
    </div>
    <!------------------------------------ Login Modal ------------------------------------>
    <div class="modal fade" id="login-modal" tabindex="-1" aria-labelledby="login-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content primary">
                <div class="modal-header text-center">
                    <h1 class="modal-title fs-5 text-center">Login</h1>
                    <i class="fa-sharp fa-solid fa-xmark" data-bs-dismiss="modal" aria-label="Close"></i>
                </div>
                <?php
                if (isset($_SESSION['login_error'])) {
                    echo '<p class="error-message">' . htmlspecialchars($_SESSION['login_error'], ENT_QUOTES, 'UTF-8') . '</p>';
                    unset($_SESSION['login_error']);
                }
                ?>
                <div class="modal-body">
                    <form method="post" action="process_login.php">
                        <div class="mb-3">
                            <label for="login-username" class="col-form-label">Username:</label>
                            <input type="text" name="username" class="form-control" id="login-username" required
                                maxlength="12">
                        </div>
                        <div class="mb-3">
                            <label for="login-password" class="col-form-label">Password:</label>
                            <input type="password" name="password" class="form-control" id="login-password" required
                                maxlength="15">
                        </div>
                        <script>
                            console.log("User Role: <?php echo isset($_SESSION['user_role']) ? htmlspecialchars($_SESSION['user_role'], ENT_QUOTES, 'UTF-8') : 'Unknown'; ?>");
                        </script>
                        <div class="d-flex flex-column text-center align-items-center justify-content-center">
                            </button>
                            <button type="submit" class="login-submit btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!------------------------------------ Register Modal ------------------------------------>
    <div class="modal fade" id="reg-modal" tabindex="-1" aria-labelledby="login-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
            <div class="modal-content primary">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Register</h1>
                    <i class="fa-sharp fa-solid fa-xmark" data-bs-dismiss="modal" aria-label="Close"></i>
                </div>

                <?php
                if (isset($_SESSION['registration_error'])) {
                    echo '<div class="error-message">' . htmlspecialchars($_SESSION['registration_error'], ENT_QUOTES, 'UTF-8') . '</div>';
                    unset($_SESSION['registration_error']);
                }
                ?>

                <div class="modal-body">
                    <form method="post" action="process_registration.php">
                        <div class="mb-3">
                            <label for="username" class="col-form-label">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" required
                                maxlength="12">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="col-form-label">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required
                                minlength="8" maxlength="15">
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="col-form-label">Confirm Password:</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                required minlength="8" maxlength="15">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="col-form-label">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="mobile" class="col-form-label">Mobile Number:</label>
                            <input type="tel" class="form-control" id="mobile" name="mobile" required>
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="col-form-label">Gender:</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="age" class="col-form-label">Age:</label>
                            <input type="number" class="form-control" id="age" name="age" required>
                        </div>

                        <div class="mb-3">
                            <label for="course" class="col-form-label">Course:</label>
                            <select class="form-select" id="course" name="course" required>
                                <option value="engineering">Engineering</option>
                                <option value="business">Business</option>
                                <option value="arts">Arts</option>

                            </select>
                        </div>

                        <div class="d-flex flex-column text-center align-items-center justify-content-center">
                            <input type="submit" class="login-submit btn btn-primary" value="Register">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


</html>