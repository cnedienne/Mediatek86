#!/bin/bash

# Se déplacer dans le dossier du projet
cd ~/www/Mediatek86 || exit

# Enregistre l'heure du déploiement dans le fichier de log
echo "Déploiement commencé à $(date)" >> ~/www/Mediatek86/deploy.log

# Récupérer les derniers changements du dépôt Git
git pull origin main >> ~/www/Mediatek86/deploy.log 2>&1

# Installer les dépendances avec Composer
composer install --no-dev --optimize-autoloader >> ~/www/Mediatek86/deploy.log 2>&1

# Vérifier les permissions
chmod -R 775 ~/www/Mediatek86/var >> ~/www/Mediatek86/deploy.log 2>&1

# Vider le cache de Symfony
php bin/console cache:clear --env=prod --no-debug >> ~/www/Mediatek86/deploy.log 2>&1

# D'autres commandes que tu utilises pendant le déploiement

# Enregistre l'heure de fin du déploiement
echo "Déploiement terminé à $(date)" >> ~/www/Mediatek86/deploy.log
