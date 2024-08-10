<?php
session_start();
require_once "connect_db.php";

$rh_email = $_SESSION["rh_email"] ?? null;

if ($rh_email) {
    $sql = "SELECT * FROM conges";
    $result = mysqli_query($conn, $sql);
    $conges = [];

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $conges[] = $row; 
        }
    }
} else {
    echo "Erreur!"; 
    exit;
}

// Handle approval or rejection of congé requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $_SESSION['rh_email'] = $rh_email;
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $action = $_POST['action'];

    if ($action === 'rejeter') {
        $etat = 'rejeté';
    } elseif ($action === 'approuver') {
        $etat = 'approuvé';
    } else {
        $etat = 'en cours';
    }

    $sql = "UPDATE conges SET etat='$etat' WHERE email='$email' AND start_date='$start_date'";
    if (mysqli_query($conn, $sql)) {
        header("Location: demandes.php"); // Refresh the page after the update
        exit;
    } else {
        echo "Erreur de mise à jour: " . mysqli_error($conn);
    }
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
            <h1 class="">Demandes de congés déposés</h1>
        </div>


        <table class="table table-striped table-responsive">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Date Debut</th>
                    <th>Date fin</th>
                    <th>Raison</th>
                    <th>Etat</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($conges as $cong) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cong["email"]); ?></td>
                        <td><?php echo htmlspecialchars($cong["start_date"]); ?></td>
                        <td><?php echo htmlspecialchars($cong["end_date"]); ?></td>
                        <td><?php echo htmlspecialchars($cong["raison"]); ?></td>
                        <td><?php echo htmlspecialchars($cong["etat"]); ?></td>
                        <td>
                            <!-- Add a form for each button or a link with query parameters -->
                            <form method="POST" action="<?php  echo $_SERVER["PHP_SELF"] ?>">
                                <input type="hidden" name="email" value="<?php echo htmlspecialchars($cong["email"]); ?>">
                                <input type="hidden" name="start_date"
                                    value="<?php echo htmlspecialchars($cong["start_date"]); ?>">
                                <input type="hidden" name="end_date"
                                    value="<?php echo htmlspecialchars($cong["end_date"]); ?>">
                                <button type="submit" name="action" value="rejeter" class="btn btn-danger">Rejeter</button>
                                <button type="submit" name="action" value="approuver"
                                    class="btn btn-success">Approuvé</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>