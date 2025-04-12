<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6" style="display: flex;
    justify-content: space-between;
    flex-direction: row-reverse; ">
                    <h1 class="m-0" style="font-weight: 500 !important; ">Les étudiants</h1>
                    <button class="mb-2 btn btn-primary" style="font-weight: bold;" type="button" @click="addUser">
                        Ajouter nouveau Etudiant
                    </button>
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
                        <th>Téléphone du père</th>
                        <th>Téléphone de la mère</th>
                        <th>Adresse</th>
                        <th>Niveau</th>
                        <th>Filière</th>
                        <th>Matières</th>
                        <th>Date d'inscription</th>
                        <th>Date de début</th>

                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(user, index) in users" :key="user.id">
                        <!-- <td>{{ index + 1 }}</td> -->
                        <td>{{ user.Nom }}</td>
                        <td>{{ user.Prenom }}</td>
                        <td>{{ user.Tele }}</td>
                        <td>{{ user.Tele2 }}</td>
                        <td>{{ user.Adresse }}</td>
                        <td>{{ user.IdNiv }}</td>
                        <td>{{ user.IdFil }}</td>

                        <td>
                            <ul>
                                <li v-for="matiere in user.Matieres" :key="matiere">
                                    {{ matiere }}
                                </li>
                            </ul>
                        </td>

                        <td>{{ user.created_at }}</td>
                        <td v-if="user.Date_debut == ''"></td>
                        <td v-else>{{ user.Date_debut }}</td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="#" @click.prevent="editUser(user)">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a class="btn btn-success btn-sm ml-3" href="#" @click.prevent="affectationMatieres(user)">
                                <i class="fa fa-tasks"></i>
                            </a>
                            <a class="btn btn-danger btn-sm ml-4" href="#" @click.prevent="confirmUserDeletion(user)">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal pour ajouter un nouvel Etudiant -->
    <div id="userFormModal" aria-hidden="true" aria-labelledby="userFormModalLabel" class="modal fade" role="dialog"
         tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="margin-top: -28px !important;">
                <div class="modal-header">
                    <h5 id="userFormModalLabel" class="modal-title">
                        <span v-if="editing">Modifier l'étudiant</span>
                        <span v-else>Ajouter nouveau étudiant</span>
                    </h5>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <!--The form is here => -->
                <Form ref="form" v-slot="{ errors }" :initial-values="formValues"
                      :validation-schema="editing ? editUserSchema : createUserSchema" @submit="handleSubmit">
                    <div class="modal-body">
                        <!-- Formulaire pour ajouter un nouvel Etudiant -->

                        <div class="form-group">
                            <label for="name">Nom</label>
                            <Field id="nom" v-model="formValues.nom" :class="{ 'is-invalid': errors.nom }" class="form-control"
                                   name="nom"
                                   placeholder="Entrer nom" required type="text"/>
                            <span class="invalid-feedback">{{ errors.nom }}</span>
                        </div>
                        <div class="form-group">
                            <label for="prenom">Prenom</label>
                            <Field id="prenom" v-model="formValues.prenom" :class="{ 'is-invalid': errors.prenom }"
                                   class="form-control" name="prenom"
                                   placeholder="Entrer prenom" required type="text"/>
                            <span class="invalid-feedback">{{ errors.prenom }}</span>
                        </div>
                        <div class="form-group">
                            <label for="tele">Téléphone du père</label>
                            <Field id="tele" v-model="formValues.tele" :class="{ 'is-invalid': errors.tele }" class="form-control"
                                   name="tele"
                                   placeholder="Entrer telephone" type="text"/>
                            <span class="invalid-feedback">{{ errors.tele }}</span>
                        </div>
                        <div class="form-group">
                            <label for="tele2">Téléphone de la mère</label>
                            <Field id="tele2" v-model="formValues.tele2" :class="{ 'is-invalid': errors.tele }" class="form-control"
                                   name="tele2"
                                   placeholder="Entrer telephone" type="text"/>
                            <span class="invalid-feedback">{{ errors.tele }}</span>
                        </div>
                        <div class="form-group">
                            <label for="adresse">Adresse</label>
                            <Field id="adresse" v-model="formValues.adresse" :class="{ 'is-invalid': errors.adresse }"
                                   class="form-control" name="adresse"
                                   placeholder="Entrer adresse" type="text"/>
                            <span class="invalid-feedback">{{ errors.adresse }}</span>
                        </div>


                        <div class="form-group">
                            <label for="niv">Niveau</label>
                            <select id="niv" v-model="selectedNiveau"
                                    class="form-control"
                                    required style="color: black !important;" @change="handleNiveauChange($event.target.value)">
                                <option v-if="!editing" value="">Sélectionner un niveau</option>
                                <option v-for="niveau in niveaux" :key="niveau.id" :value="niveau"
                                        style="color: black !important;">{{
                                        niveau
                                    }}
                                </option>
                            </select>
                            <!-- <span class="invalid-feedback">{{ errors.niv }}</span> -->
                            <span v-if="selectedNiveau === ''" style="font-size: 80%; color: #dc3545;">Veuillez sélectionner un niveau
                !!</span>
                        </div>


                        <div class="form-group">
                            <label for="fil">Filière</label>
                            <select id="fil" v-model="selectedFiliere"
                                    class="form-control"
                                    required style="color: black !important;" @change="handleFiliereChange($event.target.value)">
                                <option v-if="!editing" value="">Sélectionner une filière</option>
                                <option v-for="filiere in filieres" :key="filiere.id" :value="filiere"
                                        style="color: black !important;">{{
                                        filiere
                                    }}
                                </option>
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

                        <div class="form-group">
                            <label for="Date_Debut">Date de début(mm/jj/aaaa)</label>
                            <Field id="Date_Debut" v-model="formValues.Date_debut" :class="{ 'is-invalid': errors.Date_debut }"
                                   class="form-control"
                                   name="Date_debut" placeholder="Entrer la date de début" required
                                   type="date"
                                   v-bind:value="formValues && formValues.Date_debut ? formValues.Date_debut : getDefaultDate()"/>
                            <span class="invalid-feedback">{{ errors.Date_debut }}</span>

                        </div>


                    </div>
                    <div class="modal-footer">
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-dismiss="modal" type="button" @click="cancelEdit">
                                Annuler
                            </button>
                            <button class="btn btn-primary" type="submit">Enregistrer</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal pour ajouter un nouvel Etudiant -->
    <div id="deleteUserModal" aria-hidden="true" aria-labelledby="userFormModalLabel" class="modal fade" role="dialog"
         tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="userFormModalLabel" class="modal-title">
                        <span>Supprimer étudiant</span>
                    </h5>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <h5>Êtes-vous sûr de vouloir supprimer cet étudiant</h5>
                </div>

                <div class="modal-footer">

                    <button class="btn btn-secondary" data-dismiss="modal" type="button">Annuler</button>
                    <button class="btn btn-primary" type="button" @click.prevent="deleteUser">Supprimer</button>

                </div>

            </div>
        </div>
    </div>
