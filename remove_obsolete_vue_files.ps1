# Script pour supprimer les fichiers Vue/Inertia obsolètes

# Liste des dossiers et fichiers à supprimer
$itemsToRemove = @(
    # Dossiers
    "resources/js/Pages/Admin/Users",
    "resources/js/Pages/Admin/Professeurs",
    "resources/js/Pages/Admin/Centres",
    "resources/js/Pages/Assistant",
    "resources/js/Pages/Eleve",
    "resources/js/Pages/Auth",
    "resources/js/Pages/Admin/Eleves",
    "resources/js/Pages/Admin/Filieres",
    "resources/js/Pages/Admin/Matieres",
    "resources/js/Pages/Admin/Niveaux",
    "resources/js/Pages/Admin/Packs",
    "resources/js/Pages/Admin/Paiements",
    "resources/js/Pages/Admin/Parametres",
    "resources/js/Pages/Admin/Salaires",
    "resources/js/Pages/Dashboard",
    "resources/js/Pages/Eleves",
    "resources/js/Pages/Enseignements",
    "resources/js/Pages/Filieres",
    "resources/js/Pages/Matieres",
    "resources/js/Pages/Niveaux",
    "resources/js/Pages/Notifications",
    "resources/js/Pages/Paiements",
    "resources/js/Pages/Professeur",
    "resources/js/Pages/Profile",
    "resources/js/Pages/Absences",
    "resources/js/Pages/Rapports",
    "resources/js/Pages/Salaires",
    "resources/js/Pages/etudiantToMatiere",
    "resources/js/Pages/users",
    # Fichiers individuels
    "resources/js/Pages/Admin/Dashboard.vue",
    "resources/js/Pages/Admin/NewDashboard.vue",
    "resources/js/Pages/Welcome.vue"
)

# Vérifier et supprimer chaque élément
foreach ($item in $itemsToRemove) {
    $fullPath = Join-Path -Path $PSScriptRoot -ChildPath $item
    if (Test-Path $fullPath) {
        $isDirectory = (Get-Item $fullPath) -is [System.IO.DirectoryInfo]
        $type = if ($isDirectory) { "dossier" } else { "fichier" }
        
        Write-Host "Suppression du $type $item..."
        try {
            if ($isDirectory) {
                Remove-Item -Path $fullPath -Recurse -Force -ErrorAction Stop
            } else {
                Remove-Item -Path $fullPath -Force -ErrorAction Stop
            }
            Write-Host "$item a été supprimé avec succès." -ForegroundColor Green
        } catch {
            Write-Host "Erreur lors de la suppression de $item : $_" -ForegroundColor Red
        }
    } else {
        Write-Host "$item n'existe pas, passage au suivant..." -ForegroundColor Yellow
    }
}

Write-Host "Nettoyage terminé !" -ForegroundColor Green
