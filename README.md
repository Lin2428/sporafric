<p align="center"><a href="https://www.motors-congo.com/" target="_blank"><img src="https://www.google.com/imgres?q=sporafric%20motors&imgurl=http%3A%2F%2Fwww.motors-congo.com%2Fimages%2Flogo_grasset_sporafric_motors.png&imgrefurl=http%3A%2F%2Fwww.motors-congo.com%2F&docid=ixNRkoSLRuAZ6M&tbnid=rQ2WiUFtdm8NhM&vet=12ahUKEwiugoTP7sONAxWkYEEAHRYwCvUQM3oECHAQAA..i&w=5655&h=951&hcb=2&ved=2ahUKEwiugoTP7sONAxWkYEEAHRYwCvUQM3oECHAQAA" width="400" alt="Laravel Logo"></a></p>
# 🛠️ GMAO - Sporafric Motor

**GMAO** (Gestion de la Maintenance Assistée par Ordinateur) pour **Sporafric Motor** est une application web développée pour faciliter la planification, le suivi et la gestion des interventions de maintenance sur les équipements et les véhicules de l'entreprise.

---

## 🚀 Fonctionnalités principales

- 🔧 **Gestion des interventions**
  - Création, mise à jour et clôture des interventions
  - Historique complet par contrat ou équipement

- 📝 **Gestion des contrats**
  - Suivi des contrats de maintenance (code site, prix mensuel, etc.)
  - Liaison des interventions aux contrats

- 📊 **Suivi des pièces utilisées**
  - Ajout des pièces consommées par intervention
  - Calcul du coût des pièces

- 👷‍♂️ **Gestion des techniciens**
  - Attribution des interventions
  - Historique des interventions par technicien

- 📅 **Planification & Statuts**
  - Suivi des statuts : planifié, en cours, terminé, annulé
  - Types d'intervention : préventive, curative, contrôle, etc.

---

## 🖼️ Vue synthétique des interventions

Une vue regroupe les interventions par contrat avec les informations suivantes :

- 📅 Date de l’intervention  
- 🆔 Identifiant de l’intervention  
- 🏢 Code du site (provenant du contrat)  
- 🛠️ Statut de l’intervention  
- 📘 Type d’intervention  
- 🔩 Nombre total de pièces utilisées  
- 💵 Montant de l’intervention  
- 🧾 Prix mensuel du contrat  
- 👷 Techniciens associés  

---

## 🧰 Technologies utilisées

- **Backend** : Laravel 11
- **Frontend** : Laravel Blade + Filament 3
- **Base de données** : MySQL
- **Langage** : PHP 8.2

---

## ⚙️ Installation

```bash
git clone https://github.com/ton-utilisateur/gmao-sporafric_motor.git
cd gmao-sporafric_motor
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve 
