Banque en Ligne - Gestion des Comptes et Transferts


Description :

Ce projet est une application de gestion de comptes bancaires en ligne permettant aux clients de :

- Créer et gérer leurs comptes bancaires (courant, épargne, entreprise).
- Effectuer des virements d'argent entre leurs comptes ou vers des bénéficiaires ajoutés.
- Ajouter et gérer une liste de bénéficiaires.
- Consulter l'historique des transferts envoyés et reçus.
- Sécuriser les transactions avec une protection contre les injections SQL et XSS.

L'application est construite avec PHP et utilise une base de données MySQL pour stocker les informations relatives aux clients, comptes bancaires, bénéficiaires, et transferts.

Fonctionnalités
1. Gestion des Comptes
- Inscription des clients avec des informations personnelles (nom, prénom, téléphone, email, mot de passe).
- Création et gestion des comptes bancaires (Courant, Épargne, Entreprise).
- Consultation du solde actuel et des informations de chaque compte.

2. Virements et Transferts
- Effectuer des virements entre les comptes d'un client ou vers les bénéficiaires ajoutés.
- Choix du compte émetteur et du bénéficiaire lors du virement.
- Gestion des montants et des motifs de transferts.

3. Gestion des Bénéficiaires
- Ajout de bénéficiaires en précisant leur IBAN.
- Consultation de la liste des bénéficiaires, modification et suppression des bénéficiaires.
- Limitation : Les comptes ajoutés en tant que bénéficiaires doivent être valides dans la base de données.

4. Historique des Transferts
- Consultation de l'historique des transferts effectués.
- Consultation de l'historique des transferts reçus.

5. Sécurité
- Validation des formulaires avec JavaScript (numéro de compte, montants, types de comptes).
- Protection contre les injections SQL grâce à l'utilisation de requêtes préparées.
- Protection contre les failles XSS via la validation et l'échappement des entrées utilisateur.

Technologies Utilisées
Frontend :
- HTML/CSS
- Bootstrap pour la mise en page réactive
- JavaScript pour la validation des formulaires
Backend :
- PHP
- MySQL pour la base de données



*Il ne faut pas oublier d'installer le dotenv, et de renseigner les informations de connexion à la base de données dans le fichier .env.

Les dossiers:
    - NO_COMMENTED correspond à l'intégralité du code, mais non commentée.*
    - COMMENTED correspond à l'intégralité du code, mais commentée.*
    - IMAGES stocke le logo, les images.
    - BDD stocke un schéma relationnel, ainsi que 3 types de base de données (vierge, avec données, et en version texte).
    - Extras stocke les bonus qui ont été ajouté au projet.

*Pour que le contenu de ces dossiers marche, il faut copier, coller tout leur contenu à la racine du dossier "Projet".