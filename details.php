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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Identifiant de livre invalide.");
}

$stmt = $conn->prepare("SELECT * FROM livres WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$stmt->close();

if (!$book) {
    die("Livre introuvable.");
}

$descHtml = '';
if (!empty($book['description'])) {
    $parts = preg_split("/\r\n\r\n|\n\n|\r\r/", trim($book['description']));
    foreach ($parts as $p) {
        $descHtml.= '<p>'.nl2br(htmlspecialchars(trim($p))).'</p>';
    }
} else {
    $descHtml = '<p class="no-description">Aucune description disponible pour ce livre.</p>';
}

$in_wishlist = false;
$wishlist_stmt = $conn->prepare("SELECT id FROM liste_lecture WHERE id_livre = ? AND id_lecteur = ? AND date_retour IS NULL");
$wishlist_stmt->bind_param("ii", $id, $_SESSION['user_id']);
$wishlist_stmt->execute();
$wishlist_stmt->store_result();
if ($wishlist_stmt->num_rows > 0) {
    $in_wishlist = true;
}
$wishlist_stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($book['titre']); ?> - e-Library</title>
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
            border-radius: 50%;
            margin-right: 10px;
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

        .details-main {
            padding: 2rem 0;
            min-height: 70vh;
        }

        .book-details {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        @media (max-width: 768px) {
            .book-details {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }

        .book-cover {
            background: var(--light-gray);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .book-cover-placeholder {
            text-align: center;
            color: var(--text-light);
        }

        .book-cover-placeholder i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .book-info {
            padding: 1rem 0;
        }

        .book-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .book-author {
            font-size: 1.3rem;
            color: var(--primary-color);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .book-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--light-bg);
            border-radius: 10px;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .meta-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        .meta-value {
            color: var(--text-light);
        }

        .availability {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .available {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .unavailable {
            background: #ffebee;
            color: #c62828;
        }

        .book-description {
            margin-bottom: 2rem;
        }

        .description-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        .description-content {
            color: var(--text-light);
            line-height: 1.8;
        }

        .description-content p {
            margin-bottom: 1rem;
        }

        .no-description {
            font-style: italic;
            color: var(--text-light);
        }

        .book-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1rem;
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

        .btn.secondary {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn.secondary:hover {
            background: var(--primary-light);
        }

        .btn.success {
            background: #4caf50;
            color: var(--white);
        }

        .btn.success:hover {
            background: #388e3c;
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .related-section {
            margin-top: 4rem;
            padding-top: 3rem;
            border-top: 1px solid var(--border-color);
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: var(--text-dark);
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .related-book {
            background: var(--white);
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 1px solid var(--border-color);
        }

        .related-book:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(233, 30, 99, 0.15);
        }

        .related-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .related-author {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 1rem;
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

            .book-title {
                font-size: 2rem;
            }

            .book-actions {
                flex-direction: column;
            }

            .container {
                padding: 0 1rem;
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
                <div class="brand-logo" style="background: var(--primary-color); display: flex; align-items: center; justify-content: center; color: white;">
                    <i class="fas fa-book"></i>
                </div>
                <span class="brand-name">e-Library</span>
            </a>

            <nav>
                <ul class="nav-links">
                    <li><a href="index.php"><i class="fas fa-home"></i>Accueil</a></li>
                    <li><a href="results.php"><i class="fas fa-search"></i>Résultats</a></li>
                    <li><a href="wishlist.php"><i class="fas fa-bookmark"></i>Ma Liste</a></li>
                    <li><a href="#"><i class="fas fa-info-circle"></i>Aide</a></li>
                </ul>
            </nav>

            <div class="user-menu">
                <span class="user-welcome">Bonjour, <?php echo htmlspecialchars($_SESSION['nom'] ?? 'Utilisateur'); ?></span>
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
                <li><a href="results.php">Recherche</a></li>
                <li class="separator">/</li>
                <li>Détails du livre</li>
            </ul>
        </div>
    </div>

    <main class="container details-main">
        <div class="book-details">
            <div class="book-cover">
                <div class="book-cover-placeholder">
                    <i class="fas fa-book-open"></i>
                    <p>Couverture du livre</p>
                    <small>Image à remplacer</small>
                </div>
            </div>

            <div class="book-info">
                <h1 class="book-title"><?php echo htmlspecialchars($book['titre']); ?></h1>
                
                <div class="book-author">
                    <i class="fas fa-user-pen"></i>
                    <?php echo htmlspecialchars($book['auteur']); ?>
                </div>

                <div class="book-meta">
                    <div class="meta-item">
                        <span class="meta-label">Maison d'édition</span>
                        <span class="meta-value"><?php echo htmlspecialchars($book['maison_edition'] ?? 'Non spécifiée'); ?></span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Exemplaires disponibles</span>
                        <span class="meta-value"><?php echo (int)$book['nombre_exemplaire']; ?></span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Disponibilité</span>
                        <span class="availability <?php echo ($book['nombre_exemplaire'] > 0) ? 'available' : 'unavailable'; ?>">
                            <i class="fas <?php echo ($book['nombre_exemplaire'] > 0) ? 'fa-check' : 'fa-times'; ?>"></i>
                            <?php echo ($book['nombre_exemplaire'] > 0) ? 'Disponible' : 'Indisponible'; ?>
                        </span>
                    </div>
                </div>

                <div class="book-description">
                    <h3 class="description-title">Description</h3>
                    <div class="description-content">
                        <?php echo $descHtml; ?>
                    </div>
                </div>

                <div class="book-actions">
                    <?php if ($in_wishlist): ?>
                        <button class="btn success" disabled>
                            <i class="fas fa-check"></i>Déjà dans votre liste
                        </button>
                    <?php else: ?>
                        <a href="wishlist.php?action=add&book_id=<?php echo $id; ?>" class="btn primary">
                            <i class="fas fa-bookmark"></i>Ajouter à ma liste de lecture
                        </a>
                    <?php endif; ?>
                    
                    <a href="results.php" class="btn secondary">
                        <i class="fas fa-arrow-left"></i>Retour aux résultats
                    </a>
                    
                    <button class="btn secondary" onclick="window.print()">
                        <i class="fas fa-print"></i>Imprimer
                    </button>
                </div>
            </div>
        </div>

        <div class="related-section">
            <h2 class="section-title">Livres Similaires</h2>
            <div class="related-grid">
                <div class="related-book">
                    <h4 class="related-title">CSS Avancé et Moderne</h4>
                    <p class="related-author">Pierre Martin</p>
                    <a href="details.php?id=3" class="btn primary" style="padding: 8px 16px; font-size: 0.9rem; width: 100%;">
                        Voir détails
                    </a>
                </div>
                
                <div class="related-book">
                    <h4 class="related-title">PHP et Bases de Données</h4>
                    <p class="related-author">Alexandre Tech</p>
                    <a href="details.php?id=4" class="btn primary" style="padding: 8px 16px; font-size: 0.9rem; width: 100%;">
                        Voir détails
                    </a>
                </div>
                
                <div class="related-book">
                    <h4 class="related-title">JavaScript Contemporain</h4>
                    <p class="related-author">Sarah Dev</p>
                    <a href="details.php?id=5" class="btn primary" style="padding: 8px 16px; font-size: 0.9rem; width: 100%;">
                        Voir détails
                    </a>
                </div>
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
                            <a href="https://youtube.com/@AlexandreSteeven"><i class="fab fa-youtube"></i></a></div>
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
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.book-cover, .book-info, .related-book');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.6s ease';
                
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
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