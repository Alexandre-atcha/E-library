<?php
session_start();

$host = 'sql110.infinityfree.com';
$user = 'if0_41313525';
$pass = 'Infinity2026';
$db = 'if0_41313525_elearning_db';


try {
    $mysqli = new mysqli($host, $user, $pass, $db);
    
    if ($mysqli->connect_error) {
        throw new Exception('Erreur de connexion MySQL: ' . $mysqli->connect_error);
    }
} catch (Exception $e) {
    $mysql_error = $e->getMessage();
    $mysqli = null;
}

$action = $_POST['action'] ?? '';
$error_message = '';
$success_message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'register') {
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($nom && $prenom && $email) {
            if (!$mysqli) {
                $error_message = "Erreur de base de données. Le système est temporairement indisponible.";
            } else {
                $stmt = $mysqli->prepare("SELECT id FROM lecteurs WHERE email=? LIMIT 1");
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error_message = "Un compte existe déjà avec cet email.";
                } else {
                    $stmt = $mysqli->prepare("INSERT INTO lecteurs (nom, prenom, email) VALUES (?,?,?)");
                    $stmt->bind_param('sss', $nom, $prenom, $email);
                    if ($stmt->execute()) {
                        $success_message = "Compte créé avec succès! Redirection...";
                        echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 2000);</script>";
                    } else {
                        $error_message = "Une erreur est survenue : ".$mysqli->error;
                    }
                }
            }
        } else {
            $error_message = "Tous les champs sont requis.";
        }
    }
    elseif ($action === 'login') {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($nom && $email) {
            if (!$mysqli) {
                $error_message = "Erreur de base de données : " . $mysql_error;
            } else {
                $stmt = $mysqli->prepare("SELECT id, nom, email FROM lecteurs WHERE nom=? AND email=? LIMIT 1");
                $stmt->bind_param('ss', $nom, $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($id, $nom, $email);
                    $stmt->fetch();
                    $_SESSION['user_id'] = $id;
                    $_SESSION['nom'] = $nom;
                    $_SESSION['email'] = $email;

                    header('Location: index.php');
                    exit;
                } else {
                    $error_message = "Identifiants invalides.";
                }
            }
        } else {
            $error_message = "Tous les champs sont requis.";
        }
    }
}