</template>


<script charset="utf-8" setup>

import axios from 'axios';
import {onMounted, ref} from 'vue';
import {Field, Form} from 'vee-validate';
import * as yup from 'yup';
import {useToastr} from '../../toastr.js';
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
    tele2: '',
    adresse: '',
    Date_debut: '',

});
const form = ref(null);
const userIdBeingDeleted = ref(null);

const selectedNiveau = ref('')
const niveaux = ref([]);

const selectedFiliere = ref('');
const filieres = ref([]);

let showFiliere = false;

let showMat = false;

const cancelEdit = () => {
    editing.value = false;
    form.value.resetForm();
    resetForm();
    resetFormValues(); // Réinitialiser le formulaire
    // Autres actions si nécessaire lors de l'annulation de la modification
};

const resetFormValues = () => {
    form.value.resetForm(); // Utilisez la méthode resetForm() fournie par VeeValidate pour réinitialiser le formulaire
    // Remettre à zéro les valeurs sélectionnées et autres états si nécessaire
    selectedNiveau.value = '';
    selectedFiliere.value = '';

    // Autres remises à zéro si nécessaire
};


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
    const options = {month: 'long', year: 'numeric'};
    return new Date(date).toLocaleDateString('fr-FR', options);
};


const IsBigtable = ref(false);


