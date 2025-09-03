/**
 * Script principal pour l'administration
 * Gère la navigation, les thèmes et les interactions utilisateur
 */

document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const html = document.documentElement;
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const themeToggle = document.getElementById('theme-toggle');
    const dropdownToggles = document.querySelectorAll('[data-dropdown-toggle]');
    const dropdownMenus = document.querySelectorAll('.dropdown-menu');
    
    // Gestion du menu mobile
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';
            mobileMenuButton.setAttribute('aria-expanded', !isExpanded);
            mobileMenu.classList.toggle('hidden');
            
            // Ajouter/supprimer la classe de défilement verrouillée sur le body
            if (!isExpanded) {
                document.body.classList.add('overflow-hidden');
            } else {
                document.body.classList.remove('overflow-hidden');
            }
        });
    }
    
    // Gestion du thème clair/sombre
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const isDark = html.classList.contains('dark');
            
            if (isDark) {
                html.classList.remove('dark');
                localStorage.theme = 'light';
                themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
            } else {
                html.classList.add('dark');
                localStorage.theme = 'dark';
                themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
            }
        });
        
        // Mettre à jour l'icône du bouton de thème au chargement
        const isDark = html.classList.contains('dark');
        themeToggle.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    }
    
    // Gestion des menus déroulants
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdownId = this.getAttribute('data-dropdown-toggle');
            const dropdownMenu = document.getElementById(dropdownId);
            
            if (dropdownMenu) {
                // Fermer tous les autres menus déroulants
                dropdownMenus.forEach(menu => {
                    if (menu !== dropdownMenu && !menu.classList.contains('hidden')) {
                        menu.classList.add('hidden');
                    }
                });
                
                // Basculer le menu actuel
                dropdownMenu.classList.toggle('hidden');
            }
        });
    });
    
    // Fermer les menus déroulants en cliquant à l'extérieur
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            dropdownMenus.forEach(menu => {
                if (!menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                }
            });
        }
    });
    
    // Gestion de la sidebar sur mobile
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            const isExpanded = sidebarToggle.getAttribute('aria-expanded') === 'true';
            sidebarToggle.setAttribute('aria-expanded', !isExpanded);
            sidebar.classList.toggle('-translate-x-full');
            
            // Sauvegarder l'état de la sidebar dans localStorage
            localStorage.setItem('sidebar-collapsed', isExpanded);
        });
        
        // Restaurer l'état de la sidebar au chargement
        const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('-translate-x-full');
            sidebarToggle.setAttribute('aria-expanded', 'false');
        } else {
            sidebar.classList.remove('-translate-x-full');
            sidebarToggle.setAttribute('aria-expanded', 'true');
        }
    }
    
    // Gestion des onglets
    const tabButtons = document.querySelectorAll('[data-tab]');
    const tabContents = document.querySelectorAll('[data-tab-content]');
    
    if (tabButtons.length > 0 && tabContents.length > 0) {
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Désactiver tous les onglets
                tabButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.setAttribute('aria-selected', 'false');
                });
                
                // Masquer tout le contenu des onglets
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Activer l'onglet cliqué
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                
                // Afficher le contenu correspondant
                const activeTabContent = document.getElementById(tabId);
                if (activeTabContent) {
                    activeTabContent.classList.remove('hidden');
                }
            });
        });
        
        // Activer le premier onglet par défaut
        if (tabButtons[0]) {
            tabButtons[0].click();
        }
    }
    
    // Gestion des messages flash
    const flashMessages = document.querySelectorAll('.alert');
    
    flashMessages.forEach(message => {
        // Fermer le message en cliquant sur le bouton de fermeture
        const closeButton = message.querySelector('.alert-close');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                message.style.transition = 'opacity 0.3s ease';
                message.style.opacity = '0';
                
                setTimeout(() => {
                    message.remove();
                }, 300);
            });
        }
        
        // Fermeture automatique après 5 secondes
        if (!message.classList.contains('alert-persistent')) {
            setTimeout(() => {
                message.style.transition = 'opacity 0.3s ease';
                message.style.opacity = '0';
                
                setTimeout(() => {
                    message.remove();
                }, 300);
            }, 5000);
        }
    });
    
    // Initialisation des tooltips
    const tooltipTriggers = document.querySelectorAll('[data-tooltip]');
    
    tooltipTriggers.forEach(trigger => {
        const tooltipId = 'tooltip-' + Math.random().toString(36).substr(2, 9);
        const tooltipText = trigger.getAttribute('data-tooltip');
        const tooltipPosition = trigger.getAttribute('data-tooltip-position') || 'top';
        
        // Créer l'élément tooltip
        const tooltip = document.createElement('div');
        tooltip.id = tooltipId;
        tooltip.className = `tooltip tooltip-${tooltipPosition} hidden`;
        tooltip.textContent = tooltipText;
        
        // Ajouter le tooltip au DOM
        document.body.appendChild(tooltip);
        
        // Positionner le tooltip
        const updateTooltipPosition = () => {
            const rect = trigger.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();
            
            switch (tooltipPosition) {
                case 'top':
                    tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltipRect.width / 2)}px`;
                    tooltip.style.top = `${rect.top - tooltipRect.height - 8}px`;
                    break;
                case 'right':
                    tooltip.style.left = `${rect.right + 8}px`;
                    tooltip.style.top = `${rect.top + (rect.height / 2) - (tooltipRect.height / 2)}px`;
                    break;
                case 'bottom':
                    tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltipRect.width / 2)}px`;
                    tooltip.style.top = `${rect.bottom + 8}px`;
                    break;
                case 'left':
                    tooltip.style.left = `${rect.left - tooltipRect.width - 8}px`;
                    tooltip.style.top = `${rect.top + (rect.height / 2) - (tooltipRect.height / 2)}px`;
                    break;
            }
        };
        
        // Afficher/masquer le tooltip
        trigger.addEventListener('mouseenter', () => {
            tooltip.classList.remove('hidden');
            updateTooltipPosition();
        });
        
        trigger.addEventListener('mouseleave', () => {
            tooltip.classList.add('hidden');
        });
        
        // Mettre à jour la position lors du redimensionnement
        window.addEventListener('resize', updateTooltipPosition);
    });
    
    // Initialisation des modales
    const modalToggles = document.querySelectorAll('[data-modal-toggle]');
    
    modalToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-toggle');
            const modal = document.getElementById(modalId);
            
            if (modal) {
                modal.classList.toggle('hidden');
                
                // Ajouter/supprimer la classe de défilement verrouillée sur le body
                if (!modal.classList.contains('hidden')) {
                    document.body.classList.add('overflow-hidden');
                } else {
                    document.body.classList.remove('overflow-hidden');
                }
            }
        });
    });
    
    // Fermer les modales en cliquant sur le bouton de fermeture
    const modalCloses = document.querySelectorAll('[data-modal-close]');
    
    modalCloses.forEach(closeButton => {
        closeButton.addEventListener('click', function() {
            const modal = this.closest('.modal');
            
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });
    });
    
    // Fermer les modales en cliquant en dehors
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    });
});

