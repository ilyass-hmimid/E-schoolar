
<template>

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6" style="display: flex;
      justify-content: space-between;">
            <h1 class="m-0" style="font-weight: 600 !important; ">Mes étudiants</h1> <br>
            <h2 style="background-color: #007bff; color : white; height: 37px;">{{ TotalEtu }}</h2>
<div style="width: 75% !important; display: flex; flex-direction: column;">

    <select v-model="selectedClasse" @change="handleClasseChange($event.target.value)" class="form-control"
                id="fil" required style="color: black !important;">
                <option  value="Tous">Tous</option>
                <option v-for="classe in Classes" :key="classe.id" :value="classe.Classe" style="color: black !important;">{{
                  classe.Classe }}</option>
              </select>

    <Field  name="MoisPorAfficher" type="date" class="form-control"
    id="selectedMonth2" placeholder="Entrer la date de début" required :value="getdate()"
    v-model="selectedMonth2" @change="handleDateChange" />

</div>


    <!-- <Field name="name" as="select" class="form-control"  id="EnseignementsProf">
    <option v-for="prof in enseignements" :key="prof.id"
        :value="JSON.stringify({ IdNiv: prof.IdProf, IdFil: prof.IdFil, IdMat: prof.IdMat })"
        style="color: black !important;">
        {{ prof.Prenom }} {{ prof.Nom }}
    </option>
</Field> -->


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
        <div style="display:flex;">
          <!-- <h3 style="color:#007bff; font-weight: bold;">Salaire attendu : {{ Salaire }} dh </h3> -->
          <!-- <h3 style="color:green; font-weight: 700px;">Salaire actuel : {{ users.length > 0 ?
            users[users.length - 1].SalaireActuelle + ' dh' : 'N/A' }}</h3> -->
        </div>
        <br>

        <div class="container"
          style="overflow : auto !important; height:  calc(100vh - 176px) !important; max-width: 2040px !important;">

          <table id="myTable" class="table table-striped table-bordered">
            <thead>
              <tr>
                <!-- <th>#</th> -->
                <th>Etudiant</th>
                <!-- <th>Classe</th> -->
                <th>État</th>
                <!-- <th>Niveau</th>
                <th>Filière</th>
                <th>Matière</th> -->
                <!-- <th>Date</th> -->
                <!-- <th>Date de paiment</th> -->



              </tr>
            </thead>
            <tbody>
              <tr v-for="(user, index) in users" :key="user.id">
                <!-- <td>{{ index + 1 }}</td> -->
                <td>{{ user.Nom }}</td>
                <!-- <td>{{ user.Filiere}}</td> -->
                <td v-if="user.Etat === 'Absent'" style="color: red; font-weight: bold;">{{ user.Etat }}</td>
                <td v-else style="color: green; font-weight: bold;">{{ user.Etat }}</td>
                <!-- <td>{{ user.Montant }}</td>
                <td>{{ user.Reste }}</td>
                <td>{{ user.Matiere }}</td> -->
                <!-- <td>{{ user.DatePaiment }} </td> -->


                <!-- <td>{{ user.DatePaiment }}</td> -->
                <!-- <td v-if="IsPresent"> -->
                    <td v-if="user.Etat === 'Présent'">
                  <a href="#" @click.prevent="MarquerAbsent(user)" class="btn btn-danger btn-sm">
    <i class="fa fa-bell" ></i>
  </a>
