
<template>

    <div class="content-header">
   <div class="container-fluid">
   <div class="row mb-2">
   <div class="col-sm-6" style="display: flex;
    justify-content: space-between;">
   <h1 class="m-0" style="font-weight: 500 !important; ">Les salaires du mois</h1>
  <Field
  style="width: 30% !important;"
  name="MoisPorAfficher"
  type="month"
  class="form-control"
  id="selectedMonth"
  placeholder="Entrer la date de début"
  required
  :value="getDefaultMonth()"
  v-model="selectedMonth"
  @change="getUsers"
/>

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



  <div class="container" style="overflow : auto !important; height:  calc(100vh - 176px) !important; max-width: 2040px !important;">

    <table id="myTable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <!-- <th>#</th> -->
              <th>Nom</th>
              <th>Prenom</th>
              <th>État de salaire</th>
              <th>Somme à payé</th>
              <th>Montant payé</th>
              <th>Reste a payé</th>
              <th>Date de salaire</th>



            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in users" :key="user.id">
              <!-- <td>{{ index + 1 }}</td> -->
              <td>{{ user.Nom }}</td>
              <td>{{ user.Prenom}}</td>
              <td v-if="user.Etat === 'Non payé'" style="color: red; font-weight: bold;">{{ user.Etat }}</td>
              <td v-else-if="user.Etat === 'Payé'" style="color: green; font-weight: bold;">{{ user.Etat }}</td>
              <td v-else-if="user.Etat === 'Payé et plus'" style="color: green; font-weight: bold;">{{ user.Etat }}</td>
              <td v-else style="color: orangered; font-weight: bold;">{{ user.Etat }}</td>
              <td>{{ user.SommeApaye }} dh</td>
              <td>{{ user.Montant }} dh</td>
              <td>{{ user.Reste }} dh</td>
              <td>{{ user.DatePaiment }}</td>
              <td>
                <a href="#" @click.prevent="editUser(user)" class="btn btn-primary btn-sm">
  <i class="fa fa-edit"></i>
</a>  </td>




            </tr>
          </tbody>
        </table>
  </div>




   </div>
   </div>


   <!-- Modal pour ajouter un nouvel Etudiant -->
    <div class="modal fade" id="userFormModal" tabindex="-1" role="dialog" aria-labelledby="userFormModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content" style="margin-top: -28px !important;">
          <div class="modal-header">
            <h5 class="modal-title" id="userFormModalLabel">
            <span >Effectuer un salaire</span>

            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>


<!--The form is here => -->
          <Form ref="form" @submit="handleSubmit" :validation-schema="createUserSchema"
          v-slot="{errors}" :initial-values="formValues">
          <div class="modal-body">
            <!-- Formulaire pour ajouter un nouvel Etudiant -->

              <div class="form-group">
                <label for="SommeApaye">Somme a payé</label>
                <Field name="SommeApaye" type="number" class="form-control"
                :class="{'is-invalid': errors.SommeApaye }"
                id="SommeApaye" placeholder="Entrer somme a payé par mois" required />
                <span class="invalid-feedback">{{ errors.SommeApaye }}</span>
              </div>
              <div class="form-group">
                <label for="Montant">Somme payé</label>
                <Field name="Montant" type="number" class="form-control"
                :class="{'is-invalid': errors.Montant }"
                id="Montant" placeholder="Entrer la somme payé" required />
                <span class="invalid-feedback">{{ errors.Montant }}</span>
              </div>




        <div class="form-group">
    <label for="DatePaiment">Date de salaire (mm/jj/aaaa)</label>
    <Field
      name="DatePaiment"
      type="date"
      class="form-control"
      :class="{'is-invalid': errors.DatePaiment }"
      id="DatePaiment"
      placeholder="Entrer la date de paiment"
      required
      v-bind:value="getDefaultDate()"
    />
    <span class="invalid-feedback">{{ errors.DatePaiment }}</span>
  </div>



          </div>
          <div class="modal-footer">
            <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cancelEdit">Annuler</button>
    <button type="submit"   class="btn btn-primary">Enregistrer</button>
</div>

          </div>
        </form>
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
const formValues = ref();
const form = ref(null);


const selectedFiliere = ref('');
const filieres = ref([]);

const selectedMatieres = ref([]); // Utilisation d'un tableau pour stocker les matières sélectionnées
const matieres = ref([]);

let showFiliere = false;
const selectedMonth = ref(''); // Initialisez selectedMonth comme une référence avec une valeur initiale vide


const cancelEdit = () => {
    resetFormValues(); // Réinitialiser le formulaire
    // Autres actions si nécessaire lors de l'annulation de la modification
};

