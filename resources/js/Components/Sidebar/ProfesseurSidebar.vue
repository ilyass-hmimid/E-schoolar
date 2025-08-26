<script setup>
import { Link } from '@inertiajs/vue3';
import { useRoute } from '@/utils/route';

const { route, currentRoute } = useRoute();
const emit = defineEmits(['close']);

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  }
});
</script>

<template>
  <div class="sidebar" :class="{ 'sidebar-closed': !isOpen }">
    <div v-if="isOpen" class="sidebar-overlay" @click="$emit('close')"></div>
    <div class="sidebar-header">
      <h3>Espace Professeur</h3>
    </div>
    
    <nav class="sidebar-nav">
      <ul>
        <li>
          <Link :href="route('dashboard')" :class="{ 'active': currentRoute() === 'dashboard' }">
            <i class="fas fa-tachometer-alt"></i>
            <span>Tableau de bord</span>
          </Link>
        </li>
        
        <li class="nav-section">
          <span class="nav-section-title">Mes cours</span>
          <ul>
            <li>
              <Link :href="route('professeur.emploi-du-temps')" :class="{ 'active': currentRoute().startsWith('professeur.emploi-du-temps') }">
                <i class="fas fa-calendar-alt"></i>
                <span>Emploi du temps</span>
              </Link>
            </li>
            <li>
              <Link :href="route('professeur.matieres.index')" :class="{ 'active': currentRoute().startsWith('professeur.matieres') }">
                <i class="fas fa-book"></i>
                <span>Mes matières</span>
              </Link>
            </li>
            <li>
              <Link :href="route('professeur.classes.index')" :class="{ 'active': currentRoute().startsWith('professeur.classes') }">
                <i class="fas fa-users"></i>
                <span>Mes classes</span>
              </Link>
            </li>
          </ul>
        </li>
        
        <li class="nav-section">
          <span class="nav-section-title">Évaluation</span>
          <ul>
            <li>
              <Link :href="route('professeur.notes.index')" :class="{ 'active': currentRoute().startsWith('professeur.notes') }">
                <i class="fas fa-clipboard-check"></i>
                <span>Saisir les notes</span>
              </Link>
            </li>
            <li>
              <Link :href="route('professeur.absences.index')" :class="{ 'active': currentRoute().startsWith('professeur.absences') }">
                <i class="fas fa-user-times"></i>
                <span>Gérer les absences</span>
              </Link>
            </li>
            <li>
              <Link :href="route('professeur.bulletins.index')" :class="{ 'active': currentRoute().startsWith('professeur.bulletins') }">
                <i class="fas fa-file-alt"></i>
                <span>Bulletins</span>
              </Link>
            </li>
          </ul>
        </li>
        
        <li class="nav-section">
          <span class="nav-section-title">Ressources</span>
          <ul>
            <li>
              <Link href="/professeur/cours" :class="{ 'active': $page.url.startsWith('/professeur/cours') }">
                <i class="fas fa-file-upload"></i>
                <span>Déposer des cours</span>
              </Link>
            </li>
            <li>
              <Link :href="route('professeur.devoirs.index')" :class="{ 'active': currentRoute().startsWith('professeur.devoirs') }">
                <i class="fas fa-tasks"></i>
                <span>Devoirs</span>
              </Link>
            </li>
            <li>
              <Link :href="route('professeur.ressources.index')" :class="{ 'active': currentRoute().startsWith('professeur.ressources') }">
                <i class="fas fa-folder-open"></i>
                <span>Ressources partagées</span>
              </Link>
            </li>
          </ul>
        </li>
        
        <li class="nav-section">
          <span class="nav-section-title">Mon compte</span>
          <ul>
            <li>
              <Link :href="route('professeur.profil.edit')" :class="{ 'active': currentRoute().startsWith('professeur.profil') }">
                <i class="fas fa-user-edit"></i>
                <span>Mon profil</span>
              </Link>
            </li>
            <li>
              <Link :href="route('professeur.messagerie.index')" :class="{ 'active': currentRoute().startsWith('professeur.messagerie') }">
                <i class="fas fa-envelope"></i>
                <span>Messagerie</span>
              </Link>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
    
    <div class="sidebar-footer">
      <div class="user-info">
        <div class="user-avatar">
          <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="user-details">
          <div class="user-name">{{ $page.props.auth.user.name }}</div>
          <div class="user-role">Professeur</div>
        </div>
      </div>
      <Link :href="route('logout')" method="post" as="button" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>
        <span>Déconnexion</span>
      </Link>
    </div>
  </div>
</template>

<style scoped>
.sidebar {
  width: 250px;
  height: 100vh;
  background-color: #2c3e50;
  color: #ecf0f1;
  display: flex;
  flex-direction: column;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
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
  background-color: #e67e22;
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
