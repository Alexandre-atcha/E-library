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

$q = trim($_GET['q'] ?? '');
$author = trim($_GET['auteur'] ?? '');


if ($q === '' && $author === '') {
    $message = "Veuillez saisir un titre et/ou un auteur pour lancer la recherche.";
    $books = [];
} else {
    if ($q !== '' && $author !== '') {
        $sql = "SELECT id, titre, auteur, description, maison_edition FROM livres WHERE titre LIKE ? AND auteur LIKE ? ORDER BY titre";
        $stmt = $conn->prepare($sql);
        $likeQ = "%$q%";
        $likeA = "%$author%";
        $stmt->bind_param("ss", $likeQ, $likeA);
    } elseif ($q !== '') {
        $sql = "SELECT id, titre, auteur, description, maison_edition FROM livres WHERE titre LIKE ? ORDER BY titre";
        $stmt = $conn->prepare($sql);
        $likeQ = "%$q%";
        $stmt->bind_param("s", $likeQ);
    } else {
        $sql = "SELECT id, titre, auteur, description, maison_edition FROM livres WHERE auteur LIKE ? ORDER BY titre";
        $stmt = $conn->prepare($sql);
        $likeA = "%$author%";
        $stmt->bind_param("s", $likeA);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $books = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (empty($books)) {
        $message = "Aucun livre trouvé pour votre recherche.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Résultats de Recherche - e-Library</title>
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

        .results-main {
            padding: 2rem 0;
            min-height: 60vh;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        .search-info {
            background: var(--light-bg);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .search-query {
            font-weight: 600;
            color: var(--primary-color);
        }

        .results-count {
            color: var(--text-light);
            margin-top: 0.5rem;
        }

        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .book-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 1px solid var(--border-color);
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(233, 30, 99, 0.15);
        }

        .book-image {
            width: 100%;
            height: 200px;
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
        }

        .book-content {
            padding: 1.5rem;
        }

        .book-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .book-author {
            color: var(--primary-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .book-publisher {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .book-description {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .book-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
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
            flex: 2;
        }

        .btn.primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn.secondary {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            flex: 1;
        }

        .btn.secondary:hover {
            background: var(--primary-light);
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            background: var(--light-bg);
            border-radius: 15px;
        }

        .no-results-icon {
            font-size: 4rem;
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        .no-results h3 {
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .no-results p {
            color: var(--text-light);
            margin-bottom: 2rem;
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

            .results-grid {
                grid-template-columns: 1fr;
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
                    <li><a href="results.php" class="active"><i class="fas fa-search"></i>Résultats</a></li>
                    <li><a href="wishlist.php"><i class="fas fa-bookmark"></i>Ma Liste</a></li>
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
                <li>Résultats de recherche</li>
            </ul>
        </div>
    </div>

    <main class="container results-main">
        <h1 class="page-title">Résultats de Votre Recherche</h1>
        
        <div class="search-info">
            <p><strong>Votre recherche :</strong> 
                <span class="search-query">
                    <?php 
                    if ($q && $author) {
                        echo "Titre: \"$q\" et Auteur: \"$author\"";
                    } elseif ($q) {
                        echo "Titre: \"$q\"";
                    } elseif ($author) {
                        echo "Auteur: \"$author\"";
                    } else {
                        echo "Aucun critère spécifié";
                    }
                    ?>
                </span>
            </p>
            <p class="results-count">
                <?php 
                if (empty($books)) {
                    echo "Aucun livre trouvé";
                } else {
                    echo count($books) . " livre(s) trouvé(s)";
                }
                ?>
            </p>
        </div>

        <?php if (!empty($message) && empty($books)): ?>
            <div class="no-results">
                <div class="no-results-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Aucun résultat trouvé</h3>
                <p><?php echo htmlspecialchars($message); ?></p>
                <a href="index.php" class="btn primary">
                    <i class="fas fa-arrow-left"></i>Retour à la recherche
                </a>
            </div>
        <?php elseif (!empty($books)): ?>
            <div class="results-grid">
                <?php foreach ($books as $book): ?>
                    <article class="book-card">
                        <div class="book-image">
                            <i class="fas fa-book-open fa-3x" style="color: #e91e63;"></i>
                        </div>
                        <div class="book-content">
                            <h3 class="book-title"><?php echo htmlspecialchars($book['titre']); ?></h3>
                            <p class="book-author">
                                <i class="fas fa-user-pen"></i>
                                <?php echo htmlspecialchars($book['auteur']); ?>
                            </p>
                            <p class="book-publisher">
                                <i class="fas fa-building"></i>
                                <?php echo htmlspecialchars($book['maison_edition'] ?? 'Éditeur non spécifié'); ?>
                            </p>
                            <p class="book-description">
                                <?php 
                                $description = $book['description'] ?? 'Description non disponible pour ce livre.';
                                echo htmlspecialchars(mb_strimwidth($description, 0, 120, '...'));
                                ?>
                            </p>
                            <div class="book-actions">
                                <a href="details.php?id=<?php echo (int)$book['id']; ?>" class="btn primary">
                                    <i class="fas fa-eye"></i>Voir détails
                                </a>
                                <a href="wishlist.php?action=add&book_id=<?php echo (int)$book['id']; ?>" class="btn secondary">
                                    <i class="fas fa-bookmark"></i>Ajouter
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
            const cards = document.querySelectorAll('.book-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.6s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
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