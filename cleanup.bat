@echo off
echo Nettoyage du cache de l'application...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

echo Regénération des clés d'application...
php artisan key:generate

echo Nettoyage des sessions...
del /f /q storage\framework\sessions\*

echo Nettoyage des vues compilées...
del /f /q storage\framework\views\*

echo Nettoyage terminé !
pause
