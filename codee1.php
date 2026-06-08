<?php
// 1. DATABASE CONNECTION SETTINGS
$host = 'localhost';
$db   = 'school'; 
$user = 'root';      
$pass = '';          
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$message = "";
$onyesha_dashboard = false;
$jina_la_mtu = "";

// Angalia kama mtumiaji tayari ameshalogin kupitia URL (Njia mbadala ya Session)
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $onyesha_dashboard = true;
    $jina_la_mtu = isset($_GET['user']) ? htmlspecialchars($_GET['user']) : "Mtumiaji";
}

// 2. LOGIC YA KULOGIN
if (isset($_POST['login_submit'])){
    $login_user = trim($_POST['login_username']);
    $login_pass = trim($_POST['login_passs']);
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM student WHERE username = :uname AND pasword = :upass");
        $stmt->execute([
            ':uname' => $login_user,
            ':upass' => $login_pass
        ]);
        $user_row = $stmt->fetch();
        
        if ($user_row) {
            // Hapa tunampeleka moja kwa moja kwenye dashboard kwa nguvu bila kutumia Session zilizogama!
            header("Location: index.php?status=success&user=" . urlencode($user_row['fname']));
            exit();
        } else {
            $message = "<p style='color: #ff3333; text-align:center; font-size:20px; font-weight:bold; margin-top:20px;'>Login Failed! Wrong Username or Password ❌</p>";
        }
    } catch (\PDOException $e) {
        $message = "<p style='color:red; text-align:center;'>Error: " . $e->getMessage() . "</p>";
    }
}

// 3. LOGIC YA KUSAJILI MWANAFUNZI
if (isset($_POST['submit'])){
    $u_name = trim($_POST['username']);
    $u_pass = trim($_POST['passs']); 
    $f_name = trim($_POST['fname']);
    
    $sql = "INSERT INTO student (username, pasword, fname) VALUES (:uname, :upass, :fname)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uname' => $u_name,
            ':upass' => $u_pass,
            ':fname' => $f_name
        ]);
        // Baada ya kusajili, tunamrudisha huku akiwa bado yupo ndani
        echo "<script>alert('Mwanafunzi amesajiliwa kikamilifu!'); window.location.href='index.php?status=success';</script>";
        exit();
    } catch (\PDOException $e) {
        $message = "<p style='color:red; text-align:center;'>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>School System</title>
    <style>
        body {
         background-color: gray; 
         font-family: Arial, sans-serif;
          color: white; 4
          padding: 20px;
         }
        .center-box { 
            display: flex; justify-content: center; align-items: center; margin-top: 60px; }
        .form-box { background-color: #444; padding: 30px; border-radius: 8px; box-shadow: 0px 4px 15px rgba(0,0,0,0.3); width: 350px; text-align: center; }
        h2 { margin-top: 0; border-bottom: 2px solid white; padding-bottom: 10px; }
        label { font-size: 18px; display: block; margin-bottom: 8px; text-align: left; }
        input { margin-bottom: 20px; padding: 8px; font-size: 16px; width: 93%; border-radius: 4px; border: 1px solid #ccc; color: black; }
        button { padding: 10px 20px; border: 1px solid #ccc; font-weight: bold; width: 100%; border-radius: 4px; font-size: 16px; cursor: pointer; }
        button:hover { background-color: blue; color: white; }
        .logout-btn { background-color: red; color: white; padding: 5px 15px; text-decoration: none; float: right; border-radius: 4px; font-weight: bold; }
        table { margin-top: 25px; border-collapse: collapse; width: 70%; margin-left: auto; margin-right: auto; background-color: white; }
        th, td { border: 1px solid black; padding: 10px; text-align: center; color: black; }
        th { background-color: #e3e3e3; }
    </style>
</head>
<body>

    <?php echo $message; ?>

    <?php if ($onyesha_dashboard === false): ?>
        
        <div class="center-box">
            <div class="form-box">
                <h2>LOGIN TO SYSTEM</h2>
                <form method="POST" action="">
                    <label>User Name:</label>
                    <input type="text" name="login_username" required>
                    
                    <label>Password:</label>
                    <input type="password" name="login_passs" required>
                    
                    <button type="submit" name="login_submit" style="background-color: #008CBA; color: white;">LOGIN</button>
                </form>
            </div>
        </div>

    <?php else: ?>
        
        <div style="overflow: hidden; padding-bottom: 20px;">
            <span style="font-size: 20px; font-weight: bold; color: lightgreen;">
                Welcome, <?php echo $jina_la_mtu; ?>!
            </span>
            <a href="index.php" class="logout-btn">LOGOUT</a>
        </div>

        <div class="center-box" style="margin-top: 10px;">
            <div class="form-box">
                <h2>REGISTER STUDENT</h2>
                <form method="POST" action="">
                    <label>User Name:</label>
                    <input type="text" name="username" required>
                    
                    <label>Password:</label>
                    <input type="password" name="passs" required>
                    
                    <label>Full Name:</label>
                    <input type="text" name="fname" required>
                    
                    <button type="submit" name="submit">REGISTER NOW!</button>
                </form>
                
                <form method="POST" action="" style="margin-top: 15px;">
                    <button type="submit" name="view_data" style="background-color: #222; color: white;">VIEW ALL DATA</button>
                </form>
            </div>
        </div>

        <?php
        if (isset($_POST['view_data'])) {
            try {
                $stmt = $pdo->query("SELECT username, fname, pasword FROM student");
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
                    echo "<p style='text-align:center; color:white; font-size:20px;'>Hakuna data.</p>";
                }
            } catch (\PDOException $e) {
                echo "<p style='color:red; text-align:center;'>Error: " . $e->getMessage() . "</p>";
            }
        }
        ?>

    <?php endif; ?>

</body>
</html>