const initDataTable = () => {
    $('#myTable').DataTable({
        ddom: 'Bfrtip',
        sSwfPath: "http://datatables.net/release-datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf",
        buttons: ['excel', 'pdf',],
        paging: true,
        lengthChange: true, // Force l'affichage des options de changement de longueur
        columns: [
            {data: 'Nom'},
            {data: 'Prenom'},
            {data: 'Tele'},
            {data: 'Tele2'},
            {data: 'Adresse'},
            {data: 'IdNiv'},
            {data: 'IdFil'},
            {data: 'Matieres'},
            {data: 'created_at'},
            {data: 'Date_debut'},

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
            }


        ],
        data: users.value
    });
};


const getUsers = () => {
    axios.get('/api/etudiants')
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

const createUser = (values, {resetForm, setErrors}) => {
    axios.post('/api/etudiants', {
        ...values,
        niv: selectedNiveau.value,
        fil: selectedFiliere.value,

    })

        .then((response) => {
            if (response.data) {
                users.value.unshift(response.data);
                setTimeout(() => {
                    $('#userFormModal').modal('hide');
                }, 10);
                resetForm();
                toastr.success('Étudiant créé avec succès !');
                getUsers(); // Mettre à jour la DataTable après la création
                //   location.reload(); // Rechargement de la page après la suppression
            } else {
                setTimeout(() => {
                    $('#userFormModal').modal('hide');
                }, 10);
                resetForm();
                toastr.error('Étudiant déja exist !');

            }
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

    $('#userFormModal').modal('show');

    // Initialiser les valeurs pour Nom, Prenom, Telephone, Adresse
    formValues.value = {
        id: user.id,
        nom: user.Nom,
        prenom: user.Prenom,
        tele: user.Tele,
        tele2: user.Tele2,
        adresse: user.Adresse,
        niv: user.IdNiv,
        fil: user.IdFil,

        Date_debut: user.Date_debut
    };

    // Initialiser les valeurs sélectionnées pour Niveau et Filière
    selectedNiveau.value = user.IdNiv; // Sélectionner l'ancienne valeur pour le niveau
    selectedFiliere.value = user.IdFil; // Sélectionner l'ancienne valeur pour la filière

};


const updateUser = (values, {setErrors}) => {
    for (let i = 0; i < 2; i++) {
        axios.put('/api/etudiants/' + formValues.value.id, {
            ...values,
            niv: selectedNiveau.value,
            fil: selectedFiliere.value
        })
            .then((response) => {
                const index = users.value.findIndex(user => user.id === response.data.id);
                users.value[index] = response.data;
                setTimeout(() => {
                    $('#userFormModal').modal('hide');
                }, 10);
                resetFormValues();
                if (i == 1) toastr.success('Étudiant mis à jour avec succès !');
                getUsers(); // Mettre à jour la DataTable après la mise à jour
                if (IsBigtable.value) { // Utiliser directement IsBigtable.value pour vérifier si la mise à jour provient de la DataTable
                    window.location.reload();
                }
                //   location.reload(); // Rechargement de la page après la suppression
            }).catch((error) => {
            setErrors(error.response.data.errors);
            console.log(error);
        });
    }
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

const affectationMatieres = (user) => {
    window.location.href = `/students/inscriptions/${user.id}`;
    //router.push(`/students/inscriptions/${user.id}`);
};

const deleteUser = () => {
    axios.delete(`/api/etudiants/${userIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteUserModal').modal('hide');
            toastr.success('Étudiant supprimé avec succès !');
            users.value = users.value.filter(user => user.id !== userIdBeingDeleted.value);
            userIdBeingDeleted.value = null;
            getUsers(); // Mettre à jour la DataTable après la suppression

            if (IsBigtable.value) { // Utiliser directement IsBigtable.value pour vérifier si la mise à jour provient de la DataTable
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


    };

    document.head.appendChild(jQueryScript);

});
</script>

