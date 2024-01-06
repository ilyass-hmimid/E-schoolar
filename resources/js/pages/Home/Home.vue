<template>
  <div class="homepage">
    <header class="header">
      <!-- Barre de navigation -->
      <nav class="navbar">
        <!-- Logo -->
        <div class="navbar-logo">

          <!-- <img src="./imgs/logo.png" alt="Logo" /> -->
        </div>


      </nav>
    </header>

    <main class="main-content">
      <!-- Section principale -->
      <section class="hero-section">
        <div class="hero-content">
          <h1>Bienvenue dans notre site</h1>
          <!-- <p>Exploitez votre potentiel au maximum.</p> -->
        </div>
        <div class="hero-image">
          <!-- Image ou vidéo d'illustration -->
          <img src="./imgs/logo.png" alt="Logo" style="height: 466px;" />
        </div>
      </section>

      <!-- Autres sections ou composants -->
      <section class="other-section">
        <!-- Contenu de la section -->
      </section>
    </main>

    <footer class="footer">
      <!-- Pied de page -->
      <div class="footer-content">
        <!-- Informations de contact, liens sociaux, etc. -->
      </div>
      <div class="footer-bottom">
        <p>&copy; 2024 CENTRE CONCEPT ETUDES. Tous droits réservés.</p>
      </div>
    </footer>

    <!-- Modale d'authentification -->
    <div class="auth-modal" v-if="showAuthModal">
      <!-- Contenu de la modale -->
    </div>
  </div>
</template>


<script>
export default {
  data() {
    return {
      showAuthModal: false,
    };
  },
  methods: {
    toggleAuthModal() {
      this.showAuthModal = !this.showAuthModal;
    },
    // Autres méthodes pour gérer les fonctionnalités
  },
};
</script>





<style scoped>
/* Styles spécifiques à cette page */
/* Utilisez des classes CSS pour styliser chaque composant de votre page */

/* En-tête */
.header {
  background-color: #333;
  color: white;
  padding: 20px;
}

/* Barre de navigation */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.navbar-logo img {
  max-width: 100px;
}

/* Contenu principal */
.main-content {
  /* Styles du contenu principal */
}

/* Section héros */
.hero-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 50px;
  background-color: #f5f5f5;
}

.hero-content {
  max-width: 600px;
}

/* Bouton principal */
.hero-section button {
  padding: 10px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

/* Pied de page */
.footer {
  background-color: #333;
  color: white;
  text-align: center;
  padding: 20px;
}

/* Modale d'authentification */
.auth-modal {
  /* Styles de la modale */
}
</style>
