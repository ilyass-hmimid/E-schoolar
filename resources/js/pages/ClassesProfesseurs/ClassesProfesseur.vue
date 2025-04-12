<template>

  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-12" style="display: flex; flex-direction: column; align-items: center;">
          <h1 class="m-2" style="font-weight: 500 !important; ">Liste des étudiants</h1> <br>

          <select v-model="selectedClasse" @change="handleClasseChange($event.target.value)" class="form-control mb-2"
            id="fil" required style="color: black !important;">
            <option v-for="classe in Classes" :key="classe.id" :value="classe.Classe" style="color: black !important;">
              {{ classe.Classe }}</option>
          </select>

          <Field name="MoisPorAfficher" type="date" class="form-control" id="selectedMonth2"
            placeholder="Entrer la date de début" required :value="getdate()" v-model="selectedMonth2"
            @change="handleDateChange" />
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
              <th>Etudiant</th>
              <th>État</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id">
              <td>{{ user.Nom }}</td>
              <td>
                <span v-if="user.Etat === 'Absent'" style="color: red; font-weight: bold;">{{ user.Etat }}</span>
                <span v-else style="color: green; font-weight: bold;">{{ user.Etat }}</span>
              </td>
              <td>
                <span v-if="user.Etat === 'Présent'">
                  <a href="#" @click.prevent="MarquerAbsent(user)" class="btn btn-danger btn-sm">
                    <i class="fa fa-user-times"></i>
                  </a>
                </span>
                <span v-else>
                  <a href="#" @click.prevent="MarquerPresent(user)" class="btn btn-success btn-sm">
                    <i class="fa fa-user-check"></i>
                  </a>
                </span>
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
import { Field } from 'vee-validate';
import { useToastr } from '../../toastr.js';
import $ from 'jquery';
import 'datatables.net';

const toastr = useToastr();
const users = ref([]);

const selectedMonth2 = ref(''); // Initialisez selectedMonth2 comme une référence avec une valeur initiale vide

const selectedClasse = ref(localStorage.getItem('selectedClasse') || 'Tous'); // Utilisez la valeur sauvegardée dans localStorage ou 'Tous' par défaut


const handleClasseChange = (newVal) => {
  selectedClasse.value = newVal;
  localStorage.setItem('selectedClasse', selectedClasse.value);
  // Mettre à jour les données en fonction du nouveau mois sélectionné
  window.location.reload();
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


const getdate = () => {
  const now = new Date();
  const year = now.getFullYear();
  const month = (now.getMonth() + 1).toString().padStart(2, '0'); // +1 car les mois vont de 0 à 11
  const day = now.getDate().toString().padStart(2, '0');
  return `${year}-${month}-${day}`;
};

const ProfId = ref('');


const getRole = () => {
  axios.get('/api/getIdProf')
    .then((response) => {
      getProfClasses(response.data);
      getUsers(response.data, selectedClasse);
      //console.log(response.data);
      ProfId.value = response.data;
      // getSalaire(response.data);
    }
    )
    .catch((error) => {
      console.error('Erreur lors de la récupération de role:', error);
    });
};

const TotalEtu = ref('');

const getUsers = (id, selectedClasse) => {
  axios.get('/api/etudiantsForProfForAbsence', { params: { date: selectedMonth2.value, id: id, selectedClasse: selectedClasse.value } })
    .then((response) => {
      users.value = response.data;
      // localStorage.setItem('selectedMonth2', selectedMonth2.value);
      // console.log(users.value[0]['TotalEtudiants'])
      TotalEtu.value = users.value[0]['TotalEtudiants'];


      if ($.fn.DataTable.isDataTable('#myTable')) {
        $('#myTable').DataTable().destroy();
      }
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

const IsPresent = ref('true');



const MarquerAbsent = (user) => {
  // console.log(user);
  IsPresent.value = false;

  const dataToUpdate = {
    data: user,
    ProfId: ProfId.value,
    selectedMonth2: selectedMonth2.value,
  };

  axios.put('/api/absence', dataToUpdate)
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

  axios.put('/api/presence', dataToUpdate)
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
