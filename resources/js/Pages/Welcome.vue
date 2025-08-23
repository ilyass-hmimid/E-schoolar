<template>
  <div>
    <Head title="Allo Tawjih - Centre de Soutien Scolaire" />
    
    <!-- Barre de navigation fixe -->
    <nav class="fixed top-0 left-0 right-0 w-full bg-white shadow-lg z-50 border-b border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
          <!-- Logo -->
          <div class="flex-shrink-0">
            <a href="#accueil" @click.prevent="scrollToSection('accueil')" class="text-2xl font-bold text-indigo-600 hover:text-indigo-700 transition-colors">
              <span class="flex items-center ">
                <img src="../../../public/imgs/logo.png" class="fas fa-graduation-cap mr-2"></img>
                <b style="margin-left: 10px; margin-top:-8px;">Allo Tawjih</b>
              </span>
            </a>
          </div>
          
          <!-- Menu desktop -->
          <div class="hidden md:flex items-center space-x-8">
            <a href="#accueil" @click="scrollToSection('accueil', $event)" class="px-3 py-2 text-sm font-medium text-gray-800 hover:text-black transition-colors duration-200">
              Accueil
            </a>
            <a href="#services" @click="scrollToSection('services', $event)" class="px-3 py-2 text-sm font-medium text-gray-800 hover:text-black transition-colors duration-200">
              Nos services
            </a>
            <a href="#a-propos" @click="scrollToSection('a-propos', $event)" class="px-3 py-2 text-sm font-medium text-gray-800 hover:text-black transition-colors duration-200">
              À propos
            </a>
            <a href="#contact" @click="scrollToSection('contact', $event)" class="px-3 py-2 text-sm font-medium text-gray-800 hover:text-black transition-colors duration-200">
              Contact
            </a>
          </div>
          
          <!-- Bouton de connexion -->
          <div class="hidden md:flex items-center">
            <a :href="getDestinationUrl()" class="px-4 py-2 rounded-md text-sm font-medium text-white bg-black hover:bg-gray-800 transition-colors duration-200 whitespace-nowrap">
              Se connecter
            </a>
          </div>
          
          <!-- Bouton menu mobile -->
          <div class="md:hidden flex items-center">
            <button @click="isMenuOpen = !isMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 focus:outline-none">
              <span class="sr-only">Ouvrir le menu</span>
              <svg class="h-6 w-6" :class="{'hidden': isMenuOpen, 'block': !isMenuOpen}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg class="h-6 w-6" :class="{'block': isMenuOpen, 'hidden': !isMenuOpen}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Menu mobile -->
      <div v-if="isMenuOpen" class="md:hidden bg-white border-t border-gray-200 shadow-lg">
        <div class="px-4 py-3 space-y-2">
          <a href="#accueil" @click="handleMenuClick('accueil', $event)" class="block px-4 py-2.5 text-sm font-medium text-gray-800 hover:text-black">
            Accueil
          </a>
          <a href="#services" @click="handleMenuClick('services', $event)" class="block px-4 py-2.5 text-sm font-medium text-gray-800 hover:text-black">
            Nos services
          </a>
          <a href="#features" @click="handleMenuClick('features', $event)" class="block px-4 py-2.5 text-sm font-medium text-gray-800 hover:text-black">
            Fonctionnalités
          </a>
          <a href="#a-propos" @click="handleMenuClick('a-propos', $event)" class="block px-4 py-2.5 text-sm font-medium text-gray-800 hover:text-black">
            À propos
          </a>
          <a href="#contact" @click="handleMenuClick('contact', $event)" class="block px-4 py-2.5 text-sm font-medium text-gray-800 hover:text-black">
            Contact
          </a>
          <div class="border-t border-gray-200 my-2"></div>
          <a :href="getDestinationUrl()" class="block text-center px-4 py-2.5 rounded-md text-sm font-medium text-white bg-black hover:bg-gray-800">
            Se connecter
          </a>
        </div>
      </div>
    </nav>
    
    <!-- Espace pour la barre de navigation fixe -->
    <div class="h-16"></div>
    
    <!-- Hero Section -->
    <div id="accueil" class="hero-section">
      <div class="hero-container">
        <div class="hero-content fade-in">
          <h1 class="hero-title">
            Votre partenaire de réussite scolaire
            <span class="hero-subtitle">Accompagnement personnalisé pour chaque élève</span>
          </h1>
          <p class="hero-description">
            Renforcez vos connaissances, comblez vos lacunes et améliorez vos résultats
            avec notre équipe d'enseignants qualifiés et nos programmes adaptés.
          </p>
          <div class="hero-buttons">
            <a :href="getDestinationUrl()" class="hero-button primary">
              Se Connecter
            </a>
            <a href="#services" class="hero-button secondary" @click="scrollToSection('services', $event)">
              Découvrir nos services
            </a>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Services Section -->
    <div id="services" class="services-section">
      <div class="section-container">
        <div class="section-header fade-in">
          <h2 class="section-title">
            Nos services complets
          </h2>
          <p class="section-description">
            Découvrez comment notre plateforme peut transformer la gestion de votre établissement
          </p>
        </div>
        
        <div class="services-grid">
          <!-- Service 1 -->
          <div class="service-card">
            <div class="service-content">
              <div class="service-icon bg-blue-100">
                <i class="fas fa-user-graduate"></i>
              </div>
              <h3 class="service-title">Gestion des étudiants</h3>
              <p class="service-description">
                Suivez les dossiers académiques, les présences et les performances de tous vos étudiants en un seul endroit.
              </p>
              <ul class="service-features">
                <li class="feature-item">
                  <i class="fas fa-check-circle feature-icon"></i>
                  Inscription et suivi des étudiants
                </li>
                <li class="feature-item">
                  <i class="fas fa-check-circle feature-icon"></i>
                  Gestion des dossiers académiques
                </li>
                <li class="feature-item">
                  <i class="fas fa-check-circle feature-icon"></i>
                  Suivi des présences
                </li>
              </ul>
            </div>
          </div>
          
          <!-- Service 2 -->
          <div class="service-card">
            <div class="service-content">
              <div class="service-icon bg-green-100">
                <i class="fas fa-chalkboard-teacher"></i>
              </div>
              <h3 class="service-title">Gestion des enseignants</h3>
              <p class="service-description">
                Optimisez l'emploi du temps et la gestion des ressources pédagogiques de vos enseignants.
              </p>
              <ul class="service-features">
                <li class="feature-item">
                  <i class="fas fa-check-circle feature-icon"></i>
                  Gestion des emplois du temps
                </li>
                <li class="feature-item">
                  <i class="fas fa-check-circle feature-icon"></i>
                  Suivi des charges de travail
                </li>
                <li class="feature-item">
                  <i class="fas fa-check-circle feature-icon"></i>
                  Communication simplifiée
                </li>
              </ul>
            </div>
          </div>
          
          <!-- Service 3 -->
          <div class="service-card">
            <div class="service-content">
              <div class="service-icon bg-purple-100">
                <i class="fas fa-chart-line"></i>
              </div>
              <h3 class="service-title">Suivi des performances</h3>
              <p class="service-description">
                Analysez les performances des étudiants et générez des rapports détaillés pour une meilleure prise de décision.
              </p>
              <ul class="service-features">
                <li class="feature-item">
                  <i class="fas fa-check-circle feature-icon"></i>
                  Tableaux de bord personnalisables
                </li>
                <li class="feature-item">
                  <i class="fas fa-check-circle feature-icon"></i>
                  Rapports détaillés
                </li>
                <li class="feature-item">
                  <i class="fas fa-check-circle feature-icon"></i>
                  Analyse des tendances
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Features Section -->
    <div class="features-section">
      <div class="section-container">
        <div class="features-grid">
          <!-- Feature 1 -->
          <div class="feature-card">
            <div class="feature-icon-container bg-blue-50">
              <i class="fas fa-mobile-alt feature-icon"></i>
            </div>
            <h3 class="feature-title">Interface intuitive</h3>
            <p class="feature-description">
              Une interface moderne et facile à utiliser, accessible depuis n'importe quel appareil.
            </p>
          </div>
          
          <!-- Feature 2 -->
          <div class="feature-card">
            <div class="feature-icon-container bg-green-50">
              <i class="fas fa-shield-alt feature-icon"></i>
            </div>
            <h3 class="feature-title">Sécurité renforcée</h3>
            <p class="feature-description">
              Vos données sont protégées avec les dernières technologies de chiffrement.
            </p>
          </div>
          
          <!-- Feature 3 -->
          <div class="feature-card">
            <div class="feature-icon-container bg-purple-50">
              <i class="fas fa-headset feature-icon"></i>
            </div>
            <h3 class="feature-title">Support 24/7</h3>
            <p class="feature-description">
              Une équipe dédiée est disponible pour vous accompagner à tout moment.
            </p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Call to Action -->
    <div class="cta-section">
      <div class="cta-container">
        <h3 class="cta-title">Découvrez comment nous pouvons vous accompagner dans votre parcours d'orientation?</h3>
        <p class="cta-description">Rejoignez nos élèves qui font confiance à Allo Tawjih pour leur réussite scolaire</p>
        <Link 
          :href="getDestinationUrl()" 
          class="cta-button"
        >
          <span>Commencer maintenant</span>
          <svg class="cta-button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
          </svg>
        </Link>
      </div>
    </div>
    
    <!-- About Section -->
    <div id="a-propos" class="about-section">
      <div class="section-container">
        <div class="about-content">
          <div class="about-text fade-in">
            <h2 class="about-title">
              À propos de <span class="highlight">Allo Tawjih</span>
            </h2>
            <p class="about-description">
              Allo Tawjih est un centre de soutien scolaire qui accompagne les élèves à travers des cours supplémentaires et un suivi personnalisé pour renforcer leurs connaissances, combler leurs lacunes et améliorer leurs résultats.
            </p>
            <p class="about-description">
              Grâce à une équipe d'enseignants qualifiés et des programmes adaptés à chaque niveau, nous aidons les étudiants à progresser dans leurs études, à se préparer aux examens et à développer leur confiance en eux.
            </p>
            <div class="features-list">
              <div class="feature-item">
                <div class="feature-icon">
                  <i class="fas fa-check"></i>
                </div>
                <p class="feature-text">
                  Cours de soutien dans toutes les matières principales
                </p>
              </div>
              <div class="feature-item">
                <div class="feature-icon">
                  <i class="fas fa-check"></i>
                </div>
                <p class="feature-text">
                  Suivi personnalisé et évaluation continue
                </p>
              </div>
              <div class="feature-item">
                <div class="feature-icon">
                  <i class="fas fa-check"></i>
                </div>
                <p class="feature-text">
                  Préparation aux examens nationaux
                </p>
              </div>
            </div>
          </div>
          <div class="about-image">
            <div class="image-container">
              <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80" alt="Étudiants en cours" class="about-img">
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Contact Section -->
    <section id="contact" class="py-16 bg-gray-50">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Contactez-nous</h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Notre équipe est là pour répondre à toutes vos questions
          </p>
        </div>
        
        <div class="contact-grid">
          <div class="contact-info">
            <h3>Informations de contact</h3>
            
            <div class="contact-method">
              <div class="contact-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="contact-details">
                <h4>Email</h4>
                <p>contact@allotawjih.ma</p>
              </div>
            </div>
            
            <div class="contact-method">
              <div class="contact-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
              </div>
              <div class="contact-details">
                <h4>Téléphone</h4>
                <p>+212 5XX-XXXXXX</p>
              </div>
            </div>
            
            <div class="contact-method">
              <div class="contact-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div class="contact-details">
                <h4>Adresse</h4>
                <p>123 Avenue Mohammed V<br>Casablanca, Maroc</p>
              </div>
            </div>
            
            <div class="social-links">
              <a href="#" class="social-link" aria-label="Facebook">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                </svg>
              </a>
              <a href="#" class="social-link" aria-label="Twitter">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                </svg>
              </a>
              <a href="#" class="social-link" aria-label="Instagram">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path fill-rule="evenodd" d="M12 2.25c-2.76 0-5 2.19-5 4.95 0 2.06 1.3 3.71 3.47 4.17.385.13.77.25 1.17.25.77 0 1.35-.46 1.46-1.08C11.33 9.2 10.5 9 9 9c-1.03 0-1.9.57-2.22 1.42-.315.92-.21 2.07.46 3.17.67 1.1 1.84 2.02 3.38 2.02 2.22 0 4-1.57 4.38-3.62C16.62 10.22 15.85 9 14.38 9c-1.4 0-2.55.59-3.33 1.59a3.42 3.42 0 00-.33 1.62 3.42 3.42 0 00.33 1.62c.78 1 2.04 1.59 3.59 1.59 1.66 0 3.14-.8 4.07-2.08.92-1.3 1.48-2.96 1.48-4.95C20 5.19 17.92 2.25 12 2.25z" clip-rule="evenodd" />
                </svg>
              </a>
              <a href="#" class="social-link" aria-label="LinkedIn">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                </svg>
              </a>
            </div>
          </div>
          
          <div class="contact-form">
            <h3>Envoyez-nous un message</h3>
            <form @submit.prevent="submitContact" class="space-y-6">
              <div class="form-group">
                <label for="name">Nom complet</label>
                <input type="text" id="name" name="name" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="subject">Sujet</label>
                <input type="text" id="subject" name="subject" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="message">Votre message</label>
                <textarea id="message" name="message" rows="5" class="form-control" required></textarea>
              </div>
              <button type="submit" class="btn-send-message">Envoyer le message <span class="arrow">→</span></button>
            </form>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Footer simplifié -->
    <footer class="bg-gray-100 border-t border-gray-200 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <p class="text-gray-600">
            Allo Tawjih - Centre de Soutien Scolaire
          </p>
          <p class="text-gray-500 text-sm mt-2">
            123 Avenue Hassan II, Marrakech 20000, Maroc<br>
            Tél: <a href="tel:+212522000000" class="text-indigo-600 hover:underline">+212 708-775909</a> | 
            Email: <a href="mailto:contact@allotawjih.ma" class="text-indigo-600 hover:underline">contact@allotawjih.ma</a>
          </p>
          <p class="text-gray-400 text-xs mt-4">
            &copy; {{ currentYear }} Allo Tawjih. Tous droits réservés.
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const page = usePage();
const isMenuOpen = ref(false);
const currentYear = ref(new Date().getFullYear());

