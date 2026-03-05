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

$result = $conn->query("SELECT COUNT(*) as count FROM livres");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $livres = [
        ["Le Petit Prince", "Antoine de Saint-Exupéry", "Le Petit Prince est un roman français. Livre à succès, il est l'oeuvre la plus connue d'Antoine de Saint-Exupéry. Publié en anglais le 6 avril 1943 à New York par l'éditeur Hitchcock. Paru en plus de six cents langues et dialectes différent.Le Petit Prince est l'ouvrage la plus traduit au monde après la Bible. Le narrateur est un aviateur qui, à la suite d'une panne moteur, doit se poser en catastrophe dans le  désert du sahara et tente seul de réparer son avion.", "Reynal & Hitchcock", 15],
        ["L'Etranger", "Albert Camus", "L'Etranger est le premier roman publié d'Albert Camus, paru en 1942. Les premières esquisses datent de 1938, mais le roman ne prend vraiment forme que dans les premiers mois de 1940 et sera travaillé par Camus jusqu'en 1941. Il prend place dans la tétralogie que Camus nommera Cycle de l'absurde qui décrit les fondements de la philosophie camusienne : l'absurde. Cette tétralogie comprend également l'essai. Le Mythe de Sisyphe ainsi que les pièces de théâtre Caligula et Le Malentendu", "Frémeaux & Associés", 7],
        ["1984", "George Orwell", "Novlangue, police de la pensée, Big Brother...Soixante-dix ans après la publication du roman d'anticipation de George Orwell, les concepts clés de 1984 sont devenus des références essentielles pour comprendre les ressorts totalitaires de la société actuelle. Dans un monde où la télésurveillance s'est généralisée, où la numérisation a donné un élan sans précédant au pouvoir et à l'arbitraire des administration. Où le passé tend à se dissoudre dans l'éternel présent de l'innovation, le cef Orwell est à redécouvrir dans une nouvelle traduction et une édition critique.", "Les éditions de rue Dorion", 15],
        ["Les Misérables", "Victor Hugo", "La foule des malheureux contre qui s'acharnent la mailice du sort et la malignité de l'homme, Hugo a su la peindre sous des aspects aussi atroces que grandiose dans des tableaux dont la plupart sont désormais classiques. C'est Jean Valjean, ce rude gaillard qui peut, d'un coup de reins, soulever une charrette embourbée, cet acrobate aux évasions spectaculaires, cet ancien forçat qui peut-être un saint...C'est Gavroche, le petit Parusuen gouailleur. Ce sont Cosette, la fillette martyre, Javert, le policier inflexible, des malheureux et des coquins : Les Misérables", "Gallimard", 11],
        ["Harry Potter à l'école des sociers", "J.K. Rowling", "Le début de ma saga Harry Potter. Voler en balai, jeter des sorts, combattre les trolls : Harry révèle de grands talents. Sorti à Londrs le 26 juin 1997, il est initialement tiré à 500 examplaires, puis connaît au fil des mois un succès grandissant. En 2001, le volume est adapté au cinéma. Il trouve son importance puisqu'il sert de base introductve aux tomes de la série qu'à la pièce de théâtre Harry et l'Enfant maudit", "Gallimard", 26],
        ["Le Petit Voyageur", "S. M'Baye", "Un court roman de voyage et de reflexion. L'auteur emmène le lecteur à travers des paysages et rencontrs qui interrogent l'identité et le sens du dépacement.", "Editions Soleil", 5],
        ["Initiation au JavaScript", "M. Koffi", "Guide pratique pou débuter en JavaScript. Comprend des exercices et des exemples pour construire des applications web interactives.", "TechPress", 8],
        ["Histoire de l'Afrique", "A. Touré", "Un panorama historique des grandes époques africaines. Le livre propose une synthèse accessible pour les étudiants et curieux.", "Anthologie", 3],
        ["Design web Moderne", "L. Saga", "Principes et pratique du design web comtemporain. Traite du responsive, de l'accessibilité et des bonnes pratiques UX/UI.", "WebLivre", 9],
        ["Algorithmes et structures", "P. N'Diaye", "Cours d'algorithmes pour informaticiens. Des exercices corrigés et des illustration complètent chaque chapitre.", "Université Press", 2],
        ["Economie pour tous", "F. Akpo", "Concepts économiques expliqués de manière simple et terre à terre. Idéal pour les débutants en économie, pour les chercheurs passionnés et por les citoyens curieux. Ce livre offre des exercices de tout type et des corrigés.", "EcoEditions", 7],
        ["Photographie créative", "R. Dossou", "Techniques et astuces pour photographes amateur. Beaucoup d'exemples pratiques et de projets à réaliser grâce à ce livre. Vous soyez un photograpes amateurs capables de faire des photos claires et attrayantes, des shotting, des photos de tout genre: photos d'identité, photo professionnelle etc; ce livre est pour vous. Il vous permet de franchir un étape dans les photos.", "ImagePress", 2],
        ["Programmation PHP avancée", "C. Mensah", "Approfondissement des concepts PHP php moderne. Sécurité, PDO, patterns et deploiement en production.", "DevBooks", 5],
        ["Gestion de projet Agile", "D. Kouassi", "Méthodes agiles pour gérer efficacement les projets. Exemples concrets en entreprise et sur des cas réels.", "ProManage", 6],
        ["Cuisine du Monde", "S. Adjovi", "Recettes et techniques culinaires internationales. Des plats simples aux recettes plus élaborées, illustrés par étape. ", "Gastronome", 3],
        ["Ces différences qui nous rassembles", "Daniel Henkel", "Dans cet ouvrage, avec franchise, humilité, humour et, je pense, bon sens, je m'éfforce de traiter sans faux-semblants les grands sujets qui concerne chacun, des thèmes au fil desquels une vérité s'impose: quelque part sur le chemin, nous avons abandonné l'essentiel, le contact humain.", "Plon", 19],
        ["Archictecture des systèmes", "B. Kouman", "Principes d'architecture pour systèmes distribués. Etudes de cas et bonne pratique pour concevoir des architectures robustes.", "UniPress", 11],
        ["Poèmes de la ville", "L. Sissoko", "Recueil de poèmes inspirés par la vie urbaine. Rythmes, images et émotions se mêlent pour peindre la cité.", "Rivages", 8],
        ["Blockchain expliquée", "N. Houngbédji", "Concepts et usages de la blockchain. Applications, smart-contrats et impact sur l'économie numérique.", "TechPress", 12],
        ["Apologie de Socrate", "Platon", "L'Appologie de Socrate est un récit du procès de Socrate à Athènes en 399 av. Jésus-Christ où il est accusé de corrompre la jeunesse et d'impiété. Le livre, qui est une défence de Socrate par lui même, dénonce les dangers de la mauvaise foi et de l'ignorance et défend la philosophie comme la seule quête digne d'être vécue. Le texte, bien qu'une plaidoirie, est considéré comme un texte fondateur de la philosophie.", "FLAMMARION", 22],
        ["Entrepreneuriat local", "K. Adé", "Creer et développer une PME locale. Conseils pratique, étude de marché et gestion financière.", "BusinssBooks", 7],
        ["Energies Renouvelables", "P. Zannou ", "Techniques et enjeux des éneergies propres. Analyses et solutions adaptées aux contexte locaux.", "GreenEditions", 4]
    ];
    
    $stmt = $conn->prepare("INSERT INTO livres (titre, auteur, description, maison_edition, nombre_exemplaire) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($livres as $livre) {
        $stmt->bind_param("ssssi", $livre[0], $livre[1], $livre[2], $livre[3], $livre[4]);
        if (!$stmt->execute()) {
            echo "Erreur lors de l'insertion".$stmt->error;
        }
    }
    
    $stmt->close();
}

