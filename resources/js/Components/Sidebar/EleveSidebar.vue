<template>
  <div class="sidebar" :class="{ 'sidebar-closed': !isOpen }">
    <div v-if="isOpen" class="sidebar-overlay" @click="$emit('close')"></div>
    <div class="sidebar-header">
      <h3>Espace Élève</h3>
    </div>
    
    <nav class="sidebar-nav">
      <ul>
        <li>
          <Link href="/dashboard" :class="{ 'active': $page.url === '/dashboard' }">
            <i class="fas fa-tachometer-alt"></i>
            <span>Tableau de bord</span>
          </Link>
        </li>
        
        <li class="nav-section">
          <span class="nav-section-title">Mes études</span>
          <ul>
            <li>
              <Link href="/eleve/emploi-du-temps" :class="{ 'active': $page.url.startsWith('/eleve/emploi-du-temps') }">
                <i class="fas fa-calendar-alt"></i>
                <span>Emploi du temps</span>
              </Link>
            </li>
            <li>
              <Link href="/eleve/matieres" :class="{ 'active': $page.url.startsWith('/eleve/matieres') }">
                <i class="fas fa-book"></i>
                <span>Mes matières</span>
              </Link>
            </li>
            <li>
              <Link href="/eleve/notes" :class="{ 'active': $page.url.startsWith('/eleve/notes') }">
                <i class="fas fa-clipboard-check"></i>
                <span>Mes notes</span>
              </Link>
            </li>
            <li>
              <Link href="/eleve/absences" :class="{ 'active': $page.url.startsWith('/eleve/absences') }">
                <i class="fas fa-user-times"></i>
                <span>Mes absences</span>
              </Link>
            </li>
          </ul>
        </li>
        
        <li class="nav-section">
          <span class="nav-section-title">Travail personnel</span>
          <ul>
            <li>
              <Link href="/eleve/devoirs" :class="{ 'active': $page.url.startsWith('/eleve/devoirs') }">
                <i class="fas fa-tasks"></i>
                <span>Devoirs</span>
              </Link>
            </li>
            <li>
              <Link href="/eleve/cours" :class="{ 'active': $page.url.startsWith('/eleve/cours') }">
                <i class="fas fa-book-reader"></i>
                <span>Cours en ligne</span>
              </Link>
            </li>
            <li>
              <Link href="/eleve/ressources" :class="{ 'active': $page.url.startsWith('/eleve/ressources') }">
                <i class="fas fa-folder-open"></i>
                <span>Ressources</span>
              </Link>
            </li>
          </ul>
        </li>
        
        <li class="nav-section">
          <span class="nav-section-title">Vie scolaire</span>
          <ul>
            <li>
              <Link href="/eleve/bulletins" :class="{ 'active': $page.url.startsWith('/eleve/bulletins') }">
                <i class="fas fa-file-alt"></i>
                <span>Mes bulletins</span>
              </Link>
            </li>
            <li>
              <Link href="/eleve/evenements" :class="{ 'active': $page.url.startsWith('/eleve/evenements') }">
                <i class="fas fa-calendar-day"></i>
                <span>Événements</span>
              </Link>
            </li>
            <li>
              <Link href="/eleve/messagerie" :class="{ 'active': $page.url.startsWith('/eleve/messagerie') }">
                <i class="fas fa-envelope"></i>
                <span>Messagerie</span>
              </Link>
            </li>
          </ul>
        </li>
        
        <li class="nav-section">
          <span class="nav-section-title">Mon compte</span>
          <ul>
            <li>
              <Link href="/eleve/profil" :class="{ 'active': $page.url.startsWith('/eleve/profil') }">
                <i class="fas fa-user-edit"></i>
                <span>Mon profil</span>
              </Link>
            </li>
            <li>
              <Link href="/eleve/parametres" :class="{ 'active': $page.url.startsWith('/eleve/parametres') }">
                <i class="fas fa-cog"></i>
                <span>Paramètres</span>
              </Link>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
    
    <div class="sidebar-footer">
      <div class="user-info">
        <div class="user-avatar">
          <i class="fas fa-user-graduate"></i>
        </div>
        <div class="user-details">
          <div class="user-name">{{ $page.props.auth.user.name }}</div>
          <div class="user-role">Élève</div>
        </div>
      </div>
      <Link href="/logout" method="post" as="button" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>
        <span>Déconnexion</span>
      </Link>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  }
});

const emit = defineEmits(['close']);
</script>

<style scoped>
.sidebar {
  width: 250px;
  height: 100vh;
  background-color: #2c3e50;
  color: #ecf0f1;
  display: flex;
  flex-direction: column;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
  position: fixed;
  z-index: 50;
}

.sidebar-closed {
  transform: translateX(-100%);
}

.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 40;
  display: none;
}

@media (max-width: 1023px) {
  .sidebar-overlay {
    display: block;
  }
}

.sidebar-header {
  padding: 20px;
  border-bottom: 1px solid #34495e;
}

.sidebar-header h3 {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 600;
  color: #fff;
}

.sidebar-nav {
  flex: 1;
  overflow-y: auto;
  padding: 15px 0;
}

.nav-section {
  margin-bottom: 15px;
}

.nav-section-title {
  display: block;
  padding: 10px 20px;
  font-size: 0.75rem;
  text-transform: uppercase;
  color: #7f8c8d;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.sidebar-nav ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar-nav a {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  color: #bdc3c7;
  text-decoration: none;
  transition: all 0.3s ease;
}

.sidebar-nav a:hover,
.sidebar-nav a.active {
  background-color: #34495e;
  color: #fff;
}

.sidebar-nav i {
  width: 20px;
  margin-right: 10px;
  text-align: center;
}

.sidebar-nav .nav-section ul a {
  padding-left: 40px;
  font-size: 0.9rem;
}

.sidebar-footer {
  padding: 15px;
  border-top: 1px solid #34495e;
  background-color: #2c3e50;
}

.user-info {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
  padding: 10px;
  background-color: rgba(255, 255, 255, 0.05);
  border-radius: 5px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #3498db;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 10px;
}

.user-avatar i {
  font-size: 1.2rem;
  color: white;
}

.user-details {
  flex: 1;
  overflow: hidden;
}

.user-name {
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  font-size: 0.8rem;
  color: #7f8c8d;
}

.logout-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
  background-color: #e74c3c;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.logout-btn:hover {
  background-color: #c0392b;
}

.logout-btn i {
  margin-right: 8px;
}
</style>
