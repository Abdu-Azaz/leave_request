
<?php
session_start();
require_once("connect_db.php");

if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_date = $end_date = $raison = "";
    $start_dateErr = $end_dateErr = $raisonErr = "";

    if (empty($_POST["start_date"])) $start_dateErr = "Date de début est requise";
    else $start_date = test_input($_POST["start_date"]);

    if (empty($_POST["end_date"])) $end_dateErr = "Date de fin est requise";
    else $end_date = test_input($_POST["end_date"]);

    if (empty($_POST["raison"])) $raisonErr = "Raison est requise";
    else $raison = test_input($_POST["raison"]);

    if (empty($start_dateErr) && empty($end_dateErr) && empty($raisonErr)) {
        $email = $_SESSION['email'];
        $start_date = mysqli_real_escape_string($conn, $start_date);
        $end_date = mysqli_real_escape_string($conn, $end_date);
        $raison = mysqli_real_escape_string($conn, $raison);

        // Calculate number of days
        $start_date_obj = new DateTime($start_date);
        $end_date_obj = new DateTime($end_date);
        $interval = $start_date_obj->diff($end_date_obj);
        $days = $interval->days + 1;

        // Check user's solde
        $email = mysqli_real_escape_string($conn, $email);
        $sql = "SELECT solde FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        if ($row['solde'] >= $days) {
            // Insert congé
            $sql = "INSERT INTO conges (email, raison, etat, start_date, end_date) VALUES ('$email', '$raison', 'en cours', '$start_date', '$end_date')";
            if (mysqli_query($conn, $sql)) {
                // Update solde
                $new_solde = $row['solde'] - $days;
                $sql = "UPDATE users SET solde=$new_solde WHERE email='$email'";
                if (mysqli_query($conn, $sql)) {
                    echo "Congé demandé avec succès";
                } else {
                    echo "Erreur lors de la mise à jour du solde: " . mysqli_error($conn);
                }
            } else {
                echo "Erreur lors de la demande de congé: " . mysqli_error($conn);
            }
        } else {
            echo "Vous n'avez pas le droit de prendre un congé";
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
            <h1 class="">Demande de congé</h1>
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
                    <label for="start_date" class="form-label">Date debut</label>
                    <input class="form-control <?php echo !empty($start_dateErr) ? 'is-invalid' : ''; ?>" type="date" name="start_date" id="start_date"
                    value="<?php echo htmlspecialchars($start_date); ?>"
                    >
                    <?php if (!empty($start_dateErr)): ?>
                        <div class="invalid-feedback">
                            <?php echo $start_dateErr; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label">Date fin</label>
                    <input type="date" name="end_date" id="end_date"
                        class="form-control <?php echo !empty($end_dateErr) ? 'is-invalid' : ''; ?>"
                        >
                        <?php if (!empty($end_dateErr)): ?>
                        <div class="invalid-feedback">
                            <?php echo $end_dateErr; ?>
                        </div>
                    <?php endif; ?>
                </div>


                <div class="mb-3">
                    <label for="raison" class="form-label">Raison</label>
                    <input type="text" class="form-control <?php echo !empty($end_dateErr) ? 'is-invalid' : ''; ?>" name="raison" id="raison"
                    >
                    <?php if (!empty($raisonErr)): ?>
                        <div class="invalid-feedback">
                            <?php echo $raisonErr; ?>
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