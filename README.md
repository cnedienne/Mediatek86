# Mediatekformation
## Présentation
Ce site, développé avec Symfony 6.4, permet d'accéder aux vidéos d'auto-formation proposées par une chaîne de médiathèques et qui sont aussi accessibles sur YouTube.
La présentation de l'application d'origine se situe dans le readme du dépôt d'origine qui se trouve à l'adresse suivante : 
https://github.com/CNED-SLAM/mediatekformation
J'ai développée la partie back-office, elle contient les nouvelles fonctionnalités globales suivantes :
## Front-office

### L'accueil
Cette page présente le fonctionnement du site et les 2 dernières vidéos mises en ligne.<br>
La partie du haut contient une bannière (logo, nom et phrase présentant le but du site) et le menu permettant d'accéder aux 3 pages principales (Accueil, Formations, Playlists,Catégories).<br>
Le centre contient un texte de présentation avec, entre autres, les liens pour accéder aux 2 autres pages principales.<br>
La partie basse contient les 2 dernières formations mises en ligne. Cliquer sur une image permet d'accéder à la page de présentation de la formation.<br>
Le bas de page contient un lien pour accéder à la page des CGU : ce lien est présent en bas de chaque page excepté la page des CGU.<br>
![image](https://github.com/user-attachments/assets/bc3955da-4c3d-4593-b184-0c41d61a3b29)


### Les playlists
![image](https://github.com/user-attachments/assets/2aac0001-4a85-4959-8b04-25bd12ce314f)


![img5](https://github.com/user-attachments/assets/bbe8934f-8d4b-4da2-8216-60b96b726d8a)
### Détail d'une playlist
![image](https://github.com/user-attachments/assets/7cb8d027-d9ba-4644-9988-08f253d5d814)

![img6](https://github.com/user-attachments/assets/f216a9e7-084a-4683-9b4e-cada5574a0e2)
## Back office
### Login

![image](https://github.com/user-attachments/assets/b746b65c-fce0-40d3-8c84-ea28d73cd886)
### Les formations

![image](https://github.com/user-attachments/assets/e5db89a0-86ab-4d3f-a7e1-1e19a7777eac)


### Modifier une formation

![image](https://github.com/user-attachments/assets/50faff20-6c2e-4201-977e-a886299376f7)

### Playlist
![image](https://github.com/user-attachments/assets/08dd9970-636c-470f-999e-719793a261a9)
### Ajouter une playlist
![image](https://github.com/user-attachments/assets/1631e6fe-b396-4090-bf06-cabf75ddd8df)

### Catégories 
![image](https://github.com/user-attachments/assets/bc62e4b9-fe2a-4384-9508-d4e99ac4d8ab)


## Test de l'application en local
https://mediatek.alwaysdata.net/ 
- Vérifier que Composer, Git et Wamserver (ou équivalent) sont installés sur l'ordinateur.
- Télécharger le code et le dézipper dans www de Wampserver (ou dossier équivalent) puis renommer le dossier en "mediatekformation".<br>
- Ouvrir une fenêtre de commandes en mode admin, se positionner dans le dossier du projet et taper "composer install" pour reconstituer le dossier vendor.<br>
- Dans phpMyAdmin, se connecter à MySQL en root sans mot de passe et créer la BDD 'mediatekformation'.<br>
- Récupérer le fichier mediatekformation.sql en racine du projet et l'utiliser pour remplir la BDD (si vous voulez mettre un login/pwd d'accès, il faut créer un utilisateur, lui donner les droits sur la BDD et il faut le préciser dans le fichier ".env" en racine du projet).<br>
- De préférence, ouvrir l'application dans un IDE professionnel. L'adresse pour la lancer est : http://localhost/mediatekformation/public/index.php<br>