</td>
<td v-else>
  <a href="#" @click.prevent="MarquerPresent(user)" class="btn btn-success btn-sm" >
    <i class="fa fa-bell"></i>
  </a></td>





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

              <div class="form-group">
                <label for="SommeApaye">Somme a payé</label>
                <Field name="SommeApaye" type="number" class="form-control" :class="{ 'is-invalid': errors.SommeApaye }"
                  id="SommeApaye" placeholder="Entrer somme a payé par mois" required />
                <span class="invalid-feedback">{{ errors.SommeApaye }}</span>
              </div>
              <div class="form-group">
                <label for="Montant">Somme payé</label>
                <Field name="Montant" type="number" class="form-control" :class="{ 'is-invalid': errors.Montant }"
                  id="Montant" placeholder="Entrer la somme payé" required />
                <span class="invalid-feedback">{{ errors.Montant }}</span>
              </div>




              <div class="form-group">
                <label for="DatePaiment">Date de paiement (mm/jj/aaaa)</label>
                <Field name="DatePaiment" type="date" class="form-control" :class="{ 'is-invalid': errors.DatePaiment }"
                  id="DatePaiment" placeholder="Entrer la date de paiment" required v-bind:value="getDefaultDate()" />
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

  const toastr = useToastr();
  const users = ref([]);
  const formValues = ref();
  const form = ref(null);


  const selectedFiliere = ref('');
  const filieres = ref([]);

  const selectedMatieres = ref([]); // Utilisation d'un tableau pour stocker les matières sélectionnées
  const matieres = ref([]);

  let showFiliere = false;
  const selectedMonth2 = ref(''); // Initialisez selectedMonth2 comme une référence avec une valeur initiale vide

  const EnseignementsProf = ref(''); // Initialisez selectedMonth comme une référence avec une valeur initiale vide



  const cancelEdit = () => {
    resetFormValues(); // Réinitialiser le formulaire
    // Autres actions si nécessaire lors de l'annulation de la modification
  };

  const resetFormValues = () => {
    form.value.resetForm(); // Utilisez la méthode resetForm() fournie par VeeValidate pour réinitialiser le formulaire
    // Remettre à zéro les valeurs sélectionnées et autres états si nécessaire

    // Autres remises à zéro si nécessaire
  };

  const selectedClasse = ref(localStorage.getItem('selectedClasse') || 'Tous'); // Utilisez la valeur sauvegardée dans localStorage ou 'Tous' par défaut


  const handleClasseChange = (newVal) => {
    selectedClasse.value = newVal;
    localStorage.setItem('selectedClasse', selectedClasse.value);
    // Mettre à jour les données en fonction du nouveau mois sélectionné
    window.location.reload();

    // getUsers(ProfId.value,selectedClasse);


  // showMat = true;

};

