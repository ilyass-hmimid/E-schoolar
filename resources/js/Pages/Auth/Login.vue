<script setup>
import { ref, onMounted } from 'vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';

const page = usePage();
const showPassword = ref(false);
const isLoading = ref(false);
const errors = page.props.errors;

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  isLoading.value = true;
  form.post('/login', {
    onFinish: () => {
      form.reset('password');
      isLoading.value = false;
    },
    onError: () => {
      isLoading.value = false;
    }
  });
};

</script>

<template>
  <div class="auth-container">
    <div class="auth-card">
      <!-- Logo et titre -->
      <div class="auth-header">
        <div class="logo-container">
          <img src="/imgs/logo.png" alt="Logo Allo Tawjih" class="logo-img">
        </div>
        <h1>Allo Tawjih</h1>
        <p class="welcome-text">Bienvenue, veuillez vous connecter</p>
      </div>

      <!-- Messages d'erreur -->
      <div v-if="$page.props.flash && $page.props.flash.error" class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <span>{{ $page.props.flash.error }}</span>
      </div>

      <!-- Messages de succès -->
      <div v-else-if="$page.props.flash && $page.props.flash.success" class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <span>{{ $page.props.flash.success }}</span>
      </div>

      <!-- Formulaire -->
      <form @submit.prevent="submit" class="auth-form">
        <!-- Email -->
        <div class="form-group">
          <label for="email">Adresse email</label>
          <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input 
              id="email" 
              v-model="form.email" 
              type="email" 
              required 
              autofocus 
              autocomplete="username"
              placeholder="votre@email.com"
              class="form-control"
            />
          </div>
        </div>

        <!-- Mot de passe -->
        <div class="form-group">
          <div class="form-header">
            <label for="password">Mot de passe</label>
            <Link href="/forgot-password" class="forgot-password">Mot de passe oublié ?</Link>
          </div>
          <div class="input-group">
            <i class="fas fa-lock"></i>
            <input 
              :type="showPassword ? 'text' : 'password'" 
              id="password" 
              v-model="form.password" 
              required 
              autocomplete="current-password"
              placeholder="Votre mot de passe"
              class="form-control"
            />
            <button 
              type="button" 
              @click="togglePassword" 
              class="toggle-password" 
              :title="showPassword ? 'Cacher le mot de passe' : 'Afficher le mot de passe'"
            >
              <i :class="showPassword ? 'far fa-eye-slash' : 'far fa-eye'"></i>
            </button>
          </div>
        </div>

        <!-- Bouton de connexion -->
        <button type="submit" class="submit-button" :disabled="isLoading">
          <i v-if="isLoading" class="fas fa-circle-notch fa-spin"></i>
          {{ isLoading ? 'Connexion en cours...' : 'Se connecter' }}
        </button>

        <!-- Inscription -->
        <div class="auth-footer">
          <p>Vous n'avez pas de compte ? <Link href="/register">S'inscrire</Link></p>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
/* Variables */
:root {
  --primary: #6c63ff;
  --primary-dark: #5a52e0;
  --secondary: #4a90e2;
  --text: #2c3e50;
  --text-light: #7f8c8d;
  --border: #e0e6ed;
  --border-radius: 12px;
  --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  --transition: all 0.3s ease;
}

/* Reset et base */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
  color: var(--text);
  line-height: 1.6;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Conteneur principal */
.auth-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  padding: 20px;
  background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%236c63ff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
}

/* Carte d'authentification */
.auth-card {
  width: 90%;
  max-width: 400px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(0, 0, 0, 0.05);
  overflow: hidden;
  transform: translateY(0);
  transition: var(--transition);
  animation: fadeInUp 0.6s ease-out;
  margin: 1rem auto;
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
}

/* En-tête */
.auth-header {
  text-align: center;
  padding: 30px 20px;
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  color: white;
  position: relative;
  overflow: hidden;
  flex-shrink: 0;
}

.auth-header::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
  transform: rotate(30deg);
  pointer-events: none;
}

.logo-container {
  width: 100px;
  height: 100px;
  margin: 0 auto 15px;
  background: white;
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.logo-img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.auth-header h1 {
  font-size: 24px;
  font-weight: 700;
  margin: 10px 0 5px;
  letter-spacing: -0.5px;
  color: white;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.welcome-text {
  font-size: 15px;
  opacity: 0.9;
  font-weight: 400;
}

/* Formulaire */
.auth-form {
  padding: 20px;
  display: flex;
  flex-direction: column;
}

.form-group {
  margin-bottom: 16px;
  position: relative;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: var(--text);
  margin-bottom: 8px;
}

.form-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.forgot-password {
  font-size: 13px;
  color: var(--primary);
  text-decoration: none;
  transition: var(--transition);
  font-weight: 500;
}

.forgot-password:hover {
  text-decoration: underline;
  color: var(--primary-dark);
}

.input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.input-group i {
  position: absolute;
  left: 16px;
  color: var(--text-light);
  font-size: 16px;
  transition: var(--transition);
}

.form-control {
  width: 100%;
  padding: 14px 16px 14px 46px;
  font-size: 15px;
  border: 1px solid var(--border);
  border-radius: 8px;
  background-color: #f8fafc;
  transition: var(--transition);
  color: var(--text);
  height: 48px;
}

.form-control:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.1);
  background-color: white;
}

