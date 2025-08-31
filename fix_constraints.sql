-- Désactiver temporairement les contraintes de clé étrangère
SET FOREIGN_KEY_CHECKS = 0;

-- Supprimer la contrainte problématique si elle existe
ALTER TABLE absences DROP FOREIGN KEY IF EXISTS `absences_professeur_id_foreign`;
ALTER TABLE absences DROP INDEX IF EXISTS `IDX_F9C0EFFFBAB22EE9`;

-- Recréer la contrainte correctement
ALTER TABLE absences 
ADD CONSTRAINT `absences_professeur_id_foreign` 
FOREIGN KEY (`professeur_id`) 
REFERENCES `users` (`id`) 
ON DELETE SET NULL;

-- Réactiver les contraintes de clé étrangère
SET FOREIGN_KEY_CHECKS = 1;