const handleDateChange = () => {
    localStorage.setItem('selectedMonth2', selectedMonth2.value);
    // Mettre à jour les données en fonction du nouveau mois sélectionné
    window.location.reload();

    // getUsers(ProfId.value,selectedClasse);


  // showMat = true;

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


  const getdate = () => {
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
    lengthChange: true,
    columns: [
      { data: 'Nom' },
    //   { data: 'Filiere' },
      { data: 'Etat' },
      {
        data: null,
        render: function (data, type, row) {
          const editBtn = document.createElement('button');
          editBtn.classList.add('btn', 'btn-sm', 'edit-btn');
          editBtn.innerHTML = '<i class="fa fa-bell"></i>';
          editBtn.addEventListener('click', function () {
            if (row.Etat === 'Absent') {
              MarquerPresent(row);
            } else {
              MarquerAbsent(row);
            }
          });

          if (row.Etat === 'Absent') {
            editBtn.classList.add('btn-success');
          } else {
            editBtn.classList.add('btn-danger');
          }

          return editBtn.outerHTML;
        }
      }
    ],
    columnDefs: [
      {
        targets: 1,
        render: function (data, type, row) {
          if (data === 'Absent') {
            return '<span style="color: red; font-weight: bold;">' + data + '</span>';
          } else {
            return '<span style="color: green; font-weight: bold;">' + data + '</span>';
          }
        },
      },
    ],
    createdRow: function (row, data, dataIndex) {
      const editBtn = $(row).find('.edit-btn');
      editBtn.on('click', function () {
        if (data.Etat === 'Absent') {
          MarquerPresent(data);
        } else {
          MarquerAbsent(data);
        }
        location.reload();
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
  const ProfId = ref('');


  const getRole = () => {
    axios.get('/api/getIdProf')
      .then((response) => {
        getProfClasses(response.data);
        getUsers(response.data,selectedClasse);
        getEnseignementsParProf(response.data);
        //console.log(response.data);
        ProfId.value = response.data;
        // getSalaire(response.data);
      }
      )
      .catch((error) => {
        console.error('Erreur lors de la récupération de role:', error);
      });
  };

  const Salaire = ref('');


  const getSalaire = (id) => {

    axios.get('/api/salaireProf', { params: { id: id } })
      .then((response) => {
        Salaire.value = response.data;

      })
      .catch((error) => {
        console.error('Erreur lors de la récupération de salaire :', error);
      });
  };
  const TotalEtu = ref('');

  const getUsers = (id,selectedClasse) => {
    axios.get('/api/etudiantsForProfForAbsence', { params: { date: selectedMonth2.value, id: id,selectedClasse: selectedClasse.value } })
      .then((response) => {
        users.value = response.data;
        // localStorage.setItem('selectedMonth2', selectedMonth2.value);
        // console.log(users.value[0]['TotalEtudiants'])
        TotalEtu.value = users.value[0]['TotalEtudiants'];


        if ($.fn.DataTable.isDataTable('#myTable')) {
          $('#myTable').DataTable().destroy();
        }

        initDataTable(); // Réinitialiser la table avec les nouvelles données
      })
      .catch((error) => {
        console.error('Erreur lors de la récupération des étudiants :', error);
      });
  };

  const Classes = ref([]);


  const getProfClasses = (id) => {

axios.get('/api/getProfClasses', { params: { id: id } })
  .then((response) => {
    // console.log(response.data);
    Classes.value = response.data;


  })
  .catch((error) => {
    console.error('Erreur lors de la récupération des classes de prof :', error);
  });
};


  const enseignements = ref([]);

  const getEnseignementsParProf = (id) => {

axios.get('/api/EnseignementsParProf', { params: { id: id } })
  .then((response) => {
    enseignements.value = response.data;



  })
  .catch((error) => {
    console.error('Erreur lors de la récupération des enseignements :', error);
  });
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
    axios.put('/api/paiements/' + formValues.value.id, values)
      .then((response) => {
        const index = users.value.findIndex(user => user.id === response.data.id);
        users.value[index] = response.data;
        setTimeout(() => {
          $('#userFormModal').modal('hide');
        }, 10);
        toastr.success('Paiement mis à jour avec succès !');
        getRole();
        //   location.reload(); // Rechargement de la page après la suppression
      }).catch((error) => {
        setErrors(error.response.data.errors);
        console.log(error);
      })
  };
  const IsPresent = ref('true');



  const MarquerAbsent = (user) => {
// console.log(user);
IsPresent.value = false;

const dataToUpdate = {
    data: user,
    ProfId: ProfId.value,
    selectedMonth2: selectedMonth2.value,


  };

    axios.put('/api/absence' , dataToUpdate)
      .then((response) => {
        const index = users.value.findIndex(user => user.id === response.data.id);
        users.value[index] = response.data;
        setTimeout(() => {
          $('#userFormModal').modal('hide');
        }, 10);
        toastr.success('L\'bsence est marqué avec succès !');
        getRole(); // Mettre à jour la DataTable après la mise à jour
        //   location.reload(); // Rechargement de la page après la suppression
      }).catch((error) => {
        setErrors(error.response.data.errors);
        console.log(error);
      })
  };


  const MarquerPresent = (user) => {
// console.log(user);
IsPresent.value = true;

const dataToUpdate = {
    data: user,
    ProfId: ProfId.value,
    selectedMonth2: selectedMonth2.value,


  };

    axios.put('/api/presence' , dataToUpdate)
      .then((response) => {
        const index = users.value.findIndex(user => user.id === response.data.id);
        users.value[index] = response.data;
        setTimeout(() => {
          $('#userFormModal').modal('hide');
        }, 10);
        toastr.success('La présence est marqué avec succès !');
        getRole(); // Mettre à jour la DataTable après la mise à jour
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
      selectedMonth2.value = getDefaultDate();
      getRole();

      // getUsers(id); // Utilisez l'ID retourné pour obtenir les utilisateurs



    };

    document.head.appendChild(jQueryScript);

  });
  </script>


  <style>
  /* Ajoutez ce style dans votre composant Vue */

  .hide-btn {
    display: none;
    /* Masque les boutons ayant cette classe */
  }
  </style>

