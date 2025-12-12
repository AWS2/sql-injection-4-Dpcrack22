<html>
<head>
    <title>SQL Injection - Secure Version</title>
    <style>
        .user {
            background-color: yellow;
        }
    </style>
</head>

<body>
    <h1>PDO Seguro contra SQL Injection</h1>

    <?php
    if (isset($_POST["user"])) {

        $dbhost = $_ENV["DB_HOST"];
        $dbname = $_ENV["DB_NAME"];
        $dbuser = $_ENV["DB_USER"];
        $dbpass = $_ENV["DB_PASSWORD"];

        try {
            // Conexión segura con manejo de errores
            $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8",
                           $dbuser, $dbpass,
                           [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (PDOException $e) {
            die("Error en la conexión a la base de datos: " . $e->getMessage());
        }

        // Recogemos valores del formulario
        $username = $_POST["user"];
        $pass = $_POST["password"];

        // Consulta segura con parámetros
        $qstr = "SELECT * FROM users WHERE name = :username AND password = SHA2(:password, 512)";
        $consulta = $pdo->prepare($qstr);

        // 🔐 Vincular parámetros del formulario
        $consulta->bindParam(':username', $username, PDO::PARAM_STR);
        $consulta->bindParam(':password', $pass, PDO::PARAM_STR);

        // Ejecutar consulta
        $consulta->execute();

        // **Solo para debug** (Puedes comentarlo después)
        echo "<br>$qstr<br>";

        // Comprobamos si existe usuario
        if ($consulta->rowCount() >= 1) {
            foreach ($consulta as $user) {
                echo "<div class='user'>Hola " .
                    htmlspecialchars($user["name"]) .
                    " (" . htmlspecialchars($user["role"]) . ").</div>";
            }
        } else {
            echo "<div class='user'>No hi ha cap usuari amb aquest nom i contrasenya.</div>";
        }
    }
    ?>

    <fieldset>
        <legend>Login form</legend>
        <form method="post">
            User: <input type="text" name="user" /><br>
            Pass: <input type="password" name="password" /><br>
            <input type="submit" value="Login" /><br>
        </form>
    </fieldset>

</body>

</html>
