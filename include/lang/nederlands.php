<?

function translate($s, $i="") {
    global $lang_array;

    if(!isset($lang_array['nederlands'][$s]))
      return ("[Translation required] : $s");

    if($lang_array['nederlands'][$s]!="") {
	if($i == "") return $lang_array['nederlands'][$s];
        $sprint = $lang_array['nederlands'][$s];
	return sprintf("$sprint" , $i);
    }
    else return ("[Translation required] : $s");
}


$lang_array['nederlands'] = array(
		   //BTN
		   "OK_BTN" => "OK",
		   "YES" => "Ja",
		   "NO" => "Neen",
		   
                   "BACK" => "Terug",

		   //ARCHIVE
		   "ARCHIVE_TITLE" => "Archieven",
		   "ARCHIVE_CHOOSE" => "Kies een nieuwsbrief",
		   "ARCHIVE_SUBJECT" => "Onderwerp",
		   "ARCHIVE_DATE" => "Datum",
		   "ARCHIVE_FORMAT" => "Formaat",
		   "ARCHIVE_DISPLAY" => "Toon deze boodschap",
		   "ARCHIVE_BROWSE" => "Doorzoek archieven",
		   "ARCHIVE_DELETE" => "Wis dit archief",
		   "ARCHIVE_DELETE_TITLE" => "Wissen van archief",
		   "ARCHIVE_DELETED" => "Archief succesvol gewist",
		   "ARCHIVE_NOT_FOUND" => "Geen archieven gevonden",
		   
		   //INDEX
		   "PHPMYNEWSLETTER_TITLE" => "phpMyNewsletter",
		   "EMAIL_ADDRESS" => "Email adres",
		   "EMAIL_ADDRESS_NOT_VALID" => "Dit email adres is niet geldig",
		   "NEWSLETTER_SUBSCRIPTION" => "Inschrijven",
		   "NEWSLETTER_UNSUBSCRIPTION" => "Uitschrijven",
		   "AVAILABLE_NEWSLETTER" => "Beschikbare nieuwsbrieven",
		   
		   //ADMIN LOGIN admin/login.php
		   "LOGIN_TITLE" => "Login",
		   "LOGIN_PLEASE_ENTER_PASSWORD" => "Een paswoord is noodzakelijk voor administratieve toegang",
		   "LOGIN_PASSWORD" => "Paswoord",
		   "LOGIN" => "Login !",
		   "LOGIN_BAD_PASSWORD" => "Verkeerd paswoord !",
		   
		   
		   //MENU
		   "MENU_SUBSCRIBERS"=> "Abonnementen",
		   "MENU_COMPOSE" => "Nieuwe Boodschap",
		   "MENU_ARCHIVES" => "Archieven",
		   "MENU_NEWSLETTER" => "Nieuwsbrief Parameters",
		   "MENU_CONFIG" => "Globale Parameters",
		   "MENU_LOGOUT" => "Logout",
		   
		   "SELECTED_NEWSLETTER" => "Geselecteerde nieuwsbrief",
		   
		   
		   //ADMIN NEWSLETTER
		   "NEWSLETTER_CHOOSE" => "Kies een te beheren nieuwsbrief",
		   "NEWSLETTER_ACTION" => "Nieuwsbrief aktie",
		   "NEWSLETTER_NEW" => "Maak een nieuwe nieuwsbrief",
		   "NEWSLETTER_DEL" => "Wissen van '%s'",
		   "NEWSLETTER_SETTINGS" => "Nieuwsbrief parameters",
		   "NEWSLETTER_NAME" => "Nieuwsbrief naam",
		   "NEWSLETTER_FROM_ADDR" => "Email adres verzender",
		   "NEWSLETTER_FROM_NAME" => "Naam verzender",
		   "NEWSLETTER_SUBJECT" => "Onderwerp",
		   "NEWSLETTER_HEADER" => "Hoofding",
		   "NEWSLETTER_FOOTER" => "Voettekst",
		   "NEWSLETTER_SUB_MSG_SUBJECT" => "Onderwerp van inschrijvingsboodschap",
		   "NEWSLETTER_SUB_MSG_BODY" => "Inschrijvingsboodschap",
		   "NEWSLETTER_WELCOME_MSG_SUBJECT" => "Onderwerp welkomstboodschap",
		   "NEWSLETTER_WELCOME_MSG_BODY" => "Welkomstboodschap",
		   "NEWSLETTER_UNSUB_MSG_SUBJECT" => "Onderwerp uitschrijvingsboodschap",
		   "NEWSLETTER_UNSUB_MSG_BODY" => "Uitschrijvingsboodschap",
		   "NEWSLETTER_SAVE_SETTINGS" => "Bewaar deze parameters",
		   "NEWSLETTER_SETTINGS_SAVED" => "Parameters bewaard",
		   "NEWSLETTER_CREATE" => "Maak een nieuwsbrief",
		   "NEWSLETTER_SAVE_NEW" => "Maak deze nieuwsbrief",
		   "NEWSLETTER_DELETED" => "Nieuwsbrief succesvol gewist",
		   "NEWSLETTER_DELETE_WARNING" => "Wis alle gegevens in verband met deze nieuwsbrief",
		   "NEWSLETTER_SETTINGS_CREATED" => "Nieuwsbrief succesvol aangemaakt",
		   "NEWSLETTER_DEFAULT_HEADER" => "========= HOOFDING =========\n".
						  "Tik hier wat je wilt.\n".
						  "Dit zal bovenaan elke boodschap verschijnen",
		   
		   "NEWSLETTER_DEFAULT_FOOTER" => "======== VOETTEKST =======\n".
						  "Je kan hier een boodschap intikken",  
		   "NEWSLETTER_SUB_DEFAULT_SUBJECT" => "Gelieve uw inschrijving te bevestigen",
                   "NEWSLETTER_SUB_DEFAULT_BODY" => "Iemand, waarschijnlijk uzelf, heeft een inschrijving aangevraagd voor deze nieuwsbrief. Gelieve de instructies hieronder op te volgen", 
		   
                   "NEWSLETTER_WELCOME_DEFAULT_SUBJECT" => "Welkom bij deze nieuwsbrief",
   		   "NEWSLETTER_WELCOME_DEFAULT_BODY" => "Welkom bij deze nieuwsbrief!",		   
		   "NEWSLETTER_UNSUB_DEFAULT_SUBJECT" => "Gelieve uw uitschrijving te bevestigen",
                   "NEWSLETTER_UNSUB_DEFAULT_BODY" => "Iemand, waarschijnlijk uzelf, heeft het uitschrijven uit deze nieuwsbrief aangevraagd. Gelieve de instructies hieronder op te volgen", 


		   //SUBSCRIBER
		   "SUBSCRIBER_ADD_TITLE" => "Toevoegen van een abonnee",
		   "SUBSCRIBER_ADD_BTN" => "Voeg dit email adres toe",
		   "SUBSCRIBER_ADDED" => "%s succesvol toegevoegd",
		   
		   "SUBSCRIBER_IMPORT_TITLE" => "Importeer een lijst email adressen",
		   "SUBSCRIBER_IMPORT_BTN" => "Importeer",
		   "SUBSCRIBER_IMPORT_HELP" => "U kan een lijst email adressen direkt importeren uit een bestand.<br />Dit bestand moet het volgende formaat volgen:<br />adres1@domain.com<br />adres2@domain.com<br/>adres3@domain.com",
		   
		   "SUBSCRIBER_DELETE_TITLE" => "Een abonnee wissen",
		   "SUBSCRIBER_DELETE_BTN" => "Wis dit email adres",
		   "SUBSCRIBER_DELETED" => "Abonnee successvol gewist",
		   
		   "SUBSCRIBER_EXPORT_TITLE" => "Exporteer abonnees",
		   "SUBSCRIBER_EXPORT_BTN" => "Exporteer",
		   
		   "SUBSCRIBER_TEMP_TITLE" => "Inschrijving in beraad",
		   "SUBSCRIBER_TEMP_BTN" => "Wis dit email adres",
		   "SUBSCRIBER_TEMP_DELETED" => "Email adres succesvol gewist",
		   
		   
		   //COMPOSE 
		   "COMPOSE_NEW" => "Maak een nieuwe boodschap aan",
		   "COMPOSE_SUBJECT" => "Onderwerp",
		   "COMPOSE_FORMAT" => "Formaat",
		   "COMPOSE_FORMAT_TEXT" => "Platte tekst",
		   "COMPOSE_FORMAT_HTML" => "HTML",
		   "COMPOSE_PREVIEW" => "Boodschap Bekijken",
		   "COMPOSE_PREVIEW_TITLE" => "Boodschap Bekijken",
		   "COMPOSE_BACK" => "Terug",
		   "COMPOSE_SEND" => "Verzend deze boodschap",
		   "COMPOSE_SENDING" => "Boodschap wordt verzonden ...",
		   "COMPOSE_SENT" => "Boodschap succesvol verzonden",


		   //GLOBAL CONFIG
		   "GCONFIG_TITLE" => "Globale Parameters",
		   "GCONFIG_DB_TITLE"=> "Database parameters",
		   "GCONFIG_DB_HOST" => "Hostnaam",
		   "GCONFIG_DB_LOGIN" => "Gebruiker Login",
		   "GCONFIG_DB_DBNAME" => "Database Naam",
		   "GCONFIG_DB_PASSWD" => "Gebruiker Password",
		   "GCONFIG_DB_CONFIG_TABLE" => "Configuratie tabel",
		   "GCONFIG_DB_TABLE_MAIL" => "Abonnee emails worden bewaard in",
		   "GCONFIG_DB_TABLE_TEMPORARY" => "Tijdelijke tabel",
		   "GCONFIG_DB_TABLE_NEWSCONFIG" => "Nieuwsbrief configuratie tabel",
		   "GCONFIG_DB_TABLE_ARCHIVES" => "Archieven worden bewaard in",
		   "GCONFIG_DB_TABLE_SUBMOD" => "Inschrijvingen die moeten bevestigd worden in",

		   
		   "GCONFIG_DB_CONFIG_UNWRITABLE" => "Met schrijf-toelating op %s zouden meer database parameters beschikbaar zijn.",
		   "GCONFIG_MISC_TITLE" => "Andere Parameters",
		   "GCONFIG_MISC_ADMIN_PASSW" => "Admin toegang paswoord",
		   "GCONFIG_MISC_BASE_URL" => "Basis URL",
		   "GCONFIG_MISC_BASE_PATH" => "Pad naar phpMyNewsletter",
		   "GCONFIG_MISC_LANGUAGE" => "Taal",

		   "GCONFIG_MESSAGE_HANDLING_TITLE" => "Boodschap Behandeling",
		   "GCONFIG_MESSAGE_ADMIN_NAME" => "Default naam van verzender (<i>From:</i> veld)",
		   "GCONFIG_MESSAGE_ADMIN_MAIL" => "Default email adres van verzender",
		   "GCONFIG_MESSAGE_NUM_LOOP" => "Aantal boodschappen in elke zendcyclus",
		   "GCONFIG_MESSAGE_SEND_METHOD" => "Methode van verzenden",
                   "GCONFIG_MESSAGE_SEND_METHOD_FUNCTION" => "PHP mail() functie",
		   "GCONFIG_MESSAGE_SMTP_HOST" => "SMTP server hostnaam",
		   "GCONFIG_MESSAGE_SMTP_AUTH" => "SMTP authentificatie nodig ?",
		   "GCONFIG_MESSAGE_SMTP_LOGIN" =>"SMTP gebruikersnaam",
		   "GCONFIG_MESSAGE_SMTP_PASSWORD" => "SMTP paswoord",

		   "GCONFIG_SUBSCRIPTION_TITLE" => "Inschrijving",
		   "GCONFIF_SUBSCRIPTION_VALIDATION_TIMEOUT" => "Abonnees hebben %s dag(en) om hun inschrijving te bevestigen",
		   "GCONFIG_SUBSCRIPTION_CONFIRM_SUB" => "Abonnees moeten hun inschrijving bevestigen ?",
		   "GCONFIG_SUBSCRIPTION_CONFIRM_UNSUB" => "Abonnees moeten hun uitschrijving bevestigen ?",
		   
		   "GCONFIG_SUBSCRIPTION_MODERATE" => "Inschrijving wordt gemodereerd ?",

		   "GCONFIG_SAVE_BTN" => "Bewaar deze parameters",
		   "GCONFIG_SUCCESSFULLY_SAVED" => "Globale parameters sucessvol bewaard",
		   //ERROR
		   "ERROR_SQL" => "Database Error :<br/>%s<br/>",
		   "ERROR_SQL2" => "SQL Error :<br/>%s<br/>",
	
		   "ERROR_DBCONNECT" => "Kon niet met de Database verbinding maken.",
		   "ERROR_DBCONNECT_2" => "Kon niet met de Database verbinden: <br />%s.<br />Kijk uw Database parameters na.",
		   "ERROR_DBCONNECT_3" => "Connectie Fout",

		   
		   "ERROR_FLUSHING_TEMP_TABLE" => "Fout bij het ledigen van de tijdelijke tabel (%s)",
		   "ERROR_SAVING_SETTINGS" => "Fout bij het bewaren van de parameters: %s",
		   "ERROR_DELETING_NEWSLETTER" => "Fout bij het wissen van deze nieuwsbrief",
		   "ERROR_CHOOSE_ANOTHER_NEWSLETTER" => "Kies een andere nieuwsbrief, of <a href=\"index.php?page=home&action=create\">maak er een nieuwe aan</a>",
		   "ERROR_NO_NEWSLETTER_CREATE_ONE" => "Gelieve een <a href=\"index.php?page=newsletterconf&action=create\">nieuwe nieuwsbrief aan te maken</a>",

		   "ERROR_NO_SUCH_NEWSLETTER" => "Niewsbrief niet gevonden",
		   "ERROR_ADDING_SUBSCRIBER" => "Fout bij het toevoegen van %s",
		   "ERROR_ALREADY_SUBSCRIBER" => "%s is reeds in de abonneelijst opgenomen",
		   "ERROR_SUPPLY_VALID_EMAIL" => "Gelieve een geldig email adres op te geven",
		   "ERROR_DELETING_SUBSCRIBER" => "Fout bij het wissen van dit adres: %s",
		   "ERROR_NO_EMAIL_IN_FILE"=>"Geen email adres in dit bestand",
		   "ERROR_IMPORT_FILE_MISSING" => "Geef een bestan op om te importeren",
		   "ERROR_DELETING_TEMP" => "Four bij het wissen van dit adres: %s",
		   "ERROR_UNABLE_TO_SEND" => "Geen abonnees, onmogelijk een, nieuwe boodschap te maken",
		   "ERROR_ALL_FIELDS_REQUIRED" => "Alle velden zijn verplicht",
		   "ERROR_SENDING" => "Fout bij het verzenden van deze boodschap",
		   "ERROR_DELETING_ARCHIVE" => "Fout bij het wissen van dit archief",
		   "ERROR_UNKNOWN" => "Onbekende fout",
                   "ERROR_SENDING_CONFIRM_MAIL" => "Fout bij het verzenden van de bevestigingsmail",
	
		   //MSG
		   "NO_SUBSCRIBER" => "Geen abonnee in de database",
		   "NO_ARCHIVE" => "Geen archief voor deze nieuwsbrief",
		   "NEWSLETTER_NOT_YET" => "Nog geen niewsbrief aangemaakt.",
		   "BACK" => "Terug",
		   "EXAMPLE" => "Voorbeeld",
		   "DONE" => "Gedaan",
		   
		   
		   //install
		   "INSTALL_TITLE" => "phpMyNewsletter installatie",
		   "INSTALL_LANGUAGE" => "Taal",
		   "INSTALL_LANGUAGE_LABEL" => "Kies uw voorkeur taal",
		   "INSTALL_DB_TYPE" => "Database Type",
		   "INSTALL_DB_TITLE" => "Database",
		   "INSTALL_DB_HOSTNAME" => "Hostnaam",
		   "INSTALL_DB_NAME" => "Database naam",
		   "INSTALL_DB_LOGIN"=> "Login",
		   "INSTALL_DB_PASS" => "Paswoord",
		   "INSTALL_DB_TABLE_PREFIX" => "Database tabellen prefix",
		   "INSTALL_DB_CREATE_DB" => "Creatie van database ?",
		   "INSTALL_DB_CREATE_TABLES" => "Creatie van database tabellen ?",
		   "INSTALL_GENERAL_SETTINGS" => "Gemeenschappelijke parameters",
		   "INSTALL_ADMIN_PASS" => "Admin paswoord",
		   "INSTALL_ADMIN_BASEURL"=> "Basis Url",
		   "INSTALL_ADMIN_PATH_TO_PMNL" => "Pad naar phpMyNewsletter",
		   "INSTALL_ADMIN_NAME" => "Admin naam",
		   "INSTALL_ADMIN_EMAIL" => "Admin email adres",
		   
		   "INSTALL_MESSAGE_SENDING_TITLE" => "Verzenden boodschappen",
		   "INSTALL_MESSAGE_SENDING_LOOP" => "Nieuwsbrief wordt verzonden in cyclus van %s boodschappen.",
		   "INSTALL_VALIDATION_PERIOD" => "Abonnees hebben %s dagen om hun inschrijving te bevestigen.",
		   "INSTALL_SENDING_METHOD" => "Methode van verzenden",
		   "INSTALL_PHP_MAIL_FONCTION" => "php mail() functie (default)",
		   "INSTALL_PHP_MAIL_FONCTION_ONLINE" => "Online.net : email()",

		   "INSTALL_SMTP_HOST" => "SMTP host",
		   "INSTALL_SMTP_AUTH_NEEDED" => "Authentificatie nodig ?",
		   "INSTALL_SMTP_USERNAME" => "Gebruikersnaam",
		   "INSTALL_SMTP_PASSWORD" => "Paswoord",
		   
		   "INSTALL_SUBSCRIPTION_TITLE" => "Inschrijven / Uitschrijven",
		   "INSTALL_SUB_CONFIRM" => "Abonnees moeten hun inschrijving bevestigen ?",
		   "INSTALL_UNSUB_CONFIRM"=>"Abonnees moeten hun uitschrijving bevestigen ?",
		   
		   "INSTALL_SAVE_CREATE_DB" => "Creatie van %s database",
		   "INSTALL_SAVE_CREATE_TABLE" => "Creatie van %s tabel",
		   "INSTALL_SAVE_CONFIG" => "Bewaren van parameters",
		   "INSTALL_SAVE_CONFIG_FILE" => "Bewaren van parameterbestand",
		   "INSTALL_UNABLE_TO_SAVE_CONFIG_FILE" => "Onmogelijk parameterbestand te bewaren.",
		   "INSTALL_CONFIG_MANUALLY" => "Helieve volgende lijnen te kopiëren en in het parameterbestand (<em>include/config.php</em>) te plakken.",
		   "INSTALL_FINISHED" => "Einde",
		   
		   
		   "LOGOUT_DONE" => "U bent uitgelogd",
		   "LOGOUT_ERROR"=> "Fout bij het iotloggen",
		   "LOGOUT_TITLE" => "Logout",
		   "LOGOUT_BACK" => "Terug",


		   //subscription.php
		   "SUBSCRIPTION_TITLE" => "Nieuwsbrief inschrijving",
		   "SUBSCRIPTION_SEND_CONFIRM_MESSAGE" => "Confirmatie-aanvraag werd per email verstuurd.",
		   "SUBSCRIPTION_ALREADY_SUBSCRIBER" => "U bent reeds geabonneerd op deze nieuwsbrief.",
		   "SUBSCRIPTION_CONFIRMATION" => "Bevestig uw inschrijving",
                   "SUBSCRIPTION_FINISHED" => "Inschrijving succesvol",


		   "SUBSCRIPTION_MAIL_BODY" => "Ga naar de volgende URL om uw inschrijving te bevestigen",
		   "SUBSCRIPTION_UNSUBSCRIBE_LINK" => "Klik op de volgende link om uit te schrijven",
	
		   "SUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS" => "Fout: onbekend email adres",


		   "UNSUBSCRIPTION_TITLE" => "Uitschrijven van nieuwsbrief",	
                   "UNSUBSCRIPTION_MAIL_BODY" => "Om het uitschrijven te bevestigen, klik op de volgende link",
		   "UNSUBSCRIPTION_SEND_CONFIRM_MESSAGE" => "Bevestigingsaanvraag per email verstuurd.",
		   "UNSUBSCRIPTION_CONFIRMATION" => "Bevestig uw uitschrijving",

		   "UNSUBSCRIPTION_FINISHED" => "Uitgeschreven",


		   "NEWSLETTER_TITLE" => "Nieuwsbrieven",
                   "UNSUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS" => "Fout: Onbekend email adres",	
		   "SEND_UNSUBSCRIPTION_LINK" => "\r\n\r\nUitschrijven :\r\n",

		   
		   );



?>
