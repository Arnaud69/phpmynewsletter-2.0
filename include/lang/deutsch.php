<?

function translate($s, $i="") {
    global $lang_array;

    if(!isset($lang_array['deutsch'][$s]))
      return ("[Translation required] : $s");

    if($lang_array['deutsch'][$s]!="") {
	if($i == "") return $lang_array['deutsch'][$s];
        $sprint = $lang_array['deutsch'][$s];
	return sprintf("$sprint" , $i);
    }
    else return ("[Translation required] : $s");
}


$lang_array['deutsch'] = array(
		   //BTN
		   "OK_BTN" => "OK",
		   "YES" => "Ja",
		   "NO" => "Nein",
		   
                   "BACK" => "zurueck",

		   //ARCHIVE
		   "ARCHIVE_TITLE" => "Archiv",
		   "ARCHIVE_CHOOSE" => "Waehle einen Newsletter",
		   "ARCHIVE_SUBJECT" => "Betreff",
		   "ARCHIVE_DATE" => "Datum",
		   "ARCHIVE_FORMAT" => "Format",
		   "ARCHIVE_DISPLAY" => "Zeige diese Message",
		   "ARCHIVE_BROWSE" => "Newsletter Archiv anzeigen",
		   "ARCHIVE_DELETE" => "Loesche dieses Archiv",
		   "ARCHIVE_DELETE_TITLE" => "Archiv wird geloescht",
		   "ARCHIVE_DELETED" => "Archiv erfolgreich geloescht",
		   "ARCHIVE_NOT_FOUND" => "Kein Archiv gefunden",
		   
		   //INDEX
		   "PHPMYNEWSLETTER_TITLE" => "phpMyNewsletter",
		   "EMAIL_ADDRESS" => "Email Adresse",
		   "EMAIL_ADDRESS_NOT_VALID" => "Diese Emailadresse ist ungueltig",
		   "NEWSLETTER_SUBSCRIPTION" => "Eintragen",
		   "NEWSLETTER_UNSUBSCRIPTION" => "Austragen",
		   "AVAILABLE_NEWSLETTER" => "verfuegbare Newsletters",
		   
		   //ADMIN LOGIN admin/login.php
		   "LOGIN_TITLE" => "Login",
		   "LOGIN_PLEASE_ENTER_PASSWORD" => "Passwort fuer den Zugriff zum Admin Bereich",
		   "LOGIN_PASSWORD" => "Passwort",
		   "LOGIN" => "Login !",
		   "LOGIN_BAD_PASSWORD" => "Falsches Passwort !",
		   
		   
		   //MENU
		   "MENU_SUBSCRIBERS"=> "Empfaenger",
		   "MENU_COMPOSE" => "Erstellen",
		   "MENU_ARCHIVES" => "Archive",
		   "MENU_NEWSLETTER" => "Newsletter Einstellungen",
		   "MENU_CONFIG" => "Globale Einstellungen",
		   "MENU_LOGOUT" => "Logout",
		   
		   "SELECTED_NEWSLETTER" => "Ausgewaehlter Newsletter",
		   "NEWSLETTER_TOTAL_SUBSCRIBER" => "Empfaenger",
		   "NEWSLETTER_TOTAL_SUBSCRIBERS" => "Empfaenger",
		   
		   
		   //ADMIN NEWSLETTER
		   "NEWSLETTER_CHOOSE" => "Waehle einen Newsletter aus",
		   "NEWSLETTER_ACTION" => "Newsletter Aktion",
		   "NEWSLETTER_NEW" => "Erstelle einen neuen Newsletter",
		   "NEWSLETTER_DEL" => "Loesche '%s'",
		   "NEWSLETTER_SETTINGS" => "Newsletter Einstellungen",
		   "NEWSLETTER_NAME" => "Name des Newsletter",
		   "NEWSLETTER_FROM_ADDR" => "Email Adresse des Versenders",
		   "NEWSLETTER_FROM_NAME" => "Name des Senders",
		   "NEWSLETTER_SUBJECT" => "Betreff der Message",
		   "NEWSLETTER_HEADER" => "Message Header",
		   "NEWSLETTER_FOOTER" => "Message Footer",
		   "NEWSLETTER_SUB_MSG_SUBJECT" => "Betreff bei Eintragungsmail",
		   "NEWSLETTER_SUB_MSG_BODY" => "Empfaenger Mail",
		   "NEWSLETTER_WELCOME_MSG_SUBJECT" => "Willkommen Message Betreff",
		   "NEWSLETTER_WELCOME_MSG_BODY" => "Willkommen Message",
		   "NEWSLETTER_UNSUB_MSG_SUBJECT" => "Betreff bei Mail zur Loeschung",
		   "NEWSLETTER_UNSUB_MSG_BODY" => "Mail zu Loeschung",
		   "NEWSLETTER_SAVE_SETTINGS" => "Sichern dieser Einstellungen",
		   "NEWSLETTER_SETTINGS_SAVED" => "Einstellungen gesichert",
		   "NEWSLETTER_CREATE" => "Erstelle einen Newsletter",
		   "NEWSLETTER_SAVE_NEW" => "Erstelle diesen Newsletter",
		   "NEWSLETTER_DELETED" => "Newsletter erfolgreich geloescht",
		   "NEWSLETTER_DELETE_WARNING" => "Loesche alle Daten fuer diesen Newsletter",
		   "NEWSLETTER_SETTINGS_CREATED" => "Newsletter erfolgreich erstellt",
		   "NEWSLETTER_DEFAULT_HEADER" => "========= HEADER =========\n".
						  "Schreibe was immer Du willst.\n".
						  "Dieser Text wird in jede Mail am Anfang eingefuegt",
		   
		   "NEWSLETTER_DEFAULT_FOOTER" => "======== FOOTER =======\n".
						  "Hier kannst Du irgendwas eintragen",  
		   "NEWSLETTER_SUB_DEFAULT_SUBJECT" => "Bitte bestaetigen Sie Ihre Anmeldung",
                   "NEWSLETTER_SUB_DEFAULT_BODY" => "Sie haben sich zum Newsletter eingetragen oder wurden eingetragen, bitte folgen Sie den Anweisungen.", 
		   
                   "NEWSLETTER_WELCOME_DEFAULT_SUBJECT" => "Willkommen bei diesem Newsletter",
   		   "NEWSLETTER_WELCOME_DEFAULT_BODY" => "Willkommen bei diesem Newsletter !",		   
		   "NEWSLETTER_UNSUB_DEFAULT_SUBJECT" => "Bitte bestaetigen Sie Ihre Loeschung",
                   "NEWSLETTER_UNSUB_DEFAULT_BODY" => "Sie haben sich zum Newsletter ausgetragen oder wurden ausgetragen, bitte folgen Sie den Anweisungen.", 


		   //SUBSCRIBER
		   "SUBSCRIBER_ADD_TITLE" => "Zufuegen eines Empfaengers",
		   "SUBSCRIBER_ADD_BTN" => "Emailadresse zufuegen",
		   "SUBSCRIBER_ADDED" => "%s erfolgreich dazugefuegt",
		   
		   "SUBSCRIBER_IMPORT_TITLE" => "Import eine Liste von Emailadressen",
		   "SUBSCRIBER_IMPORT_BTN" => "Import",
		   "SUBSCRIBER_IMPORT_HELP" => "Du kannst Emailadressen aus einer Datei importieren.<br />Die Daten muessen dieses Format haben:<br/>adress1@domain.com<br />adress2@domain.com<br/>adress3@domain.com",
		   
		   "SUBSCRIBER_DELETE_TITLE" => "Loesche einen Empfaenger",
		   "SUBSCRIBER_DELETE_BTN" => "Loesche diese Email Adresse",
		   "SUBSCRIBER_DELETED" => "Empfaenger erfolgreich geloescht",
		   
		   "SUBSCRIBER_EXPORT_TITLE" => "Exportieren Empfaenger",
		   "SUBSCRIBER_EXPORT_BTN" => "Exportiere jetzt",
		   
		   "SUBSCRIBER_TEMP_TITLE" => "Bestaetigung wird noch erwartet",
		   "SUBSCRIBER_TEMP_BTN" => "Loesche diese Email Adresse",
		   "SUBSCRIBER_TEMP_DELETED" => "Email Adresse erfolgreich geloescht",
		   
		   
		   //COMPOSE 
		   "COMPOSE_NEW" => "Eine neue Message erstellen",
		   "COMPOSE_SUBJECT" => "Betreff",
		   "COMPOSE_FORMAT" => "Format",
		   "COMPOSE_FORMAT_TEXT" => "Plain text",
		   "COMPOSE_FORMAT_HTML" => "HTML",
		   "COMPOSE_FORMAT_HTML_NOTICE" => "(nur HTMLcode zwischen folgenden <em>&lt;body&gt;&lt;/body&gt;<em> tags) verwenden",
		   "COMPOSE_PREVIEW" => "Message Vorschau",
		   "COMPOSE_RESET" => "Reset",
		   "COMPOSE_PREVIEW_TITLE" => "Message Vorschau",
		   "COMPOSE_BACK" => "Zurueck",
		   "COMPOSE_SEND" => "Sende diese Message",
		   "COMPOSE_SENDING" => "Sende Message ...",
		   "COMPOSE_SENT" => "Message erfolgreich versendet",


		   //GLOBAL CONFIG
		   "GCONFIG_TITLE" => "Globale Einstellungen",
		   "GCONFIG_DB_TITLE"=> "Database Einstellungen",
		   "GCONFIG_DB_HOST" => "Hostname",
		   "GCONFIG_DB_LOGIN" => "User Login",
		   "GCONFIG_DB_DBNAME" => "Database Name",
		   "GCONFIG_DB_PASSWD" => "User Passwort",
		   "GCONFIG_DB_CONFIG_TABLE" => "Konfigurations Tabellen",
		   "GCONFIG_DB_TABLE_MAIL" => "Empfaenger Emails gesichert in",
		   "GCONFIG_DB_TABLE_TEMPORARY" => "Temp Tabellen",
		   "GCONFIG_DB_TABLE_NEWSCONFIG" => "Newsletter Konfigurationstabellen",
		   "GCONFIG_DB_TABLE_ARCHIVES" => "Archive gesichert in",
		   "GCONFIG_DB_TABLE_SUBMOD" => "Subscription waiting for moderation are stored in",

		   
		   "GCONFIG_DB_CONFIG_UNWRITABLE" => "With write permissions on %s, More database settings would be available.",
		   "GCONFIG_MISC_TITLE" => "Misc Einstellungen",
		   "GCONFIG_MISC_ADMIN_PASSW" => "Admin Passwort",
		   "GCONFIG_MISC_BASE_URL" => "Base URL",
		   "GCONFIG_MISC_BASE_PATH" => "Pfad zu phpMyNewsletter",
		   "GCONFIG_MISC_LANGUAGE" => "Sprache",

		   "GCONFIG_MESSAGE_HANDLING_TITLE" => "Message Handling",
		   "GCONFIG_MESSAGE_ADMIN_NAME" => "Default Name des Versenders (<i>From:</i> field)",
		   "GCONFIG_MESSAGE_ADMIN_MAIL" => "Default Email Adresse des Versenders",
		   "GCONFIG_MESSAGE_NUM_LOOP" => "Anzahl der zu sendenden Message fuer jeden Lauf",
		   "GCONFIG_MESSAGE_SEND_METHOD" => "Versand Methode",
                   "GCONFIG_MESSAGE_SEND_METHOD_FUNCTION" => "PHP mail() function",
		   "GCONFIG_MESSAGE_SMTP_HOST" => "SMTP server hostname",
		   "GCONFIG_MESSAGE_SMTP_AUTH" => "SMTP authentification  needed ?",
		   "GCONFIG_MESSAGE_SMTP_LOGIN" =>"SMTP username",
		   "GCONFIG_MESSAGE_SMTP_PASSWORD" => "SMTP password",

		   "GCONFIG_SUBSCRIPTION_TITLE" => "Mitgliedschaft",
		   "GCONFIF_SUBSCRIPTION_VALIDATION_TIMEOUT" => "Empfaenger haben %s Tag(e)Zeit fuer Ihre Bestaetigungmail",
		   "GCONFIG_SUBSCRIPTION_CONFIRM_SUB" => "Muessen Empfaenger ihre Teilnahme bestaetigen ?",
		   "GCONFIG_SUBSCRIPTION_CONFIRM_UNSUB" => "Muessen Empfaenger das loeschen bestaetigen ?",
		   
		   "GCONFIG_SUBSCRIPTION_MODERATE" => "Subscriptions are moderated ?",

		   "GCONFIG_SAVE_BTN" => "Sicherung der Einstellungen",
		   "GCONFIG_SUCCESSFULLY_SAVED" => "Globale Einstellung erfolgreich gesichert",
		   //ERROR
		   "ERROR_SQL" => "Database Error :<br/>%s<br/>",
		   "ERROR_SQL2" => "SQL Error :<br/>%s<br/>",
	
		   "ERROR_DBCONNECT" => "Keine Verbindung zur Datenbank moeglich.",
		   "ERROR_DBCONNECT_2" => "Keine Verbindung zur DAtenbank: <br />%s.<br />ueberpruefen Sie die Datenbankeinstellungen.",
		   "ERROR_DBCONNECT_3" => "Connection Error",

		   
		   "ERROR_FLUSHING_TEMP_TABLE" => "Fehler waehrend dem loeschen der temporaeren Tabellen (%s)",
		   "ERROR_SAVING_SETTINGS" => "Fehler waehrend dem sichern der Einstellungen: %s",
		   "ERROR_DELETING_NEWSLETTER" => "Fehler waehrend des loeschens dieses Newsletter",
		   "ERROR_CHOOSE_ANOTHER_NEWSLETTER" => "Bitte waehle einen anderen Newsletter, oder <a href=\"index.php?page=home&action=create\">erstelle einen neuen</a>",
		   "ERROR_NO_NEWSLETTER_CREATE_ONE" => "Bitte <a href=\"index.php?page=newsletterconf&action=create\">erstelle einen neuen Newsletter</a>",

		   "ERROR_NO_SUCH_NEWSLETTER" => "Newsletter nicht gefunden",
		   "ERROR_ADDING_SUBSCRIBER" => "Fehler beim zufuegen %s",
		   "ERROR_ALREADY_SUBSCRIBER" => "%s ist bereits in der Empfaengerliste",
		   "ERROR_SUPPLY_VALID_EMAIL" => "Bitte verwende eine richtige Emailadresse",
		   "ERROR_DELETING_SUBSCRIBER" => "Fehler waehrend dem loeschen der Emailadresse: %s",
		   "ERROR_NO_EMAIL_IN_FILE"=>"Keine Emailadresse in dieser Datei",
		   "ERROR_IMPORT_FILE_MISSING" => "Bitte eine Datei fuer den Import angeben",
 	           "ERROR_IMPORT_TMPDIR_NOT_WRITABLE" => "Kann nicht in admin/import schreiben !",
		   "ERROR_DELETING_TEMP" => "Fehler waehrend dem loeschen dieser Emailadresse: %s",
		   "ERROR_UNABLE_TO_SEND" => "Keine Empfaenger, es kann kein Newsletter erstellt werden",
		   "ERROR_ALL_FIELDS_REQUIRED" => "Alle Fleder werden benoetigt",
		   "ERROR_SENDING" => "Fehler waehrend dem senden diese Message",
		   "ERROR_DELETING_ARCHIVE" => "Fehler waehrend dem loeschen dieses Archives",
		   "ERROR_UNKNOWN" => "Unbekannter Fehler",
                   "ERROR_SENDING_CONFIRM_MAIL" => "Fehler beim Senden der Bestaetigungsmail",
		   "ERROR_LOG_CREATE" => "Es ist nicht moeglich ein Logfile anzulegen.<br/>".
					  "Bitte Zugriffsrechte von admin/logs ueberpruefen",

		   //MSG
		   "NO_SUBSCRIBER" => "Kein Empfaenger in der Datenbank",
		   "NO_ARCHIVE" => "Keine Newsletter im Archiv",
		   "NEWSLETTER_NOT_YET" => "Newsletter noch nicht konfiguriert.",
		   "BACK" => "Zurueck",
		   "EXAMPLE" => "Beispiel",
		   "DONE" => "Erledigt",
		   
		   
		   //install
		   "INSTALL_TITLE" => "phpMyNewsletter Installation",
		   "INSTALL_LANGUAGE" => "Sprache",
		   "INSTALL_LANGUAGE_LABEL" => "Bitte die Sprache auswaehlen",
		   "INSTALL_DB_TYPE" => "Database Type",
		   "INSTALL_DB_TITLE" => "Database",
		   "INSTALL_DB_HOSTNAME" => "Hostname",
		   "INSTALL_DB_NAME" => "Database name",
		   "INSTALL_DB_LOGIN"=> "Login",
		   "INSTALL_DB_PASS" => "Passwort",
		   "INSTALL_DB_TABLE_PREFIX" => "Datenbank Tabellen Prefix",
		   "INSTALL_DB_CREATE_DB" => "Erstelle Datenbank?",
		   "INSTALL_DB_CREATE_TABLES" => "Erstelle Datenbanktabellen?",
		   "INSTALL_GENERAL_SETTINGS" => "Allgemeine Einstellungen",
		   "INSTALL_ADMIN_PASS" => "Admin Passwort",
		   "INSTALL_ADMIN_BASEURL"=> "Basis Url",
		   "INSTALL_ADMIN_PATH_TO_PMNL" => "Pfad zu phpMyNewsletter",
		   "INSTALL_ADMIN_NAME" => "Admin name",
		   "INSTALL_ADMIN_EMAIL" => "Admin Emailadresse",
		   
		   "INSTALL_MESSAGE_SENDING_TITLE" => "Message wird gesendet",
		   "INSTALL_MESSAGE_SENDING_LOOP" => "Newsletter wird gesendet in einer Runde von %s Messages.",
		   "INSTALL_VALIDATION_PERIOD" => "Empfaenger haben %s Tage Zeit fuer das senden ihrer Bestaetigungsmail .",
		   "INSTALL_SENDING_METHOD" => "Versandmethode",
		   "INSTALL_PHP_MAIL_FONCTION" => "php mail() function (default)",
		   "INSTALL_PHP_MAIL_FONCTION_ONLINE" => "Online.net specific email() php function",

		   "INSTALL_SMTP_HOST" => "SMTP host",
		   "INSTALL_SMTP_AUTH_NEEDED" => "Authentificaton needed ?",
		   "INSTALL_SMTP_USERNAME" => "Username",
		   "INSTALL_SMTP_PASSWORD" => "Password",
		   
		   "INSTALL_SUBSCRIPTION_TITLE" => "Eintragung / Loeschung",
		   "INSTALL_SUB_CONFIRM" => "Empfaenger muessen ihre Teilnahme bestaetigen ?",
		   "INSTALL_UNSUB_CONFIRM"=>"Empfaenger muessen ihre Loeschung bestaetigen ?",
		   
		   "INSTALL_SAVE_CREATE_DB" => "Erstellung %s Datenbank",
		   "INSTALL_SAVE_CREATE_TABLE" => "Erstellung %s Tabellen",
		   "INSTALL_SAVE_CONFIG" => "Sichere Konfiguration",
		   "INSTALL_SAVE_CONFIG_FILE" => "Sichere Konfigurationsdatei",
		   "INSTALL_UNABLE_TO_SAVE_CONFIG_FILE" => "Es ist nicht moeglich die Konfigurationsdatei zu sichern.",
		   "INSTALL_CONFIG_MANUALLY" => "Please cut and paste the lines below in the config file (<em>include/config.php</em>).",
		   "INSTALL_FINISHED" => "FERTIG",
		   
		   
		   "LOGOUT_DONE" => "Du bist ausgeloggt",
		   "LOGOUT_ERROR"=> "Fehler waehrend dem ausloggen",
		   "LOGOUT_TITLE" => "Logout",
		   "LOGOUT_BACK" => "Zurueck",


		   //subscription.php
		   "SUBSCRIPTION_TITLE" => "Newsletter Anmeldung",
		   "SUBSCRIPTION_SEND_CONFIRM_MESSAGE" => "Bestaetigungsmail zu Ihrer Anmeldung wurde gesendet.",
		   "SUBSCRIPTION_ALREADY_SUBSCRIBER" => "Sie sind bereits fuer den Newsletter angemeldet.",
		   "SUBSCRIPTION_CONFIRMATION" => "Bestaetigung der Teilnahme",
                   "SUBSCRIPTION_FINISHED" => "Teilnahme erfolgreich",


		   "SUBSCRIPTION_MAIL_BODY" => "Klicken Sie auf folgenden Link um Ihre Anmeldung zu bestaetigen",
		   "SUBSCRIPTION_UNSUBSCRIBE_LINK" => "Klicken Sie auf folgenden Link um sich auszutragen",
	
		   "SUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS" => "Fehler: Unbekannte Emailadresse",


		   "UNSUBSCRIPTION_TITLE" => "Newsletter Teilnahmeloeschung",	
                   "UNSUBSCRIPTION_MAIL_BODY" => "Zur Bestaetigung Ihrer Loeschung bitte auf folgenden Link klicken",
		   "UNSUBSCRIPTION_SEND_CONFIRM_MESSAGE" => "Bestaetigungsanforderung ueber Email gesendet.",
		   "UNSUBSCRIPTION_CONFIRMATION" => "Bestaetigen Sie ihre Loeschung",

		   "UNSUBSCRIPTION_FINISHED" => "Loeschung erledigt",


		   "NEWSLETTER_TITLE" => "Newsletters",
                   "UNSUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS" => "Fehler: Unbekannte Emailadresse",
			
		   "SEND_UNSUBSCRIPTION_LINK" => "\r\n\r\nUnsubscription :\r\n",

		   
		   );



?>
