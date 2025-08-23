<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Exemples de composants - Allo Tawjih</title>
    
    <!-- Préchargement des polices -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chargement des assets avec Vite -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        [v-cloak] { display: none; }
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; }
        
        /* Styles temporaires pour le débogage */
        .debug-border { border: 1px solid #dee2e6; }
        .debug-bg { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-5">Exemples de composants</h1>
        
        <!-- Boutons -->
        <section class="mb-5">
            <h2 class="mb-4">Boutons</h2>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <button class="btn btn-primary">Primaire</button>
                <button class="btn btn-secondary">Secondaire</button>
                <button class="btn btn-success">Succès</button>
                <button class="btn btn-danger">Danger</button>
                <button class="btn btn-warning">Avertissement</button>
                <button class="btn btn-info">Info</button>
                <button class="btn btn-light">Clair</button>
                <button class="btn btn-dark">Sombre</button>
            </div>
            
            <h3 class="h5 mt-4">Tailles</h3>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                <button class="btn btn-primary btn-sm">Petit</button>
                <button class="btn btn-primary">Normal</button>
                <button class="btn btn-primary btn-lg">Grand</button>
            </div>
            
            <h3 class="h5 mt-4">Styles</h3>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <button class="btn btn-outline-primary">Outline</button>
                <button class="btn btn-text-primary">Texte</button>
                <button class="btn btn-underline">Souligné</button>
                <button class="btn btn-primary btn-loading">Chargement</button>
            </div>
        </section>
        
        <!-- Badges -->
        <section class="mb-5">
            <h2 class="mb-4">Badges</h2>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <span class="badge bg-primary">Primaire</span>
                <span class="badge bg-secondary">Secondaire</span>
                <span class="badge bg-success">Succès</span>
                <span class="badge bg-danger">Danger</span>
                <span class="badge bg-warning text-dark">Avertissement</span>
                <span class="badge bg-info text-dark">Info</span>
                <span class="badge bg-light text-dark">Clair</span>
                <span class="badge bg-dark">Sombre</span>
            </div>
            
            <h3 class="h5 mt-4">Styles</h3>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                <span class="badge rounded-pill bg-primary">Pill</span>
                <span class="badge bg-outline-primary">Outline</span>
                <span class="badge bg-dot bg-primary"></span>
            </div>
        </section>
        
        <!-- Alertes -->
        <section class="mb-5">
            <h2 class="mb-4">Alertes</h2>
            <div class="alert alert-primary" role="alert">
                Une simple alerte primaire !
            </div>
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Bien joué !</h4>
                <p>Vous avez réussi à lire ce message d'alerte important. Ce texte s'affiche en exemple pour vous montrer comment fonctionne l'alerte.</p>
                <hr>
                <p class="mb-0">N'oubliez pas de vérifier tout ce qui précède.</p>
            </div>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Attention !</strong> Cette alerte peut être fermée.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        </section>
        
        <!-- Cartes -->
        <section class="mb-5">
            <h2 class="mb-4">Cartes</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Carte simple</h5>
                            <p class="card-text">Un exemple de texte dans une carte. Le texte peut être aussi long que nécessaire.</p>
                            <a href="#" class="btn btn-primary">Aller quelque part</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Carte avec fond coloré</h5>
                            <p class="card-text">Cette carte utilise un fond coloré pour mettre en valeur le contenu.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="card-icon mb-3">
                                <i class="fas fa-star"></i>
                            </div>
                            <h5 class="card-title">Carte avec icône</h5>
                            <p class="card-text">Idéal pour mettre en avant des fonctionnalités ou des statistiques.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Barres de progression -->
        <section class="mb-5">
            <h2 class="mb-4">Barres de progression</h2>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span>Progression</span>
                    <span>75%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            
            <div class="progress-with-label">
                <div class="d-flex justify-content-between mb-1">
                    <span>Tâche terminée</span>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100">90%</div>
                </div>
            </div>
        </section>
        
        <!-- Listes -->
        <section class="mb-5">
            <h2 class="mb-4">Listes</h2>
            <div class="row">
                <div class="col-md-6">
                    <h3 class="h5">Liste simple</h3>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Élément 1
                            <span class="badge bg-primary rounded-pill">14</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Élément 2
                            <span class="badge bg-primary rounded-pill">2</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Élément 3
                            <span class="badge bg-primary rounded-pill">1</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h3 class="h5">Liste personnalisée</h3>
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action active">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">Titre de l'élément de liste</h5>
                                <small>Il y a 3 jours</small>
                            </div>
                            <p class="mb-1">Contenu de l'élément de liste. Peut contenir du texte et d'autres éléments.</p>
                            <small>Et quelques petits caractères.</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">Deuxième élément</h5>
                                <small class="text-muted">Il y a 5 jours</small>
                            </div>
                            <p class="mb-1">Un autre contenu d'élément de liste.</p>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Avatars -->
        <section class="mb-5">
            <h2 class="mb-4">Avatars</h2>
            <div class="d-flex gap-3 align-items-center mb-4">
                <div class="avatar avatar-sm">
                    <img src="https://ui-avatars.com/api/?name=John+Doe&background=random" alt="John Doe">
                </div>
                <div class="avatar">
                    <img src="https://ui-avatars.com/api/?name=Jane+Smith&background=random" alt="Jane Smith">
                </div>
                <div class="avatar avatar-lg">
                    <img src="https://ui-avatars.com/api/?name=Robert+Johnson&background=random" alt="Robert Johnson">
                </div>
                <div class="avatar avatar-xl">
                    <img src="https://ui-avatars.com/api/?name=Emily+Davis&background=random" alt="Emily Davis">
                </div>
            </div>
            
            <h3 class="h5 mt-4">Avec statut</h3>
            <div class="d-flex gap-3 align-items-center">
                <div class="avatar avatar-status-online">
                    <img src="https://ui-avatars.com/api/?name=John+Doe&background=random" alt="John Doe">
                </div>
                <div class="avatar avatar-status-offline">
                    <img src="https://ui-avatars.com/api/?name=Jane+Smith&background=random" alt="Jane Smith">
                </div>
                <div class="avatar avatar-status-away">
                    <img src="https://ui-avatars.com/api/?name=Robert+Johnson&background=random" alt="Robert Johnson">
                </div>
                <div class="avatar avatar-status-busy">
                    <img src="https://ui-avatars.com/api/?name=Emily+Davis&background=random" alt="Emily Davis">
                </div>
            </div>
        </section>
        
        <!-- Cartes de statistiques -->
        <section class="mb-5">
            <h2 class="mb-4">Cartes de statistiques</h2>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-card-body">
                            <div class="stats-card-icon bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="stats-card-value">2,345</h3>
                            <p class="stats-card-title">Utilisateurs</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card stats-card-primary">
                        <div class="stats-card-body">
                            <div class="stats-card-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h3 class="stats-card-value">1,234</h3>
                            <p class="stats-card-title">Commandes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card stats-card-success">
                        <div class="stats-card-body">
                            <div class="stats-card-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <h3 class="stats-card-value">$12,345</h3>
                            <p class="stats-card-title">Revenus</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card stats-card-gradient">
                        <div class="stats-card-body">
                            <div class="stats-card-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 class="stats-card-value">+12.5%</h3>
                            <p class="stats-card-title">Croissance</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Onglets -->
        <section class="mb-5">
            <h2 class="mb-4">Onglets</h2>
            <div class="card">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Accueil</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Profil</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Contact</button>
                    </li>
                </ul>
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <h5>Contenu de l'onglet Accueil</h5>
                            <p>Ceci est un exemple de contenu pour l'onglet Accueil. Vous pouvez y mettre tout ce que vous voulez.</p>
                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <h5>Contenu de l'onglet Profil</h5>
                            <p>Ceci est un exemple de contenu pour l'onglet Profil.</p>
                        </div>
                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                            <h5>Contenu de l'onglet Contact</h5>
                            <p>Ceci est un exemple de contenu pour l'onglet Contact.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Accordeons -->
        <section class="mb-5">
            <h2 class="mb-4">Accordéons</h2>
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Élément d'accordéon #1
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <strong>Ceci est le premier élément du corps de l'accordéon.</strong> Il est affiché par défaut, jusqu'à ce que le plugin de repli ajoute les classes appropriées que nous utilisons pour styliser chaque élément. Ces classes contrôlent l'apparence globale, ainsi que l'affichage et le masquage via des transitions CSS. Vous pouvez modifier tout cela avec du CSS personnalisé ou remplacer nos variables par défaut. Il est également intéressant de noter que tout HTML placé dans un <code>.accordion-body</code> est limité en hauteur et défile si nécessaire.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Élément d'accordéon #2
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <strong>Ceci est le deuxième élément du corps de l'accordéon.</strong> Il est masqué par défaut, jusqu'à ce que le plugin de collapsible ajoute les classes appropriées que nous utilisons pour styliser chaque élément. Ces classes contrôlent l'apparence globale, ainsi que l'affichage et le masquage via des transitions CSS. Vous pouvez modifier tout cela avec du CSS personnalisé ou remplacer nos variables par défaut. Il est également intéressant de noter que tout HTML placé dans un <code>.accordion-body</code> est limité en hauteur et défile si nécessaire.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Élément d'accordéon #3
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <strong>Ceci est le troisième élément du corps de l'accordéon.</strong> Il est masqué par défaut, jusqu'à ce que le plugin de collapsible ajoute les classes appropriées que nous utilisons pour styliser chaque élément. Ces classes contrôlent l'apparence globale, ainsi que l'affichage et le masquage via des transitions CSS. Vous pouvez modifier tout cela avec du CSS personnalisé ou remplacer nos variables par défaut. Il est également intéressant de noter que tout HTML placé dans un <code>.accordion-body</code> est limité en hauteur et défile si nécessaire.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <!-- Scripts -->
    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous" defer></script>
    @vite(['resources/js/app.js'])
</body>
</html>