/**
 * Fonction utilitaire pour afficher un message de notification
 * @param {string} message - Le message à afficher
 * @param {string} type - Le type de notification (success, error, warning, info)
 * @param {number} duration - Durée d'affichage en millisecondes (optionnel)
 */
function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Créer le bouton de fermeture
    const closeButton = document.createElement('button');
    closeButton.className = 'notification-close';
    closeButton.innerHTML = '&times;';
    closeButton.addEventListener('click', () => {
        notification.remove();
    });
    
    notification.appendChild(closeButton);
    
    // Ajouter la notification au conteneur
    const container = document.getElementById('notifications') || document.body;
    container.appendChild(notification);
    
    // Fermeture automatique
    if (duration > 0) {
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, duration);
    }
    
    return notification;
}

/**
 * Fonction utilitaire pour formater une date
 * @param {Date|string} date - La date à formater
 * @param {string} format - Le format de sortie (date, datetime, time, relative)
 * @returns {string} La date formatée
 */
function formatDate(date, format = 'date') {
    if (!date) return '';
    
    const d = new Date(date);
    if (isNaN(d.getTime())) return '';
    
    const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    };
    
    switch (format) {
        case 'date':
            return d.toLocaleDateString(undefined, {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            
        case 'datetime':
            return d.toLocaleString(undefined, {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
        case 'time':
            return d.toLocaleTimeString(undefined, {
                hour: '2-digit',
                minute: '2-digit'
            });
            
        case 'relative':
            const now = new Date();
            const diffInSeconds = Math.floor((now - d) / 1000);
            
            if (diffInSeconds < 60) {
                return 'à l\'instant';
            }
            
            const diffInMinutes = Math.floor(diffInSeconds / 60);
            if (diffInMinutes < 60) {
                return `il y a ${diffInMinutes} min`;
            }
            
            const diffInHours = Math.floor(diffInMinutes / 60);
            if (diffInHours < 24) {
                return `il y a ${diffInHours} h`;
            }
            
            const diffInDays = Math.floor(diffInHours / 24);
            if (diffInDays < 7) {
                return `il y a ${diffInDays} j`;
            }
            
            return d.toLocaleDateString(undefined, {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            
        default:
            return d.toLocaleDateString();
    }
}

/**
 * Fonction utilitaire pour formater un nombre
 * @param {number} number - Le nombre à formater
 * @param {number} decimals - Nombre de décimales (par défaut: 0)
 * @param {string} decimalSeparator - Séparateur décimal (par défaut: ',')
 * @param {string} thousandsSeparator - Séparateur de milliers (par défaut: ' ')
 * @returns {string} Le nombre formaté
 */
function formatNumber(number, decimals = 0, decimalSeparator = ',', thousandsSeparator = ' ') {
    if (isNaN(number)) return '0';
    
    const parts = number.toFixed(decimals).split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);
    
    if (decimals > 0) {
        return parts.join(decimalSeparator);
    }
    
    return parts[0];
}

// Exposer les fonctions globales
window.showNotification = showNotification;
window.formatDate = formatDate;
window.formatNumber = formatNumber;
