<template>
  <div class="sidebar">
    <div class="sidebar-header">
      <h3>Administration</h3>
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
          <span class="nav-section-title">Gestion des utilisateurs</span>
          <ul>
            <li>
              <Link href="/admin/eleves" :class="{ 'active': $page.url.startsWith('/admin/eleves') }">
                <i class="fas fa-user-graduate"></i>
                <span>Élèves</span>
              </Link>
            </li>
            <li>
              <Link href="/admin/professeurs" :class="{ 'active': $page.url.startsWith('/admin/professeurs') }">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Professeurs</span>
              </Link>
            </li>
            <li>
              <Link href="/admin/assistants" :class="{ 'active': $page.url.startsWith('/admin/assistants') }">
                <i class="fas fa-user-tie"></i>
                <span>Assistants</span>
              </Link>
            </li>
          </ul>
        </li>
        
        <li class="nav-section">
          <span class="nav-section-title">Gestion pédagogique</span>
          <ul>
            <li>
              <Link href="/admin/matieres" :class="{ 'active': $page.url.startsWith('/admin/matieres') }">
                <i class="fas fa-book"></i>
                <span>Matières</span>
              </Link>
            </li>
            <li>
              <Link href="/admin/niveaux" :class="{ 'active': $page.url.startsWith('/admin/niveaux') }">
                <i class="fas fa-layer-group"></i>
                <span>Niveaux</span>
              </Link>
            </li>
            <li>
              <Link href="/admin/inscriptions" :class="{ 'active': $page.url.startsWith('/admin/inscriptions') }">
                <i class="fas fa-clipboard-check"></i>
                <span>Inscriptions</span>
              </Link>
            </li>
            <li>
              <Link href="/admin/absences" :class="{ 'active': $page.url.startsWith('/admin/absences') }">
                <i class="fas fa-user-times"></i>
                <span>Absences</span>
              </Link>
            </li>
          </ul>
        </li>
        
        <li class="nav-section">
          <span class="nav-section-title">Gestion financière</span>
          <ul>
            <li>
              <Link href="/admin/paiements" :class="{ 'active': $page.url.startsWith('/admin/paiements') }">
                <i class="fas fa-money-bill-wave"></i>
                <span>Paiements</span>
              </Link>
            </li>
            <li>
              <Link href="/admin/salaires" :class="{ 'active': $page.url.startsWith('/admin/salaires') }">
                <i class="fas fa-hand-holding-usd"></i>
                <span>Salaires</span>
              </Link>
            </li>
          </ul>
        </li>
        
        <li class="nav-section">
          <span class="nav-section-title">Paramètres</span>
          <ul>
            <li>
              <Link href="/admin/parametres" :class="{ 'active': $page.url.startsWith('/admin/parametres') }">
                <i class="fas fa-cog"></i>
                <span>Paramètres généraux</span>
              </Link>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
    
    <div class="sidebar-footer">
      <div class="user-info">
        <div class="user-avatar">
          <i class="fas fa-user-shield"></i>
        </div>
        <div class="user-details">
          <div class="user-name">{{ $page.props.auth.user.name }}</div>
          <div class="user-role">Administrateur</div>
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