// Fonction pour faire défiler vers une section
const scrollToSection = (sectionId, event) => {
  if (event) {
    event.preventDefault();
  }
  
  const element = document.getElementById(sectionId);
  if (element) {
    const headerOffset = 80; // Hauteur de la barre de navigation
    const elementPosition = element.getBoundingClientRect().top;
    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

    window.scrollTo({
      top: offsetPosition,
      behavior: 'smooth'
    });
    
    // Mise à jour de l'URL sans rechargement de la page
    history.pushState(null, null, `#${sectionId}`);
  }
  
  isMenuOpen.value = false;
};

// Gestion du clic sur le menu mobile
const handleMenuClick = (sectionId, event) => {
  if (event) {
    event.preventDefault();
  }
  scrollToSection(sectionId, event);
  isMenuOpen.value = false;
};

// Fermer le menu lors du défilement
onMounted(() => {
  window.addEventListener('scroll', () => {
    if (isMenuOpen.value) {
      isMenuOpen.value = false;
    }
  });
});

const getDestinationUrl = () => {
  return page.props.auth?.user !== null ? '/dashboard' : '/login';
};

// Gestion de la soumission du formulaire de contact
const submitContact = (event) => {
  const form = event.target;
  const formData = new FormData(form);
  
  // Ici, vous pouvez ajouter la logique pour envoyer les données du formulaire
  // Par exemple, avec axios ou fetch vers votre API
  console.log('Formulaire soumis avec les données :', Object.fromEntries(formData));
  
  // Afficher un message de confirmation
  alert('Merci pour votre message ! Nous vous contacterons bientôt.');
  
  // Réinitialiser le formulaire
  form.reset();
};
</script>

