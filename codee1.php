<?php
// 1. DATABASE CONNECTION SETTINGS
$host = 'localhost';
$db   = 'school'; 
$user = 'root';      
$pass = '';          
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// 2. LOGIC YA KU-SAVE DATA
if (isset($_POST['submit'])){
    $u_name = $_POST['username'];
    $u_pass = $_POST['passs']; 
    $f_name = $_POST['fname'];
    
    $sql = "INSERT INTO student (username, pasword, fname) VALUES (:uname, :upass, :fname)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uname' => $u_name,
            ':upass' => $u_pass,
            ':fname' => $f_name
        ]);
        echo "<script>alert('Record inserted successfully!');</script>";
    } catch (\PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <style>
            body{
                background-color: gray;
            }
            label{
                color:white;
                font-size: 25px;
            }
        form {
            text-align: center;
             color:white;
        }
        /* Style ya button zote */
        button {
            padding: 10px 20px;
            margin: 5px;
            border: 1px solid #ccc;
        }
        button:hover {
            background-color: blue;
            color: white;
            cursor: pointer;
        }
        input {
            margin-bottom: 25px;
        }
        table {
            margin-top: 20px;
            border-collapse: collapse;
            width: 60%;
            margin-left: auto;
            margin-right: auto;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
    </style>
    <title>simple form</title>
</head>
<body>
    <h1><mr class="harrison"></mr></h1>
    
    <form method="POST" action="">
        <label>user name:</label>
        <input type="text" name="username" required><br>
        
        <label>pasword:</label>
        <input type="password" name="passs" required><br>
        
        <label>full name:</label>
        <input type="text" name="fname" required><br>
        
        <button type="submit" name="submit">REGISTER!</button>
        <!-- BUTTON MPYA YA KUDISPLAY -->
        <button type="submit" name="view_data">VIEW DATA</button>
    </form>

    <?php
    // 3. LOGIC YA KUDISPLAY DATA (Inatokea hapa chini ikibonyezwa)
    if (isset($_POST['view_data'])) {
        try {
            $stmt = $pdo->query("SELECT username, fname, pasword FROM pili");
            $results = $stmt->fetchAll();

            if ($results) {
                echo "<table>";
                echo "<tr><th>Username</th><th>Full Name</th><th>Password</th></tr>";
                foreach ($results as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['fname']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['pasword']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='text-align:center;'>Hakuna data kwenye database.</p>";
            }
        } catch (\PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    ?>
</body>
</html>