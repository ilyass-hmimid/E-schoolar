
<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6" style="display: flex;
    justify-content: space-between;
    flex-direction: row-reverse; ">
          <h1 class="m-0" style="font-weight: 500 !important; ">Les enseignements</h1>
          <!-- <button @click="addUser" type="button" class="mb-2 btn btn-primary" style="font-weight: bold;" >
        Ajouter nouveau Enseignement
    </button> -->
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
   <li class="breadcrumb-item active">Enseignements</li> -->
          </ol>
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
              <th>Nom Prof</th>
              <th>Prenom Prof</th>
              <th>Niveau</th>
              <th>Filière</th>
              <th>Matière</th>
              <th>Nombre d'étudiants enseignés</th>
              <th>Salaire par étudiant</th>
              <th>Somme</th>
              <th>Date de début</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in users" :key="user.id">
              <td>{{ user.Nom }}</td>
              <td>{{ user.Prenom }}</td>

              <td>{{ user.IdNiv }}</td>
              <td>{{ user.IdFil }}</td>
              <td>{{ user.IdMat }}</td>
              <td>{{ user.NbrEtu }}</td>
              <td>{{ user.SalaireParEtu }}</td>
              <td>{{ user.Somme }}</td>
              <td v-if="user.Date_debut == ''"></td>
              <td v-else>{{ user.Date_debut }}</td>
              <td>
                <a href="#" @click.prevent="editUser(user)" class="btn btn-primary btn-sm">
                  <i class="fa fa-edit"></i>
                </a>
              </td>



              <td> <a href="#" @click.prevent="confirmUserDeletion(user)" class="btn btn-danger btn-sm ml-4">
                  <i class="fa fa-trash"></i>
                </a></td>

              <td>
                <a href="#" @click.prevent="addUser(user)" class="btn btn-success btn-sm">
                  <i class="fa fa-plus"></i>
                </a>
              </td>


            </tr>
          </tbody>
        </table>
      </div>




    </div>
  </div>


  <!-- Modal pour ajouter un nouvel Enseignement -->
  <div class="modal fade" id="userFormModal" tabindex="-1" role="dialog" aria-labelledby="userFormModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content" style="margin-top: -28px !important;">
        <div class="modal-header">
          <h5 class="modal-title" id="userFormModalLabel">
            <span v-if="editing">Modifier le enseignement</span>

            <!-- <span v-else>Ajouter nouveau enseignement</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div> -->
            <span v-else>Ajouter nouveau enseignement pour le prof {{ currentProf.Prenom }} {{ currentProf.Nom }}</span>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>


        <!--The form is here => -->
        <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editUserSchema : createUserSchema"
          v-slot="{ errors }" :initial-values="formValues">
          <div class="modal-body">
            <!-- Formulaire pour ajouter un nouvel enseignement -->



            <!-- <label >Ensignement</label>

              <strong>  <hr style="  color : black"></strong> -->




            <div class="form-group">
              <label for="niv">Niveau</label>
              <select v-model="selectedNiveau" @change="handleNiveauChange($event.target.value)" class="form-control"
                id="niv" required style="color: black !important;">
                <option v-if="!editing" value="">Sélectionner un niveau</option>
                <option v-for="niveau in niveaux" :key="niveau.id" :value="niveau" style="color: black !important;">{{
                  niveau }}</option>
              </select>
              <!-- <span class="invalid-feedback">{{ errors.niv }}</span> -->
              <span v-if="selectedNiveau === ''" style="    font-size: 80%;
    color: #dc3545;">Veuillez sélectionner un niveau!!</span>

            </div>

            <div class="form-group">
              <label for="fil">Filière</label>
              <select v-model="selectedFiliere" @change="handleFiliereChange($event.target.value)" class="form-control"
                id="fil" required style="color: black !important;">
                <option v-if="!editing" value="">Sélectionner une filière</option>
                <option v-for="filiere in filieres" :key="filiere.id" :value="filiere" style="color: black !important;">{{
                  filiere }}</option>
              </select>
              <!-- <span class="invalid-feedback">{{ errors.fil }}</span> -->
              <span v-if="selectedFiliere === ''" style="    font-size: 80%;
    color: #dc3545; ">Veuillez sélectionner une filière!!</span>
            </div>

            <div class="form-group">
              <label for="mat">Matière</label>
              <select v-model="selectedMatiere" @change="handleMatiereChange($event.target.value)" class="form-control"
                id="mat" required style="color: black !important;">
                <option v-if="!editing" value="">Sélectionner une matière</option>
                <option v-for="matiere in matieres" :key="matiere.id" :value="matiere" style="color: black !important;">{{
                  matiere }}</option>
              </select>
              <span v-if="selectedMatiere === ''" style="font-size: 80%; color: #dc3545;">Veuillez sélectionner une
                matière !!</span>
            </div>

            <div v-if="editing" class="form-group">
              <label for="SalaireParEtu">Salaire par étudiant</label>
              <Field name="SalaireParEtu" type="number" class="form-control"
                :class="{ 'is-invalid': errors.SalaireParEtu }" id="SalaireParEtu"
                placeholder="Entrer un nouveau salaire par étudiant" required v-model="formValues.SalaireParEtu"/>
              <span class="invalid-feedback">{{ errors.SalaireParEtu }}</span>
            </div>


            <!-- <div class="form-group">
                <label for="slaireParEtu">Salaire pour chaque etudiant</label>
                <Field name="slaireParEtu"  type="number" class="form-control"
                :class="{'is-invalid': errors.slaireParEtu }"
                 id="slaireParEtu" placeholder="Entrer un salaire pour chaque etudiant enseigné" required />
                 <span class="invalid-feedback">{{ errors.slaireParEtu }}</span>
              </div> -->

            <!-- <strong>  <hr style="  color : black"></strong>

