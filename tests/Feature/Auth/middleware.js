export default function (to, from, next) {
    if (window.Laravel.isAuthenticated) {
        return next();
    }

    return next({ name: 'login' }); // Redirection vers la page de login
}
