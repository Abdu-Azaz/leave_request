<?php
session_start();
require_once ("connect_db.php");

$email = $password = "";
$emailErr = $passwordErr = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["email"])) {
        $emailErr = "Email est requis";
    } else {
        $email = test_input($_POST["email"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Mot de passe est requis";
    } else {
        $password = test_input($_POST["password"]);
    }

    if (empty($emailErr) && empty($passwordErr)) {
        $email = mysqli_real_escape_string($conn, $email);
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if ($password == $row['password']) {
                $success = true;
                if ($row['status'] == 'rh') {
                    $_SESSION['rh_email'] = $email;
                    header("Location: demandes.php"); // Redirect to RH page
                } else {
                    $_SESSION['email'] = $email;
                    header("Location: conge.php"); // Redirect to congé page
                }
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
    <?php
    require ('navbar.php')
        ?>

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