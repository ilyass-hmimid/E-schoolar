// Import des styles
import '../sass/app.scss';

// Fonction principale
document.addEventListener('DOMContentLoaded', () => {
    console.log('Application initialisée');
    
    // Vérifier si Font Awesome est chargé
    if (typeof window.FontAwesomeConfig === 'undefined') {
        console.warn('Font Awesome non chargé');
    } else {
        console.log('Font Awesome chargé');
    }
    
    // Vérifier si Bootstrap est chargé
    if (typeof bootstrap === 'undefined') {
        console.warn('Bootstrap non chargé');
    } else {
        console.log('Bootstrap chargé');
        
        // Initialisation des tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(tooltipTriggerEl => {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Initialisation des popovers
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.forEach(popoverTriggerEl => {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }
});
