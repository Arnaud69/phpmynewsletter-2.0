<div class="jumbotron">
<h1><img src="https://www.phpmynewsletter.com/css/images/phpmynewsletter_v2.gif" /> Le mailing, comme un pro ! </h1>
<p class="lead">PhpMyNewsLetter réunit tous les éléments techniques des professionnels pour vous permettre de réaliser des campagnes emailing simplement.</p>
</div>

<div class="row marketing">
<div class="col-lg-12">
<h4>Historique</h4>
<p>PhpMyNewsletter a été créé en 2000 (première relase publique le 5 juin 2000) par <a href="http://gregory.kokanosky.free.fr/v4/phpmynewsletter/" target="_blank" title="le 1er portail PhpMyNewsLetter">Grégory Kokanosky</a>. 
PhpMyNewsletter est un logiciel libre disponible sous les termes de la <a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">Licence Publique Générale</a> du projet <a href="http://www.gnu.org" target="_blank">GNU</a> (Gnu GPL).
<br>Avec l'accord de Gregory Kokanosky, j'ai repris le projet PhpMyNewsLetter alors qu'il était à la version 0.8 beta 5, j'ai donc pris le nom de domaine, et pour rester dans le courant du web, j'ai décidé de repartir de la base en réécrivant complètement l'outil en version 2.0.
</p>
<b>Concernant les choix du développement</b> :<br>
PhpMyNewsletter a été développé en PHP, et restera développé en PHP. Il n'intègre pas de <a href="https://www.youtube.com/watch?v=DuB6UjEsY_Y" target="_blank">framework</a> et est donc écrit en simple langage procédural que chacun peut suivre assez facilement s'il veut l'améliorer.<br>
Du fait de l'appartenance de MySql à Oracle, j'ai également choisi MariaDB pour développer le projet. L'interface PhpMyNewsletter fait appel à des bibliothèques accessibles via des "cdn", c'est un choix personnel que de ne pas alourdir inutilement le package complet.
</p>
<h4>Fonctionnalités</h4>
<p>La version 2.0.5 est riche en fonctionnalités et améliorée en présentation.<br>
<b>Un bel outil doit être fonctionnel et agréable à utiliser</b>.
Ainsi, pour l'ergonomie j'ai choisi <a href="https://jquery.com/" target="_blank">jQuery</a> pour tout ce qui JavaScript et le framework <a href="http://getbootstrap.com/" target="_blank">Bootstrap</a> pour la mise en page.</p>
<p>

<b>Concernant les fonctionnalités</b> :<br>
Rédiger un mail, l'envoyer, traiter les retours en erreur (les "bounces") et avoir un compte rendu d'ouverture, voilà la ligne directrice de ce que je voulais pour cette refonte, pour cet outil.
</p>

<h4>Rédiger un mail</h4>
<p>PhpMyNewsLetter vous propose 2 solutions pour rédiger un mail :<br>
<ul>
<li>Rédaction traditionnelle avec la mise en page via <a href="https://www.tinymce.com/" target="_blank">TinyMCE</a>. L'usage est simple, la composition est facile, et les barres d'outils vous proposent les fonctionnalités ordinaires de mise en page.
<li>Dans cette nouvelle version de PhpMyNewsLetter, vous découvrirez un module de mise en page WysIsWyG ([oui-zi-wyg]) qui vous permet de glisser des objets sur une page et permet d'obtenir des mails dits "responsives" ou si vous préférez adaptifs aux supports de lecture (mobiles, tablettes ou PC).
</ul>
Dans chaque mail est inséré un traceur qui permet d'obtenir des informations générales sur le contexte d'ouvertures des mails. Ce traceur peut être désactivé.
</p>

<h4>Envoyer une campagne</h4>
<p>PhpMyNewsLetter vous permet d'envoyer vos campagnes en direct (envoi en Ajax), ou en planifié (crontab sen environnement Linux, sur serveur dédié, ou chez certains hébergeurs en mutualisé).</p>

