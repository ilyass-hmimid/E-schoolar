
<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6" style="display: flex;
    justify-content: space-between;">
          <h1 class="m-0" style="font-weight: 500 !important; ">Les paiments du mois</h1>
          <Field style="width: 30% !important;" name="MoisPorAfficher" type="month" class="form-control"
            id="selectedMonth" placeholder="Entrer la date de début" required :value="getDefaultMonth()"
            v-model="selectedMonth" @change="getUsers" />

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
              <th>État de paiement</th>
              <th>Somme à payé</th>
              <th>Montant payé</th>
              <th>Reste a payé</th>
              <th>Date de paiment</th>



            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in users" :key="user.id">
              <!-- <td>{{ index + 1 }}</td> -->
              <td>{{ user.Nom }}</td>
              <td>{{ user.Prenom }}</td>
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
                </a>
              </td>




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
            <span>Effectuer un paiement</span>

          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>


        <!--The form is here => -->
        <Form ref="form" @submit="handleSubmit" :validation-schema="createUserSchema" v-slot="{ errors }"
          :initial-values="formValues">
          <div class="modal-body">
            <!-- Formulaire pour ajouter un nouvel Etudiant -->

            <div v-if="IsAdmin" class="form-group">
              <label for="SommeApaye">Somme a payé</label>
              <Field name="SommeApaye" type="number" class="form-control" :class="{ 'is-invalid': errors.SommeApaye }"
                id="SommeApaye" placeholder="Entrer somme a payé par mois" required v-model="formValues.SommeApaye"/>
              <span class="invalid-feedback">{{ errors.SommeApaye }}</span>
            </div>
            <div v-else style="  display: none;" class="form-group">
              <label for="SommeApaye">Somme a payé</label>
              <Field name="SommeApaye" type="number" class="form-control" :class="{ 'is-invalid': errors.SommeApaye }"
                id="SommeApaye" placeholder="Entrer somme a payé par mois" required v-model="formValues.SommeApaye"/>
              <span class="invalid-feedback">{{ errors.SommeApaye }}</span>
            </div>
            <div class="form-group">
              <label for="Montant">Somme payé</label>
              <Field name="Montant" type="number" class="form-control" :class="{ 'is-invalid': errors.Montant }"
                id="Montant" placeholder="Entrer la somme payé" required v-model="formValues.Montant" />
              <span class="invalid-feedback">{{ errors.Montant }}</span>
            </div>




            <div class="form-group">
              <label for="DatePaiment">Date de paiement (mm/jj/aaaa)</label>
              <Field name="DatePaiment" type="date" class="form-control" :class="{ 'is-invalid': errors.DatePaiment }"
                id="DatePaiment" placeholder="Entrer la date de paiment" required v-model="formValues.DatePaiment" />
              <span class="invalid-feedback">{{ errors.DatePaiment }}</span>
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
import jsPDF from 'jspdf';

const toastr = useToastr();
const users = ref([]);
const formValues = ref({
  id: '',
  nom: '',
  prenom: '',
  Etat: '',
  SommeApaye: '',
  Montant: '',
  Reste: '',
  DatePaiment: 'getDefaultDate()'
});
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


const IsAdmin = ref('');

const getRole = () => {
  axios.get('/api/getRole')
    .then((response) => {
      IsAdmin.value = response.data;


    }

    )
    .catch((error) => {
      console.error('Erreur lors de la récupération de role:', error);
    });
};





const getDefaultMonth = () => {
  const now = new Date();
  const month = (now.getMonth() + 1).toString().padStart(2, '0'); // Obtenir le mois actuel
  const year = now.getFullYear().toString(); // Obtenir l'année actuelle
  return `${year}-${month}`; // Format YYYY-MM pour le champ month
};

const getUsers = () => {

  axios.get('/api/etudiantsForPaiment', { params: { date: selectedMonth.value } })
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
    getUsers();
  }, 5000000);
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
//   form.value.resetForm();
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
  axios.put('/api/paiements/' + formValues.value.id, values)
    .then((response) => {
      const index = users.value.findIndex(user => user.id === response.data.id);
      //   users.value[index] = response.data;
      setTimeout(() => {
        $('#userFormModal').modal('hide');
      }, 10);
      toastr.success('Paiement mis à jour avec succès !');
      getUsers(); // Mettre à jour la DataTable après la mise à jour

      // Après la mise à jour du paiement, générer le reçu de paiement PDF ici
      generateReceiptPDF(response.data); // Appelez la fonction pour générer le reçu PDF avec les données mises à jour

      // location.reload(); // Rechargement de la page après la suppression
    }).catch((error) => {
      setErrors(error.response.data.errors);
      console.log(error);
    })
};

const generateReceiptPDF = (data) => {
  const doc = new jsPDF();
  const width = doc.internal.pageSize.getWidth();
  const height = doc.internal.pageSize.getHeight();

  // Titre centré avec une taille de police plus grande


  // Ajout du logo en bas à gauche de la page
  const logoWidth = 40;
  const logoHeight = 40;
  const logoX = width / 2 - logoWidth / 2;
  const logoY = 2;

  doc.addImage('./imgs/logo.png', 'PNG', logoX, logoY, logoWidth, logoHeight);
  doc.setFontSize(22);
  doc.text('Reçu de paiement', logoX - 13, 50);
  // Ajout des détails du paiement
  doc.setFontSize(14);
  doc.text(`Nom et Prénom : ${data.Nom} ${data.Prenom}`, 18, 70);
  doc.text(`Niveau Scolaire : ${data.Niveau}`, 140, 70);
  doc.text(`Matières : ${data.Matieres}`, 18, 85);
  doc.text(`Somme à payer : ${data.SommeApaye} dh`, 140, 85);
  doc.text(`Montant payé : ${data.Montant} dh`, 18, 100);
  doc.text(`Reste à payer : ${data.Reste} dh`, 140, 100);
  doc.text(`Signature de la derection`, 26, 120);
  doc.text(`Date de paiement : ${data.DatePaiment}`, 126, 120);

  doc.setFontSize(30);
  doc.text(`- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - `, 7, 175);

  doc.addImage('./imgs/logo.png', 'PNG', logoX, 178, logoWidth, logoHeight);
  doc.setFontSize(22);
  doc.text('Reçu de paiement', logoX - 13, 226);
  // Ajout des détails du paiement
  doc.setFontSize(14);
  doc.text(`Nom et Prénom : ${data.Nom} ${data.Prenom}`, 18, 241);
  doc.text(`Niveau Scolaire : ${data.Niveau}`, 140, 241);
  doc.text(`Matières : ${data.Matieres}`, 18, 256);
  doc.text(`Somme à payer : ${data.SommeApaye} dh`, 140, 256);
  doc.text(`Montant payé : ${data.Montant} dh`, 18, 271);
  doc.text(`Reste à payer : ${data.Reste} dh`, 140, 271);

  doc.text(`Date de paiement : ${data.DatePaiment}`, logoX - 13, 288);







  doc.save(`Recu_Paiement_${data.Nom}_${data.Prenom}_${data.DatePaiment}.pdf`);
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

    getRole();


  };

  document.head.appendChild(jQueryScript);

});
</script>

