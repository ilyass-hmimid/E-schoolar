<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-12" style="display: flex; flex-direction: column; align-items: center;">
          <h1 class="m-2" style="font-weight: 500 !important; ">Liste des absences</h1>
          <div class="mt-2" style="width: 99% !important; display: flex; flex-direction: column;">

            <Field name="MoisPorAfficher" type="date" class="form-control" id="selectedMonth2"
              placeholder="Entrer la date de début" required :value="getdate()" v-model="selectedMonth2"
              @change="handleDateChange" />
          </div>
        </div>
      </div>
    </div>
  </div>


  <div class="content">
    <div class="container-fluid">

      <div class="container"
        style="overflow : auto !important; height:  calc(100vh - 176px) !important; max-width: 2040px !important;">

        <table id="myTable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Prenom</th>
              <th>Téléphone</th>
              <th>Adresse</th>
              <th>Niveau</th>
              <th>Filière</th>
              <th>Matière</th>
              <th>Professeur</th>
              <th>Date d'absence</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in users" :key="user.id">
              <td>{{ user.Nom }}</td>
              <td>{{ user.Prenom }}</td>
              <td>{{ user.Tele }} <br>{{ user.Tele2 }}</td>
              <td>{{ user.Adresse }}</td>
              <td>{{ user.IdNiv }}</td>
              <td>{{ user.IdFil }}</td>
              <td>{{ user.Matieres }}</td>
              <td>{{ user.Professeurs }}</td>
              <td>{{ user.Date_debut }}</td>

              <td v-if="user.Tele && user.Tele.trim() !== ''">
                <a @click="openWhatsApp(user, user.Tele)" style="cursor: pointer;">
                  <!-- <svg xmlns="http://www.w3.org/2000/svg" height="30px" fill="green" viewBox="0 0 448 512"> -->
                  <svg xmlns="http://www.w3.org/2000/svg" height="30px" fill="green" viewBox="0 0 448 512">!Font Awesome
                    Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free
                    Copyright 2024 Fonticons, Inc.
                    <path
                      d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
                  </svg>

                </a>
              </td>

              <td v-if="user.Tele2 && user.Tele2.trim() !== ''">
                <a @click="openWhatsApp(user, user.Tele2)" style="cursor: pointer;">
                  <!-- <svg xmlns="http://www.w3.org/2000/svg" height="30px" fill="green" viewBox="0 0 448 512"> -->
                  <svg xmlns="http://www.w3.org/2000/svg" height="30px" fill="green" viewBox="0 0 448 512">!Font Awesome
                    Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free
                    Copyright 2024 Fonticons, Inc.
                    <path
                      d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
                  </svg>

                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>

</template>




<script setup charset="utf-8">

import axios from 'axios';
import { ref, onMounted } from 'vue';
import { useToastr } from '../../toastr.js';
import $ from 'jquery';
import 'datatables.net';
import { Field } from 'vee-validate';

const getdate = () => {
  const now = new Date();
  const year = now.getFullYear();
  const month = (now.getMonth() + 1).toString().padStart(2, '0'); // +1 car les mois vont de 0 à 11
  const day = now.getDate().toString().padStart(2, '0');
  return `${year}-${month}-${day}`;
};

const handleDateChange = () => {
  localStorage.setItem('selectedMonth2', selectedMonth2.value);
  // Mettre à jour les données en fonction du nouveau mois sélectionné
  window.location.reload();
};

const getDefaultDate = () => {
  const storedMonth2 = localStorage.getItem('selectedMonth2');
  // console.log(storedMonth2);
  if (storedMonth2) {
    return storedMonth2;
  } else {
    const now = new Date();
    const year = now.getFullYear();
    const month = (now.getMonth() + 1).toString().padStart(2, '0'); // +1 car les mois vont de 0 à 11
    const day = now.getDate().toString().padStart(2, '0');
    return `${year}-${month}-${day}`;
  }
};

const selectedMonth2 = ref('');