<style scoped>
/* Styles pour la navigation */
img{
  width: 50px;
  height: 50px;
  margin-top: -9px;
}
nav {
  transition: all 0.3s ease;
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  right: 0 !important;
  z-index: 50 !important;
  background-color: #fff !important;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
  border-bottom: 1px solid #e5e7eb !important;
  width: 100% !important;
  height : 65px ;
  margin: 0 !important;
  padding: 0 !important;
}

/* Style des liens du menu */
nav a {
  color: #1f2937 !important; /* gris-800 */
  text-decoration: none;
  transition: all 0.2s ease;
  padding-top: 9px;
  margin: 10px;
}

/* Style du bouton de connexion */
nav a.bg-black {
  background-color: #000 !important;
  color: white !important;
  border-radius: 0.375rem;
  padding: 0.5rem 1rem;
}

/* Style du séparateur */
nav .h-6 {
  height: 1.5rem;
}

nav .w-px {
  width: 1px;
}

nav .bg-gray-300 {
  background-color: #e5e7eb;
}

/* Effet de survol sur les liens du menu */
nav a {
  position: relative;
  transition: all 0.2s ease;
}

/* Animation de soulignement au survol */
nav a:not(.btn-contact)::after {
  content: '';
  position: absolute;
  width: 0;
  height: 2px;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  background-color: #4f46e5;
  transition: width 0.3s ease;
}

