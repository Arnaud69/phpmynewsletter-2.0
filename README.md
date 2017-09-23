phpmynewsletter 2.0
===================

# CURRENT : 2.0.4
 release 12/12/2016
 
# INSTALLATION
Download zip, unzip in a folder, go to install.php in your browser, complete fields and questions, enjoy !
Or go to https://www.phpmynewsletter.com/forum/viewtopic.php?pid=2354 to use upgrade.php from 2.0.3

# SYNOPSIS
Send mails, add attachment, manage bounce and track clicked links.

# Required
 - PHP 5.3 min with : imap, curl, openssl, module php exec
 - Mysql 5.x min
 - VPS/linux or dedicated server/linux for crontab and bounce management
 - mails
 - And your hands to write your mails !

# CHANGELOG :
## GENERAL :
* Correction des bugs de la version 2.0.3
* Traduction complète du script, mise en fichier de tous les textes.
* Validation de PhpMyNewsLetter pour PHP 5.4 et supérieur (tests généraux avec HHVM beaucoup plus pointu) et prêt pour PHP 7.0 (sauf erreur ou omission de ma part...)
* Suppressio des "HIDE" dans le menu gauche pour amélioration de la présentation /ergonomie
## TRADUCTIONS :
* anglais / english

## OUTILS INTEGRES :
* Phpmailer 5.2.14 
* Amcharts 3.13.2 (librairie internalisée)
* TinyMCE 4.1.9 (librairie internalisée)
* DropZone 4.0.1 (librairie internalisée)
* jQuery 1.11.2 (librairie internalisée)

## AMELIORATIONS :
* Internalisation de l'heure serveur via php, puis incrément javascript (évite les appels ajax à chaque seconde)
* Compte rendu des imports corrects et en erreur (par compteur)
* Création de tous les répertoires nécessaires centralisés dans install.php (upload, DKIM, backup_crontab)
* Test de la disponibilité de la fonction exec php
* Prise en compte du numéro de campagne pour la gestion des mails en erreur, supprimés, désinscrits
* Mot de passe administrateur obligatoire à l'installation
* Visualisation des logs en modal (plus de téléchargement)
* Suppression des espaces dans les noms documents envoyés sur le serveur par upload
* Compression du code html pour envoi des mails (suppression des espaces et retour à la ligne inutiles)
* Signalement des désinsriptions
* Mails en erreur, désinscrits, supprimés par administrateur conservés en archive pour garder les listes saines

## GESTION ET VISUALISATION DES STATISTIQUES :
* Suppression de JpGraph (incompatible HHVM)
* Intégration de AmCharts pour comptes rendus d'envois, librairies externalisées
* Statistiques des environnements à la lecture des mails :
    - Navigateurs et versions,
    - Système d'exploitation,
    - Support de lecture : ordinateur, tablette, téléphone mobile
    - Types de clients mails (thunderbird, Outlook, Icedove, etc...)
    - Identification des lectures Gmail
* Bascule simplifiée d'une liste à une autre
* Statistiques globales de toutes les listes sur la page des listes
* Intégration de la librairie Charts.js pour statistiques globales par liste et globales toutes listes confondues
* Ajout du nombre de clics dans chaque campagne

## GESTION DU TRACKING :
* Ajout de la détection des OS, version, navigateur, support
* Calcul du taux d'ouverture (Open Rate)
* Calcul du ratio CTR (taux de clics sur envois) (CTR  : Click Through Rates)
* Calcul du ratio ACTR (CTR ajusté : taux de clics sur ouvertures) (ACTR : Adjusted Click Through Rate)
* Détection des clients sur ouverture d'un mail (Outlook, Icedove, Thunderbird, Lotus,...)
* Correction d'un bug dans les calculs / stats

## GESTION DES MESSAGES ENVOYES :
* Mise en place du "multi-part message in MIME format", balise AltBody
* Messages quoted printable, et AltBody pour les clients mails non graphiques    
* Encodage des mails envoyés possible en base64 au lieu de 8bit
* Classe de test de l'email via SMTP avant envoi du mail (Attention : yahoo, ymail et rocketmail renvoient toujours OK)

## GESTION DES TACHES PLANIFIEES :
* Compte rendu automatique de fin de tâche planifiée par mail
* possibilité de suppression d'une tâche planifiée non encore réalisée

## GESTION DES LOGS :
* Ajout de l'environnement technique dans l'entête du log d'envoi final
* Log des actions d'une journée
* Log pour chaque envoi
* Visualisation des logs en modal (plus de téléchargement)

## GESTIONS DES LISTES :
* Duplication d'une liste en 2 clics
* Fusion de 2 à n listes dans une liste nouvelle, avec contrôle des doublons
* Correction du problème des imports
* Gestion d'une table des emails supprimés manuellement, désinscrits en erreur. (Evite les doublons en erreur d'envoi, permet une vérification aux inscriptions et ajouts manuels)
* Notification de nouvel inscrit dans une liste (choix optionnel, désactivable dans configuration générale, onglet inscriptions)
* Notifications des désinscriptions
* Purge d'une liste (=suppression des mails d'une liste, uniquement)

## GESTIONS DES ABONNES :
* Gestion des abonnés sur une seule page au lieu de 5
* Ajout de la vérification de la structure d'une adresse mail
* Ajout de la vérification du DNS correspondant au domaine du mail
* Ajout de la possibilité de supprimer des mails en masse depuis un fichier externe
* Ajout de couleurs pour distinguer les opérations : vert=ajout, rouge=suppression, jaune=autres opérations

## GESTIONS DES SMTPs : (Nouveau)
* Pseudo LOAD BALANCING des envois sur plusieurs SMTP paramètrables
* Ajout de la zone de choix d'un port pour configurer un nouveau SMTP
* Gestion automatiques du SMTP Gmail, des SMTP mutualisés OVH, 1and1, Gandi, Online, Infomaniak par simple choix en liste déroulante
* Distinction smtp GMAIL SSL (port 465) et TLS (port 587)

## GESTION DE LA REDACTION :
* blocage de l'accès à la preview tant que la sauvegarde initiale n'est pas faite (risque de blocage sur planification d'envoi)
* simulation de divers navigateurs (sur mobiles / tablettes) pour validation des messages dits "responsives"

## GESTION DES ENVOIS (dans la configuration globale) :
* Activation / désactivation possible du tracking

## GESTION DES BOUNCES :
* Suppression d'un email dans toutes les listes quand il est erreur bounce

# SCREENSHOT, PREVIEW

![Statistiques](https://blog.aulica-conseil.com/wp-content/uploads/2016/12/stats.png)

![Lists and stats quick view](https://blog.aulica-conseil.com/wp-content/uploads/2016/12/listes.pngg)

![Bounces](https://blog.aulica-conseil.com/wp-content/uploads/2016/12/bounces.png)



# SUPPORT
Official support on the forum, please go to https://www.phpmynewsletter.com/forum/ or tickets for bugs and support.

# Roadmap for next version, TODO 2.0.5
 Fell free to ask and/or report, i'll do !
 
# LICENSE
GNU GPL

Please, star if you like ;-)


