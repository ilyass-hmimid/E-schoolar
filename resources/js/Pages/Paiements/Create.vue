<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Nouveau Paiement
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="submitForm" class="space-y-6">
              <!-- Informations de l'étudiant -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Étudiant <span class="text-red-500">*</span>
                  </label>
                  <select
                    v-model="form.etudiant_id"
                    @change="onEtudiantChange"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Sélectionner un étudiant</option>
                    <option v-for="eleve in eleves" :key="eleve.id" :value="eleve.id">
                      {{ eleve.name }} - {{ eleve.email }} (Solde: {{ formatCurrency(eleve.somme_a_payer) }})
                    </option>
                  </select>
                  <p v-if="selectedEtudiant" class="mt-2 text-sm text-gray-600">
                    Solde actuel: <span class="font-semibold">{{ formatCurrency(selectedEtudiant.somme_a_payer) }}</span>
                  </p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Type de paiement
                  </label>
                  <div class="space-y-2">
                    <label class="flex items-center">
                      <input
                        v-model="form.type_paiement"
                        type="radio"
                        value="matiere"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                      />
                      <span class="ml-2 text-sm text-gray-700">Paiement par matière</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        v-model="form.type_paiement"
                        type="radio"
                        value="pack"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                      />
                      <span class="ml-2 text-sm text-gray-700">Paiement par pack</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Sélection matière ou pack -->
              <div v-if="form.type_paiement === 'matiere'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Matière
                  </label>
                  <select
                    v-model="form.matiere_id"
                    @change="onMatiereChange"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Sélectionner une matière</option>
                    <option v-for="matiere in matieres" :key="matiere.id" :value="matiere.id">
                      {{ matiere.nom }} - {{ formatCurrency(matiere.prix_mensuel) }}/mois
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Mois/Période
                  </label>
                  <input
                    v-model="form.mois_periode"
                    type="month"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
              </div>

              <div v-if="form.type_paiement === 'pack'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pack
                  </label>
                  <select
                    v-model="form.pack_id"
                    @change="onPackChange"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Sélectionner un pack</option>
                    <option v-for="pack in packs" :key="pack.id" :value="pack.id">
                      {{ pack.nom }} - {{ formatCurrency(pack.prix) }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Informations de paiement -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Montant <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input
                      v-model="form.montant"
                      type="number"
                      step="0.01"
                      min="0"
                      required
                      class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                      placeholder="0.00"
                    />
                    <span class="absolute left-3 top-2 text-gray-500">DH</span>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Mode de paiement <span class="text-red-500">*</span>
                  </label>
                  <select
                    v-model="form.mode_paiement"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Sélectionner un mode</option>
                    <option v-for="(label, value) in modes_paiement" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Référence de paiement
                  </label>
                  <input
                    v-model="form.reference_paiement"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Référence optionnelle"
                  />
                </div>
              </div>

              <!-- Commentaires -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Commentaires
                </label>
                <textarea
                  v-model="form.commentaires"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Commentaires optionnels..."
                ></textarea>
              </div>

              <!-- Résumé du paiement -->
              <div v-if="form.etudiant_id && form.montant" class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Résumé du paiement</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                  <div>
                    <span class="text-gray-600">Étudiant:</span>
                    <span class="ml-2 font-medium">{{ selectedEtudiant?.name }}</span>
                  </div>
                  <div>
                    <span class="text-gray-600">Montant:</span>
                    <span class="ml-2 font-medium text-green-600">{{ formatCurrency(form.montant) }}</span>
                  </div>
                  <div>
                    <span class="text-gray-600">Mode de paiement:</span>
                    <span class="ml-2 font-medium">{{ modes_paiement[form.mode_paiement] || 'Non spécifié' }}</span>
                  </div>
                  <div>
                    <span class="text-gray-600">Statut prévu:</span>
                    <span class="ml-2 font-medium" :class="getStatusClass(getPrevisionStatus())">
                      {{ getStatusLabel(getPrevisionStatus()) }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Boutons d'action -->
              <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <Link
                  :href="route('paiements.index')"
                  class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                >
                  Annuler
                </Link>
                <button
                  type="submit"
                  :disabled="isSubmitting"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span v-if="isSubmitting">Enregistrement...</span>
                  <span v-else>Enregistrer le paiement</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  eleves: Array,
  matieres: Array,
  packs: Array,
  modes_paiement: Object,
})

const isSubmitting = ref(false)

const form = ref({
  etudiant_id: '',
  matiere_id: '',
  pack_id: '',
  montant: '',
  mode_paiement: '',
  reference_paiement: '',
  commentaires: '',
  mois_periode: new Date().toISOString().slice(0, 7),
  type_paiement: 'matiere',
})

const selectedEtudiant = computed(() => {
  return props.eleves.find(eleve => eleve.id == form.value.etudiant_id)
})

const selectedMatiere = computed(() => {
  return props.matieres.find(matiere => matiere.id == form.value.matiere_id)
})

const selectedPack = computed(() => {
  return props.packs.find(pack => pack.id == form.value.pack_id)
})

const onEtudiantChange = () => {
  // Réinitialiser les montants quand l'étudiant change
  form.value.montant = ''
}

const onMatiereChange = () => {
  if (selectedMatiere.value) {
    form.value.montant = selectedMatiere.value.prix_mensuel
  }
}

const onPackChange = () => {
  if (selectedPack.value) {
    form.value.montant = selectedPack.value.prix
  }
}

const getPrevisionStatus = () => {
  if (['especes', 'carte'].includes(form.value.mode_paiement)) {
    return 'valide'
  }
  return 'en_attente'
}

const getStatusLabel = (statut) => {
  const labels = {
    'valide': 'Validé',
    'en_attente': 'En attente',
    'annule': 'Annulé'
  }
  return labels[statut] || statut
}

const getStatusClass = (statut) => {
  const classes = {
    'valide': 'text-green-600',
    'en_attente': 'text-yellow-600',
    'annule': 'text-red-600'
  }
  return classes[statut] || 'text-gray-600'
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-MA', {
    style: 'currency',
    currency: 'MAD'
  }).format(amount || 0)
}

const submitForm = () => {
  isSubmitting.value = true

  // Nettoyer les données avant envoi
  const data = {
    etudiant_id: form.value.etudiant_id,
    montant: form.value.montant,
    mode_paiement: form.value.mode_paiement,
    reference_paiement: form.value.reference_paiement,
    commentaires: form.value.commentaires,
    mois_periode: form.value.mois_periode,
  }

  // Ajouter matiere_id ou pack_id selon le type
  if (form.value.type_paiement === 'matiere') {
    data.matiere_id = form.value.matiere_id
  } else if (form.value.type_paiement === 'pack') {
    data.pack_id = form.value.pack_id
  }

  router.post(route('paiements.store'), data, {
    onSuccess: () => {
      if (window.$notify) {
        window.$notify.success('Succès', 'Paiement enregistré avec succès')
      }
    },
    onError: (errors) => {
      if (window.$notify) {
        window.$notify.error('Erreur', 'Veuillez corriger les erreurs dans le formulaire')
      }
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}
</script>
