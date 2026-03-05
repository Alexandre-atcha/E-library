<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$host = 'sql110.infinityfree.com';
$user = 'if0_41313525';
$pass = 'Infinity2026';
$db = 'if0_41313525_elearning_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erreur connexion à la base de données: ".$conn->connect_error);
$conn->set_charset("utf8mb4");

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

$action = $_GET['action'] ?? '';
$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;

if ($action === 'add' && $book_id > 0) {
    $stmt = $conn->prepare("SELECT id FROM livres WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $check_stmt = $conn->prepare("SELECT id_livre FROM liste_lecture WHERE id_livre = ? AND id_lecteur = ? AND date_retour IS NULL");
        $check_stmt->bind_param("ii", $book_id, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            $insert_stmt = $conn->prepare("INSERT INTO liste_lecture (id_livre, id_lecteur, date_emprunt) VALUES (?, ?, CURDATE())");
            $insert_stmt->bind_param("ii", $book_id, $user_id);
            
            if ($insert_stmt->execute()) {
                $success_message = "Livre ajouté à votre liste de lecture avec succès!";
            } else {
                $error_message = "Erreur lors de l'ajout du livre: " . $conn->error;
            }
            $insert_stmt->close();
        } else {
            $error_message = "Ce livre est déjà dans votre liste de lecture!";
        }
        $check_stmt->close();
    } else {
        $error_message = "Livre introuvable!";
    }
    $stmt->close();
    
} elseif ($action === 'remove' && $book_id > 0) {
    $stmt = $conn->prepare("UPDATE liste_lecture SET date_retour = CURDATE() WHERE id_livre = ? AND id_lecteur = ? AND date_retour IS NULL");
    $stmt->bind_param("ii", $book_id, $user_id);
    
    if ($stmt->execute()) {
        $success_message = "Livre retiré de votre liste de lecture avec succès!";
    } else {
        $error_message = "Erreur lors du retrait du livre: " . $conn->error;
    }
    $stmt->close();
}