if (isset($mysql_error)) {
    $system_error = "<!-- Erreur système: " . htmlspecialchars($mysql_error) . " -->";
} else {
    $system_error = "";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Connexion | e-Library</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root {
            --primary-color: #e91e63;
            --primary-light: #f8bbd9;
            --primary-dark: #c2185b;
            --white: #ffffff;
            --light-bg: #fdfafa;
            --text-dark: #333333;
            --text-light: #666666;
            --border-color: #e0e0e0;
            --shadow: 0 8px 25px rgba(233, 30, 99, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--white) 0%, var(--light-bg) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 450px;
        }

        .auth-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(233, 30, 99, 0.15);
        }

        .auth-brand {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 40px 30px;
            text-align: center;
        }

        .auth-brand h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .auth-brand p {
            font-size: 0.9rem;
            opacity: 0.9;
            font-weight: 300;
        }

        .auth-tabs {
            display: flex;
            background: var(--light-bg);
            border-bottom: 1px solid var(--border-color);
        }

        .auth-tab {
            flex: 1;
            padding: 18px;
            border: none;
            background: transparent;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: var(--text-light);
        }

        .auth-tab.active {
            background: var(--white);
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
        }

        .auth-tab:hover {
            color: var(--primary-color);
        }

        .auth-form {
            padding: 30px;
            display: none;
        }

        .auth-form.show {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .field-group {
            margin-bottom: 20px;
        }

        .field-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            transition: var(--transition);
            background: var(--white);
        }

        input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.1);
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn.primary {
            background: var(--primary-color);
            color: var(--white);
        }

        .btn.primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .auth-actions {
            margin-top: 25px;
        }

        .link {
            display: inline-block;
            margin-top: 15px;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .link:hover {
            text-decoration: underline;
        }

        .auth-note {
            text-align: center;
            padding: 20px 30px;
            border-top: 1px solid var(--border-color);
            color: var(--text-light);
            font-size: 0.8rem;
            background: var(--light-bg);
        }

        .message {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
        }

        .message.error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .message.success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        @media (max-width: 480px) {
            .auth-card {
                border-radius: 15px;
            }
            
            .auth-brand {
                padding: 30px 20px;
            }
            
            .auth-brand h1 {
                font-size: 1.7rem;
            }
            
            .field-grid {
                grid-template-columns: 1fr;
            }
            
            .auth-form {
                padding: 25px 20px;
            }
        }
    </style>
</head>

<body class="auth-body">
    <?php echo $system_error; ?>
    
    <main class="auth-container">
        <div class="auth-card">
            <div class="auth-brand">
                <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; border: 3px solid var(--white);">
                    <i class="fas fa-book-open fa-2x" style="color: white;"></i>
                </div>
                <h1>e-Library</h1>
                <p>Accédez à des centaines d'e-books. Créez un compte ou connectez-vous.</p>
            </div>

            <div class="auth-tabs">
                <button class="auth-tab active" data-tab="login"><i class="fa-solid fa-right-to-bracket"></i>Se connecter</button>
                <button class="auth-tab" data-tab="register"><i class="fa-solid fa-user-plus"></i>Créer un compte</button>
            </div>

            <?php if ($error_message): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <form action="auth.php" id="form-login" class="auth-form show" method="POST" novalidate>
                <input type="hidden" name="action" value="login">

                <div class="field-group">
                    <label for="login_nom">Nom</label>
                    <input type="text" id="login_nom" name="nom" placeholder="Votre nom" required value="<?php echo isset($_POST['nom']) && $action === 'login' ? htmlspecialchars($_POST['nom']) : ''; ?>">
                </div>

                <div class="field-group">
                    <label for="login_email">Email</label>
                    <input type="email" id="login_email" name="email" placeholder="email@example.com" required value="<?php echo isset($_POST['email']) && $action === 'login' ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="auth-actions">
                    <button type="submit" class="btn primary"><i class="fa-solid fa-right-to-bracket"></i>Se connecter</button>
                    <a href="#" class="link">Mot de passe oublié ?</a>
                </div>
            </form>

            <form action="auth.php" class="auth-form" id="form-register" method="POST" novalidate>
                <input type="hidden" name="action" value="register">
                
                <div class="field-grid">
                    <div class="field-group">
                        <label for="reg_nom">Nom</label>
                        <input type="text" id="reg_nom" name="nom" placeholder="Votre nom" required value="<?php echo isset($_POST['nom']) && $action === 'register' ? htmlspecialchars($_POST['nom']) : ''; ?>">
                    </div>

                    <div class="field-group">
                        <label for="reg_prenom">Prénom</label>
                        <input type="text" id="reg_prenom" name="prenom" placeholder="Votre prénom" required value="<?php echo isset($_POST['prenom']) && $action === 'register' ? htmlspecialchars($_POST['prenom']) : ''; ?>">
                    </div>
                </div>

                <div class="field-group">
                    <label for="reg_email">Email</label>
                    <input type="email" id="reg_email" name="email" placeholder="email@example.com" required value="<?php echo isset($_POST['email']) && $action === 'register' ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="auth-actions">
                    <button type="submit" class="btn primary"><i class="fa-solid fa-user-plus"></i>Créer mon compte</button>
                </div>
            </form>

            <p class="auth-note">En continuant, vous acceptez notre charte d'utilisation des e-books.</p>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.auth-tab');
            const forms = document.querySelectorAll('.auth-form');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');
                    
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    forms.forEach(form => {
                        form.classList.remove('show');
                        if (form.id === 'form-' + targetTab) {
                            form.classList.add('show');
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>