<a href="#" @click.prevent="ajouterEnseignement()" class="btn btn-success btn-sm">
  <i class="fa fa-plus"></i>
</a> -->



            <div class="form-group">
              <label for="Date_Debut">Date de début(mm/jj/aaaa)</label>
              <Field name="Date_debut" type="date" class="form-control" :class="{ 'is-invalid': errors.Date_debut }"
                id="Date_Debut" placeholder="Entrer la date de début" required v-model="formValues.Date_debut"
                v-bind:value="formValues && formValues.Date_debut ? formValues.Date_debut : getDefaultDate()" />
              <span class="invalid-feedback">{{ errors.Date_debut }}</span>

            </div>




          </div>
          <div class="modal-footer">
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cancelEdit">Annuler</button>
              <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>


  <!-- Modal pour ajouter un nouvel enseignement -->
  <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="userFormModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="userFormModalLabel">
            <span>Supprimer enseignement</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <h5>Êtes-vous sûr de vouloir supprimer cet enseignement</h5>
        </div>

        <div class="modal-footer">

          <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
          <button @click.prevent="deleteUser" type="button" class="btn btn-primary">Supprimer</button>

        </div>

      </div>
    </div>
  </div>
</template>




<script setup charset="utf-8">

import axios from 'axios';
import { watch } from 'vue';
import { ref, onMounted, reactive } from 'vue';
import { Form, Field } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import $ from 'jquery';
import 'datatables.net';

const toastr = useToastr();
const users = ref([]);
const editing = ref(false);
const formValues = ref({
  id: '',
  SalaireParEtu: '',
  Date_debut:'',

});
const form = ref(null);
const userIdBeingDeleted = ref(null);

const selectedNiveau = ref('')
const niveaux = ref([]);

const selectedFiliere = ref('');
const filieres = ref([]);

const selectedMatieres = ref([]); // Utilisation d'un tableau pour stocker les matières sélectionnées
const selectedMatiere = ref('');
const matieres = ref([]);

let showFiliere = false;


const cancelEdit = () => {
  resetFormValues(); // Réinitialiser le formulaire

};

const resetFormValues = () => {
  form.value.resetForm(); // Utilisez la méthode resetForm() fournie par VeeValidate pour réinitialiser le formulaire
  // Remettre à zéro les valeurs sélectionnées et autres états si nécessaire
  selectedNiveau.value = '';
  selectedFiliere.value = '';
  selectedMatiere.value = '';

  // Autres remises à zéro si nécessaire
};


// let NbrEnseigenement = 0;
// const ajouterEnseignement =() => {

//     NbrEnseigenement++;

// };



const getDefaultMonth = () => {
  const now = new Date();
  const month = (now.getMonth() + 1).toString().padStart(2, '0'); // Obtenir le mois actuel
  const year = now.getFullYear().toString(); // Obtenir l'année actuelle
  return `${year}-${month}`; // Format YYYY-MM pour le champ month
};

