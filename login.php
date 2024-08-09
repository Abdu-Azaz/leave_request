
<?php
session_start();
require_once("connect_db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $password = "";
    $emailErr = $passwordErr = "";

    if (empty($_POST["email"])) $emailErr = "Email est requis";
    else $email = test_input($_POST["email"]);

    if (empty($_POST["password"])) $passwordErr = "Mot de passe est requis";
    else $password = test_input($_POST["password"]);

    if (empty($emailErr) && empty($passwordErr)) {
        $email = mysqli_real_escape_string($conn, $email);
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if ($password == $row['password']) {
                $_SESSION['email'] = $email;
                header("Location: conge.php"); // Redirect to congé page
                exit();
            } else {
                $passwordErr = "Mot de passe incorrect";
            }
        } else {
            $emailErr = "Email non trouvé";
        }
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Gestion de congés</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Se connecter</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">S'enregistrer</a>
                    </li>
                </ul>

            </div>
        </div>
    </nav>

    <div class="container">
        <div class="mt-4 text-center">
            <h1 class="">Se connecter</h1>
        </div>


        <div class="">
            <form class=" px-5 bg-light mx-auto" style="max-width:400px"
                action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <?php if ($success): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <strong>Form submitted successfully</strong>
                        </div>
                    <?php
                    $success = 0;
                     $emailErr = $passwordErr = "";

                endif; ?>
                

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email"
                        class="form-control <?php echo !empty($emailErr) ? 'is-invalid' : ''; ?>"
                        value="<?php echo htmlspecialchars($email); ?>">
                    <?php if (!empty($emailErr)): ?>
                        <div class="invalid-feedback">
                            <?php echo $emailErr; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password"
                        class="form-control <?php echo !empty($passwordErr) ? 'is-invalid' : ''; ?>">
                    <?php if (!empty($passwordErr)): ?>
                        <div class="invalid-feedback">
                            <?php echo $passwordErr; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>