nav a:hover::after {
  width: 60%;
}

/* Style pour le bouton contact */
nav .btn-contact {
  transition: all 0.3s ease;
}

nav .btn-contact:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

/* Animation du menu mobile */
@keyframes slideDown {
  from { 
    opacity: 0;
    transform: translateY(-10px);
  }
  to { 
    opacity: 1;
    transform: translateY(0);
  }
}

/* Correction de l'affichage du menu */
.md\:flex {
  display: flex !important;
}

.hidden {
  display: none;
}

@media (min-width: 768px) {
  .md\:hidden {
    display: none !important;
  }
}

.md\:hidden {
  animation: slideDown 0.3s ease-out;
  display: none;
}

/* Style pour les liens actifs */
nav a.router-link-active,
nav a.router-link-exact-active {
  color: #4f46e5;
  font-weight: 500;
}

/* Amélioration de l'accessibilité au focus */
nav a:focus {
  outline: 2px solid #4f46e5;
  outline-offset: 2px;
  border-radius: 0.25rem;
}

/* Style pour le bouton de menu mobile */
button:focus {
  outline: 2px solid #4f46e5;
  outline-offset: 2px;
  border-radius: 0.25rem;
}

/* Styles pour le footer */
footer {
  background-color: #f9fafb;
  margin-top: 40px;
}

