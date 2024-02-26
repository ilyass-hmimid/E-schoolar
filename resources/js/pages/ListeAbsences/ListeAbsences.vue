
<template>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6" style="display: flex;
      justify-content: space-between;
      flex-direction: row-reverse; ">
            <h1 class="m-0" style="font-weight: 500 !important; ">Les absences</h1>

          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
     <li class="breadcrumb-item active">Etudiants</li> -->
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
                <!-- <th>#</th> -->
                <th>Nom</th>
                <th>Prenom</th>
                <th>Téléphone</th>
                <th>Adresse</th>
                <th>Niveau</th>
                <th>Filière</th>
                <th>Matière</th>
                <th>Professeur</th>
                <th>Date d'absence</th>
                <!-- <th></th> -->
                <!-- <th>Date de début</th> -->

              </tr>
            </thead>
            <tbody>
              <tr v-for="(user, index) in users" :key="user.id">
                <!-- <td>{{ index + 1 }}</td> -->
                <td>{{ user.Nom }}</td>
                <td>{{ user.Prenom }}</td>
                <td>{{ user.Tele }}</td>
                <td>{{ user.Adresse }}</td>
                <td>{{ user.IdNiv }}</td>
                <td>{{ user.IdFil }}</td>
                <td>{{ user.Matieres }}</td>
                <td>{{ user.Professeurs }}</td>
                <!-- <td v-if="user.Date_debut == ''"></td>
                <td v-else>{{ user.Date_debut }}</td> -->
                <td>{{ user.Date_debut }}</td>
                <!-- <td></td> -->
                <!-- <td><a href="https://wa.me/+2120681328800?text=السلام%20عليكم%2" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" height="30px" fill="green"  viewBox="0 0 448 512">!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.<path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg></a></td> -->
                <!-- <td>
                    <a href="https://wa.me/+2120681328800?text=السلام%20عليكم،نحيطكم%20علما%20أن%20التلميذ(ة)%20{{ user.Nom }}%20{{ user.Prenom }}%20تغيب(ت)%20يوم%20{{ user.Date_debut }}" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" height="30px" fill="green" viewBox="0 0 448 512">!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.<path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg></a></td> -->

                    <td>
  <a @click="openWhatsApp(user)" style="cursor: pointer;">
    <!-- <svg xmlns="http://www.w3.org/2000/svg" height="30px" fill="green" viewBox="0 0 448 512"> -->
        <svg xmlns="http://www.w3.org/2000/svg" height="30px" fill="green" viewBox="0 0 448 512">!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.<path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>

  </a>