const toastr = useToastr();
const users = ref([]);
const editing = ref(false);
const formValues = ref({
  id: '',
  nom: '',
  prenom: '',
  tele: '',
  adresse: '',
  Date_debut: '',

});
const form = ref(null);

const selectedNiveau = ref('')
const niveaux = ref([]);

const selectedFiliere = ref('');
const filieres = ref([]);

const selectedMatieres = ref([]); // Utilisation d'un tableau pour stocker les matières sélectionnées
const matieres = ref([]);

const selectedProfesseurs = ref([]); // Utilisation d'un tableau pour stocker les profs sélectionnées
const professeurs = ref([]);


let showFiliere = false;
let showProfesseurs = false;

const openWhatsApp = (user, tele) => {
  const message = encodeURIComponent(`${user.Date_debut} السلام عليكم، نحيطكم علما أن التلميذ(ة) ${user.Nom} ${user.Prenom} تغيب(ت) يوم`);
  // const message = encodeURIComponent(`Bonjour, Nous vous informons que l'élève ${user.Nom} ${user.Prenom} était absent(e) le ${user.Date_debut}. Cordialement.`);
  const whatsappURL = `https://wa.me/+212${tele}?text=${message}`;
  window.open(whatsappURL, '_blank');
};


const resetFormValues = () => {
  form.value.resetForm(); // Utilisez la méthode resetForm() fournie par VeeValidate pour réinitialiser le formulaire
  // Remettre à zéro les valeurs sélectionnées et autres états si nécessaire
  selectedNiveau.value = '';
  selectedFiliere.value = '';
  selectedMatieres.value = [];
  selectedProfesseurs.value = [];

  // Autres remises à zéro si nécessaire
};

const IsBigtable = ref(false);

const getUsers = () => {
  axios.get('/api/ListeAbsences', { params: { date: selectedMonth2.value } })
    .then((response) => {
      users.value = response.data;

      if ($.fn.DataTable.isDataTable('#myTable')) {
        $('#myTable').DataTable().destroy();
      }
    })
    .catch((error) => {
      console.error('Erreur lors de la récupération des étudiants :', error);
    });
};

const updateValuesPeriodically = () => {
  setInterval(() => {
    // getValeurPaiement();
    getUsers();
    getMatieres();
  }, 5000000);
};


const getNiveux = () => {
  axios.get('/api/niveaux') // Remplacez '/api/niveaux' par votre endpoint pour récupérer les niveaux depuis la base de données
    .then(response => {
      niveaux.value = response.data; // Assurez-vous que response.data contient les données des niveaux
    })
    .catch(error => {
      console.error('Erreur lors de la récupération des niveaux :', error);
    });
};


const getFilieres = (idNiv) => {

  axios.get(`/api/filieres/${idNiv}`)
    .then(response => {
      filieres.value = response.data;
      // Mise à jour manuelle de showFiliere en fonction des filières récupérées
      showFiliere = !!response.data.length;
    })
    .catch(error => {
      console.error('Erreur lors de la récupération des filières :', error);
    });
};


const getMatieres = () => {

  axios.get(`/api/matieres`)
    .then(response => {
      matieres.value = response.data;


    })
    .catch(error => {
      console.error('Erreur lors de la récupération des matières :', error);
    });
};


const getProfesseurs = (selectedMatieres, selectedNiveau, selectedFiliere) => {

  axios.get(`/api/selectedProfesseurs/${selectedMatieres}/${selectedNiveau}/${selectedFiliere}`)
    .then(response => {
      professeurs.value = response.data;
      // Mise à jour manuelle de showFiliere en fonction des filières récupérées
      showProfesseurs = !!response.data.length;
    })
    .catch(error => {
      console.error('Erreur lors de la récupération des professeurs :', error);
    });
};