$sql_books = "SELECT id, titre, auteur, description, maison_edition, nombre_exemplaire FROM livres ORDER BY titre";
$result_books = $conn->query($sql_books);
$all_books = [];
if ($result_books) {
    $all_books = $result_books->fetch_all(MYSQLI_ASSOC);
}

$sql_featured = "SELECT id, titre, auteur, description, maison_edition FROM livres LIMIT 12";
$result_featured = $conn->query($sql_featured);
$featured_books = [];
if ($result_featured) {
    $featured_books = $result_featured->fetch_all(MYSQLI_ASSOC);
}

$sql_purchase = "SELECT id, titre, auteur, description, maison_edition FROM livres LIMIT 19";
$result_purchase = $conn->query($sql_purchase);
$purchase_books = [];
if ($result_purchase) {
    $purchase_books = $result_purchase->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Accueil | e-Library</title>
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
            margin: 25px auto 15px;
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

        /* OVERLAY */
        #overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease, visibility 0.5s ease;
            z-index: 9998;
        }

        #overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* MODALE POLITIQUE */
        #privacy-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            width: 90%;
            max-width: 520px;
            background: rgba(20, 20, 20, 0.96); /* ✅ NOIR ÉLÉGANT */
            color: white;
            padding: 25px 25px 60px;
            border-radius: 16px;
            transform: translate(-150%, -50%);
            opacity: 0;
            visibility: hidden;
            transition: .5s ease;
            z-index: 9999;
        }

        #privacy-modal.active {
            transform: translate(-50%, -50%);
            opacity: 1;
            visibility: visible;
        }

        #privacy-modal h2 {
            margin-bottom: 10px;
            color: white;
        }

        #privacy-modal hr {
            margin: 10px 0 15px;
            border-color: rgba(255,255,255,0.2);
        }

        #privacy-modal p {
            font-size: 14px;
            color: #ddd;
            margin-bottom: 12px;
        }

        /*  BOUTON PETIT EN BAS À DROITE */
        #accept-privacy {
            position: absolute;
            bottom: 15px;
            right: 15px;
            width: auto;
            padding: 8px 14px;
            font-size: 13px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .privacy-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 22px;
            background: none;
            color: white;
            border: none;
            cursor: pointer;
        }

        /* BANNIÈRE COOKIES */
        #cookie-banner {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(20, 20, 20, 0.96); /* ✅ NOIR ÉLÉGANT */
            color: #ddd;
            max-width: 370px;
            padding: 15px;
            border-radius: 12px;
            z-index: 9999;
            display: none;
            font-size: 14px;
        }

        .cookie-h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #e91e63;
            font-weight: 500;
        }

        /* MOBILE */
        @media (max-width: 768px) {
            #cookie-banner {
                left: 0;
                right: 0;
                bottom: -100%;
                width: 100%;
                border-radius: 20px 20px 0 0;
                transition: bottom .4s ease;
            }

            #cookie-banner.show {
                bottom: 0;
            }
        }

        .cookie-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        #accept-cookies {
            background: var(--primary-color);
            color: white;
            border: none;
            flex: 1;
            padding: 10px;
            border-radius: 6px;
        }

        #reject-cookies {
            background: #444;
            color: white;
            border: none;
            flex: 1;
            padding: 10px;
            border-radius: 6px;
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

        .header-social-link {
            display: flex;
            gap: 0.0.8rem;
            margin-left: 2rem;
        }

        .header-social-link a {
            color: var(--text-light);
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .header-social-link a:hover {
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


        /* Hero Section - Version corrigée */
        .hero-section {
            color: var(--white);
            padding: 6rem 2rem 15rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); /* Fond de secours */
        }

        /* Conteneur pour toutes les images */
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
            z-index: 1;
        }

        /* Image active */
        .hero-bg.active {
            opacity: 1;
            z-index: 2;
        }

        /* Définition des images de fond avec position ajustée */
        .hero-bg-1 {
            background: url('images/mageé.jpg') center 10%/cover;
        }

        .hero-bg-2 {
            background: url('images/smiling.jpg') center/76%;
            filter: none;
        }

        .hero-bg-3 {
            background: url('images/woman.jpg') center/80%;
        }

        .hero-bg-4 {
            background: url('images/etudiants.jpg') center 10%/cover;
        }

        .hero-bg-5 {
            background: url('images/teacher-selecting.jpg') center 10%/cover;
        }

        /* Contenu texte */
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 10;
            padding: 2rem;
            border-radius: 15px;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            animation: fadeInUp 1s ease;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.7);
        }

        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            animation: fadeInUp 1s ease 0.2s both;
            color: white;
            text-shadow: 0 1px 5px rgba(0, 0, 0, 0.5);
            line-height: 1.6;
        }

        /* Bouton amélioré */
        .hero-section .btn.primary {
            background: rgba(233, 30, 99, 0.8) !important;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3) !important;
            padding: 10px 35px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }

        .hero-section .btn.primary:hover {
            background: rgba(194, 24, 91, 0.9) !important;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        }

        /* Indicateurs */
        .hero-indicators {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 12px;
            z-index: 10;
        }

        .hero-indicator {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid white;
            background: transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0;
        }

        .hero-indicator.active {
            background: white;
            transform: scale(1.3);
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .hero-indicator:hover {
            transform: scale(1.4);
            background: rgba(255, 255, 255, 0.5);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 992px) {
            .hero-section {
                padding: 4rem 1.5rem 6rem;
                min-height: 75vh;
            }
    
            .hero-title {
                font-size: 2.5rem;
            }
    
            .hero-subtitle {
                font-size: 1.1rem;
            }
    
            .hero-content {
                padding: 1.5rem;
                max-width: 90%;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 3rem 1rem 5rem;
                min-height: 70vh;
            }
    
            .hero-title {
                font-size: 2rem;
            }
    
            .hero-subtitle {
                font-size: 1rem;
            }
    
            .hero-content {
                padding: 1.2rem;
                width: 95%;
            }
    
            .hero-section .btn.primary {
                padding: 12px 25px;
                font-size: 1rem;
            }
    
            .hero-indicators {
                bottom: 20px;
                gap: 10px;
            }
    
            .hero-indicator {
                width: 12px;
                height: 12px;
            }
    
            /* Ajustement plus prononcé pour mobile */
            .hero-bg-1, .hero-bg-2, .hero-bg-3, .hero-bg-4, .hero-bg-5 {
                background-position: center 25%;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 1.8rem;
            }
    
            .hero-subtitle {
                font-size: 0.9rem;
            }
    
            .hero-section {
                min-height: 65vh;
            }
        }

        .about-section {
            padding: 4rem 2rem;
            background: var(--light-bg);
        }

        .about-container {
            max-width: 1000px;
            margin: 0 auto;
            text-align: center;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            color: var(--text-dark);
            font-size: 2.5rem;
        }

        .about-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text-light);
            margin-bottom: 2rem;
            text-align: left;
        }

        .about-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: var(--shadow);
            text-align: center;
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: var(--primary-color);
            font-size: 2rem;
        }
        
        .covers-section {
            padding: 4rem 2rem;
            background: var(--light-bg);
        }

        .covers-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 3rem;
            margin-top: 2rem;
        }

        .cover-item {
            text-align: center;
            transition: var(--transition);
        }

        .cover-item:hover {
           transform: translateY(-5px); 
        }

        .cover-img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: var(--shadow);
            margin-bottom: 0.8rem;
        }

        .cover-item h4 {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.3rem;
            font-size: 0.9rem;
            line-height: 1.3;
        }

        .cover-item p {
            color: var(--text-light);
            font-size: 0.8rem;
        }

        .cover-item a {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        @media (max-width: 1200px) {
            .covers-grid {
                grid-template-columns: (4, 1fr);
            }
        }

        @media (max-width: 992px) {
            .covers-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768) {
            .covers-grid {
                grid-template-columns: repeat(2; 1fr);
                gap: 1rem;
            }
            .cover-img {
                height: 180px;
            }
        }

        @media (max-width: 480px) {
            .covers-grid {
                grid-template-columns: 1fr;
            }
            .cover-img {
                height: 220px;
            }
        }

        .search-section {
            background: var(--light-bg);
            padding: 3rem 2rem;
        }

        .search-container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--white);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: var(--shadow);
        }

        .search-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--text-dark);
            font-size: 1.8rem;
        }

        .search-form {
            display: grid;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        .form-group select,
        .form-group input {
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.1);
        }

        .featured-section {
            padding: 4rem 2rem;
        }

        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .book-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
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
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .book-author {
            color: var(--text-light);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .book-description {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .purchase-section {
            background: var(--light-bg);
            padding: 4rem 2rem;
        }

        .purchase-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .purchase-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
        }

        .purchase-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(233, 30, 99, 0.15);
        }

        .purchase-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--primary-color);
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .purchase-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
        }

        .purchase-content {
            padding: 1.5rem;
        }

        .purchase-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .price-old {
            text-decoration: line-through;
            color: var(--text-light);
            font-size: 1rem;
            margin-left: 0.5rem;
        }

        .guide-section {
            padding: 4rem 2rem;
        }

        .guide-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .guide-step {
            text-align: center;
            padding: 2rem;
            background: var(--white);
            border-radius: 15px;
            box-shadow: var(--shadow);
        }

        .step-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: var(--primary-color);
            font-size: 2rem;
        }

        .step-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        .step-description {
            color: var(--text-light);
            font-size: 0.9rem;
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

        .site-footer {
            background: var(--text-dark);
            color: var(--white);
            padding: 3rem 0 1rem;
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
            display: grid;
            gap: 1.2rem;
            margin-top: 1rem;
            grid-template-columns: repeat(5, 1fr);
            width: 40px;
            height: 40px;
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

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .header-inner {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                gap: 1rem;
            }

            .hero-title {
                font-size: 2rem;
            }

            .books-grid,
            .covers-grid,
            .purchase-grid {
                grid-template-columns: 1fr;
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

    <!-- OVERLAY -->
    <div id="overlay"></div>

    <!-- MODALE POLITIQUE -->
        <div id="privacy-modal">
            <button class="privacy-close">&times;</button>
            <h2>Conditions d'utilisation</h2>
            <hr>

            <p>
            En appuyant sur <strong>« Accepter et continuer »</strong>, vous acceptez les
            Conditions Générales d'Utilisation du site e-Library et reconnaissez avoir pris
            connaissance de notre Politique de confidentialité.
            </p>

            <p>
            Nous collectons uniquement les données nécessaires à l'utilisation de nos services.
            Vous disposez d'un droit d'accès, de rectification, de suppression et d'opposition.
            </p>

            <p>
            Contact : <strong>alexandreatcha440@gmail.com</strong>
            </p>

            <button id="accept-privacy">Accepter et continuer</button>
        </div>

        <!-- BANNIÈRE COOKIES -->
        <div id="cookie-banner">
            <h3 class="cookie-h3">E-Library</h3>
            <p>
            Nous utilisons des cookies pour améliorer votre expérience et mesurer l'audience. Avec votre accord, nous et nos 76 partenaires utilisons des cookies ou technologies similaires pour stocker,
            consulter et traiter des données personnelles telles que votre visite sur ce site internet, les adresses IP et les identifiants de cookies.
            </p>

            <div class="cookie-actions">
                <button id="accept-cookies">Accepter tous</button>
                <button id="reject-cookies">Rejeter tous</button>
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
                        <li><a href="index.php" class="active"><i class="fas fa-home"></i>Accueil</a></li>
                        <li><a href="#featured"><i class="fas fa-star"></i>Livres Populaires</a></li>
                        <li><a href="#purchase"><i class="fas fa-shopping-cart"></i>Acheter</a></li>
                        <li><a href="wishlist.php"><i class="fas fa-bookmark"></i>Ma Liste</a></li>
                        <li><a href="#guide"><i class="fas fa-info-circle"></i>Guide</a></li>
                    </ul>
                </nav>

                <div class="header-social-link">
                    <a href="https://www.facebook.com/alexandre.atcha12"><i class="fab fa-facebook"></i></a>
                    <a href="https://x.com/steeven10082?t=oRfwpQs26sAiAED5C6_BOw&s=09"><i class="fab fa-twitter"></i></a>
                    <a href="https://wwww.instagram.com/alexandreatcha?igsh=MXJ2NnI4MGN1dnVweg=="><i class="fab fa-instagram"></i></a>
                    <a href="https://www.linkedin.com/in/alexandre-atcha-8a643936a?utm_source=share&utm_compaign=share_via&utm_content=profile&utm_medium=android_app"><i class="fab fa-linkedin"></i></a>
                    <a href="https://youtube.com/@AlexandreSteeven"><i class="fab fa-youtube"></i></a>
                </div>

                <div class="user-menu">
                    <a href="auth.php?logout=true" class="btn primary" style="padding: 8px 15px; font-size: 0.9rem;">
                        <i class="fas fa-sign-out-alt"></i>Déconnexion
                    </a>
                </div>
            </div>
        </header>

        <section class="hero-section">
            <div class="hero-bg hero-bg-1 active">
                <div class="hero-content">
                    <h1 class="hero-title">Bienvenue sur e-Library</h1>
                    <p class="hero-subtitle">Votre bibliothèque numérique de référence pour l'apprentissage et le développement personnel</p>
                    <a href="#search" class="btn primary" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 2px solid white;">
                    <i class="fas fa-search"></i>Explorer la Collection
                    </a>
                </div>
            </div>

            <div class="hero-bg hero-bg-2">
                <div class="hero-content">
                    <h1 class="hero-title">Bibliothèque de référence</h1>
                    <p class="hero-subtitle">Accès illimité à +10 000 livres totalement gratuit. Formez-vous à votre rythme, partout.</p>
                    <a href="#search" class="btn primary" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 2px solid white;">
                    <i class="fas fa-search"></i>Explorer la Collection
                    </a>
                </div>
            </div>

             <div class="hero-bg hero-bg-3">
                <div class="hero-content">
                    <h1 class="hero-title">Bibliothèque de référence</h1>
                    <p class="hero-subtitle">Accès illimité à +10 000 livres totalement gratuit. Formez-vous à votre rythme, partout.</p>
                    <a href="#search" class="btn primary" style="background: hsla(0, 12%, 65%, 0.20); backdrop-filter: blur(10px); border: 2px solid white;">
                    <i class="fas fa-search"></i>Explorer la Collection
                    </a>
                </div>
            </div>

             <div class="hero-bg hero-bg-4">
                <div class="hero-content">
                    <h1 class="hero-title">Bibliothèque de référence</h1>
                    <p class="hero-subtitle">Accès illimité à +10 000 livres totalement gratuit. Formez-vous à votre rythme, partout.</p>
                    <a href="#search" class="btn primary" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 2px solid white;">
                    <i class="fas fa-search"></i>Explorer la Collection
                    </a>
                </div>
            </div>

             <div class="hero-bg hero-bg-5">
                <div class="hero-content">
                    <h1 class="hero-title">Bibliothèque de référence</h1>
                    <p class="hero-subtitle">Accès illimité à +10 000 livres totalement gratuit. Formez-vous à votre rythme, partout.</p>
                    <a href="#search" class="btn primary" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 2px solid white;">
                    <i class="fas fa-search"></i>Explorer la Collection
                    </a>
                </div>
            </div>

            <div class="hero-indicators">
                <button class="hero-indicator active" data-slide="0"></button>
                <button class="hero-indicator" data-slide="1"></button>
                <button class="hero-indicator" data-slide="2"></button>
                <button class="hero-indicator" data-slide="3"></button>
                <button class="hero-indicator" data-slide="4"></button>
            </div>
        </section>

        <section class="about-section">
            <div class="about-container">
                <h2 class="section-title">À Propos de e-Library</h2>
                <div class="about-text">
                    <p>Bienvenue sur <strong>e-Library</strong>, la plateforme innovante qui révolutionne votre expérience de lecture. Notre mission est de rendre la connaissance accessible à tous, partout et à tout moment.</p>
                
                    <p>Fondée en 2024, notre bibliothèque numérique rassemble une collection soigneusement sélectionnée d'ouvrages techniques, littéraires et éducatifs. Que vous soyez étudiant, professionnel ou simplement passionné de lecture, vous trouverez chez nous des ressources adaptées à vos besoins.</p>

                    <p>Nous croyons en la puissance du savoir partagé et nous nous engageons à offrir une expérience utilisateur exceptionnelle, alliant simplicité d'utilisation et richesse de contenu.</p>
                </div>
            
                <div class="about-features">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3>+10,000 Livres</h3>
                        <p>Une vaste collection constamment mise à jour</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Communauté Active</h3>
                        <p>Rejoignez des milliers de lecteurs passionnés</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3>Accès Multisupport</h3>
                        <p>Lisez sur tous vos appareils favoris</p>
                    </div>
                </div>
            </div>
        </section>


        <section class="covers-section">
            <div class="container">
                <h2 class="section-title">Nos Livres Populaires</h2>
                <div class="covers-grid">
                    <?php
                    $covers_book = array_slice($featured_books, 0, 12);
                    $cover_images = [
                        'images/le_petit_prince.jpg', 'images/l_etranger.jpg', 'images/1984.jpg', 'images/les_miserables.jpg',
                        'images/harry_potter.jpg', 'images/le_petit_voyageur.jpg', 'images/les_reves_d_mon_pere.jpg', 'images/les_trois_mousq.jpg',
                        'images/l_alchimisme.jpg', 'images/le_voyageur_bout.jpg', 'images/le_seigneur.jpg', 'images/vingt_mille.jpg'
                    ];
                    $i= 0;
                    foreach ($covers_book as $book):
                    ?>
                        <div class="cover-item">
                            <a href="results.php?q=<?php echo urlencode($book['titre']); ?>">
                                <img src="<?php echo $cover_images[$i]; ?>" alt="<?php echo htmlspecialchars($book['titre']); ?>" class="cover-img">
                                <h4><?php echo htmlspecialchars(mb_strimwidth($book['titre'], 0, 35, '...')); ?></h4>
                                <p><?php echo htmlspecialchars($book['auteur']); ?></p>
                            </a>
                        </div>
                    <?php
                    $i++;
                    endforeach;
                    ?>
                </div>
            </div>
        </section>

        <section class="search-section" id="search">
            <div class="container">
                <div class="search-container">
                    <h2 class="search-title">Rechercher un Livre</h2>
                    <form action="results.php" method="GET" class="search-form">
                        <div class="form-group">
                            <label for="bookTitle">Titre du Livre</label>
                            <select id="bookTitle" name="q" required onchange="updateAuthor()">
                                <option value="">Sélectionnez un livre</option>
                                <?php foreach ($all_books as $book): ?>
                                    <option value="<?php echo htmlspecialchars($book['titre']); ?>" data-author="<?php echo htmlspecialchars($book['auteur']); ?>">
                                        <?php echo htmlspecialchars($book['titre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="bookAuthor">Auteur</label>
                            <input type="text" id="bookAuthor" name="auteur" readonly placeholder="sélectionné">
                        </div>

                        <button type="submit" class="btn primary">
                            <i class="fas fa-search"></i>Rechercher le Livre
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section class="featured-section" id="featured">
            <div class="container">
                <h2 class="section-title">Livres en Vedette</h2>
                <div class="books-grid">
                    <?php
                    $featured_books_slice = array_slice($all_books,  10, 6);
                    $featured_images = [
                        'images/mystere_France.jpg', 'images/Behanzin_face.jpg', 'images/Egypte_ant.jpg',
                        'images/France_history.jpg', 'images/L2_3.jpg', 'images/Pharaon_Egypte.jpg'
                    ];
                    $j = 0;
                    foreach ($featured_books_slice as $book):
                    ?>
                        <div class="book-card">
                            <div class="book-image">
                                <img src="<?php echo $featured_images[$j]; ?>" alt="<?php echo htmlspecialchars($book['titre']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="book-content">
                                <h3 class="book-title"><?php echo htmlspecialchars($book['titre']); ?></h3>
                                <p class="book-author">Par <?php echo htmlspecialchars($book['auteur']);?></p>
                                <p class="book-description">
                                    <?php
                                    $description = $book['description'] ?? 'Description non disponible pour ce livre';
                                    echo htmlspecialchars(mb_strimwidth($description, 0, 150, '...'));
                                    ?>
                                </p>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="details.php?id=<?php echo (int)$book['id']; ?>" class="btn primary" style="flex: 2;">
                                        <i class="fas fa-eye"></i>Voir détails
                                    </a>
                                    <a href="wishlist.php?action=add&book_id=<?php echo (int)$book['id'];?>" class="btn secondary" style="flex: 1;">
                                        <i class="fas fa-bookmark"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php
                    $j++;
                    endforeach;
                    ?>
                </div>
            </div>
        </section>

        <section class="purchase-section" id="purchase">
            <div class="container">
                <h2 class="section-title">Livres en Vente</h2>
                <p style="text-align: center; color: var(--text-light); margin-bottom: 2rem;">
                    Acquérez ces ouvrages exceptionnels pour enrichir votre collection personnelle
                </p>
                <div class="purchase-grid">
                    <?php
                    $purchase_books= array_slice($all_books, -6);
                    $purchase_images = [
                        'images/Daniel.jpg', 'images/js.jpg', 'images/paille.jpg',
                        'images/php.jpg', 'images/Socrate.png', 'images/machine_learn.jpg'
                    ];
                    $prices = [29.99, 24.99, 34.99, 19.99, 27.99, 31.99];
                    $i = 0;
                    foreach ($purchase_books as $book): 
                    ?>
                        <div class="purchase-card">
                            <div class="purchase-badge">EN VENTE</div>
                            <div class="purchase-image">
                                <img src="<?php echo  $purchase_images[$i]; ?>" alt="<?php echo htmlspecialchars($book['titre']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="purchase-content">
                                <h3 class="book-title"><?php echo htmlspecialchars($book['titre']); ?></h3>
                                <p class="book-author"><?php echo htmlspecialchars($book['auteur']); ?></p>
                                <div class="purchase-price">
                                    <?php echo number_format($prices[$i], 2, ',', ' '); ?> €
                                    <span class="price-old"><?php echo number_format($prices[$i] + 5, 2, ',', ' '); ?> €</span>
                                </div>
                                <p class="book-description">
                                    <?php echo htmlspecialchars(mb_strimwidth($book['description'] ?? 'Un livre exceptionnel qui mérite une place dans votre bibliothèque.', 0, 100, '...')); ?>
                                </p>
                                <button class="btn success" style="width: 100%;" onclick="alert('Fonctionnalité d\\'achat à implémenter - Livre: <?php echo htmlspecialchars($book['titre']); ?> ')">
                                    <i class="fas fa-shopping-cart"></i>Acheter Maintenant
                                </button>
                            </div>
                        </div>
                    <?php 
                    $i++;
                    endforeach; 
                    ?>
                </div>
            </div>
        </section>

        <section class="guide-section" id="guide">
            <div class="container">
                <h2 class="section-title">Comment Utiliser Notre Plateforme</h2>
                <div class="guide-steps">
                    <div class="guide-step">
                        <div class="step-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="step-title">1. Rechercher</h3>
                        <p class="step-description">Utilisez notre moteur de recherche intelligent pour trouver des livres par titre, auteur ou mot-clé.</p>
                    </div>

                    <div class="guide-step">
                        <div class="step-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3 class="step-title">2. Découvrir</h3>
                        <p class="step-description">Consultez les détails des livres, les résumés et les avis des autres lecteurs.</p>
                    </div>

                    <div class="guide-step">
                        <div class="step-icon">
                            <i class="fas fa-bookmark"></i>
                        </div>
                        <h3 class="step-title">3. Sauvegarder</h3>
                        <p class="step-description">Ajoutez vos livres préférés à votre liste de lecture pour y accéder facilement.</p>
                    </div>

                    <div class="guide-step">
                        <div class="step-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <h3 class="step-title">4. Lire ou Acheter</h3>
                        <p class="step-description">Empruntez les livres numériques ou achetez-les pour votre collection personnelle.</p>
                    </div>
                </div>
            </div>
        </section>

        <footer class="site-footer">
            <div class="container">
                <div class="footer-container">
                    <div class="footer-section">
                        <h3>e-Library</h3>
                        <p>Votre bibliothèque numérique de confiance, offrant un accès à des milliers d'ouvrages sélectionnés avec soin.</p>
                        <div class="social-links">
                            <a href="https://www.facebook.com/alexandre.atcha12"><i class="fab fa-facebook"></i></a>
                            <a href="https://x.com/steeven10082?t=oRfwpQs26sAiAED5C6_BOw&s=09"><i class="fab fa-twitter"></i></a>
                            <a href="https://wwww.instagram.com/alexandreatcha?igsh=MXJ2NnI4MGN1dnVweg=="><i class="fab fa-instagram"></i></a>
                            <a href="https://www.linkedin.com/in/alexandre-atcha-8a643936a?utm_source=share&utm_compaign=share_via&utm_content=profile&utm_medium=android_app"><i class="fab fa-linkedin"></i></a>
                            <a href="https://youtube.com/@AlexandreSteeven"><i class="fab fa-youtube"></i></a>
                            <a href=""><i class=" fab fa-telegram "></i></a>
                            <a href=""><i class="fab fa-threads"></i></a>
                            <a href="mailto:alexandreatcha440@gmail.com"><i class="fas fa-envelope"></i></a>
                            <a href="https://wa.me/+22990204055"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>

                    <div class="footer-section">
                        <h3>Navigation</h3>
                        <ul class="footer-links">
                            <li><a href="index.php">Accueil</a></li>
                            <li><a href="#featured">Livres Populaires</a></li>
                            <li><a href="#purchase">Acheter des Livres</a></li>
                            <li><a href="wishlist.php">Ma Liste de Lecture</a></li>
                            <li><a href="#guide">Guide d'Utilisation</a></li>
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

                    <div class="footer-section">
                        <h3>Catégories</h3>
                        <ul class="footer-links">
                            <li><a href="#">Programmation</a></li>
                            <li><a href="#">Design</a></li>
                            <li><a href="#">Sécurité</a></li>
                            <li><a href="#">Data Science</a></li>
                            <li><a href="#">Développement Web</a></li>
                        </ul>
                    </div>
                </div>

                <div class="footer-bottom">
                    <p>&copy; 2025 e-Library. Tous droits réservés. | Site réalisé par <a href="https://alexandre-atcha-portfolio.com" target="_blank">Alexandre ATCHA</a></p>
                </div>
            </div>
        </footer>


        <script>
            // ============================================
            // FONCTIONS POUR LES BANNIÈRES (SANS localStorage)
            // ============================================

            // Fonction pour mettre le contenu en flou
            function blurContent() {
                const content = document.querySelector('.main-content');
                if (content) {
                    content.style.filter = "blur(5px)";
                    content.style.transition = "filter 0.5s ease";
                }
            }

            // Fonction pour enlever le flou avec animation
            function unblurContent() {
                const content = document.querySelector('.main-content');
                if (content) {
                    content.style.filter = "blur(0)";

                    setTimeout(() => {
                        content.style.filter = "none";
                        content.style.transition = "none";
                    }, 500);
                }
            }

            // Afficher la bannière de confidentialité
            function showPrivacyModal() {
                const overlay = document.getElementById('overlay');
                const modal = document.getElementById('privacy-modal');
    
                if (overlay && modal) {
                    overlay.style.display = 'block';
                    blurContent();

                    setTimeout(() => {
                        modal.classList.add('active');
                    }, 50);
                }
            }

            // Cacher la bannière de confidentialité
            function hidePrivacyModal() {
                const overlay = document.getElementById('overlay');
                const modal = document.getElementById('privacy-modal');
    
                if (modal) modal.classList.remove('active');
                unblurContent();
    
                setTimeout(() => {
                    if (overlay) overlay.style.display = 'none';
                }, 500);
            }

            // Afficher les cookies
            function showCookieBanner() {
                const banner = document.getElementById('cookie-banner');
                if (banner) {
                    banner.style.display = 'block';

                    if (window.innerWidth <= 768) {
                        setTimeout(() => {
                            banner.classList.add('show');
                        }, 50);
                    }
                }
            }

            // Cacher les cookies
            function hideCookieBanner() {
                const banner = document.getElementById('cookie-banner');

                if (banner) {
                    if (window.innerWidth <= 768) {
                        banner.classList.remove('show');
                        
                        setTimeout(() => {
                            banner.style.display = 'none';
                        }, 400);
                    } else {
                        banner.style.display = 'none';
                    }
                }
            }

            // ============================================
            // CHRONOLOGIE PRINCIPALE - À CHAQUE ACTUALISATION
            // ============================================

            document.addEventListener('DOMContentLoaded', function() {
                console.log("Page chargée - Les bannières s'afficheront à chaque F5");
    
                // 1. Cacher tout au départ
                const overlay = document.getElementById('overlay');
                const modal = document.getElementById('privacy-modal');
                const cookieBanner = document.getElementById('cookie-banner');
    
                if (overlay) overlay.style.display = 'none';
                if (modal) modal.classList.remove('active');
                if (cookieBanner) cookieBanner.style.display = 'none';
    
                // 2. Gestion du preloader (5 secondes)
                const preloader = document.getElementById('preloader');
                if (preloader) {
                    // Preloader tourne 5 secondes
                    setTimeout(() => {
                        preloader.style.opacity = '0';
            
                        setTimeout(() => {
                            preloader.style.display = 'none';
                
                            // TOUJOURS afficher après 2 secondes (total 7s)
                            setTimeout(() => {
                                showPrivacyModal();
                            }, 2000);
                
                        }, 500); // Fin animation preloader
                    }, 5000); // Durée preloader
                }
    
                // 3. Gestion des boutons
    
                // Bouton "Accepter et continuer"
                const acceptPrivacyBtn = document.getElementById('accept-privacy');
                if (acceptPrivacyBtn) {
                    acceptPrivacyBtn.addEventListener('click', function() {
                        hidePrivacyModal();
                        // 4 secondes après, cookies
                        setTimeout(showCookieBanner, 4000);
                    });
                }
    
                // Bouton fermeture (×)
                const closeBtn = document.querySelector('.privacy-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        hidePrivacyModal();
                        setTimeout(showCookieBanner, 4000);
                    });
                }
    
                // Clic sur l'overlay
                if (overlay) {
                    overlay.addEventListener('click', function(e) {
                        if (e.target === this) {
                            hidePrivacyModal();
                            setTimeout(showCookieBanner, 4000);
                        }
                    });
                }
    
                // Cookies
                const acceptCookiesBtn = document.getElementById('accept-cookies');
                const rejectCookiesBtn = document.getElementById('reject-cookies');
    
                if (acceptCookiesBtn) {
                    acceptCookiesBtn.addEventListener('click', function() {
                        hideCookieBanner();
                    });
                }
    
                if (rejectCookiesBtn) {
                    rejectCookiesBtn.addEventListener('click', function() {
                        hideCookieBanner();
                    });
                }
            });


            // Fonction pour mettre à jour l'auteur
            function updateAuthor() {
                const bookSelect = document.getElementById('bookTitle');
                const authorInput = document.getElementById('bookAuthor');
    
                if (bookSelect && authorInput) {
                    const selectedOption = bookSelect.options[bookSelect.selectedIndex];
                    authorInput.value = selectedOption.value !== '' 
                    ? (selectedOption.getAttribute('data-author') || '') 
                    : '';
                }
            }

            // Animation des images hero
            function initHeroAnimation() {
                const images = document.querySelectorAll('.hero-bg');
                const indicators = document.querySelectorAll('.hero-indicator');
    
                if (images.length === 0) return;
    
                let currentIndex = 0;
                let slideInterval;
    
                function goToSlide(index) {
                    images.forEach(img => img.classList.remove('active'));
                    indicators.forEach(indicator => indicator.classList.remove('active'));
        
                    images[index].classList.add('active');
                    if (indicators[index]) indicators[index].classList.add('active');
                    currentIndex = index;
                }
    
                function nextSlide() {
                    goToSlide((currentIndex + 1) % images.length);
                }
    
                function startAutoSlide() {
                    if (slideInterval) clearInterval(slideInterval);
                    slideInterval = setInterval(nextSlide, 4000);
                }
    
                function stopAutoSlide() {
                    if (slideInterval) clearInterval(slideInterval);
                }
    
                goToSlide(0);
                startAutoSlide();
    
                indicators.forEach((indicator, index) => {
                    indicator.addEventListener('click', () => {
                        goToSlide(index);
                        stopAutoSlide();
                        startAutoSlide();
                    });
                });
    
                const heroSection = document.querySelector('.hero-section');
                if (heroSection) {
                    heroSection.addEventListener('mouseenter', stopAutoSlide);
                    heroSection.addEventListener('mouseleave', startAutoSlide);
                }
            }

            // Animation des éléments au scroll
            function initScrollAnimations() {
                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }
                    });
                }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

                document.querySelectorAll('.book-card, .guide-step, .purchase-card, .cover-item, .feature-card').forEach(el => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(30px)';
                    el.style.transition = 'all 0.6s ease';
                    observer.observe(el);
                });
            }

            // ============================================
            // INITIALISATION FINALE
            // ============================================

            document.addEventListener('DOMContentLoaded', function() {
                // 1. Animation hero
                initHeroAnimation();
    
                // 2. Animation scroll
                initScrollAnimations();
            });

            // Support pour les vieux navigateurs (Edge)
            if (typeof NodeList.prototype.forEach !== 'function') {
                NodeList.prototype.forEach = Array.prototype.forEach;
            }
        </script>
    </div>
</body>
</html>

<?php $conn->close(); ?>