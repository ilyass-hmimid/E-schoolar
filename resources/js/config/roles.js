/**
 * Configuration des rôles et permissions
 * 
 * Ce fichier définit les rôles disponibles dans l'application
 * et leurs permissions associées.
 */

export const ROLES = {
    ADMIN: 'admin',
    ASSISTANT: 'assistant',
    TEACHER: 'professeur',
    STUDENT: 'etudient'
};

/**
 * Définition des permissions par rôle
 */
export const PERMISSIONS = {
    // Permissions administrateur
    [ROLES.ADMIN]: {
        dashboard: true,
        users: {
            view: true,
            create: true,
            edit: true,
            delete: true
        },
        settings: {
            view: true,
            edit: true
        },
        students: {
            view: true,
            create: true,
            edit: true,
            delete: true
        },
        teachers: {
            view: true,
            create: true,
            edit: true,
            delete: true
        },
        classes: {
            view: true,
            create: true,
            edit: true,
            delete: true
        },
        subjects: {
            view: true,
            create: true,
            edit: true,
            delete: true
        },
        payments: {
            view: true,
            create: true,
            edit: true,
            delete: true
        },
        salaries: {
            view: true,
            create: true,
            edit: true,
            delete: true
        },
        reports: {
            view: true,
            generate: true
        },
        attendance: {
            view: true,
            manage: true
        }
    },
    
    // Permissions assistant
    [ROLES.ASSISTANT]: {
        dashboard: true,
        users: {
            view: true,
            create: true,
            edit: true,
            delete: false
        },
        settings: {
            view: false,
            edit: false
        },
        students: {
            view: true,
            create: true,
            edit: true,
            delete: false
        },
        teachers: {
            view: true,
            create: false,
            edit: false,
            delete: false
        },
        classes: {
            view: true,
            create: false,
            edit: false,
            delete: false
        },
        subjects: {
            view: true,
            create: false,
            edit: false,
            delete: false
        },
        payments: {
            view: true,
            create: true,
            edit: true,
            delete: false
        },
        salaries: {
            view: true,
            create: false,
            edit: false,
            delete: false
        },
        reports: {
            view: true,
            generate: false
        },
        attendance: {
            view: true,
            manage: true
        }
    },
    
    // Permissions professeur
    [ROLES.TEACHER]: {
        dashboard: true,
        users: {
            view: false,
            create: false,
            edit: false,
            delete: false
        },
        settings: {
            view: false,
            edit: false
        },
        students: {
            view: true,  // Seulement ses étudiants
            create: false,
            edit: false,
            delete: false
        },
        teachers: {
            view: false,
            create: false,
            edit: false,
            delete: false
        },
        classes: {
            view: true,  // Seulement ses classes
            create: false,
            edit: false,
            delete: false
        },
        subjects: {
            view: true,  // Seulement ses matières
            create: false,
            edit: false,
            delete: false
        },
        payments: {
            view: false,
            create: false,
            edit: false,
            delete: false
        },
        salaries: {
            view: true,  // Seulement son propre salaire
            create: false,
            edit: false,
            delete: false
        },
        reports: {
            view: false,
            generate: false
        },
        attendance: {
            view: true,  // Seulement pour ses classes
            manage: true  // Peut marquer les présences pour ses cours
        }
    },
    
    // Permissions étudiant
    [ROLES.STUDENT]: {
        dashboard: true,
        users: {
            view: false,
            create: false,
            edit: false,
            delete: false
        },
        settings: {
            view: false,
            edit: false
        },
        students: {
            view: false,
            create: false,
            edit: false,
            delete: false
        },
        teachers: {
            view: true,  // Peut voir ses professeurs
            create: false,
            edit: false,
            delete: false
        },
        classes: {
            view: true,  // Seulement ses classes
            create: false,
            edit: false,
            delete: false
        },
        subjects: {
            view: true,  // Seulement ses matières
            create: false,
            edit: false,
            delete: false
        },
        payments: {
            view: true,  // Seulement ses paiements
            create: false,
            edit: false,
            delete: false
        },
        salaries: {
            view: false,
            create: false,
            edit: false,
            delete: false
        },
        reports: {
            view: false,
            generate: false
        },
        attendance: {
            view: true,  // Peut voir ses propres absences
            manage: false
        }
    }
};

/**
 * Vérifie si un utilisateur a une certaine permission
 * @param {string} role - Le rôle de l'utilisateur
 * @param {string} permissionPath - Le chemin de la permission (ex: 'users.create')
 * @returns {boolean} True si l'utilisateur a la permission, false sinon
 */
export function hasPermission(role, permissionPath) {
    if (!role || !PERMISSIONS[role]) {
        return false;
    }
    
    const parts = permissionPath.split('.');
    let current = PERMISSIONS[role];
    
    for (const part of parts) {
        if (current[part] === undefined) {
            return false;
        }
        current = current[part];
    }
    
    return Boolean(current);
}

/**
 * Vérifie si un utilisateur a un certain rôle
 * @param {string} userRole - Le rôle de l'utilisateur
 * @param {string|string[]} requiredRoles - Le(s) rôle(s) requis
 * @returns {boolean} True si l'utilisateur a l'un des rôles requis
 */
export function hasRole(userRole, requiredRoles) {
    if (!userRole) return false;
    if (!Array.isArray(requiredRoles)) {
        requiredRoles = [requiredRoles];
    }
    return requiredRoles.includes(userRole);
}

/**
 * Récupère les permissions d'un rôle
 * @param {string} role - Le rôle
 * @returns {Object|null} Les permissions du rôle ou null si le rôle n'existe pas
 */
export function getRolePermissions(role) {
    return PERMISSIONS[role] || null;
}

/**
 * Vérifie si un utilisateur peut accéder à une route en fonction de son rôle
 * @param {string} userRole - Le rôle de l'utilisateur
 * @param {Object} route - L'objet route de Vue Router
 * @returns {boolean} True si l'accès est autorisé
 */
export function canAccessRoute(userRole, route) {
    // Si la route n'a pas de meta.roles, l'accès est autorisé
    if (!route.meta || !route.meta.roles) {
        return true;
    }
    
    // Vérifier si l'utilisateur a l'un des rôles requis
    return hasRole(userRole, route.meta.roles);
}