footer a {
  transition: color 0.2s ease;
}

footer a:hover {
  color: #4f46e5;
  text-decoration: underline;
}

/* Styles pour la section Contact */
#contact {
  background-color: #f9fafb;
  padding: 6rem 0;
}

#contact h2 {
  font-size: 2.25rem;
  font-weight: 800;
  color: #111827;
  margin-bottom: 1rem;
  text-align: center;
}

#contact p {
  color: #6b7280;
  font-size: 1.125rem;
  text-align: center;
  max-width: 42rem;
  margin-left: auto;
  margin-right: auto;
}

.contact-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 3rem;
  margin-top: 3rem;
  max-width: 1280px;
  margin-left: auto;
  margin-right: auto;
  padding: 0 1rem;
}

@media (min-width: 1024px) {
  .contact-grid {
    grid-template-columns: 1fr 1fr;
    padding: 0 2rem;
  }
}

.contact-info h3,
.contact-form h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 1.5rem;
}

.contact-method {
  display: flex;
  margin-bottom: 1.5rem;
  align-items: flex-start;
}

.contact-icon {
  background-color: rgba(37, 99, 235, 0.1);
  border-radius: 0.5rem;
  padding: 0.5rem;
  margin-right: 1rem;
  color: #2563eb;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
}

.contact-icon svg {
  width: 1.25rem;
  height: 1.25rem;
}

.contact-details h4 {
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.25rem;
}

.contact-details p {
  color: #6b7280;
  margin: 0;
  text-align: left;
  font-size: 1rem;
  line-height: 1.5;
}

.social-links {
  display: flex;
  margin-top: 2rem;
  gap: 1rem;
}

