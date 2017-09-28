<div class="modal-body">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="jumbotron text-center">
					<h2>Générer un message en WYSIWYG</h2>
				</div>
				<h2>En bref :</h2>
				<b>WYSIWYG</b> : WYSIWYG signifie <b>W</b>hat <b>Y</b>ou <b>S</b>ee <b>I</b>s <b>W</b>hat <b>Y</b>ou <b>G</b>et.<br>
				En d'autres termes, ce que vous voyez est ce que vous pouvez prendre. Cet éditeur vous permet de déplacer des objets de la colonne de gauche dans le corps du message.
				<br>Vous pouvez déplacer ces objets dans le message, et bien sûr les personnaliser :
				<ul>
					<li>Couleur du texte
					<li>Couleur du fon de paragraphe
					<li>Images
					<li>Lien sur les images
				</ul>
				L'objectif est de vous aider à créer <b>rapidement</b> un mail dit "responsive" (qui s'adapte aux déifférentes supports de lecture de vos destinataires).
				La procédure globale de création et d'envoi d'un mail est la suivante :
				<ul>
					<li>Création du template global du mail en WYSIWYG > suivant
					<li>Passage par l'éditeur classique pour terminer et affiner la rédaction du mail > Suivant
					<li>Prévisualisation à l'écran > Envoi d'une preview à l'adresse mail définie dans Configuration newsletetr
					<li>Envoi en direct ou envoi par planification (si accès crontab possible)
				</ul>
				<h2>En détail :</h2>
				<div class="row" style="margin-bottom:5px;">
					<div class="col-md-2">
						<button type="button" class="btn btn-primary">NOUVEAU</button>
					</div>
					<div class="col-md-10">
						Permet d'effacer la zone de "déposer" des objets proposés, et de créer un nouveau template de mail
					</div>
				</div>
				<div class="row" style="margin-bottom:5px;">
					<div class="col-md-2">
						<button type="button" class="btn btn-primary">ENREGISTRER</button>
					</div>
					<div class="col-md-10">
						Permet d'enregistrer le travail en cours. Vous pouvez quitter l'application, vous retrouverez votre template à la future connexion.
					</div>
				</div>
				<div class="row" style="margin-bottom:5px;">
					<div class="col-md-2">
						<button type="button" class="btn btn-primary">SUIVANT</button>
					</div>
					<div class="col-md-10">
						Permet de passer à l'étape suivante qui est la rédaction du mail dans l'éditeur classique. 
						<br><b>ATTENTION</b> : la présentation dans l'éditeur classique peut être altérée. 
						<br>Ne modifiez pas le code, et attendez la preview et l'envoi sur votre liste de tests pour modifier le code !
					</div>
				</div>
				<div class="row" style="margin-bottom:5px;">
					<div class="col-md-2">
						<img src="css/wysiwyg/switch_preview.png" width=105 alt="switch de preview mobile" title="bouton de switch en preview mobile" />
					</div>
					<div class="col-md-10">
						Ce switch vous permet de visualiser votre composition dans un mobile standard et ainsi valider la qualité "responsive".
						<br>Vous ne pouvez pas composer votre mail pendant cette preview.
					</div>
				</div>
				<div class="row" style="margin-bottom:5px;">
					<div class="col-md-2">
						<img src="css/wysiwyg/header.jpg" width=105" />
					</div>
					<div class="col-md-10">
						En glissant/déposant une zone "header" sur la composition de votre mail, vous ajouterez une zone d'en-tête avec menu au corps de votre mail.
					</div>
				</div>
				<div class="row" style="margin-bottom:5px;">
					<div class="col-md-2">
						<img src="css/wysiwyg/separator.png" width=105 />
					</div>
					<div class="col-md-10">
						En glissant/déposant une zone "separator" sur la composition de votre mail, vous ajouterez une zone de séparation entre 2 zones dans votre email (exemple : entre un header et une zone de texte).
						<br>Il est important de composer un mail aéré, agréable à regarder, qui sera finalement clair sur les supports autres que les ordinateurs (mobiles ou tablettes).
					</div>
				</div>
				<div class="row" style="margin-bottom:5px;">
					<div class="col-md-2">
						<img src="css/wysiwyg/image.jpg" width=105 />
					</div>
					<div class="col-md-10">
						En glissant/déposant une zone "image" sur la composition de votre mail, vous ajouterez une image de 440 pixels de large à votre mail, donc toute la largeur de votre email.
						<br>Ne changez pas la largeur ! Elle sera adaptée à de très nombreux outils de lecture de mails de vos destinataires de campagne. La hauteur sera automatiquement adaptée.
					</div>
				</div>
				<div class="row" style="margin-bottom:5px;">
					<div class="col-md-2">
						<img src="css/wysiwyg/imagelefttxt.jpg" width=105 />
					</div>
					<div class="col-md-10">
						Permet l'ajout d'une zone avec une image dans la zone gauche et du texte aligné à droite dans la zone droite.
					</div>
				</div>
				<div class="row" style="margin-bottom:5px;">
					<div class="col-md-2">
						<img src="css/wysiwyg/imagerighttxt.jpg" width=105 />
					</div>
					<div class="col-md-10">
						Permet l'ajout d'une zone avec une image dans la zone droite et du texte aligné à gauche dans la zone gauche.
					</div>
				</div>
				<div class="row" style="margin-bottom:5px;">
					<div class="col-md-2">
						<img src="css/wysiwyg/image2txt.jpg" width=105 />
					</div>
					<div class="col-md-10">
						Permet l'ajout d'une zone avec 2 colonnes qui comprennent chacune une image en haut et du texte aligné à gauche en dessous
					</div>
				</div>
				<div class="row" style="margin-bottom:5px;">
					<div class="col-md-2">
						<img src="css/wysiwyg/full-text.jpg" width=105 />
					</div>
					<div class="col-md-10">
						Permet l'ajout d'une zone complète de texte centré avec un titre.
					</div>
				</div>
				<div class="row" style="margin-bottom:5px;">
					<div class="col-md-2">
						<img src="css/wysiwyg/social.jpg" width=105 />
					</div>
					<div class="col-md-10">
						Permet l'ajout d'une zone d'affichage des icones des réseaux sociaux liés à votre mailing, votre site web, votre identité.
					</div>
				</div>
				<h2>Modifier les templates :</h2>
				
			</div>
		</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-primary" data-dismiss="modal">Fermer</button>
	</div>
</div>