const getDefaultDate = () => {
  const now = new Date();
  const year = now.getFullYear();
  let nextMonth = now.getMonth() + 2; // +1 pour passer au mois suivant, +1 supplémentaire pour obtenir le mois suivant

  let newYear = year;
  if (nextMonth > 12) {
    nextMonth -= 12; // Retourner au premier mois de l'année suivante
    newYear += 1; // Ajouter une année
  }

  const month = nextMonth;
  const day = 1; // Le jour sera toujours le premier jour du mois
  return `${newYear}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
};









const formatMonth = (date) => {
  const options = { month: 'long', year: 'numeric' };
  return new Date(date).toLocaleDateString('fr-FR', options);
};






const initDataTable = () => {
  $('#myTable').DataTable({
    ddom: 'Bfrtip',
    sSwfPath: "http://datatables.net/release-datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf",
    buttons: ['excel', 'pdf',],
    paging: true,
    lengthChange: true, // Force l'affichage des options de changement de longueur
    columns: [
      { data: 'Nom' },
      { data: 'Prenom' },
      { data: 'IdNiv' },
      { data: 'IdFil' },
      { data: 'IdMat' },
      { data: 'NbrEtu' },
      { data: 'SalaireParEtu' },
      { data: 'Somme' },
      { data: 'Date_debut' },

      {
        data: null,
        render: function () {
          return '<button class="btn btn-primary btn-sm edit-btn"><i class="fa fa-edit"></i></button>';
        },
        createdCell: function (cell, cellData, rowData) {
          const editBtn = document.createElement('button');
          editBtn.classList.add('btn', 'btn-primary', 'btn-sm', 'edit-btn');
          editBtn.innerHTML = '<i class="fa fa-edit"></i>';
          editBtn.addEventListener('click', function () {
            IsBigtable.value = true;
            editUser(rowData);
          });
          cell.innerHTML = '';
          cell.appendChild(editBtn);
        }
      },
      {
        data: null,
        render: function () {
          return '<button class="btn btn-danger btn-sm delete-btn"><i class="fa fa-trash"></i></button>';
        },
        createdCell: function (cell, cellData, rowData) {
          const deleteBtn = document.createElement('button');
          deleteBtn.classList.add('btn', 'btn-danger', 'btn-sm', 'delete-btn');
          deleteBtn.innerHTML = '<i class="fa fa-trash"></i>';
          deleteBtn.addEventListener('click', function () {
            IsBigtable.value = true;
            confirmUserDeletion(rowData);
          });
          cell.innerHTML = '';
          cell.appendChild(deleteBtn);
        }
      },
      {
        data: null,
        render: function () {
          return '<button class="btn btn-success btn-sm add-btn"><i class="fa fa-plus"></i></button>';
        },
        createdCell: function (cell, cellData, rowData) {
          const addBtn = document.createElement('button');
          addBtn.classList.add('btn', 'btn-success', 'btn-sm', 'add-btn');
          addBtn.innerHTML = '<i class="fa fa-plus"></i>';
          addBtn.addEventListener('click', function () {
            IsBigtable.value = true;
            addUser(rowData);
          });
          cell.innerHTML = '';
          cell.appendChild(addBtn);
        }
      },



    ],
    data: users.value
  });
};

const IsBigtable = ref(false);



const getUsers = () => {
  axios.get('/api/enseignements')
    .then((response) => {
      users.value = response.data;

      if ($.fn.DataTable.isDataTable('#myTable')) {
        $('#myTable').DataTable().destroy();
      }

      initDataTable(); // Réinitialiser la table avec les nouvelles données
    })
    .catch((error) => {
      console.error('Erreur lors de la récupération des enseignements :', error);
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

// watch(selectedNiveau, (newVal) => {
//   if (newVal) {
//     getFilieres(newVal); // Appel de getFilieres avec l'ID du niveau sélectionné
//     showFiliere = true; // Mettre à jour showFiliere une fois que les données sont disponibles
//   } else {
//     showFiliere = false; // Masquer les filières si aucun niveau n'est sélectionné
//   }
// });

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
      console.error('Erreur lors de la récupération des filières :', error);
    });
};
const handleNiveauChange = (newVal) => {
  selectedNiveau.value = newVal; // Mettre à jour la valeur de selectedNiveau avec l'ID du niveau sélectionné
  // console.log(selectedNiveau.value);
  if (newVal) {
    getFilieres(newVal);
  } else {
    showFiliere = false;
    filieres.value = [];
  }
};

const handleFiliereChange = (newVal) => {
  selectedFiliere.value = newVal;

};

const handleMatiereChange = (value) => {
  selectedMatiere.value = value; // Mettez à jour la matière sélectionnée
  // Ajoutez ici toute logique supplémentaire à exécuter lorsqu'une nouvelle matière est sélectionnée
};










const createUserSchema = yup.object({

  Date_debut: yup.date().required(),


});

const editUserSchema = yup.object({


  SalaireParEtu: yup.string().required(),
  Date_debut: yup.date().required(),

});

const createUser = (values, { resetForm, setErrors }) => {
  axios.post('/api/enseignements', {
    ...values,
    niv: selectedNiveau.value,
    fil: selectedFiliere.value,
    mat: selectedMatiere.value,
    currentProf: currentProf.value,
  })

    .then((response) => {
      users.value.unshift(response.data);
      setTimeout(() => {
        $('#userFormModal').modal('hide');
      }, 10);
      resetForm();
      toastr.success('Enseignement créé avec succès !');
      getUsers(); // Mettre à jour la DataTable après la création
      if(IsBigtable.value){ // Utiliser directement IsBigtable.value pour vérifier si la mise à jour provient de la DataTable
        window.location.reload();
      }
      //   location.reload(); // Rechargement de la page après la suppression
    })
    .catch((error) => {
      if (error.response.data.errors) {
        setErrors(error.response.data.errors);
      }
    })
};


let currentProf = ref('');


const addUser = (user) => {
  currentProf.value = user;
  formValues.value.Date_debut = getDefaultDate(); // Initialiser Date_debut avec getDefaultDate()
  editing.value = false;
//   resetFormValues();
  $('#userFormModal').modal('show');
};



const editUser = (user) => {
  editing.value = true;
//   resetFormValues();
//   form.value.resetForm();
  getFilieres(user.IdNiv);
  $('#userFormModal').modal('show');

  formValues.value = {
    id: user.id,
    niv: user.IdNiv,
    fil: user.IdFil,
    mat: user.IdMat, // Garder les matières sélectionnées
    SalaireParEtu: user.SalaireParEtu, // Garder les matières sélectionnées
    Date_debut: user.Date_debut
  };

  // Initialiser les valeurs sélectionnées pour Niveau et Filière
  selectedNiveau.value = user.IdNiv; // Sélectionner l'ancienne valeur pour le niveau
  selectedFiliere.value = user.IdFil; // Sélectionner l'ancienne valeur pour la filière

  // Cocher les anciennes valeurs pour Matières
  selectedMatiere.value = user.IdMat; // Cocher les matières précédemment sélectionnées
};





const updateUser = (values, { setErrors }) => {
  axios.put('/api/enseignements/' + formValues.value.id, {
    ...values,
    niv: selectedNiveau.value,
    fil: selectedFiliere.value,
    mat: selectedMatiere.value,
  })
    .then((response) => {
      const index = users.value.findIndex(user => user.id === response.data.id);
      users.value[index] = response.data;
      setTimeout(() => {
        $('#userFormModal').modal('hide');
      }, 10);
      resetFormValues();

      toastr.success('Enseignement mis à jour avec succès !');
      getUsers(); // Mettre à jour la DataTable après la mise à jour
      if(IsBigtable.value){ // Utiliser directement IsBigtable.value pour vérifier si la mise à jour provient de la DataTable
        window.location.reload();
      }
      //   location.reload(); // Rechargement de la page après la suppression
    }).catch((error) => {
      setErrors(error.response.data.errors);
      console.log(error);
    })
};

const handleSubmit = (values, actions) => {

  if (editing.value) {
    updateUser(values, actions);
  } else {
    createUser(values, actions);
  }
};



const confirmUserDeletion = (user) => {
  userIdBeingDeleted.value = user.id;
  $('#deleteUserModal').modal('show');
};

const deleteUser = () => {
  axios.delete(`/api/enseignements/${userIdBeingDeleted.value}`)
    .then(() => {
      $('#deleteUserModal').modal('hide');
      toastr.success('Enseignement supprimé avec succès !');
      users.value = users.value.filter(user => user.id !== userIdBeingDeleted.value);
      userIdBeingDeleted.value = null;
      getUsers(); // Mettre à jour la DataTable après la suppression
      if(IsBigtable.value){ // Utiliser directement IsBigtable.value pour vérifier si la mise à jour provient de la DataTable
        window.location.reload();
      }
      //   location.reload(); // Rechargement de la page après la suppression
    })
    .catch((error) => {
      console.error('Erreur lors de la suppression de l\'utilisateur :', error);
    });
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

  document.head.appendChild(jQueryScript);

});
</script>