$current_loans = [];
$stmt_current = $conn->prepare("
    SELECT l.id, l.titre, l.auteur, ll.date_emprunt 
    FROM liste_lecture ll 
    JOIN livres l ON ll.id_livre = l.id 
    WHERE ll.id_lecteur = ? AND ll.date_retour IS NULL 
    ORDER BY ll.date_emprunt DESC
");
$stmt_current->bind_param("i", $user_id);
$stmt_current->execute();
$result_current = $stmt_current->get_result();
while ($row = $result_current->fetch_assoc()) {
    $current_loans[] = $row;
}
$stmt_current->close();

$loan_history = [];
$stmt_history = $conn->prepare("
    SELECT l.id, l.titre, l.auteur, ll.date_emprunt, ll.date_retour 
    FROM liste_lecture ll 
    JOIN livres l ON ll.id_livre = l.id 
    WHERE ll.id_lecteur = ? 
    ORDER BY ll.date_emprunt DESC
");
$stmt_history->bind_param("i", $user_id);
$stmt_history->execute();
$result_history = $stmt_history->get_result();
while ($row = $result_history->fetch_assoc()) {
    $loan_history[] = $row;
}
$stmt_history->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ma Liste de Lecture - e-Library</title>
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
            --light-gray: #f5f5f5;
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
            background-color: var(--white);
            color: var(--text-dark);
            line-height: 1.6;
        }

      #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .preloader-content {
            width: 22%;
            height: 24%;
            text-align: center;
            border: 2px solid #ffffff;
            background: #ffffff;
            border-radius: 20px;
        }

        .preloader-spinner {
            justify-content: center;
            align-items: center;
            width: 50px;
            height: 50px;
            border: 4px solid #e0e0e0;
            background: #ffffff;
            border-top: 4px solid #e91e63;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto 15px;
        }

        .preloader-text {
            font-size: 24px;
            text-align: center;
            color: #e91e63;
            font-weight: 600;
        }

        .preloader-text-second {
            margin-top: 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg);}
            100% { transform: rotate(360deg);}
        }

        .main-content-loaded {
            opacity: 1;
        }


        .site-header {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .brand {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.5rem;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            border-radius: 50%;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .nav-links a.active {
            color: var(--primary-color);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-welcome {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* Breadcrumb */
        .breadcrumb {
            background: var(--light-bg);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }

        .breadcrumb-links {
            display: flex;
            list-style: none;
            gap: 0.5rem;
            font-size: 0.9rem;
            flex-wrap: wrap;
        }

        .breadcrumb-links a {
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb-links a:hover {
            color: var(--primary-color);
        }

        .breadcrumb-links .separator {
            color: var(--text-light);
        }

        .wishlist-main {
            padding: 2rem 0;
            min-height: 70vh;
        }

        .page-title {
            font-family: 'Courier New', Courier, monospace;
            font-size: 2.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1565c0;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #1565c0;
            font-family: Georgia, 'Times New Roman', Times, serif;
        }

        .message {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
        }

        .message.success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .message.error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .table-container {
            background: var(--white);
            border-radius: 10px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 3rem;
            border: 1px solid var(--border-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background: var(--light-bg);
            font-weight: 600;
            color: var(--text-dark);
        }

        tr:hover {
            background: var(--light-bg);
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn.primary {
            background: var(--primary-color);
            color: var(--white);
        }

        .btn.primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn.danger {
            background: #f44336;
            color: var(--white);
        }

        .btn.danger:hover {
            background: #d32f2f;
            transform: translateY(-2px);
        }

        .btn.secondary {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn.secondary:hover {
            background: var(--primary-light);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .no-books {
            text-align: center;
            padding: 3rem;
            background: var(--light-bg);
            border-radius: 15px;
            color: var(--text-light);
        }

        .no-books i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--primary-light);
        }

        .history-section {
            margin-top: 3rem;
            padding-top: 3rem;
            border-top: 1px solid var(--border-color);
        }

        .history-toggle {
            margin-bottom: 1.5rem;
        }

        .hidden {
            display: none;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-current {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-returned {
            background: #e3f2fd;
            color: #1565c0;
        }

        .site-footer {
            background: var(--text-dark);
            color: var(--white);
            padding: 3rem 0 1rem;
            margin-top: 4rem;
        }

        .footer-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            margin-bottom: 1rem;
            color: var(--white);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--primary-light);
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            color: var(--white);
            font-size: 1.2rem;
            transition: var(--transition);
        }

        .social-links a:hover {
            color: var(--primary-light);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #444;
            color: #ccc;
            font-size: 0.9rem;
        }

        .footer-bottom a {
            color: var(--primary-light);
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .header-inner {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                gap: 1rem;
            }

            table {
                display: block;
                overflow-x: auto;
            }

            .container {
                padding: 0 1rem;
            }

            .btn {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body>

    <div id="preloader">
        <div class="preloader-content">
            <div class="preloader-spinner"></div>
            <div class="preloader-text">Chargement en cours...</div>
            <div class="preloader-text-second">Veuillez patienter</div>
        </div>
    </div>
    

    <div class="main-content">
    <header class="site-header">
        <div class="container header-inner">
            <a href="index.php" class="brand">
                <div class="brand-logo">
                    <i class="fas fa-book"></i>
                </div>
                <span class="brand-name">e-Library</span>
            </a>

            <nav>
                <ul class="nav-links">
                    <li><a href="index.php"><i class="fas fa-home"></i>Accueil</a></li>
                    <li><a href="results.php"><i class="fas fa-search"></i>Résultats</a></li>
                    <li><a href="wishlist.php" class="active"><i class="fas fa-bookmark"></i>Ma Liste</a></li>
                    <li><a href="#"><i class="fas fa-info-circle"></i>Aide</a></li>
                </ul>
            </nav>

            <div class="user-menu">
                <a href="auth.php?logout=true" class="btn primary" style="padding: 8px 15px; font-size: 0.9rem;">
                    <i class="fas fa-sign-out-alt"></i>Déconnexion
                </a>
            </div>
        </div>
    </header>

    <div class="breadcrumb">
        <div class="container">
            <ul class="breadcrumb-links">
                <li><a href="index.php">Accueil</a></li>
                <li class="separator">/</li>
                <li>Ma Liste de Lecture</li>
            </ul>
        </div>
    </div>

    <main class="container wishlist-main">
        <h1 class="page-title">Ma Liste de Lecture</h1>

        <?php if ($success_message): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="current-loans">
            <h2 class="section-title">Livres Actuellement Empruntés</h2>
            <?php if (empty($current_loans)): ?>
                <div class="no-books">
                    <i class="fas fa-book-open"></i>
                    <p>Vous n'avez aucun livre emprunté pour le moment.</p>
                    <a href="index.php" class="btn primary" style="margin-top: 1rem;">
                        <i class="fas fa-search"></i>Découvrir des livres
                    </a>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Date d'emprunt</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($current_loans as $loan): ?>
                                <tr>
                                    <td><?php echo $loan['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($loan['titre']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($loan['auteur']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($loan['date_emprunt'])); ?></td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                            <button class="btn primary" onclick="openReader(<?php echo $loan['id']; ?>)">
                                                <i class="fas fa-eye"></i>Voir
                                            </button>
                                            <a href="wishlist.php?action=remove&book_id=<?php echo $loan['id']; ?>" class="btn danger">
                                                <i class="fas fa-trash"></i>Supprimer
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="history-section">
            <div class="history-toggle">
                <button id="toggleHistory" class="btn secondary">
                    <i class="fas fa-history"></i>Voir mon historique d'emprunts
                </button>
            </div>
            <div id="historyContent" class="hidden">
                <h2 class="section-title">Historique des Emprunts</h2>
                <?php if (empty($loan_history)): ?>
                    <div class="no-books">
                        <i class="fas fa-history"></i>
                        <p>Vous n'avez aucun historique d'emprunt.</p>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titre</th>
                                    <th>Auteur</th>
                                    <th>Date d'emprunt</th>
                                    <th>Date de retour</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($loan_history as $history): ?>
                                    <tr>
                                        <td><?php echo $history['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($history['titre']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($history['auteur']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($history['date_emprunt'])); ?></td>
                                        <td>
                                            <?php if ($history['date_retour']): ?>
                                                <?php echo date('d/m/Y', strtotime($history['date_retour'])); ?>
                                            <?php else: ?>
                                                <em>En cours</em>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($history['date_retour']): ?>
                                                <span class="status-badge status-returned">Retourné</span>
                                            <?php else: ?>
                                                <span class="status-badge status-current">En cours</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-container">
                <div class="footer-section">
                    <h3>Notre Bibliothèque</h3>
                    <p>La plus grande bibliothèque numérique de la région, offrant un accès à des milliers d'ouvrages.</p>
                    <div class="social-links">
                         <a href="https://www.facebook.com/alexandre.atcha12"><i class="fab fa-facebook"></i></a>
                            <a href="https://x.com/steeven10082?t=oRfwpQs26sAiAED5C6_BOw&s=09"><i class="fab fa-twitter"></i></a>
                            <a href="https://wwww.instagram.com/alexandreatcha?igsh=MXJ2NnI4MGN1dnVweg=="><i class="fab fa-instagram"></i></a>
                            <a href="https://www.linkedin.com/in/alexandre-atcha-8a643936a?utm_source=share&utm_compaign=share_via&utm_content=profile&utm_medium=android_app"><i class="fab fa-linkedin"></i></a>
                            <a href="https://youtube.com/@AlexandreSteeven"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3>Liens Rapides</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="results.php">Recherche</a></li>
                        <li><a href="wishlist.php">Ma Liste de Lecture</a></li>
                        <li><a href="#">Guide d'Utilisation</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Informations</h3>
                    <ul class="footer-links">
                        <li><a href="#">Conditions d'utilisation</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                        <li><a href="#">Mentions légales</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Nous contacter</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 e-Library. Tous droits réservés. | Site réalisé par <a href="https://votreportfolio.com" target="_blank">Alexandre ATCHA</a></p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('toggleHistory').addEventListener('click', function() {
            const historyContent = document.getElementById('historyContent');
            const isHidden = historyContent.classList.contains('hidden');
            
            if (isHidden) {
                historyContent.classList.remove('hidden');
                this.innerHTML = '<i class="fas fa-history"></i>Masquer mon historique d\'emprunts';
            } else {
                historyContent.classList.add('hidden');
                this.innerHTML = '<i class="fas fa-history"></i>Voir mon historique d\'emprunts';
            }
        });

        function openReader(bookId) {
            alert('Ouverture du lecteur pour le livre ID: ' + bookId + '\n\nCette fonctionnalité ouvrirait normalement le PDF dans une application de lecture intégrée.');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                row.style.transition = 'all 0.5s ease';
                
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateX(0)';
                }, 100 * index);
            });
        });


         // Progress
            document.addEventListener('DOMContentLoaded', function() {
                const preloader = document.getElementById('preloader');
                const mainContent = document.querySelector('.main-content');
                const startTime = Date.now();
                const minDisplayTime = 3000;

                function hidePreloader() {
                    const elapsedTime = Date.now() - startTime;
                    const remainingTime = Math.max(0, minDisplayTime - elapsedTime);

                    setTimeout(function() {
                        preloader.style.opacity = '0';
                        mainContent.classList.add('loaded');

                        setTimeout(function() {
                            preloader.style.display = 'none';
                        }, 500);
                    }, remainingTime);
                }

                hidePreloader();
                setTimeout(hidePreloader, 6000);
            });

            // Afficher le preloader au clic sur les liens externes
            document.addEventListener('DOMContentLoaded', function() {
                const links = document.querySelectorAll('a[href*=".php"], a[href*=".html"]');

                links.forEach(link => {
                    link.addEventListener('click', function(e) {
                        if (!this.getAttribute('href').startsWith('#')) {
                            e.preventDefault();
                            const targetUrl = this.getAttribute('href');

                            const preloader = document.getElementById('preloader');
                            const mainContent = document.querySelector('.main-content');

                            preloader.style.display = 'flex';
                            preloader.style.opacity = '1';
                            mainContent.classList.remove('loaded');

                            // Rediriger après un court délai pour voir l'animation
                            setTimeout(function() {
                                window.location.href = targetUrl;
                            }, 500);
                       }
                    });
                });
            });
    </script>
    </div>
</body>
</html>
<?php $conn->close(); ?>