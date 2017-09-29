phpmynewsletter 2.0
===================

# CURRENT : 2.0.5
 release 28/09/2017
 
# INSTALLATION
Download zip, unzip in a folder, go to install.php in your browser, complete fields and questions, enjoy !
Actually writing upgrade.php for upgrade from 2.0.4 to 2.0.5

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
- Choix du menu : horizontal avec menus déroulants ou vertical traditionnel (configuration globale > règlages divers)
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
- Phpmailer 5.2.24
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
- calcul du profil des abonnés (rubrique Profils des abonnés)
- pagination de la liste des abonnés en erreur en ajax
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

![Statistiques](--)

![Lists and stats quick view](--)

![Bounces](--)


# SUPPORT
Official support on the forum, please go to https://www.phpmynewsletter.com/forum/ or tickets for bugs and support.

# Roadmap for next version, TODO 2.0.6
- Champs de personnalisation des emails
- Compatibilité avec Postgresql et MsSQL
- Mise à jour d'un simple clic
- Si vous pensez à quelque chose qui puisse améliorer le produit, demandez !
 
# LICENSE
GNU GPL

Please, star if you like ;-)


