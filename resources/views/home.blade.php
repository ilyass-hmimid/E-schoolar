<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allo TAWJIH - Votre partenaire éducatif</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #00FF88;
            --primary-dark: #00cc6a;
            --primary-transparent: rgba(0, 255, 136, 0.1);
            --dark: #0a0a0a;
            --darker: #050505;
            --light: #ffffff;
            --light-gray: #e0e0e0;
            --gray: #888;
            --dark-gray: #1a1a1a;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            font-size: 16px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark);
            color: var(--light);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Header Styles */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            padding: 1rem 0;
            transition: var(--transition);
            background: rgba(10, 10, 10, 0.9);
            backdrop-filter: blur(10px);
        }

        header.scrolled {
            padding: 0.5rem 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--light);
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .logo span {
            color: var(--primary);
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        nav a {
            color: var(--light);
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: var(--transition);
            position: relative;
            padding: 0.5rem 0;
        }

        nav a:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: var(--transition);
        }

        nav a:hover,
        nav a.active {
            color: var(--primary);
        }

        nav a:hover:after,
        nav a.active:after {
            width: 100%;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--light);
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 1001;
        }

        /* Hero Section */
        #hero {
            padding: 150px 0 80px;
            background: linear-gradient(135deg, var(--darker) 0%, var(--dark) 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-content {
            text-align: left;
            position: relative;
            z-index: 2;
        }

        .hero-content h1 {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            background: var(--primary);
            color: var(--dark);
            text-shadow: 0 0 20px rgba(0, 255, 136, 0.3);
            position: relative;
            display: inline-block;
            min-height: 1.2em; /* Ensure space for the cursor */
        }
        
        /* Cursor animation */
        .typing-text::after {
            content: '|';
            display: inline-block;
            margin-left: 2px;
            animation: blink 0.7s infinite;
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        .hero-content h1:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { opacity: 0; transform: translateX(-100%); }
            20% { opacity: 1; }
            100% { opacity: 0; transform: translateX(100%); }
        }

        .hero-content p {
            font-size: 1.2rem;
            max-width: 600px;
            margin-bottom: 2.5rem;
            color: var(--light-gray);
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            color: var(--dark);
            font-weight: 600;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            z-index: 1;
            text-decoration: none;
        }

        .btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: var(--light);
            transition: var(--transition);
            z-index: -1;
        }

        .btn:hover {
            color: var(--dark);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 255, 136, 0.2);
        }

        .btn:hover:before {
            width: 100%;
        }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 32px;
            margin: 0;
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 50px; 
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            min-width: 180px;
            position: relative;
            z-index: 1;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease, color 0.2s ease, background-color 0.2s ease;
            box-shadow: 0 4px 15px rgba(0, 255, 136, 0.1);
            box-sizing: border-box;
            text-align: center;
            vertical-align: middle;
            line-height: 1.5;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            user-select: none;
        }
        
        /* Premier bouton - Effet de remplissage par le bas */
        .btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 0%;
            background: var(--dark);
            z-index: -1;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 50px;
        }
        
        .btn:hover {
            color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 255, 136, 0.3);
        }
        
        .btn:hover:before {
            height: 100%;
        }
        
        /* Deuxième bouton - Effet de balayage diagonal */
        .btn-outline:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 136, 0.1), transparent);
            transition: 0.5s;
            z-index: -1;
        }
        
        .btn-outline:hover {
            color: var(--light);
            border-color: var(--primary);
            background: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 255, 136, 0.3);
        }
        
        .btn-outline:hover:before {
            left: 100%;
        }

        .hero-buttons {
            display: flex;
            gap: 1.5rem;
            margin-top: 2.5rem;
            flex-wrap: wrap;
        }

        @media (max-width: 576px) {
            .hero-buttons {
                flex-direction: column;
                width: 100%;
            }
            
            .btn, .btn-outline {
                width: 100%;
                margin: 0.5rem 0;
            }
        }

        .btn-outline:hover {
            background: var(--primary);
            color: var(--dark);
        }

        .hero-image {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transform: perspective(1000px) rotateY(-5deg) rotateX(5deg);
            transition: transform 0.5s ease, box-shadow 0.5s ease;
            max-height: 500px;
        }

        .hero-image:hover {
            transform: perspective(1000px) rotateY(0);
        }

        .hero-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
        }

        /* Stats Section */
        #stats {
            background-color: var(--darker);
            padding: 5rem 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }

        .stat-item {
            padding: 2rem;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 10px;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.1);
            border-color: var(--primary-transparent);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.5rem;
            display: block;
        }

        .stat-label {
            font-size: 1rem;
            color: var(--light-gray);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* About Section */
        .about-container {
            display: flex;
            align-items: center;
            gap: 4rem;
        }

        .about-content {
            flex: 1;
        }

        .about-content h2 {
            margin-bottom: 2rem;
            text-align: left;
        }

        .about-content h2:after {
            left: 0;
            transform: none;
        }

        .about-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 2rem 0;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--light-gray);
        }

        .feature i {
            color: var(--primary);
            font-size: 1.2rem;
        }

        .about-image {
            flex: 1;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
        }

        .about-image img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.5s ease;
        }

        .about-image:hover img {
            transform: scale(1.05);
        }

        /* Services Section */
        #services {
            background-color: var(--darker);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 15px;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .service-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-transparent), transparent);
            opacity: 0;
            transition: var(--transition);
            z-index: -1;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 255, 136, 0.1);
            border-color: var(--primary-transparent);
        }

        .service-card:hover:before {
            opacity: 1;
        }

        .service-icon {
            width: 80px;
            height: 80px;
            background: rgba(0, 255, 136, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: var(--primary);
            transition: var(--transition);
        }

        .service-card:hover .service-icon {
            background: var(--primary);
            color: var(--dark);
            transform: rotateY(180deg);
        }

        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--light);
        }

        .service-card p {
            font-size: 1rem;
            margin-bottom: 1.5rem;
            color: var(--light-gray);
        }

        /* Team Section */
        #team {
            padding: 6rem 0;
            position: relative;
            overflow: hidden;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .team-member {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 15px;
            overflow: hidden;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
            z-index: 1;
        }

        .team-member:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 255, 136, 0.1);
            border-color: var(--primary-transparent);
        }

        .member-image {
            width: 100%;
            height: 300px;
            overflow: hidden;
        }

        .member-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .team-member:hover .member-image img {
            transform: scale(1.1);
        }

        .member-info {
            padding: 1.5rem;
            text-align: center;
        }

        .member-info h4 {
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
            color: var(--light);
        }

        .member-role {
            display: block;
            color: var(--primary);
            font-weight: 500;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .member-social {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .member-social a {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--light);
            transition: var(--transition);
        }

        .member-social a:hover {
            background: var(--primary);
            color: var(--dark);
            transform: translateY(-3px);
        }

        /* Contact Section */
        #contact {
            padding: 6rem 0;
            background-color: var(--darker);
            position: relative;
            overflow: hidden;
        }

        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 4rem;
            margin-top: 3rem;
        }

        .contact-info h3, 
        .contact-form h3 {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: var(--light);
            position: relative;
            display: inline-block;
        }

        .contact-info h3:after,
        .contact-form h3:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary);
        }

        .contact-info p {
            margin-bottom: 2rem;
            color: var(--light-gray);
        }

        .contact-details {
            margin-top: 2rem;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background: rgba(0, 255, 136, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--primary);
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .contact-text h4 {
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
            color: var(--light);
        }

        .contact-text a, 
        .contact-text p {
            color: var(--light-gray);
            text-decoration: none;
            transition: var(--transition);
            margin: 0;
        }

        .contact-text a:hover {
            color: var(--primary);
        }

        .social-links h4 {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: var(--light);
        }

        .footer-social {
            display: flex;
            gap: 1rem;
        }

        .footer-social a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--light);
            transition: var(--transition);
        }

        .footer-social a:hover {
            background: var(--primary);
            color: var(--dark);
            transform: translateY(-3px);
        }

        /* Contact Form */
        .contact-form {
            background: rgba(255, 255, 255, 0.02);
            padding: 2.5rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--light);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            color: var(--light);
            font-family: 'Inter', sans-serif;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 255, 136, 0.1);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        /* CTA Section */
        #cta {
            padding: 6rem 0;
            text-align: center;
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.03), transparent);
            position: relative;
            overflow: hidden;
        }

        #cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: var(--light);
        }

        #cta p {
            max-width: 700px;
            margin: 0 auto 2.5rem;
            color: var(--light-gray);
            font-size: 1.1rem;
        }

        #cta .btn {
            font-size: 1.1rem;
            padding: 1rem 3rem;
        }

        /* Footer */
        footer {
            background: #050505;
            padding: 6rem 0 0;
            position: relative;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
            margin-bottom: 4rem;
        }

        .footer-about h3 {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: var(--light);
        }

        .footer-about p {
            color: var(--light-gray);
            margin-bottom: 1.5rem;
        }

        .footer-links h3,
        .footer-services h3,
        .footer-newsletter h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: var(--light);
            position: relative;
            padding-bottom: 0.75rem;
        }

        .footer-links h3:after,
        .footer-services h3:after,
        .footer-newsletter h3:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background: var(--primary);
        }

        .footer-links ul,
        .footer-services ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li,
        .footer-services li {
            margin-bottom: 0.75rem;
        }

        .footer-links a,
        .footer-services a {
            color: var(--light-gray);
            text-decoration: none;
            transition: var(--transition);
            display: inline-block;
        }

        .footer-links a:hover,
        .footer-services a:hover {
            color: var(--primary);
            transform: translateX(5px);
        }

        .footer-newsletter p {
            color: var(--light-gray);
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .newsletter-form {
            display: flex;
            position: relative;
        }

        .newsletter-form input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50px;
            color: var(--light);
            font-family: 'Inter', sans-serif;
            padding-right: 50px;
        }

        .newsletter-form button {
            position: absolute;
            right: 5px;
            top: 5px;
            bottom: 5px;
            width: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: var(--dark);
            border: none;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .newsletter-form button:hover {
            background: var(--light);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 1.5rem 0;
            text-align: center;
        }

        .footer-bottom p {
            margin: 0;
            color: var(--light-gray);
            font-size: 0.9rem;
        }

        .footer-bottom a {
            color: var(--light);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-bottom a:hover {
            color: var(--primary);
        }

        /* Animations */
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

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 255, 136, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(0, 255, 136, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 255, 136, 0); }
        }

        .animate-on-scroll.animated {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        /* Button hover effect */
        .btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 32px;
            margin: 0;
            background: var(--primary);
            color: var(--dark);
            border: 2px solid var(--primary);
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
            z-index: 1;
            overflow: hidden;
            min-width: 180px;
            max-width: 100%;
            box-shadow: 0 4px 15px rgba(0, 255, 136, 0.2);
            box-sizing: border-box;
            text-align: center;
            vertical-align: middle;
            line-height: 1.5;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            user-select: none;
        }
        
        .btn:active, .btn:focus {
            transform: translateY(1px);
            outline: none;
            box-shadow: 0 2px 8px rgba(0, 255, 136, 0.2);
        }
        
        .btn-outline:active, .btn-outline:focus {
            transform: translateY(1px);
            outline: none;
            box-shadow: 0 2px 8px rgba(0, 255, 136, 0.1);
        }
        
        /* Suppression de l'effet de zoom au focus */
        .btn:focus:not(:focus-visible) {
            transform: translateY(-3px);
        }
        
        .btn-outline:focus:not(:focus-visible) {
            transform: translateY(-3px);
        }

        .btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--dark);
            z-index: -1;
            transition: opacity 0.3s ease, transform 0.3s ease;
            opacity: 0;
            border-radius: 50px;
        }

        .btn:hover:before {
            opacity: 1;
            transform: scale(1);
        }

        /* Section headers */
        .section-header {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 4rem;
            position: relative;
        }

        .section-header h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: var(--light);
            position: relative;
            display: inline-block;
        }

        .section-header h2:after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--primary);
        }

        .section-header p {
            font-size: 1.1rem;
            color: var(--light-gray);
            line-height: 1.7;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .about-container {
                flex-direction: column;
            }

            .about-image {
                margin-top: 3rem;
                max-width: 600px;
            }
        }

        @media (max-width: 992px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 3rem;
                text-align: center;
            }

            .hero-content {
                text-align: center;
            }

            .hero-buttons {
                justify-content: center;
            }

            .contact-container {
                grid-template-columns: 1fr;
                gap: 3rem;
            }

            .team-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .section-header h2 {
                font-size: 2rem;
            }

            .section-header p {
                font-size: 1rem;
            }

            .team-grid {
                grid-template-columns: 1fr;
                max-width: 400px;
                margin: 0 auto;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 576px) {
            .section-header h2 {
                font-size: 1.8rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .about-features {
                grid-template-columns: 1fr;
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--darker);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        /* Preloader */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--dark);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s, visibility 0.5s;
        }

        #preloader.fade-out {
            opacity: 0;
            visibility: hidden;
        }

        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Back to top button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--primary);
            color: var(--dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 998;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 255, 136, 0.3);
        }

        .back-to-top.active {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background: var(--light);
            transform: translateY(-5px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            nav {
                position: fixed;
                top: 0;
                right: -100%;
                width: 80%;
                max-width: 300px;
                height: 100vh;
                background: var(--darker);
                padding: 6rem 2rem 2rem;
                transition: var(--transition);
                z-index: 1000;
                box-shadow: -5px 0 30px rgba(0, 0, 0, 0.2);
            }

            nav.active {
                right: 0;
            }

            nav ul {
                flex-direction: column;
                gap: 1.5rem;
            }

            .hero-content {
                padding-right: 0;
                text-align: center;
                margin-bottom: 3rem;
            }

            .hero-buttons {
                justify-content: center;
                flex-direction: column;
            }

            .btn-outline {
                margin-left: 0;
                margin-top: 1rem;
            }

            .hero-image {
                width: 100%;
                max-width: 500px;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header id="header">
        <div class="container header-container">
            <a href="#" class="logo">
                Allo<span>TAWJIH</span>
            </a>

            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>

            <nav id="nav">
                <ul>
                    <li><a href="#hero" class="active">Accueil</a></li>
                    <li><a href="#about">À propos</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#team">Équipe</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="hero">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content">
                    <h1 class="typing-text"></h1>
                    <p>Découvrez une nouvelle façon d'apprendre et de vous épanouir avec Allo TAWJIH. Notre plateforme éducative innovante vous accompagne à chaque étape de votre parcours d'apprentissage.</p>
                    <div class="hero-buttons">
                        <a href="{{ route('login') }}" class="btn">
                            <span>Se connecter</span>
                            <i class="fas fa-arrow-right" style="margin-left: 8px; font-size: 0.9em;"></i>
                        </a>
                        <a href="#about" class="btn-outline">
                            <span>Découvrir plus</span>
                            <i class="fas fa-chevron-circle-right" style="margin-left: 8px;"></i>
                        </a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Étudiants en apprentissage">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number" data-count="2000">0</span>
                    <span class="stat-label">Étudiants</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="10">0</span>
                    <span class="stat-label">Ans d'expérience</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="150">0</span>
                    <span class="stat-label">Mentors experts</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="500">0</span>
                    <span class="stat-label">Parcours réussis</span>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about">
        <div class="container">
            <div class="about-container">
                <div class="about-content">
                    <h2>Transformer les idées en chefs-d'œuvre</h2>
                    <p>Allo TAWJIH est bien plus qu'une simple plateforme éducative. Nous sommes une communauté dédiée à l'épanouissement personnel et professionnel de chaque étudiant.</p>
                    <p>Notre approche innovante combine apprentissage personnalisé, mentorat expert et technologies de pointe pour offrir une expérience éducative inégalée.</p>
                    <div class="about-features">
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Parcours personnalisés</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Mentorat expert</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Ressources illimitées</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Communauté active</span>
                        </div>
                    </div>
                    <a href="#contact" class="btn" style="margin-top: 2rem;">Nous rejoindre</a>
                </div>
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Équipe Allo TAWJIH">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services">
        <div class="container">
            <div class="section-header">
                <h2>Nos Services</h2>
                <p>Découvrez notre gamme complète de services conçus pour répondre à tous vos besoins éducatifs</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-paint-brush"></i>
                    </div>
                    <h3>Conseil d'Orientation</h3>
                    <p>Un accompagnement personnalisé pour vous aider à trouver la voie qui vous correspond le mieux.</p>
                    <a href="#contact" class="btn btn-outline">En savoir plus</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3>Préparation aux Études</h3>
                    <p>Préparez-vous efficacement pour intégrer les meilleures institutions avec nos programmes sur mesure.</p>
                    <a href="#contact" class="btn btn-outline">En savoir plus</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-cube"></i>
                    </div>
                    <h3>Coaching Scolaire</h3>
                    <p>Améliorez vos résultats scolaires grâce à un suivi personnalisé par nos experts pédagogiques.</p>
                    <a href="#contact" class="btn btn-outline">En savoir plus</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-film"></i>
                    </div>
                    <h3>Ateliers Thématiques</h3>
                    <p>Développez des compétences clés à travers nos ateliers pratiques et interactifs.</p>
                    <a href="#contact" class="btn btn-outline">En savoir plus</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section id="team">
        <div class="container">
            <div class="section-header">
                <h2>Notre Équipe</h2>
                <p>Rencontrez notre équipe d'experts passionnés par l'éducation et l'accompagnement</p>
            </div>
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-image">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Membre de l'équipe">
                    </div>
                    <div class="member-info">
                        <h4>Khadija El kamali</h4>
                        <span class="member-role">Professeure de physique-chimie</span>
                        <div class="member-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-member">
                    <div class="member-image">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Membre de l'équipe">
                    </div>
                    <div class="member-info">
                        <h4>Zakaria El Houari</h4>
                        <span class="member-role">Prof de mathématiques</span>
                        <div class="member-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-member">
                    <div class="member-image">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Membre de l'équipe">
                    </div>
                    <div class="member-info">
                        <h4>Karima Ait Ali</h4>
                        <span class="member-role">Professeure de SVT</span>
                        <div class="member-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-member">
                    <div class="member-image">
                        <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Membre de l'équipe">
                    </div>
                    <div class="member-info">
                        <h4>Ilyass Hmimid</h4>
                        <span class="member-role">Professeur d'anglais</span>
                        <div class="member-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact">
        <div class="container">
            <div class="section-header">
                <h2>Contactez-nous</h2>
                <p>Nous sommes là pour répondre à toutes vos questions</p>
            </div>
            <div class="contact-container">
                <div class="contact-info">
                    <h3>Informations de contact</h3>
                    <p>N'hésitez pas à nous contacter pour toute question ou demande d'information.</p>
                    
                    <div class="contact-details">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-text">
                                <h4>Adresse</h4>
                                <p>123 Rue de l'Éducation, Marrakech, Maroc</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="contact-text">
                                <h4>Téléphone</h4>
                                <a href="tel:+212612345678">+212 6 12 34 56 78</a>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-text">
                                <h4>Email</h4>
                                <a href="mailto:contact@allotawjih.ma">contact@allotawjih.ma</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="social-links" style="margin-top: 2rem;">
                        <h4>Suivez-nous</h4>
                        <div class="footer-social">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h3>Envoyez-nous un message</h3>
                    <form id="contactForm">
                        <div class="form-group">
                            <label for="name">Nom complet</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="subject">Sujet</label>
                            <input type="text" id="subject" name="subject" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn">Envoyer le message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="cta">
        <div class="container">
            <h2>Prêt à commencer votre parcours ?</h2>
            <p>Rejoignez des milliers d'étudiants qui ont déjà fait confiance à Allo TAWJIH pour leur orientation et leur réussite académique.</p>
            <a href="#contact" class="btn">Nous contacter</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-about">
                    <h3>Allo TAWJIH</h3>
                    <p>Votre partenaire de confiance pour l'orientation et la réussite académique.</p>
                    <div class="footer-social">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h3>Liens rapides</h3>
                    <ul>
                        <li><a href="#hero">Accueil</a></li>
                        <li><a href="#about">À propos</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#team">Équipe</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-services">
                    <h3>Nos services</h3>
                    <ul>
                        <li><a href="#">Conseil d'Orientation</a></li>
                        <li><a href="#">Préparation aux Études</a></li>
                        <li><a href="#">Coaching Scolaire</a></li>
                        <li><a href="#">Ateliers Thématiques</a></li>
                        <li><a href="#">Bilans de Compétences</a></li>
                    </ul>
                </div>
                <div class="footer-newsletter">
                    <h3>Newsletter</h3>
                    <p>Abonnez-vous à notre newsletter pour recevoir nos dernières actualités et offres spéciales.</p>
                    <form class="newsletter-form">
                        <input type="email" placeholder="Votre adresse email" required>
                        <button type="submit"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 Allo TAWJIH. Tous droits réservés. | <a href="#">Politique de confidentialité</a> | <a href="#">Conditions d'utilisation</a></p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const nav = document.getElementById('nav');
        
        mobileMenuBtn.addEventListener('click', () => {
            nav.classList.toggle('active');
            mobileMenuBtn.innerHTML = nav.classList.contains('active') ? 
                '<i class="fas fa-times"></i>' : 
                '<i class="fas fa-bars"></i>';
        });

        // Close menu when clicking on a nav link
        document.querySelectorAll('nav a').forEach(link => {
            link.addEventListener('click', () => {
                nav.classList.remove('active');
                mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
            });
        });

        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for anchor links (only for internal page anchors)
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            // Skip if it's not an internal page anchor (e.g., starts with # but has route)
            if (anchor.getAttribute('href').includes('{{ route(') || 
                anchor.getAttribute('href').startsWith('http') ||
                anchor.getAttribute('href').startsWith('mailto:') ||
                anchor.getAttribute('href').startsWith('tel:')) {
                return;
            }
            
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Update URL without page reload
                    if (history.pushState) {
                        history.pushState(null, null, targetId);
                    } else {
                        location.hash = targetId;
                    }
                }
            });
        });

        // Animate stats counter
        function animateStats() {
            const statNumbers = document.querySelectorAll('.stat-number');
            
            statNumbers.forEach(stat => {
                const target = parseInt(stat.getAttribute('data-count'));
                const duration = 2000; // 2 seconds
                const step = Math.ceil(target / (duration / 16)); // 60fps
                let current = 0;
                
                const updateCount = () => {
                    current += step;
                    if (current >= target) {
                        stat.textContent = target.toLocaleString();
                    } else {
                        stat.textContent = current.toLocaleString();
                        requestAnimationFrame(updateCount);
                    }
                };
                
                updateCount();
            });
        }

        // Animate on scroll
        function animateOnScroll() {
            const elements = document.querySelectorAll('.animate-on-scroll');
            
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                
                if (elementTop < windowHeight - 100) {
                    element.classList.add('animated');
                }
            });
        }

        // Initialize animations when page loads
        document.addEventListener('DOMContentLoaded', () => {
            // Add animation classes to elements
            document.querySelectorAll('section').forEach(section => {
                const headings = section.querySelectorAll('h2, h3, p, .btn, .service-card, .team-member, .contact-item, .form-group');
                headings.forEach((heading, index) => {
                    heading.classList.add('animate-on-scroll');
                    heading.style.opacity = '0';
                    heading.style.transform = 'translateY(30px)';
                    heading.style.transition = `all 0.6s ease ${index * 0.1}s`;
                });
            });

            // Initial check for elements in viewport
            animateOnScroll();
            
            // Animate stats when stats section is in view
            const statsSection = document.getElementById('stats');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateStats();
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            
            if (statsSection) {
                observer.observe(statsSection);
            }
        });

        // Handle form submission
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form data
                const formData = new FormData(this);
                const formObject = {};
                formData.forEach((value, key) => {
                    formObject[key] = value;
                });
                
                // Here you would typically send the form data to a server
                console.log('Form submitted:', formObject);
                
                // Show success message
                alert('Merci pour votre message ! Nous vous contacterons bientôt.');
                this.reset();
            });
        }

        // Newsletter form submission
        const newsletterForm = document.querySelector('.newsletter-form');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const emailInput = this.querySelector('input[type="email"]');
                const email = emailInput.value.trim();
                
                if (email) {
                    // Here you would typically send the email to your server
                    console.log('Newsletter subscription:', email);
                    alert('Merci de vous être abonné à notre newsletter !');
                    emailInput.value = '';
                }
            });
        }

        // Add animation classes on scroll
        window.addEventListener('scroll', () => {
            animateOnScroll();
            
            // Update active navigation link
            const scrollPosition = window.scrollY + 100;
            document.querySelectorAll('section').forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    document.querySelectorAll('nav a').forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === `#${sectionId}`) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        });

        // Add hover effect to buttons
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                this.style.setProperty('--x', `${x}px`);
                this.style.setProperty('--y', `${y}px`);
            });
        });
        // Back to top button
        const backToTopButton = document.createElement('div');
        backToTopButton.className = 'back-to-top';
        backToTopButton.innerHTML = '<i class="fas fa-arrow-up"></i>';
        document.body.appendChild(backToTopButton);

        // Show/hide back to top button
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('active');
            } else {
                backToTopButton.classList.remove('active');
            }
        });

        // Back to top functionality
        backToTopButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Preloader
        window.addEventListener('load', () => {
            const preloader = document.createElement('div');
            preloader.id = 'preloader';
            preloader.innerHTML = '<div class="loader"></div>';
            document.body.appendChild(preloader);

            // Hide preloader after page loads
            setTimeout(() => {
                preloader.classList.add('fade-out');
                setTimeout(() => {
                    preloader.remove();
                }, 500);
            }, 1000);
        });

        // Add ripple effect to buttons
        function createRipple(event) {
            event.preventDefault();
            const button = event.currentTarget;
            
            // Créer l'élément ripple
            const circle = document.createElement('span');
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            
            // Positionner l'effet ripple
            circle.style.width = circle.style.height = `${size}px`;
            circle.style.position = 'absolute';
            circle.style.borderRadius = '50%';
            circle.style.backgroundColor = 'rgba(255, 255, 255, 0.3)';
            circle.style.transform = 'scale(0)';
            circle.style.transition = 'transform 0.6s ease-out, opacity 0.6s ease-out';
            circle.style.pointerEvents = 'none';
            
            // Position absolue par rapport au bouton parent
            button.style.position = 'relative';
            button.style.overflow = 'hidden';
            
            // Positionner le cercle au point de clic
            const x = event.clientX - rect.left;
            const y = event.clientY - rect.top;
            circle.style.left = `${x - size/2}px`;
            circle.style.top = `${y - size/2}px`;
            
            // Supprimer les anciens ripples
            const ripples = button.getElementsByClassName('ripple');
            while(ripples[0]) {
                ripples[0].remove();
            }
            
            // Ajouter le nouveau ripple
            circle.classList.add('ripple');
            button.appendChild(circle);
            
            // Démarrer l'animation
            setTimeout(() => {
                circle.style.transform = 'scale(4)';
                circle.style.opacity = '0';
            }, 10);
            
            // Nettoyer après l'animation
            setTimeout(() => {
                if (circle.parentNode === button) {
                    button.removeChild(circle);
                }
            }, 600);
        }

        // Add ripple effect to all buttons
        const buttons = document.querySelectorAll('.btn, .service-card, .team-member, .member-social a');
        buttons.forEach(button => {
            // Préparer le bouton pour l'effet ripple
            button.style.position = 'relative';
            button.style.overflow = 'hidden';
            button.style.transform = 'translateZ(0)'; // Forcer l'accélération matérielle
            
            // Ajouter l'écouteur d'événement
            button.addEventListener('mousedown', createRipple);
            
            // Empêcher le comportement par défaut du clic si c'est un lien
            if (button.tagName === 'A') {
                button.addEventListener('click', (e) => {
                    if (button.getAttribute('href') === '#') {
                        e.preventDefault();
                    }
                });
            }
        });

        // Parallax effect for hero section
        const hero = document.getElementById('hero');
        if (hero) {
            window.addEventListener('scroll', () => {
                const scrollPosition = window.pageYOffset;
                hero.style.backgroundPositionY = scrollPosition * 0.5 + 'px';
            });
        }

        // Add smooth scroll to all links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Typing animation for hero text
        const heroText = "Votre avenir commence ici";
        const typingElement = document.querySelector('.typing-text');
        
        if (typingElement) {
            let charIndex = 0;
            const typingSpeed = 100; // milliseconds
            const deleteSpeed = 50;
            const pauseAtEnd = 2000; // 2 seconds
            let isDeleting = false;
            let isPaused = false;
            
            function typeWriter() {
                if (isPaused) {
                    setTimeout(typeWriter, pauseAtEnd);
                    isPaused = false;
                    return;
                }
                
                const currentText = typingElement.textContent;
                
                if (!isDeleting && charIndex < heroText.length) {
                    // Typing
                    typingElement.textContent = heroText.substring(0, charIndex + 1);
                    charIndex++;
                    setTimeout(typeWriter, typingSpeed);
                } else if (isDeleting && charIndex > 0) {
                    // Deleting
                    typingElement.textContent = heroText.substring(0, charIndex - 1);
                    charIndex--;
                    setTimeout(typeWriter, deleteSpeed);
                } else {
                    // Change direction
                    isDeleting = !isDeleting;
                    if (!isDeleting) {
                        isPaused = true;
                    }
                    setTimeout(typeWriter, isDeleting ? pauseAtEnd : typingSpeed);
                }
            }
            
            // Start the animation after a short delay
            setTimeout(typeWriter, 1000);
        }
        
        // Add active class to current section in navigation
        const sections = document.querySelectorAll('section');
        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= (sectionTop - sectionHeight / 3)) {
                    current = section.getAttribute('id');
                }
            });

            document.querySelectorAll('nav a').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
