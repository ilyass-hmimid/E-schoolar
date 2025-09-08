// Importation d'Alpine.js
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import focus from '@alpinejs/focus';

// Initialisation d'Alpine.js
window.Alpine = Alpine;
Alpine.plugin(intersect);
Alpine.plugin(focus);

// Initialisation des stores Alpine
Alpine.store('sidebar', {
    mobileOpen: false,
    isMobile() {
        return window.innerWidth < 768;
    },
    toggle() {
        this.mobileOpen = !this.mobileOpen;
        document.body.classList.toggle('overflow-hidden', this.mobileOpen);
    },
    close() {
        this.mobileOpen = false;
        document.body.classList.remove('overflow-hidden');
    }
});

// Fichier JavaScript principal
console.log('Application JavaScript chargée');

// Gestion de la sidebar
function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileMenuBackdrop = document.getElementById('mobile-menu-backdrop') || document.createElement('div');
    
    if (!mobileMenuBackdrop.id) {
        mobileMenuBackdrop.id = 'mobile-menu-backdrop';
        mobileMenuBackdrop.className = 'fixed inset-0 bg-gray-900 bg-opacity-50 z-30 hidden';
        document.body.appendChild(mobileMenuBackdrop);
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            const sidebarStore = Alpine.store('sidebar');
            sidebarStore.toggle();
            sidebar.classList.toggle('-translate-x-full');
            mobileMenuBackdrop.classList.toggle('hidden');
        });
    }

    if (mobileMenuBackdrop) {
        mobileMenuBackdrop.addEventListener('click', function() {
            const sidebarStore = Alpine.store('sidebar');
            sidebarStore.close();
            sidebar.classList.add('-translate-x-full');
            this.classList.add('hidden');
        });
    }

    // Fermer la sidebar lors du clic sur un lien
    const sidebarLinks = document.querySelectorAll('#sidebar a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) { // Seulement sur mobile
                const sidebarStore = Alpine.store('sidebar');
                sidebarStore.close();
                sidebar.classList.add('-translate-x-full');
                mobileMenuBackdrop.classList.add('hidden');
            }
        });
    });
}

// Initialisation des graphiques du tableau de bord
function initDashboardCharts() {
    const chartEl = document.getElementById('activityChart');
    if (!chartEl) return;

    console.log('Initialisation du graphique...');
    
    // Vérifier si Chart.js est disponible
    if (typeof Chart === 'undefined') {
        console.error('Chart.js n\'est pas chargé');
        return;
    }

    // Créer un graphique simple
    new Chart(chartEl, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr'],
            datasets: [{
                label: 'Activité',
                data: [30, 45, 28, 50],
                borderColor: '#10B981',
                borderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

// Initialiser les composants au chargement du document
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé, initialisation...');
    
    // Initialisation d'Alpine.js
    Alpine.start();
    
    // Initialisation des composants
    initSidebar();
    initDashboardCharts();
});

export { Alpine };