const resetFormValues = () => {
    form.value.resetForm(); // Utilisez la méthode resetForm() fournie par VeeValidate pour réinitialiser le formulaire
    // Remettre à zéro les valeurs sélectionnées et autres états si nécessaire

    // Autres remises à zéro si nécessaire
};





const getDefaultDate = () => {
  const now = new Date();
  const year = now.getFullYear();
  const month = (now.getMonth() + 1).toString().padStart(2, '0'); // +1 car les mois vont de 0 à 11
  const day = now.getDate().toString().padStart(2, '0');
  return `${year}-${month}-${day}`;
};







const initDataTable = () => {
  $('#myTable').DataTable({
    ddom: 'Bfrtip',
    sSwfPath: "http://datatables.net/release-datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf",
    buttons: ['excel', 'pdf'],
    paging: true,
    lengthChange: true, // Force l'affichage des options de changement de longueur
    columns: [
      { data: 'Nom' },
      { data: 'Prenom' },
      { data: 'Etat' },
      { data: 'SommeApaye' },
      { data: 'Montant' },
      { data: 'Reste' },
      { data: 'DatePaiment' },
      {
        data: null,
        render: function (data, type, row) {
          const editBtn = document.createElement('button');
          editBtn.classList.add('btn', 'btn-primary', 'btn-sm', 'edit-btn');
          editBtn.innerHTML = '<i class="fa fa-edit"></i>';
          editBtn.addEventListener('click', function () {
            editUser(row);
          });
          return editBtn.outerHTML;
        }
      },
    ],
    columnDefs: [
      {
        targets: 2, // Indice de la colonne 'État de paiement'
        render: function (data, type, row) {
          if (data === 'Non payé') {
            return '<span style="color: red; font-weight: bold;">' + data + '</span>';
          } else if (data === 'Payé' || data === 'Payé et plus') {
            return '<span style="color: green; font-weight: bold;">' + data + '</span>';
          } else {
            return '<span style="color: orangered; font-weight: bold;">' + data + '</span>';
          }
        },
      },
    ],
    createdRow: function (row, data, dataIndex) {
      $(row).find('.edit-btn').on('click', function () {
        editUser(data);
      });
    },
    data: users.value
  });
};





const getDefaultMonth = () => {
  const now = new Date();
  const month = (now.getMonth() + 1).toString().padStart(2, '0'); // Obtenir le mois actuel
  const year = now.getFullYear().toString(); // Obtenir l'année actuelle
  return `${year}-${month}`; // Format YYYY-MM pour le champ month
};




const getUsers = () => {

    axios.get('/api/professeursForSalaire', { params: { date: selectedMonth.value } })
    .then((response) => {
      users.value = response.data;

      if ($.fn.DataTable.isDataTable('#myTable')) {
        $('#myTable').DataTable().destroy();
      }

      initDataTable(); // Réinitialiser la table avec les nouvelles données
    })
    .catch((error) => {
      console.error('Erreur lors de la récupération des professeurs :', error);
    });
};

const updateValuesPeriodically = () => {
  setInterval(() => {
    getUsers();
  }, 50000000);
};





















const createUserSchema = yup.object({


    SommeApaye: yup.string().required(),
    Montant: yup.string().required(),
    DatePaiment: yup.date().required(),





});

const editUserSchema = yup.object({

    SommeApaye: yup.string().required(),
    Montant: yup.string().required(),
    DatePaiment: yup.date().required(),
});



const addUser = () => {
    resetFormValues();
    $('#userFormModal').modal('show');
};



const editUser = (user) => {
    form.value.resetForm();
    $('#userFormModal').modal('show');

    // Initialiser les valeurs pour Nom, Prenom, Etat, SommeApaye
    formValues.value = {
        id: user.id,
        nom: user.Nom,
        prenom: user.Prenom,
        Etat: user.Etat,
        SommeApaye: user.SommeApaye,
        Montant: user.Montant,
        Reste: user.Reste,
        DatePaiment: user.DatePaiment ? user.DatePaiment : getDefaultDate(),


    };


};





const updatePaiement = (values, { setErrors }) => {
  axios.put('/api/salaires/' + formValues.value.id, values)
    .then((response) => {
      const index = users.value.findIndex(user => user.id === response.data.id);
      users.value[index] = response.data;
      setTimeout(() => {
        $('#userFormModal').modal('hide');
      }, 10);
      toastr.success('Paiement mis à jour avec succès !');
      getUsers(); // Mettre à jour la DataTable après la mise à jour
    //   location.reload(); // Rechargement de la page après la suppression
    }).catch((error) => {
      setErrors(error.response.data.errors);
      console.log(error);
    })
};

const handleSubmit = (values, actions) => {


        updatePaiement(values, actions);


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
     selectedMonth.value = getDefaultMonth();
    getUsers();
    updateValuesPeriodically();


  };

  document.head.appendChild(jQueryScript);

});
</script>

