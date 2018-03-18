![PhpMyNewsLetter 2.0](https://www.phpmynewsletter.com/css/images/phpmynewsletter_v2.png) 
phpmynewsletter 2.0
===================

# VERSION COURANTE : 2.0.5
 
# INSTALLATION
Télécharger le fichier zip : https://github.com/Arnaud69/phpmynewsletter-2.0/archive/master.zip
Dézipper dans un répertoire ou à la racine d'un sous-domaine dédié aux newsletter, appelez le script install.php depuis votre navigateur, remplissez les champs, et suivez la procédure.

# SYNOPSIS
Envoyez des emails, ajoutez des pièces jointes, gérez les retours (bounces), suivez les clics, les ouvertures, géolocalisation, etc...

# CONFIGURATION MINIMALE REQUISE :
 - PHP 5.3 min avec : imap, curl, openssl, module php exec
 - Mysql 5.x min
 - VPS/linux ou dédié server/linux pour un accès à crontab et gestion des bounces
 - les emails de vos correspondants
 - Et vos petites mains pour écrire de jolis mails et faire de belles campagnes !

# CHANGELOG :
## GENERAL :
- Compatibilité PHP 7 totale
## CORRECTIONS :
- Correction des bugs des versions antérieures
- Amélioration du script d'installation et correction du bug de création de la base
- Amélioration de la qualité des calculs des statistiques d'ouvertures (navigateurs, OS,...)
- Correction de la table des codes Géoloc (https://www.iso.org/obp/ui/)
- Correction du bug qui altérait le css dans les messages
- Ajout du "sql_mode = ''" pour les serveurs dédiés (tolérance aux champs non définis avec des valeurs par défaut)
- Amélioration du process unique_id, il y avait un risque de doublons de calcul de hash
- Adaptation globale pour une installation en sous-domaine
- Amélioration du recensement des mails en erreur à l'envoi (via phpmailer) en tâche planifiée (n'étaient pas comptabilisés)
- Correction du rattachement des clés DKIM
- Correction du traitement des mails en bounce
- Ajout du calcul du prefix des tables (ex : "pmnl2_") dans la config pour gestion des tables plus fine
## NOUVEAUTES :
- Champs sujets passés en utf8mb4 pour permettre usage des Emojis
- Gestion du Pre-Header
- Choix du menu : horizontal avec menus déroulants ou vertical traditionnel (configuration globale > règlages divers). Préférez le menu horizontal !
- Choix d'afficher ou ne pas afficher le loader (configuration globale > règlages divers)
- Géolocalisation des ouvertures (amcharts)
- Création de templates par un éditeur Wysiwyg (What You See Is What You Get)
- Création de la gestion des droits (un ou plusieurs droits à des utilisateurs crées par un admin) et log des actions
- Test des enregistrement DKIM, SPF et DMARC du domaine expéditeur
- Sauvegarde de la base à la demande et téléchargement (nombre de sauvegardes paramétrable)
- Regénération d'un mot de passe envoyé par mail si perdu
- Possibilité aux abonnés "free mobile" de recevoir des textos de fin de tâche d'envoi, d'inscriptions et désinscriptions
- Utilisation des CDNs pour l'import des librairies JS et CSS (au maximum)
- Vérification obligatoire des liens contenus dans un message avant preview
## OUTILS INTEGRES :
- Phpmailer 5.2.26
## AMELIORATIONS :
- Meilleure gestion de la comparaison des versions pour mises à jour possibles de versions mineures
- Affichage des mails en erreur par liste
- Code html de souscription basculé dans les paramètres des newsletters
## GESTION DU TRACKING :
- Géolocalisation des ouvertures (amcharts)
- Affichage des liens cliqués en modal
## GESTION DES MESSAGES ENVOYES :
- Suppression de la mention phpmynewsletter 2.0 en bas des mails envoyés
- Ajout des mails de REPLY et de BOUNCE
## GESTION DES TACHES PLANIFIEES :
- Correction du bug de suppression de la tâche planifiée
## GESTION DES LOGS :
- Correction du décalage dans les colonnes lorsqu'il n'y a pas de fichier log présent
- Affichage des logs en modal
## GESTIONS DES ABONNES :
- Calcul du profil des abonnés (rubrique Profils des abonnés)
- Pagination de la liste des abonnés en erreur en ajax
## GESTIONS DES SMTPs :
- Modification possible d'un smtp déclaré
- Remise à 0 des compteurs (load balancing smtp) lors de la preview
## GESTION DE LA REDACTION :
- Ajout de templates responsive (depuis TinyMCE)
- Thème "pmnl" des outils de rédaction TinyMCE
## GESTION DES BOUNCES :
- Correction du bug qui empêchait la suppression correcte des mails en erreur
- Ajout de la possibilité d'un mail de bounce différent de l'expéditeur (alias d'un Return Path)
- Ajout du paramètre array('DISABLE_AUTHENTICATOR' => 'GSSAPI') à la connexion imap pour les accéder aux serveurs de messagerie de type Exchange
- Amélioration du REGEXP pour récupération des mails en bounce sur serveurs de messagerie de type Exchange 

# SCREENSHOT, PREVIEW
## Présentation en menu vertical
![Menu vertical](https://www.phpmynewsletter.com/images/2.0.5/vertical_menu.png)
## Le nouvel éditeur en mode Wysiwyg : glisser et déposer des blocs, puis les personnaliser
![Editeur en mode WysiWyg](https://www.phpmynewsletter.com/images/2.0.5/wysiwyg.png)
## Présentation en menu vertical et vue de la gestion des utilisateurs
![Menu horizontal et gestion des utilisateurs de Phpmynewsletter](https://www.phpmynewsletter.com/images/2.0.5/account_manager.png)
## La nouvelle gestion des comptes utilsateurs de PhpMyNewsLetter
![Gestion des comptes expéditeurs](https://www.phpmynewsletter.com/images/2.0.5/account_manager.png)
## La gestion détaillée des comptes utilisateurs
![Gestion des utilisateurs de Phpmynewsletter, détail de la gestion des droits](https://www.phpmynewsletter.com/images/2.0.5/account_manager_detail.png)
## La gestion des SMTPs pour le load balancing (plusieurs smtp = distribution plus rapide des mails)
![Gestion des SMTPs pour load balancing SMTP](https://www.phpmynewsletter.com/images/2.0.5/1.jpg)
## Les statistiques globales
![Statistiques](https://www.phpmynewsletter.com/images/2.0.5/full_stats.png)
## L'accès au profil des utilsateurs
![Profils des utilisateurs](https://www.phpmynewsletter.com/images/2.0.5/users_profils.png)

# SUPPORT
Support sur forum : https://www.phpmynewsletter.com/forum/.

# Roadmap for next version, TODO 2.0.6
- Gestion indépendate des templates
- Intégration d'un formulaire d'ajout des clés DKIM
- Champs de personnalisation des emails
- Champs de personnalisation des fonds d'écran
- Compatibilité avec Postgresql et MsSQL
- Mise à jour d'un simple clic
- PHPMailer 6.x
- Traduction complète en anglais
- Si vous pensez à quelque chose qui puisse améliorer le produit, demandez !
 
# LICENSE
GNU GPL

Mettez une étoile si vous aimez  ;-)

