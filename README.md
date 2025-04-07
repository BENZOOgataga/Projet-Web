# Projet Web - ReverseH4ck (Moussa TAYAA, Steven MECHRAFI, Louis MORICE)

https://projet.benzoogataga.com
https://github.com/BENZOOgataga/Projet-Web

## Presentation

Diaporama

Voir presentation_projet dans le dossier ZIP

Contexte

L'entreprise ReverseH4ck souhaite se developper sur le marche du e-commerce et a besoin d'une plateforme en ligne pour vendre ses produits. 
Celle-ci se developpe dans le marche de la vente de multimedia et electromenager, en se demarcant par un style moderne et avec une qualite au rendez-vous

Problematique : 

Comment concevoir une architecture web performante et scalable pour un site e-commerce afin d’assurer une navigation fluide et une gestion efficace des produits ?

Objectifs : 

L'objectif du site est de permettre a l'entreprise ReverseH4ck de commercialiser en ligne ses produits multimedias et electromenagers, en offrant une experience utilisateur fluide, moderne et adaptee aux standards du e-commerce.


## Presentation Appli

Choix technologiques :

Pour le developpement du site, les environnements de developpement utilises sont Visual Studio Code et la suite JetBrains, permettant une flexibilite selon les preferences des developpeurs. La fonctionnalite Code With Me est egalement exploitee pour faciliter le travail collaboratif a distance en temps reel avec Valentin et Nassim.

Outil de travail collaboratif :

Github (Upload et hebergement en developpement avant de passer sur le serveur Web)
SGBD : MariaDB (production), MySQL (bdd en local pour chacun de nous en developpement)
IDE : VsCode (Moussa), Webstorm/PhpStorm/DataGrip (Jetbrains) pour Steven et Louis

Repartition des taches :

Moussa : Backend : Page login/register (PHP) relie a la bdd (cree par lui-meme), test de vulnerabilites web sur la phase finale du projet MOUSSA

Steven : Frontend : HTML/CSS/PHP du projet, ajouts de commentaires/clarification du code, suivi et suggestions d’ameliorations 

Louis : Frontend / Backend : Base de la strucutre du site CSS/JS/PHP, panel admin, hebergement web

Valentin : Collaboration externe dont correctifs de bugs (plus axé sur le projet application du groupe)

Nassim : Collaboration externe dont correctifs de bugs (plus axé sur le projet application du groupe)

Tout le monde a travaillé et contribué au projet lourd (application) et léger (web) pour appliquer les compétences vus en cours


Dates & Deadlines :

Janvier/Février : Debut du projet lourd et léger
Mars : Structure totale fini (bdd, git, html/css/php/js)
Fin Mars/Début Avril : Finalisation et correctifs

Deadline : Lundi 7 avril 

Pas de schéma MCD, MLD et manque de clarté présentation


## Fonctionnalites


- **Inscription et Connexion** : Les utilisateurs peuvent creer un compte et se connecter pour acceder a des fonctionnalites personnalisees.
- **Gestion du Panier** :  Les utilisateurs peuvent ajouter des produits a leur panier, modifier les quantites et finaliser leurs achats.
- **Finalisation des Commandes** : Les utilisateurs peuvent passer des commandes et recevoir une confirmation de commande.
- **Tableau de Bord Administrateur** : Les administrateurs peuvent gerer les produits, les commandes et les utilisateurs.

## Structure du Projet

- `index.php` : Page d'accueil du site.
- `assets/website/` : Contient les pages principales du site (produits, contact, panier, etc.).
- `assets/account-handling/` : Gere l'inscription, la connexion et les parametres des utilisateurs.
- `assets/admin/` : Contient les pages et les scripts pour le tableau de bord administrateur.
- `assets/styles/` : Contient les fichiers CSS pour le style du site.
- `assets/images/` : Contient les images utilisees sur le site.

## Technologies Utilises
- **PHP** : Langage de programmation principal pour le backend.
- **MySQL** : Base de donnees pour stocker les informations des utilisateurs, produits, commandes, etc.
- **Bootstrap** : Framework CSS pour le design et la mise en page.
- **JavaScript** : Utilise pour les interactions dynamiques sur le site.


## Conclusion 

- Resumé : 
Nous developpons un site e-commerce parfaitement fonctionnel dedie a la vente de produits electromenagers et multimedia, destine aux particuliers et aux professionnels.
Le site permettra de :
- Consulter les produits par categorie
- Creer un compte, se connecter et se deconnecter
- Gerer un panier d’achat
- Utiliser un formulaire de contact
- Acceder a une page "A propos"
- Gerer le contenu via un panel administrateur


L’objectif est de proposer une plateforme simple, efficace et securisee pour faciliter l’achat en ligne.

- Limites : On a pas relier les deux projets, pas utilise mm schéea de bdd (tables, cles differentes)
Manque de compatibilites

- Perspectives d’evolution: Ajout de pages d’avis, personnaliser les produits, innovation sur les produits proposes, login/register mot de passe oublie (mais bon ya pas d’envoi d’email) 

## Installation
1. Clonez le depot GitHub :
   ```bash
   git clone https://github.com/BENZOOgataga/Projet-Web.git
