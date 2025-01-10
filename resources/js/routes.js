// routes.js
import Dashboard from './components/Dashboard.vue';
import ListAppointments from "./pages/appointments/ListAppointments.vue";
import ListUsers from "./pages/users/ListUsers.vue";
import Etudiant from "./pages/Etudiants/Etudiant.vue";
import Professeur from "./pages/Professeurs/Professeur.vue";
import UpdateSetings from "./pages/setings/UpdateSetings.vue";
 import UpdateProfile from "./pages/profile/UpdateProfile.vue";
import ValeursPaiments from "./pages/Paiments/ValeursPaiment.vue";
import Paiments from "./pages/Paiments/Paiments.vue";
import Salaires from "./pages/Salaires/Salaires.vue";
import ValeursSalaires from "./pages/Salaires/ValeursSalaires.vue";
import Enseignements from "./pages/Enseignements/Enseignement.vue";
import Matieres from "./pages/Matieres/Matiere.vue";
import Niveaux from "./pages/Niveaux/Niveau.vue";
import Filieres from "./pages/Filieres/Filiere.vue";
import Home from "./pages/Home/Home.vue";
import EspaceProfesseur from "./pages/EspaceProfesseur/EspaceProfesseur.vue";
import ClassesProfesseur from "./pages/ClassesProfesseurs/ClassesProfesseur.vue";
import ListeAbsences from "./pages/ListeAbsences/ListeAbsences.vue";
import EtudiantToMatiere from "@/Pages/etudiantToMatiere/EtudiantToMatiere.vue";











const routes = [
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true },

    },
    {
        path: '/home',
        name: 'home',
        component: Home,
        meta: { requiresAuth: true },

    },
    {
        path: '/appointments',
        name: 'appointments',
        component: ListAppointments,
        meta: { requiresAuth: true },

    },
    {
        path: '/users',
        name: 'users',
        component: ListUsers,
        meta: { requiresAuth: true },

    },
    {
        path: '/students',
        name: 'students',
        component: Etudiant,
        meta: { requiresAuth: true },

    },
    {
        path: '/students/inscriptions/:id',
        name: 'etudiantMatiere',
        component: EtudiantToMatiere,
        meta: { requiresAuth: true },

    },
    {
        path: '/professeurs',
        name: 'professeurs',
        component: Professeur,
        meta: { requiresAuth: true },

    },
    {
        path: '/valeurs_paiments',
        name: 'valeurs_paiments',
        component: ValeursPaiments,
        meta: { reload: true },
        meta: { requiresAuth: true },


    },
    {
        path: '/paiments',
        name: 'paiments',
        component: Paiments,
        meta: { requiresAuth: true },

    },
    {
        path: '/valeurs_salaires',
        name: 'valeurs_salaires',
        component: ValeursSalaires,
        meta: { requiresAuth: true },

    },
    {
        path: '/salaires',
        name: 'salaires',
        component: Salaires,
        meta: { requiresAuth: true },

    },
    {
        path: '/liste_absence',
        name: 'liste_absenc',
        component: ListeAbsences,
        meta: { requiresAuth: true },

    },
    {
        path: '/enseignements',
        name: 'enseignements',
        component: Enseignements,
        meta: { requiresAuth: true },

    },
    {
        path: '/espaceprofesseur',
        name: 'espaceprofesseur',
        component: EspaceProfesseur,
        meta: { requiresAuth: true },

    },
    {
        path: '/classes_professeur',
        name: 'classes_professeur',
        component: ClassesProfesseur,
        meta: { requiresAuth: true },

    },
    {
        path: '/matieres',
        name: 'matieres',
        component: Matieres,
        meta: { requiresAuth: true },

    },
    {
        path: '/niveaux',
        name: 'niveaux',
        component: Niveaux,
        meta: { requiresAuth: true },

    },
    {
        path: '/filieres',
        name: 'filieres',
        component: Filieres,
        meta: { requiresAuth: true },
    },
    {
        path: '/setings',
        name: 'setings',
        component: UpdateSetings,
        meta: { requiresAuth: true },

    },
    {
        path: '/profile',
        name: 'profile',
        component: UpdateProfile,
        meta: { requiresAuth: true },

    }
];

export default routes;