<h4>Traiter les retours</h4>
<p>La grande difficulté de la gestion d'une liste de mails est le traitement des retours, opération nécessaire pour garder une liste saine. Ce traitement est confié à la classe de gestion des bounces écrite par <a href="http://www.crazyws.fr/dev/classes-php/classe-de-gestion-des-bounces-en-php-C72TG.html" target="_blank">crazyws</a>.<br>
La classe a été très largement adaptée à PhpMyNewsLetter pour permettre une intégration complète à l'environnement.</p>

<h4>Les statistiques</h4>
<p>Les professionnels du mailing peuvent se vanter d'avoir de superbes statistiques. Et c'est vrai que c'est superbe, je le reconnais !<br>
J'ai fait le choix de vous proposer les statistiques les plus pertinentes pour vous aider à mieux gérer vos campagnes :
<ul>
<li>Nombre de mails envoyés
<li>Nombre de lectures
<li>Nombre d'ouvertures
<li>Nombre de clics
<li>Calcul du taux d'ouverture sur nombre de mails envoyés
<li>Calcul du taux de clics sur nombre de mails envoyés (CTR)
<li>Calcul du taux de clics sur nombre de mails ouverts (ACTR)
<li>Les environnements : navigateurs, clients mails, système d'exploitation, clics par domaines, domaines présents dans les listes, support d'ouverture (mobile, tablette, PC)
<li>Les clics par tranches horaires
<li>Géolocalisation des ouvertures (par pays et par villes)
<li>Statistiques globales toutes listes confondues et statistiques par liste.
</ul>
Et croyez moi, c'est déjà bien suffisant ;-)
</p>

<h4>Mon environnement de développement</h4>
<p>Pour vous aider à bien installer PhpMyNewsLetter, et si vous souhaitez être au plus proche de mon environnement :
<ul>
<li>Serveur dédié <a href="https://www.debian.org/index.fr.html" target="_blank">debian 8.9</a>
<li>serveur intégrant SSL <a href="https://letsencrypt.org/" target="_blank">Let's Encrypt</a>
<li>Serveur web <a href="http://nginx.org/" target="_blank">Nginx</a>
<li><a href="http://php.net/" target="_blank">PHP 7.1</a> (modules curl, imap et openssl)
<li>Base de données <a href="https://mariadb.org/" target="_blank">MariaDB 10.x</a>
<li><a href="http://www.postfix.org/" target="_blank">Postfix 2.11</a>
<li>Un compte utilisateur spécifique (pmnl) avec connexion en ssh possible et accès à crontab
<li>un environnement sudoers pour appeler les commandes de traitement des bounces :
<ul><li>/usr/sbin/postsuper
<li>/usr/sbin/postqueue
</ul>
</ul>
</p>

<h4>Les projets de développement</h4>
<p>Il y en a beaucoup, mais ce qui m'intéresse le plus, c'est votre retour d'expérience. Au cas particulier, j'accepte volontiers les mails où vous me présentez vos demandes, ou suggérez des idées.<br>
Mon objectif est clair : faire au mieux !
</p>

<h4>Vous avez trouvé un bug ?</h4>
<p>Je reçois chaque jour des demandes de correction de bugs qui ne sont en fait qu'un mauvais usage de l'outil PhpMyNewsLetter. Je vous invite donc à ne passer que par le portail pour poser vos questions. Je suis très souvent connecté, je suis informé des nouveaux messages et je réponds au plus vite.<br>
De plus, vos questions permettent d'enrichir la connaissance générale et permettent le cas échéant d'améliorer PhpMyNewsLetter.
</p>

<h4>Confidentialité de mes interventions.</h4>
<p>Il m'arrive fréquemment d'intervenir sur les installations que vous faites. Je le fais avec plaisir. Mais je découvre bien souvent des environnements non sécurisés, mal calibrés, ou encore des installations sans listes de tests.<br>
Après intervention, je vous fais un compte rendu de ce que j'ai pu observer et je vous donnerai volontiers quelques conseils.<br>
Je m'engage lors de ces interventions à ne pas aller dans vos statistiques, archives, et gestion de vos abonnés. Je crée une liste personnelle, avec mes emails et je ne travaille que sur cette liste.</p>
</div>
</div>

<footer class="footer">
<p>&copy; 2017 <a href="https://www.phpmynewsletter.com" target="_blank">PhpMyNewsLetter.com</a>.</p>
</footer>

