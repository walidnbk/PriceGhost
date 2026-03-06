#  Price Ghost - Le Comparateur de Prix Intelligent (Maroc)

**Price Ghost** est un outil complet (une Extension Chrome + un Serveur Laravel) qui vous aide à faire des économies. 

**Le concept est simple :** Quand vous regardez un produit sur Amazon, l'extension cherche automatiquement si ce même produit est disponible au Maroc (sur Jumia, Electroplanet, Iris, etc.) et vous affiche le meilleur prix local.

---

## Comment ça marche ? (L'histoire d'une recherche)

L'application fonctionne en deux parties qui discutent entre elles :

1. **L'œil (L'Extension Chrome) :** - Vous ouvrez la page d'un produit sur Amazon (ex: *Samsung Galaxy A16*).
   - L'extension s'active, lit le nom du produit, et surtout, elle regarde dans quel **rayon** vous êtes (ex: *Téléphones* ou *Cuisine*).
   - Elle affiche une petite roue de chargement et envoie ces infos au serveur.

2. **Le cerveau (Le Serveur Laravel) :** - Il reçoit le nom et le rayon du produit.
   - **Il est intelligent :** S'il voit que c'est un téléphone, il va chercher sur *SetupGame* et *Electroplanet*. S'il voit que c'est une poêle, il va chercher sur *Bricoma* et *Kitea*. Ça évite de faire planter le serveur !
   - Il lit le code source de ces sites marocains, trouve les prix, efface les lettres (comme "MAD" ou "Dhs") pour ne garder que les chiffres, et trouve le moins cher.

3. **Le Résultat :**
   - Le serveur renvoie la réponse à l'extension.
   - Une belle petite carte s'affiche sur votre écran Amazon avec le meilleur prix au Maroc, le nom de la boutique, et un bouton direct pour aller l'acheter !

---

##  Les Fonctionnalités Principales

*  **Détection de Catégorie :** Le script comprend ce que vous cherchez pour cibler les bonnes boutiques marocaines.
*  **Nettoyage de Données :** Un système qui comprend les prix complexes (qui enlève les virgules, les faux centimes et les devises).
*  **Filtre Anti-Erreurs :** Ignore les accessoires pas chers (ex: une coque à 20 MAD) quand on cherche un vrai téléphone.
*  **Tableau de bord Admin :** Une page web sécurisée (`/admin`) pour voir l'historique de toutes les recherches faites par les utilisateurs.

---

##  Technologies Utilisées

Ce projet a été développé de A à Z (Full Stack) :

* **Frontend (Ce que l'utilisateur voit) :** * JavaScript pur (Vanilla JS)
  * HTML / CSS injecté directement dans la page Amazon.
* **Backend (Le moteur de recherche caché) :** * **Laravel 11** (Framework PHP)
  * Le système `HTTP Client` de Laravel pour fouiller les autres sites web (Scraping).
* **Base de données :** MySQL (pour sauvegarder l'historique).

---

## Aperçu du Projet en Images

*(Remplacer ces textes par vos vraies images de l'extension et du tableau de bord)*

![Extension en pleine action sur Amazon](lien_de_ton_image_1.png)

![Le tableau de bord administrateur Laravel](lien_de_ton_image_2.png)

---

##  Comment installer ce projet chez vous ?

Si vous voulez tester le code sur votre machine, suivez ces étapes simples :

### 1. Préparer le Serveur (Laravel)
Ouvrez votre terminal et tapez ces commandes :
```bash
# 1. Cloner le projet
git clone [VOTRE-LIEN-GITHUB-ICI]
cd price-ghost

# 2. Installer les dépendances PHP
composer install

# 3. Préparer le fichier de configuration
cp .env.example .env
php artisan key:generate

# 4. Créer la base de données et la remplir
php artisan migrate

# 5. Lancer le serveur (il doit rester allumé !)
php artisan serve