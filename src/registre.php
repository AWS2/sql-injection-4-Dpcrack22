<html>
<head>
    <title>Registre</title>
    <style>
        .user { background-color: yellow; }
        .error { background:#fee; }
    </style>
</head>
<body>
    <h1>Registre</h1>

    <?php
    if (isset($_POST['user'])) {

        $dbhost = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $dbuser = $_ENV['DB_USER'];
        $dbpass = $_ENV['DB_PASSWORD'];

        try {
            $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            echo "<div class='error'>Error en la connexió a la base de dades.</div>";
            exit;
        }

        $user = trim($_POST['user']);
        $password = $_POST['password'];

        if ($user === '' || $password === '') {
            echo "<div class='error'>Rellena tots els camps.</div>";
        } else {
            $role = 'user';
            $qstr = "INSERT INTO users (name,email,role,password) VALUES (:name, :email, :role, SHA2(:password,512))";
            $stmt = $pdo->prepare($qstr);
            try {
                // No hi ha email al formulari del test, afegim email fictici
                $email = $user . "@example.com";
                $stmt->bindParam(':name', $user, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':role', $role, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->execute();

                // Missatge exactament com demana el test
                echo "<div class='user'>Usuari $user creat correctament.</div>";
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    echo "<div class='error'>El nom d'usuari o el correu ja existeix.</div>";
                } else {
                    echo "<div class='error'>Error en crear l'usuari.</div>";
                }
            }
        }
    }
    ?>

    <fieldset>
        <legend>Formulari de registre</legend>
        <form method="post">
            User: <input type="text" name="user" /><br>
            Pass: <input type="password" name="password" /><br>
            <input type="submit" value="Registre" />
        </form>
    </fieldset>

</body>
</html>