</td>

                <!-- <td>
        <span @click="sendMessage(user.Tele)">📱</span>
    </td> -->

                <!-- <td>
                  <a href="#" @click.prevent="editUser(user)" class="btn btn-primary btn-sm">
                    <i class="fa fa-edit"></i>
                  </a>
                </td>

                <td> <a href="#" @click.prevent="confirmUserDeletion(user)" class="btn btn-danger btn-sm ml-4">
                    <i class="fa fa-trash"></i>
                  </a></td> -->


              </tr>
            </tbody>
          </table>
        </div>




      </div>
    </div>


    <!-- Modal pour ajouter un nouvel Etudiant -->
    <div class="modal fade" id="userFormModal" tabindex="-1" role="dialog" aria-labelledby="userFormModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content" style="margin-top: -28px !important;">
          <div class="modal-header">
            <h5 class="modal-title" id="userFormModalLabel">
              <span v-if="editing">Modifier l'étudiant</span>
              <span v-else>Ajouter nouveau étudiant</span>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>


          <!--The form is here => -->
          <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editUserSchema : createUserSchema"
            v-slot="{ errors }" :initial-values="formValues">
            <div class="modal-body">
              <!-- Formulaire pour ajouter un nouvel Etudiant -->

              <div class="form-group">
                <label for="name">Nom</label>
                <Field name="nom" type="text" class="form-control" :class="{ 'is-invalid': errors.nom }" id="nom"
                  placeholder="Entrer nom" required v-model="formValues.nom"/>
                <span class="invalid-feedback">{{ errors.nom }}</span>
              </div>
              <div class="form-group">
                <label for="prenom">Prenom</label>
                <Field name="prenom" type="text" class="form-control" :class="{ 'is-invalid': errors.prenom }" id="prenom"
                  placeholder="Entrer prenom" required v-model="formValues.prenom"/>
                <span class="invalid-feedback">{{ errors.prenom }}</span>
              </div>
              <div class="form-group">
                <label for="tele">Telephone</label>
                <Field name="tele" type="text" class="form-control" :class="{ 'is-invalid': errors.tele }" id="tele"
                  placeholder="Entrer telephone"  v-model="formValues.tele"/>
                <span class="invalid-feedback">{{ errors.tele }}</span>
              </div>
              <div class="form-group">
                <label for="adresse">Adresse</label>
                <Field name="adresse" type="text" class="form-control" :class="{ 'is-invalid': errors.adresse }" id="adresse"
                  placeholder="Entrer adresse"  v-model="formValues.adresse"/>
                <span class="invalid-feedback">{{ errors.adresse }}</span>
              </div>


              <div class="form-group">
                <label for="niv">Niveau</label>
                <select v-model="selectedNiveau" @change="handleNiveauChange($event.target.value)" class="form-control"
                  id="niv" required style="color: black !important;">
                  <option v-if="!editing" value="">Sélectionner un niveau</option>
                  <option v-for="niveau in niveaux" :key="niveau.id" :value="niveau" style="color: black !important;">{{
                    niveau }}</option>
                </select>
                <!-- <span class="invalid-feedback">{{ errors.niv }}</span> -->
                <span v-if="selectedNiveau === ''" style="font-size: 80%; color: #dc3545;">Veuillez sélectionner un niveau
                  !!</span>
              </div>






              <div class="form-group">
                <label for="fil">Filière</label>
                <select v-model="selectedFiliere" @change="handleFiliereChange($event.target.value)" class="form-control"
                  id="fil" required style="color: black !important;">
                  <option v-if="!editing" value="">Sélectionner une filière</option>
                  <option v-for="filiere in filieres" :key="filiere.id" :value="filiere" style="color: black !important;">{{
                    filiere }}</option>
                </select>
                <span v-if="selectedFiliere === ''" style="font-size: 80%; color: #dc3545;">Veuillez sélectionner une
                  filière!!</span>
                <span v-else>
                  <!-- Mettre à jour la valeur de showMat -->
                  <template>
                    <span v-show="showMat = true"></span>
                  </template>
                </span>
              </div>




              <div v-if="showMat" class="form-group">
                <label for="mat">Matières</label>
                <div class="d-flex flex-wrap">
                  <div v-for="(matiere, index) in matieres" :key="matiere.id" class="mr-3 mb-2">
                    <input type="checkbox" :id="'matiere_' + matiere.id" :value="matiere" v-model="selectedMatieres"
                      @change="handleMatiereChange(selectedMatieres, selectedNiveau, selectedFiliere)">
                    <label :for="'matiere_' + matiere.id" style="font-weight: 400;">{{ matiere }}</label>
                  </div>

                </div>
                <!-- <span class="invalid-feedback">{{ errors.mat }}</span> -->
                <span v-if="selectedMatieres.length == 0" style="   font-size: 80%;
      color: #dc3545;">Veuillez sélectionner ou moins une matière!!</span>

              </div>



              <div v-if="showMat" class="form-group">
                <label for="prof">Sera Enseigné par le(s) professeur(s)</label>
                <div class="d-flex flex-wrap">
                  <div v-for="(professeur, index) in professeurs" :key="professeur.id" class="mr-3 mb-2">
                    <input type="checkbox" :id="'professeur_' + professeur.id" :value="professeur"
                      v-model="selectedProfesseurs">
                    <label :for="'professeur_' + professeur.id" style="font-weight: 400;">{{ professeur }}</label>
                  </div>

                </div>
                <span v-if="selectedProfesseurs.length == 0" style="   font-size: 80%;
      color: #dc3545;">Veuillez sélectionner ou moins un professeur!!</span>

              </div>

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


    <!-- Modal pour ajouter un nouvel Etudiant -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="userFormModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="userFormModalLabel">
              <span>Supprimer étudiant</span>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <h5>Êtes-vous sûr de vouloir supprimer cet étudiant</h5>
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
    nom: '',
    prenom: '',
    tele: '',
    adresse: '',
    Date_debut:'',

  });
  const form = ref(null);
  const userIdBeingDeleted = ref(null);

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

  let showMat = false;

  const cancelEdit = () => {
    editing.value = false;
    form.value.resetForm();
    resetForm();
    resetFormValues(); // Réinitialiser le formulaire
    // Autres actions si nécessaire lors de l'annulation de la modification
  };

  const openWhatsApp = (user) => {
    const message = encodeURIComponent(`${user.Date_debut} السلام عليكم، نحيطكم علما أن التلميذ(ة) ${user.Nom} ${user.Prenom} تغيب(ت) يوم`);
// const message = encodeURIComponent(`Bonjour, Nous vous informons que l'élève ${user.Nom} ${user.Prenom} était absent(e) le ${user.Date_debut}. Cordialement.`);
  const whatsappURL = `https://wa.me/+212${user.Tele}?text=${message}`;
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



  const getDefaultMonth = () => {
    const now = new Date();
    const month = (now.getMonth() + 1).toString().padStart(2, '0'); // Obtenir le mois actuel
    const year = now.getFullYear().toString(); // Obtenir l'année actuelle
    return `${year}-${month}`; // Format YYYY-MM pour le champ month
  };
  const sendMessage = (tele) => {
        const whatsappURL = `https://wa.me/+2120681328800?text=${encodeURIComponent('السلام عليكم')}`;
        window.open(whatsappURL, '_blank');
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


  const IsBigtable = ref(false);



  const initDataTable = () => {
    $('#myTable').DataTable({
        dom: 'Bfrtip',
        sSwfPath: "http://datatables.net/release-datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf",
        buttons: ['excel', 'pdf'],
        paging: true,
        lengthChange: true,
        columns: [
            { data: 'Nom' },
            { data: 'Prenom' },
            { data: 'Tele' },
            { data: 'Adresse' },
            { data: 'IdNiv' },
            { data: 'IdFil' },
            { data: 'Matieres' },
            { data: 'Professeurs' },
            { data: 'Date_debut' },
            // { data: 'created_at' },
            {
                data: null,
                render: function(data, type, row) {
                    return '<a href="https://wa.me/+2120681328800?text=السلام%20عليكم%2" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" height="30px" fill="green"  viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg></a>';
                }
            },
        ],
        data: users.value
    });
};






  const getUsers = () => {
    axios.get('/api/ListeAbsences')
      .then((response) => {
        users.value = response.data;

        if ($.fn.DataTable.isDataTable('#myTable')) {
          $('#myTable').DataTable().destroy();
        }

        initDataTable(); // Réinitialiser la table avec les nouvelles données
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


  const handleMatiereChange = (selectedMatieres, selectedNiveau, selectedFiliere) => {
    // Mettre à jour les valeurs sélectionnées des matières
    selectedMatieres.value = selectedMatieres;

    // Autres actions à effectuer en fonction des matières sélectionnées
    if (selectedMatieres.length > 0) {
      getProfesseurs(selectedMatieres, selectedNiveau, selectedFiliere); // Appel de la fonction avec les valeurs sélectionnées
    } else {
      showProfesseurs = false;
      professeurs.value = [];
    }
  };


  const handleFiliereChange = (newVal) => {
    selectedFiliere.value = newVal;

    // showMat = true;

  };










  const createUserSchema = yup.object({

    nom: yup.string().required(),
    prenom: yup.string().required(),
  //   tele: yup.string(),
  //   adresse: yup.string(),
    Date_debut: yup.date().required(),


  });

  const editUserSchema = yup.object({

    nom: yup.string().required(),
    prenom: yup.string().required(),
  //   tele: yup.string(),
  //   adresse: yup.string(),
    Date_debut: yup.date().required(),

  });

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

  const addUser = () => {
    editing.value = false;
    formValues.value.Date_debut = getDefaultDate(); // Initialiser Date_debut avec getDefaultDate()
  //   resetFormValues();


    $('#userFormModal').modal('show');
  };



  const editUser = (user) => {
    editing.value = true;
  //   form.value.resetForm();
    getFilieres(user.IdNiv);

    getProfesseurs(selectedMatieres, selectedNiveau, selectedFiliere);

    $('#userFormModal').modal('show');

    // Initialiser les valeurs pour Nom, Prenom, Telephone, Adresse
    formValues.value = {
      id: user.id,
      nom: user.Nom,
      prenom: user.Prenom,
      tele: user.Tele,
      adresse: user.Adresse,
      niv: user.IdNiv,
      fil: user.IdFil,
      matieres: user.Matieres, // Garder les matières sélectionnées
      professeurs: user.professeurs,

      Date_debut: user.Date_debut
    };

    // Initialiser les valeurs sélectionnées pour Niveau et Filière
    selectedNiveau.value = user.IdNiv; // Sélectionner l'ancienne valeur pour le niveau
    selectedFiliere.value = user.IdFil; // Sélectionner l'ancienne valeur pour la filière

    // Cocher les anciennes valeurs pour Matières
    selectedMatieres.value = user.Matieres; // Cocher les matières précédemment sélectionnées
    selectedProfesseurs.value = user.Professeurs; // Cocher les matières précédemment sélectionnées

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
    axios.delete(`/api/etudiants/${userIdBeingDeleted.value}`)
      .then(() => {
        $('#deleteUserModal').modal('hide');
        toastr.success('Étudiant supprimé avec succès !');
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