.social-link {
  background-color: white;
  border-radius: 0.5rem;
  width: 2.5rem;
  height: 2.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  transition: all 0.3s ease;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.social-link:hover {
  color: #2563eb;
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Styles pour le formulaire de contact */
.contact-form {
  background: white;
  padding: 2rem;
  border-radius: 0.75rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.form-group {
  margin-bottom: 1.25rem;
}

.form-group label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #111827;
  margin-bottom: 0.5rem;
}

.form-control {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-control:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
}

textarea.form-control {
  min-height: 10rem;
  resize: vertical;
}

.btn-send-message {
  background: #4f46e5;
  color: white;
  border: none;
  padding: 12px 24px;
  font-size: 1rem;
  font-weight: 500;
  border-radius: 6px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  transition: all 0.3s ease;
  white-space: nowrap;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.btn-send-message:hover {
  background: #4338ca;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-send-message .arrow {
  margin-left: 8px;
  transition: transform 0.3s ease;
}

.btn-send-message:hover .arrow {
  transform: translateX(4px);
}

/* Styles pour le Footer */
footer {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: #e2e8f0;
  padding: 4rem 0 0;
  position: relative;
  overflow: hidden;
}

footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899, #f59e0b, #10b981, #3b82f6);
  background-size: 400% 400%;
  animation: gradientBG 15s ease infinite;
}

@keyframes gradientBG {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

.footer-content {
  max-width: 1280px;
  margin: 0 auto;
  padding: 2rem 1.5rem 3rem;
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 3rem;
  position: relative;
  z-index: 1;
}

@media (min-width: 768px) {
  .footer-content {
    grid-template-columns: repeat(4, 1fr);
    padding: 0 2rem;
  }
}

.footer-logo {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
}

.footer-logo-text {
  font-size: 1.25rem;
  font-weight: 700;
  color: white;
  margin-left: 0.75rem;
}

.footer-about p {
  margin-top: 1rem;
  line-height: 1.6;
  font-size: 0.875rem;
}

.footer-links h3,
.footer-contact h3 {
  color: white;
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 1.25rem;
}

.footer-links ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 0.5rem;
}

.footer-links a {
  color: #cbd5e1;
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 0.95rem;
  display: inline-block;
  position: relative;
  padding: 0.25rem 0;
}

.footer-links a::after {
  content: '';
  position: absolute;
  width: 0;
  height: 2px;
  bottom: 0;
  left: 0;
  background: linear-gradient(90deg, #3b82f6, #8b5cf6);
  transition: width 0.3s ease;
}

.footer-links a:hover {
  color: white;
  padding-left: 8px;
}

.footer-links a:hover::after {
  width: 100%;
}

.footer-links a:hover {
  color: white;
}

.contact-item {
  display: flex;
  margin-bottom: 1rem;
  align-items: flex-start;
}

.contact-item .contact-icon {
  margin-right: 0.75rem;
  color: #6b7280;
  background: none;
  padding: 0;
  font-size: 1rem;
  width: auto;
  height: auto;
  margin-top: 0.25rem;
}

.contact-text {
  color: #9ca3af;
  line-height: 1.5;
  font-size: 0.875rem;
}

.footer-bottom {
  background: rgba(0, 0, 0, 0.2);
  padding: 1.5rem 1rem;
  text-align: center;
  color: #94a3b8;
  font-size: 0.875rem;
  position: relative;
  backdrop-filter: blur(5px);
  border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.footer-bottom::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
}

/* Animations */
.fade-in {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.fade-in.visible {
  opacity: 1;
  transform: translateY(0);
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeInUp {
  animation: fadeInUp 0.6s ease-out forwards;
}
/* Styles de base */
:root {
  --primary: #2563eb;
  --primary-hover: #1d4ed8;
  --text-primary: #111827;
  --text-secondary: #6b7280;
  --border-color: #e5e7eb;
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --transition: all 0.3s ease;
}

.welcome-container {
  min-height: 100vh;
  background-color: white;
}

.header {
  background-color: white;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  position: sticky;
  top: 0;
  z-index: 50;
}

.header-content {
  max-width: 1280px;
  margin-left: auto;
  margin-right: auto;
  padding-left: 1rem;
  padding-right: 1rem;
}

.header-nav {
  display: flex;
  justify-content: space-between;
  height: 4rem;
}

.header-logo-container {
  display: flex;
  align-items: center;
}

.logo-wrapper {
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

.logo-icon {
  height: 2.5rem;
  width: 2.5rem;
  border-radius: 0.5rem;
  background-color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-svg {
  height: 1.5rem;
  width: 1.5rem;
  color: white;
}

.logo-text {
  margin-left: 0.75rem;
  font-size: 1.25rem;
  font-weight: 700;
  color: #111827;
}

.main-nav {
  display: none;
  margin-left: 2.5rem;
  gap: 2rem;
}

@media (min-width: 768px) {
  .main-nav {
    display: flex;
  }
}

.nav-link {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.25rem 0.5rem;
  border-bottom: 2px solid transparent;
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
  text-decoration: none;
  transition: color 0.2s, border-color 0.2s;
}

.nav-link:hover {
  color: #374151;
  border-bottom-color: #d1d5db;
}

.nav-link.active {
  color: #111827;
  border-bottom-color: #3b82f6;
}

/* Styles pour le bouton de connexion */
.auth-button {
  margin-left: 2rem;
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  border: 1px solid transparent;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: white;
  background-color: #2563eb;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  text-decoration: none;
  transition: background-color 0.2s;
}

.auth-button:hover {
  background-color: #1d4ed8;
}

/* Conteneur d'authentification */
.auth-container {
  display: none;
  margin-left: 1.5rem;
  align-items: center;
}

@media (min-width: 768px) {
  .auth-container {
    display: flex;
  }
}

/* Bouton de menu mobile */
.mobile-menu-button {
  margin-right: -0.5rem;
  display: flex;
  align-items: center;
  padding: 0.5rem;
  border-radius: 0.375rem;
  color: #9ca3af;
  background-color: white;
  transition: color 0.2s, background-color 0.2s;
}

.mobile-menu-button:hover {
  color: #6b7280;
  background-color: #f3f4f6;
}

.mobile-menu-button:focus {
  outline: 2px solid transparent;
  outline-offset: 2px;
  box-shadow: 0 0 0 2px #3b82f6;
}

.mobile-menu-icon {
  height: 1.5rem;
  width: 1.5rem;
}

/* Conteneur du menu mobile */
.mobile-menu-container {
  display: flex;
  align-items: center;
  margin-right: -0.5rem;
}

@media (min-width: 768px) {
  .mobile-menu-container {
    display: none;
  }
}

/* Menu mobile déroulant */
.mobile-menu-dropdown {
  padding-top: 0.5rem;
  padding-bottom: 0.75rem;
}

.mobile-menu-link {
  display: block;
  padding: 0.5rem 1rem;
  color: #4b5563;
  text-decoration: none;
  transition: background-color 0.2s, color 0.2s;
}

.mobile-menu-link:hover {
  background-color: #f3f4f6;
  color: #111827;
}

.mobile-menu-link.active {
  color: #111827;
  background-color: #e5e7eb;
  border-left: 3px solid #3b82f6;
}

/* Hero Section */
.hero-section {
  background: linear-gradient(to right, #2563eb, #4f46e5);
  color: white;
  padding: 5rem 0;
}

@media (min-width: 768px) {
  .hero-section {
    padding: 7rem 0;
  }
}

.hero-container {
  max-width: 1280px;
  margin-left: auto;
  margin-right: auto;
  padding-left: 1rem;
  padding-right: 1rem;
}

.hero-content {
  text-align: center;
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.7s ease, transform 0.7s ease;
}

.fade-in {
  opacity: 1 !important;
  transform: translateY(0) !important;
}

.hero-title {
  font-size: 2.25rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  line-height: 1.2;
}

.hero-subtitle {
  display: block;
  color: #bfdbfe;
  font-size: 1.5rem;
  margin-top: 0.5rem;
}

.hero-description {
  font-size: 1.25rem;
  color: #bfdbfe;
  margin-bottom: 2.5rem;
  max-width: 48rem;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.6;
}

.hero-buttons {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  justify-content: center;
  max-width: 28rem;
  margin-left: auto;
  margin-right: auto;
}

@media (min-width: 640px) {
  .hero-buttons {
    flex-direction: row;
    max-width: none;
  }
  
  .hero-title {
    font-size: 3rem;
  }
  
  .hero-subtitle {
    font-size: 1.75rem;
  }
}

.hero-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 2rem;
  border-radius: 0.375rem;
  font-weight: 500;
  font-size: 1.125rem;
  text-align: center;
  text-decoration: none;
  transition: all 0.3s ease;
}

.hero-button.primary {
  background-color: white;
  color: #1e40af;
  border: 1px solid transparent;
}

.hero-button.primary:hover {
  background-color: #eff6ff;
  transform: scale(1.05);
}

.hero-button.secondary {
  background-color: rgba(255, 255, 255, 0.1);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.hero-button.secondary:hover {
  background-color: rgba(255, 255, 255, 0.2);
  transform: scale(1.05);
}

/* Services Section */
.services-section {
  padding: 4rem 0;
  background-color: white;
}

.sec/* Style pour le conteneur du menu */
.max-w-7xl {
  max-width: 1280px;
  width: 100%;
  margin: 0 auto;
  padding: 0 1.5rem;
}

/* Centrage du menu */
nav .flex {
  display: flex;
}

/* Alignement des éléments du menu */
nav .items-center {
  align-items: center;
}

/* Conteneur principal du menu */
nav .justify-between {
  justify-content: space-between;
}

/* Espacement des éléments du menu */
nav .space-x-8 > * + * {
  margin-left: 2rem;
}

/* Empêcher le retrait à la ligne des éléments du menu */
nav .whitespace-nowrap {
  white-space: nowrap;
}

/* Style pour le bouton de connexion */
.md\:hidden + .md\:flex {
  margin-left: auto;
}

.section-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 1rem;
}

.section-header {
  text-align: center;
  margin-bottom: 4rem;
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.7s ease, transform 0.7s ease;
}

.section-title {
  font-size: 1.875rem;
  font-weight: 800;
  color: #111827;
  margin-bottom: 1rem;
}

.section-description {
  font-size: 1.25rem;
  color: #6b7280;
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
}

.services-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}

@media (min-width: 768px) {
  .services-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .services-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

.service-card {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.service-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.service-content {
  padding: 1.5rem;
}

.service-icon {
  width: 4rem;
  height: 4rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.service-icon i {
  font-size: 1.75rem;
}

.service-icon.bg-blue-100 i {
  color: #2563eb;
}

.service-icon.bg-green-100 i {
  color: #059669;
}

.service-icon.bg-purple-100 i {
  color: #7c3aed;
}

.service-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 0.75rem;
  line-height: 1.4;
}

.service-description {
  color: #6b7280;
  margin-bottom: 1.25rem;
  line-height: 1.6;
}

.service-features {
  list-style: none;
  padding: 0;
  margin: 0;
}

.feature-item {
  display: flex;
  align-items: center;
  color: #4b5563;
  margin-bottom: 0.5rem;
  font-size: 0.9375rem;
  line-height: 1.5;
}

.feature-icon {
  color: #10b981;
  margin-right: 0.5rem;
  font-size: 1rem;
}

/* Features Section */
.features-section {
  padding: 4rem 0;
  background-color: #f9fafb;
}

.features-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 1rem;
}

@media (min-width: 768px) {
  .features-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
  }
}

.feature-card {
  background: white;
  border-radius: 1.25rem;
  padding: 2rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  border: 1px solid #e5e7eb;
  transition: all 0.3s ease;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.feature-card:hover {
  transform: translateY(-0.5rem);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.feature-icon-container {
  width: 3.5rem;
  height: 3.5rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.5rem;
  transition: background-color 0.3s ease;
}

.feature-icon {
  font-size: 1.5rem;
  margin: 0;
  transition: transform 0.3s ease, color 0.3s ease;
}

.feature-card:hover .feature-icon {
  transform: scale(1.1);
}

.feature-card.bg-blue-50 .feature-icon {
  color: #3b82f6;
}

.feature-card.bg-green-50 .feature-icon {
  color: #10b981;
}

.feature-card.bg-purple-50 .feature-icon {
  color: #8b5cf6;
}

.feature-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 0.75rem;
  line-height: 1.4;
}

.feature-description {
  color: #6b7280;
  line-height: 1.6;
  margin: 0;
}

/* CTA Section */
.cta-section {
  padding: 6rem 1rem;
  background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
  color: white;
  text-align: center;
}

.cta-container {
  max-width: 64rem;
  margin: 0 auto;
  padding: 3rem 2rem;
  border-radius: 1.5rem;
}

.cta-title {
  font-size: 2.25rem;
  font-weight: 800;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.cta-description {
  font-size: 1.25rem;
  opacity: 0.9;
  margin-bottom: 2.5rem;
  max-width: 42rem;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.6;
}

.cta-button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background-color: white;
  color: #2563eb;
  font-size: 1.125rem;
  font-weight: 600;
  padding: 1rem 2rem;
  border-radius: 0.75rem;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.cta-button:hover {
  background-color: #f3f4f6;
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.cta-button:active {
  transform: translateY(0) scale(0.99);
}

.cta-button-icon {
  width: 1.25rem;
  height: 1.25rem;
  transition: transform 0.3s ease;
}

.cta-button:hover .cta-button-icon {
  transform: translateX(4px);
}

/* About Section */
.about-section {
  padding: 6rem 0;
  background-color: #f9fafb;
  position: relative;
  overflow: hidden;
}

.section-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 2rem;
  position: relative;
  z-index: 1;
}

.about-content {
  display: flex;
  flex-direction: column;
  gap: 4rem;
}

.about-text {
  flex: 1;
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.7s ease, transform 0.7s ease;
}

.about-title {
  font-size: 2.25rem;
  font-weight: 800;
  color: #111827;
  margin-bottom: 1.5rem;
  line-height: 1.2;
}

.about-title .highlight {
  color: #2563eb;
}

.about-description {
  font-size: 1.125rem;
  color: #4b5563;
  line-height: 1.7;
  margin-bottom: 1.5rem;
}

.features-list {
  margin-top: 2.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.feature-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
}

.feature-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 1.75rem;
  height: 1.75rem;
  border-radius: 9999px;
  background-color: #dbeafe;
  color: #2563eb;
  flex-shrink: 0;
  margin-top: 0.25rem;
}

.feature-text {
  color: #374151;
  line-height: 1.6;
  margin: 0;
}

.about-image {
  flex: 1;
  position: relative;
  border-radius: 1.5rem;
  overflow: hidden;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.image-container {
  position: relative;
  padding-bottom: 66.6667%;
  height: 0;
  overflow: hidden;
}

.about-img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.about-image:hover .about-img {
  transform: scale(1.03);
}

@media (min-width: 1024px) {
  .about-content {
    flex-direction: row;
    align-items: center;
    gap: 6rem;
  }
  
  .about-text {
    max-width: 50%;
  }
  
  .about-image {
    max-width: 50%;
  }
  
  .about-title {
    font-size: 2.5rem;
  }
}

@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes fade-in-up {
  from { 
    opacity: 0; 
    transform: translateY(30px); 
  }
  to { 
    opacity: 1; 
    transform: translateY(0); 
  }
}

.animate-fade-in {
  animation: fade-in 0.6s ease-out;
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out;
}
</style>
