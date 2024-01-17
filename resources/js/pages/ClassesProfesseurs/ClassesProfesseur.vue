
<template>

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
      lengthChange: true, // Force l'affichage des options de changement de longueur
      columns: [
        { data: 'Nom' },
        //   { data: 'Prenom' },
        { data: 'Etat' },
        //   { data: 'Montant' },
        //   { data: 'Reste' },
        //   { data: 'Matiere' },
        { data: 'SommeApaye' },
        //   { data: 'DatePaiment' },
        {
          data: null,
          render: function (data, type, row) {
            const editBtn = document.createElement('button');
            editBtn.classList.add('btn', 'btn-primary', 'btn-sm', 'edit-btn', 'hide-btn'); // Ajoutez la classe 'hide-btn'
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
          targets: 1, // Indice de la colonne 'État de paiement'
          render: function (data, type, row) {
            if (data === 'Absent') {
              return '<span style="color: red; font-weight: bold;">' + data + '</span>';
            }  else {
              return '<span style="color: green; font-weight: bold;">' + data + '</span>';
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

  const getRole = () => {
    axios.get('/api/getIdProf')
      .then((response) => {
        getUsers(response.data);
        getEnseignementsParProf(response.data);
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

  const getUsers = (id) => {

    axios.get('/api/etudiantsForProfForAbsence', { params: { date: selectedMonth.value, id: id } })
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
      selectedMonth.value = getDefaultDate();
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