const createUser = (values, { resetForm, setErrors }) => {
  axios.post('/api/etudiants', {
    ...values,
    niv: selectedNiveau.value,
    fil: selectedFiliere.value,
    matieres: selectedMatieres.value.map(matiere => matiere),
    professeurs: selectedProfesseurs.value.map(professeur => professeur)

  })

    .then((response) => {
      users.value.unshift(response.data);
      setTimeout(() => {
        $('#userFormModal').modal('hide');
      }, 10);
      resetForm();
      toastr.success('Étudiant créé avec succès !');
      getUsers(); // Mettre à jour la DataTable après la création
      //   location.reload(); // Rechargement de la page après la suppression
    })
    .catch((error) => {
      if (error.response.data.errors) {
        setErrors(error.response.data.errors);
      }
    })
};

const updateUser = (values, { setErrors }) => {
  axios.put('/api/etudiants/' + formValues.value.id, {
    ...values,
    niv: selectedNiveau.value,
    fil: selectedFiliere.value,
    matieres: selectedMatieres.value.map(matiere => matiere),
    professeurs: selectedProfesseurs.value.map(professeur => professeur)

  })
    .then((response) => {
      const index = users.value.findIndex(user => user.id === response.data.id);
      users.value[index] = response.data;
      setTimeout(() => {
        $('#userFormModal').modal('hide');
      }, 10);
      resetFormValues();
      toastr.success('Étudiant mis à jour avec succès !');
      getUsers(); // Mettre à jour la DataTable après la mise à jour
      if (IsBigtable.value) { // Utiliser directement IsBigtable.value pour vérifier si la mise à jour provient de la DataTable
        window.location.reload();
      }
      //   location.reload(); // Rechargement de la page après la suppression
    }).catch((error) => {
      setErrors(error.response.data.errors);
      console.log(error);
    })
};

onMounted(() => {
  // Chargement des liens vers les feuilles de style DataTables et des boutons d'exportation
  const dataTableCSS = document.createElement('link');
  dataTableCSS.rel = 'stylesheet';
  dataTableCSS.href = 'https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css';
  document.head.appendChild(dataTableCSS);

  const buttonsCSS = document.createElement('link');
  buttonsCSS.rel = 'stylesheet';
  buttonsCSS.href = 'https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css';
  document.head.appendChild(buttonsCSS);

  // Chargement du script de jQuery
  const jQueryScript = document.createElement('script');
  jQueryScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js';
  jQueryScript.onload = () => {
    // Une fois que jQuery est chargé avec succès, chargez les autres scripts
    const dataTableScript = document.createElement('script');
    dataTableScript.src = 'https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js';
    document.head.appendChild(dataTableScript);

    const buttonsScript = document.createElement('script');
    buttonsScript.src = 'https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js';
    document.head.appendChild(buttonsScript);

    const buttonsScript1 = document.createElement('script');
    buttonsScript1.src = 'https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js';
    document.head.appendChild(buttonsScript1);

    const buttonsScript2 = document.createElement('script');
    buttonsScript2.src = 'https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js';
    document.head.appendChild(buttonsScript2);

    const jszipScript = document.createElement('script');
    jszipScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js';
    document.head.appendChild(jszipScript);

    const pdfMakeScript = document.createElement('script');
    pdfMakeScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js';
    document.head.appendChild(pdfMakeScript);

    const pdfFontsScript = document.createElement('script');
    pdfFontsScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js';
    document.head.appendChild(pdfFontsScript);

    const buttonsHTML5Script = document.createElement('script');
    buttonsHTML5Script.src = 'https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js';
    document.head.appendChild(buttonsHTML5Script);

    const buttonsPrintScript = document.createElement('script');
    buttonsPrintScript.src = 'https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js';
    document.head.appendChild(buttonsPrintScript);


    // ... Ajoutez d'autres scripts de DataTables et de ses boutons de la même manière

    // Initialisation de la DataTable


    // Appelez la fonction pour récupérer les données et initialiser la DataTable
    getUsers();
    getNiveux();
    getMatieres();
    updateValuesPeriodically();


  };

  selectedMonth2.value = getDefaultDate();

  document.head.appendChild(jQueryScript);

});
</script>
