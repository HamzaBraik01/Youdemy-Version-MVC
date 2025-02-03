# 📚 Youdemy - Plateforme de cours en ligne

## 📝 Description
Youdemy est une plateforme de cours en ligne permettant aux étudiants d'accéder à un catalogue de cours et aux enseignants de gérer leurs contenus. Ce projet était initialement développé en PHP procédural et a été migré vers une architecture MVC (Modèle-Vue-Contrôleur) pour améliorer la modularité, la maintenabilité et l'extensibilité.

## 🎯 Objectifs de la migration MVC
- 🔄 Restructurer le code en suivant le modèle MVC.
- 📖 Améliorer la lisibilité et la maintenabilité du code.
- 💡 Implémenter les bonnes pratiques de développement (SOLID, DRY, etc.).
- 🚀 Préparer la plateforme pour des fonctionnalités futures.

## 🚀 Fonctionnalités

### 🎓 Front Office
- 📚 Catalogue des cours avec pagination et recherche.
- 👤 Inscription et connexion des utilisateurs (étudiants et enseignants).
- 🖊️ Gestion des cours pour les enseignants (ajout, modification, suppression).
- 📂 Section "Mes cours" pour les étudiants.

### 🔧 Back Office
- ✅ Validation des comptes enseignants par l’administrateur.
- 🛠️ Gestion des utilisateurs et des contenus (cours, catégories, tags).
- 📊 Statistiques globales (nombre de cours, top enseignants, etc.).

## 🏛️ Architecture MVC
### 📦 Modèle (Model)
- 🗃️ Gère les interactions avec la base de données (CRUD pour les cours, utilisateurs, tags, etc.).
- 🔗 Implémente les relations entre les entités (one-to-many, many-to-many).
- 🔐 Utilise des requêtes préparées pour éviter les injections SQL.

### 🎨 Vue (View)
- 📄 Contient les templates réutilisables pour les pages (header, footer, etc.).
- 📱 Assure un design responsive et accessible.
- ✅ Intègre la validation côté client avec HTML5 et JavaScript natif.

### 🏗️ Contrôleur (Controller)
- ⚙️ Gère la logique métier et les interactions entre les modèles et les vues.
- 🔍 Valide les données côté serveur pour prévenir les attaques XSS et CSRF.
- 🔑 Gère les sessions utilisateurs et les autorisations d'accès.

## 🛠️ Exigences Techniques
- 💻 **Langage** : PHP 8.2.12
- 🗄️ **Base de données** : PostgreSQL
- 🏛️ **Architecture** : MVC
- 🔒 **Sécurité** : Prévention XSS, CSRF, SQL Injection
- 🔑 **Sessions** : Gestion des utilisateurs connectés
- ✅ **Validation** : Côté serveur et client
- 🏆 **Bonnes pratiques** : SOLID, DRY, requêtes préparées

## 🏗️ Installation
1. **📥 Cloner le dépôt GitHub**
   ```bash
   https://github.com/HamzaBraik01/Youdemy-Version-MVC.git
