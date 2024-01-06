// routes.js
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

const routes = [
    {
        path: '/home',
        name: 'home',
        component: Home,
    },
    {
        path: '/appointments',
        name: 'appointments',
        component: ListAppointments,
    },
    {
        path: '/users',
        name: 'users',
        component: ListUsers,
    },
    {
        path: '/students',
        name: 'students',
        component: Etudiant,
    },
    {
        path: '/professeurs',
        name: 'professeurs',
        component: Professeur,
    },
    {
        path: '/valeurs_paiments',
        name: 'valeurs_paiments',
        component: ValeursPaiments,
    },
    {
        path: '/paiments',
        name: 'paiments',
        component: Paiments,
    },
    {
        path: '/valeurs_salaires',
        name: 'valeurs_salaires',
        component: ValeursSalaires,
    },
    {
        path: '/salaires',
        name: 'salaires',
        component: Salaires,
    },
    {
        path: '/enseignements',
        name: 'enseignements',
        component: Enseignements,
    },
    {
        path: '/espaceprofesseur',
        name: 'espaceprofesseur',
        component: EspaceProfesseur,
    },
    {
        path: '/matieres',
        name: 'matieres',
        component: Matieres,
    },
    {
        path: '/niveaux',
        name: 'niveaux',
        component: Niveaux,
    },
    {
        path: '/filieres',
        name: 'filieres',
        component: Filieres,
    },
    {
        path: '/setings',
        name: 'setings',
        component: UpdateSetings,
    },
    {
        path: '/profile',
        name: 'profile',
        component: UpdateProfile,
    }
];

export default routes;