.form-control:focus + i {
  color: var(--primary);
}

.toggle-password {
  position: absolute;
  right: 12px;
  background: transparent;
  border: none;
  color: var(--text-light);
  cursor: pointer;
  padding: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: var(--transition);
  border-radius: 50%;
}

.toggle-password:hover {
  background: rgba(0, 0, 0, 0.04);
  color: var(--text);
}

/* Checkbox personnalisé */
.checkbox-container {
  display: flex;
  align-items: center;
  position: relative;
  padding-left: 30px;
  cursor: pointer;
  user-select: none;
  font-size: 14px;
  color: var(--text);
  transition: var(--transition);
}

.checkbox-container:hover {
  color: var(--primary);
}

.checkbox-input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.checkmark {
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  height: 20px;
  width: 20px;
  background-color: #f8fafc;
  border: 1px solid var(--border);
  border-radius: 5px;
  transition: var(--transition);
}

.checkbox-container:hover .checkmark {
  border-color: var(--primary);
}

.checkbox-input:checked ~ .checkmark {
  background-color: var(--primary);
  border-color: var(--primary);
}

.checkmark:after {
  content: '';
  position: absolute;
  display: none;
  left: 7px;
  top: 3px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.checkbox-input:checked ~ .checkmark:after {
  display: block;
}

/* Bouton */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 14px 24px;
  font-size: 15px;
  font-weight: 600;
  line-height: 1.5;
  text-align: center;
  text-decoration: none;
  white-space: nowrap;
  vertical-align: middle;
  cursor: pointer;
  border: none;
  border-radius: 8px;
  transition: var(--transition);
  width: 100%;
  position: relative;
  overflow: hidden;
}

.btn i {
  margin-right: 8px;
  font-size: 14px;
}

.btn-primary {
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  color: white;
  box-shadow: 0 4px 15px rgba(108, 99, 255, 0.3);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(108, 99, 255, 0.4);
}

.btn-primary:active:not(:disabled) {
  transform: translateY(0);
  box-shadow: 0 2px 10px rgba(108, 99, 255, 0.3);
}

.btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none !important;
}

/* Pied de page */
.auth-footer {
  margin-top: 24px;
  text-align: center;
  font-size: 14px;
  color: var(--text-light);
  padding-top: 16px;
  border-top: 1px solid var(--border);
}

.auth-footer a {
  color: var(--primary);
  text-decoration: none;
  font-weight: 500;
  transition: var(--transition);
}

.auth-footer a:hover {
  text-decoration: underline;
  color: var(--primary-dark);
}

/* Alertes */
.alert {
  padding: 14px 18px;
  margin: 0 30px 20px;
  border-radius: 8px;
  font-size: 14px;
  display: flex;
  align-items: center;
  animation: slideIn 0.3s ease-out;
  transform: translateY(0);
  opacity: 1;
  transition: all 0.3s ease;
}

.alert i {
  margin-right: 10px;
  font-size: 16px;
}

.alert-error {
  background-color: #fef2f2;
  color: #dc3545;
  border-left: 4px solid #dc3545;
}

.alert-success {
  background-color: #f0fdf4;
  color: #28a745;
  border-left: 4px solid #28a745;
}

/* Bouton de soumission */
.submit-button {
  width: 100%;
  padding: 16px;
  height: 52px;
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 20px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.submit-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(108, 99, 255, 0.4);
}

.submit-button:active:not(:disabled) {
  transform: translateY(0);
}

.submit-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Animations */
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

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Effet de vague sur le bouton */
.btn-primary::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 5px;
  height: 5px;
  background: rgba(255, 255, 255, 0.5);
  opacity: 0;
  border-radius: 100%;
  transform: scale(1, 1) translate(-50%, -50%);
  transform-origin: 50% 50%;
}

.btn-primary:active::after {
  animation: ripple 0.6s ease-out;
}

@keyframes ripple {
  0% {
    transform: scale(0, 0);
    opacity: 0.5;
  }
  100% {
    transform: scale(20, 20);
    opacity: 0;
  }
}

/* Responsive */
@media (max-width: 480px) {
  .auth-card {
    margin: 10px;
  }
  
  .auth-header {
    padding: 30px 20px;
  }
  
  .auth-form {
    padding: 25px 20px;
  }
  
  .logo-container {
    width: 70px;
    height: 70px;
  }
  
  .logo-container i {
    font-size: 32px;
  }
  
  .auth-header h1 {
    font-size: 24px;
  }
  
  .welcome-text {
    font-size: 14px;
  }
  
  .alert {
    margin-left: 15px;
    margin-right: 15px;
  }
}

/* Animation de la carte au survol */
@media (hover: hover) {
  .auth-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
  }
  
  /* Animation du bouton */
  .btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
  }
}

/* Animation du logo */
.logo-container i {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}

/* Fin des styles */
</style>
