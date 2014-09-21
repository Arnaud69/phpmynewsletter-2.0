<?

function translate($s, $i="") {
    global $lang_array;
    if(!isset($lang_array['dansk'][$s]))
            return ("[Translation required] : $s");
    
    if($lang_array['dansk'][$s]!="") {
	if($i == "") return $lang_array['dansk'][$s];
        $sprint = $lang_array['dansk'][$s];
	return sprintf("$sprint" , $i);
    }
    else return ("[Translation required] : $s");
}


$lang_array['dansk'] = array(
		   //BTN
		   "OK_BTN" => "OK",
		   "YES" => "Ja",
		   "NO" => "Nej",
		   
                   "BACK" => "Tilbage",

		   //ARCHIVE
		   "ARCHIVE_TITLE" => "Arkiv",
		   "ARCHIVE_CHOOSE" => "Vælg et nyhedsbrev",
		   "ARCHIVE_SUBJECT" => "Emne",
		   "ARCHIVE_DATE" => "Dato",
		   "ARCHIVE_FORMAT" => "Format",
		   "ARCHIVE_DISPLAY" => "Vis denne besked",
		   "ARCHIVE_BROWSE" => "Vis arkiv",
		   "ARCHIVE_DELETE" => "Fjern dette arkiv",
		   "ARCHIVE_DELETE_TITLE" => "Fjern arkiv",
		   "ARCHIVE_DELETED" => "Arkiv fjernet",
		   "ARCHIVE_NOT_FOUND" => "Intet arkiv fundet",
		   
		   //INDEX
		   "PHPMYNEWSLETTER_TITLE" => "Golf & Wine Nyhedsbrev",
		   "EMAIL_ADDRESS" => "Email-adresse",
		   "EMAIL_ADDRESS_NOT_VALID" => "Denne email-adresse er ikke gyldig",
		   "NEWSLETTER_SUBSCRIPTION" => "Tilmeld",
		   "NEWSLETTER_UNSUBSCRIPTION" => "Afmeld",
		   "AVAILABLE_NEWSLETTER" => "Tilgængelige nyhedsbreve",
		   
		   //ADMIN LOGIN admin/login.php
		   "LOGIN_TITLE" => "Log ind",
		   "LOGIN_PLEASE_ENTER_PASSWORD" => "Password kræves for administrationsdelen",
		   "LOGIN_PASSWORD" => "Password",
		   "LOGIN" => "Log ind!",
		   "LOGIN_BAD_PASSWORD" => "Forkert password!",
		   
		   
		   //MENU
		   "MENU_SUBSCRIBERS"=> "Abonnenter",
		   "MENU_COMPOSE" => "Opret",
		   "MENU_ARCHIVES" => "Arkiv",
		   "MENU_NEWSLETTER" => "Indstillinger for nyhedsbrev",
		   "MENU_CONFIG" => "Globale indstillinger",
		   "MENU_LOGOUT" => "Log ud",
		   
		   "SELECTED_NEWSLETTER" => "Valgt nyhedsbrev",
		   "NEWSLETTER_TOTAL_SUBSCRIBER" => "Abonnent",
		   "NEWSLETTER_TOTAL_SUBSCRIBERS" => "Abonnenter",
		   
		   //ADMIN NEWSLETTER
		   "NEWSLETTER_CHOOSE" => "Vælg et nyhedsbrev at administrere",
		   "NEWSLETTER_ACTION" => "Nyhedsbrev",
		   "NEWSLETTER_NEW" => "Opret nyt nyhedsbrev",
		   "NEWSLETTER_DEL" => "Fjern '%s'",
		   "NEWSLETTER_SETTINGS" => "Indstillinger for nyhedsbrev",
		   "NEWSLETTER_NAME" => "Nyhedsbrevets namn",
		   "NEWSLETTER_FROM_ADDR" => "Afsenderadresse",
		   "NEWSLETTER_FROM_NAME" => "Afsendernavn",
		   "NEWSLETTER_SUBJECT" => "Emne",
		   "NEWSLETTER_HEADER" => "Sidehovede",
		   "NEWSLETTER_FOOTER" => "Sidefod",
		   "NEWSLETTER_SUB_MSG_SUBJECT" => "Emne på tilmeldingsbesked",
		   "NEWSLETTER_SUB_MSG_BODY" => "Tilmeldingsbesked",
		   "NEWSLETTER_WELCOME_MSG_SUBJECT" => "Emne på velkomstbesked",
		   "NEWSLETTER_WELCOME_MSG_BODY" => "Velkomstbesked",
		   "NEWSLETTER_UNSUB_MSG_SUBJECT" => "Emne på afmeldingsbesked",
		   "NEWSLETTER_UNSUB_MSG_BODY" => "Afmeldingsbesked",
		   "NEWSLETTER_SAVE_SETTINGS" => "Gem indstillinger",
		   "NEWSLETTER_SETTINGS_SAVED" => "Indstillinger gemt",
		   "NEWSLETTER_CREATE" => "Opret nyhedsbrev",
		   "NEWSLETTER_SAVE_NEW" => "Opret dette nyhedsbrev",
		   "NEWSLETTER_DELETED" => "Nyhedsbrev fjernet",
		   "NEWSLETTER_DELETE_WARNING" => "Fjern alle data relateret til nyhedsbrevet",
		   "NEWSLETTER_SETTINGS_CREATED" => "Nyhedsbrev oprettet",
		   "NEWSLETTER_DEFAULT_HEADER" => "========= SIDEHOVEDE =========\n".
						  "Sidehovedetekst.\n".
						  "Dette bliver teksten på hvert sidehovede.",
		   
		   "NEWSLETTER_DEFAULT_FOOTER" => "======== SIDEFOD =======\n".
						  "Skriv en besked her.",  
		   "NEWSLETTER_SUB_DEFAULT_SUBJECT" => "Bekræft venligst din tilmelding",
                   "NEWSLETTER_SUB_DEFAULT_BODY" => "Nogen, sandsynligvis Dem selv, har tilmeldt Deres email-adresse til dette nyhedsbrev. Følg venligst instruktionerne nedenfor.", 
		   
                   "NEWSLETTER_WELCOME_DEFAULT_SUBJECT" => "Velkommen til dette nyhedsbrev",
   		   "NEWSLETTER_WELCOME_DEFAULT_BODY" => "Velkommen til dette nyhedsbrev!",		   
		   "NEWSLETTER_UNSUB_DEFAULT_SUBJECT" => "Bekræft venligst afmelding af nyhedsbrevet",
                   "NEWSLETTER_UNSUB_DEFAULT_BODY" => "Nogen, sandsynligvis Dem selv, har afmeldt Deres email-adresse fra dette nyhedsbrev. Følg venligst instruktionerne nedenfor.", 


		   //SUBSCRIBER
		   "SUBSCRIBER_ADD_TITLE" => "Tilføj abonnent",
		   "SUBSCRIBER_ADD_BTN" => "Tilføj denne email-adresse",
		   "SUBSCRIBER_ADDED" => "%s tilføjet",
		   
		   "SUBSCRIBER_IMPORT_TITLE" => "Importer liste med email-adresser",
		   "SUBSCRIBER_IMPORT_BTN" => "Importer",
		   "SUBSCRIBER_IMPORT_HELP" => "Du kan importere en liste med email-adresser fra en fil.<br />Filen skal overholde dette format:<br/>adresse1@domæne.com<br />adresse2@domæne.com<br/>adresse3@domæne.com",
		   
		   "SUBSCRIBER_DELETE_TITLE" => "Fjern abonnent",
		   "SUBSCRIBER_DELETE_BTN" => "Fjern denne email-adresse",
		   "SUBSCRIBER_DELETED" => "Abonnent fjernet",
		   
		   "SUBSCRIBER_EXPORT_TITLE" => "Exporter abonnenter",
		   "SUBSCRIBER_EXPORT_BTN" => "Exporter nu",
		   
		   "SUBSCRIBER_TEMP_TITLE" => "Abonnent venter",
		   "SUBSCRIBER_TEMP_BTN" => "Fjern denne email-adresse",
		   "SUBSCRIBER_TEMP_DELETED" => "Email-adresse fjernet",
		   
		   
		   //COMPOSE 
		   "COMPOSE_NEW" => "Opret nyt brev",
		   "COMPOSE_SUBJECT" => "Emne",
		   "COMPOSE_FORMAT" => "Format",
		   "COMPOSE_FORMAT_TEXT" => "Almindelig text",
		   "COMPOSE_FORMAT_HTML" => "HTML",
		   "COMPOSE_FORMAT_HTML_NOTICE" => "(Indsæt kun koden mellem <em>&lt;body&gt;&lt;/body&gt;<em> tags'ene)",
		   "COMPOSE_PREVIEW" => "Se",
		   "COMPOSE_RESET" => "Nulstil",
		   "COMPOSE_PREVIEW_TITLE" => "Se",
		   "COMPOSE_BACK" => "Tilbage",
		   "COMPOSE_SEND" => "Send dette nyhedsbrev",
		   "COMPOSE_SENDING" => "Sender nyhedsbrev...",
		   "COMPOSE_SENT" => "Nyhedsbrev afsendt",


		   //GLOBAL CONFIG
		   "GCONFIG_TITLE" => "Globale Indstillinger",
		   "GCONFIG_DB_TITLE"=> "Databaseindstillinger",
		   "GCONFIG_DB_HOST" => "Hostnavn",
		   "GCONFIG_DB_LOGIN" => "Brugernavn",
		   "GCONFIG_DB_DBNAME" => "Databasenavn",
		   "GCONFIG_DB_PASSWD" => "Password",
		   "GCONFIG_DB_CONFIG_TABLE" => "Konfigurationstabel",
		   "GCONFIG_DB_TABLE_MAIL" => "Abonnentadresser gemmes i",
		   "GCONFIG_DB_TABLE_TEMPORARY" => "Midlertidig tabel",
		   "GCONFIG_DB_TABLE_NEWSCONFIG" => "Tabel for nyhedsbrevskonfiguration",
		   "GCONFIG_DB_TABLE_ARCHIVES" => "Arkivtabel",
		   "GCONFIG_DB_TABLE_SUBMOD" => "Ventende abonnenter gemmes i",

		   
		   "GCONFIG_DB_CONFIG_UNWRITABLE" => "Med skriverettigheder på %s bliver flere databaseindstillinger tilgængelige",
		   "GCONFIG_MISC_TITLE" => "Diverse indstillinger",
		   "GCONFIG_MISC_ADMIN_PASSW" => "Administratorpassword",
		   "GCONFIG_MISC_BASE_URL" => "Basis-URL",
		   "GCONFIG_MISC_BASE_PATH" => "Søgesti til phpMyNewsletter",
		   "GCONFIG_MISC_LANGUAGE" => "Sprog",

		   "GCONFIG_MESSAGE_HANDLING_TITLE" => "Beskedhåndtering",
		   "GCONFIG_MESSAGE_ADMIN_NAME" => "Standard afsendernavn (<i>Fra:</i> feltet)",
		   "GCONFIG_MESSAGE_ADMIN_MAIL" => "Standard email-adresse for afsender",
		   "GCONFIG_MESSAGE_NUM_LOOP" => "Antal beskeder at sende i hvert loop",
		   "GCONFIG_MESSAGE_SEND_METHOD" => "Afsendelsesmetode",
                   "GCONFIG_MESSAGE_SEND_METHOD_FUNCTION" => "PHP mail() funktion",
		   "GCONFIG_MESSAGE_SMTP_HOST" => "SMTP server hostnavn",
		   "GCONFIG_MESSAGE_SMTP_AUTH" => "SMTP authentication nødvendig",
		   "GCONFIG_MESSAGE_SMTP_LOGIN" =>"SMTP brugernavn",
		   "GCONFIG_MESSAGE_SMTP_PASSWORD" => "SMTP password",

		   "GCONFIG_SUBSCRIPTION_TITLE" => "Abonnement",
		   "GCONFIF_SUBSCRIPTION_VALIDATION_TIMEOUT" => "Abonnenter har %s dag(e) til at bekræfte tilmelding",
		   "GCONFIG_SUBSCRIPTION_CONFIRM_SUB" => "Behøver abonnenter at bekræfte tilmelding?",
		   "GCONFIG_SUBSCRIPTION_CONFIRM_UNSUB" => "Behøver abonnenter at bekræfte afmelding?",
		   
		   "GCONFIG_SUBSCRIPTION_MODERATE" => "Er abonnenter modererede?",

		   "GCONFIG_SAVE_BTN" => "Gem indstillinger",
		   "GCONFIG_SUCCESSFULLY_SAVED" => "Globala indstillinger gemt",
		   //ERROR
		   "ERROR_SQL" => "Databasefejl:<br/>%s<br/>",
		   "ERROR_SQL2" => "SQL-fejl:<br/>%s<br/>",
	
		   "ERROR_DBCONNECT" => "Kan ikke forbinde til databasen.",
		   "ERROR_DBCONNECT_2" => "Kan ikke forbinde til databasen: <br />%s.<br />Kontroller indtillingerne",
		   "ERROR_DBCONNECT_3" => "Forbindelsesfejl",


		   
		   "ERROR_FLUSHING_TEMP_TABLE" => "Fejl ved rensning af temporer tabel (%s)",
		   "ERROR_SAVING_SETTINGS" => "Fejl ved lagring af indstillinger: %s",
		   "ERROR_DELETING_NEWSLETTER" => "Fejl ved fjernelse af dette nyhedsbrev",
		   "ERROR_CHOOSE_ANOTHER_NEWSLETTER" => "Vælg venligst andet nyhedsbrev, eller <a href=\"index.php?page=home&action=create\">opret nyt</a>",
		   "ERROR_NO_NEWSLETTER_CREATE_ONE" => "Venligst <a href=\"index.php?page=newsletterconf&action=create\">opret nyt nyhedsbrev</a>",

		   "ERROR_NO_SUCH_NEWSLETTER" => "Nyhedsbrevet findes ikke",
		   "ERROR_ADDING_SUBSCRIBER" => "Fejl ved tilføjelse af adresse %s",
		   "ERROR_ALREADY_SUBSCRIBER" => "%s er allerede abonnent",
		   "ERROR_SUPPLY_VALID_EMAIL" => "Opgiv venligst en gyldig email-adresse",
		   "ERROR_DELETING_SUBSCRIBER" => "Fejl ved fjernelse af adresse: %s",
		   "ERROR_NO_EMAIL_IN_FILE"=>"Ingen email-adresse(r) i denne fil",
		   "ERROR_IMPORT_FILE_MISSING" => "Specificer hvilken fil som skal importeres",
		   "ERROR_DELETING_TEMP" => "Fejl ved fjernelse af adressen: %s",
		   "ERROR_UNABLE_TO_SEND" => "Ingen abonnenter, kan inte oprette ny meddelelse",
		   "ERROR_ALL_FIELDS_REQUIRED" => "Alle felter skal være udfyldt",
		   "ERROR_SENDING" => "Fejl ved afsendelse af denne meddelelse",
		   "ERROR_DELETING_ARCHIVE" => "Fejl ved fjernelse af dette arkiv",
		   "ERROR_UNKNOWN" => "Ukendt fejl",
                   "ERROR_SENDING_CONFIRM_MAIL" => "Fejl ved afsendelse af bekræftelses-email",
	
		   //MSG
		   "NO_SUBSCRIBER" => "Ingen abonnenter i databasen",
		   "NO_ARCHIVE" => "Intet arkiv for dette nyhedsbrev",
		   "NEWSLETTER_NOT_YET" => "Intet nyhedsbrev konfigureret.",
		   "BACK" => "Tilbage",
		   "EXAMPLE" => "Eksempel",
		   "DONE" => "Færdig",
		   
		   
		   //install
		   "INSTALL_TITLE" => "phpMyNewsletter installation",
		   "INSTALL_LANGUAGE" => "Sprog",
		   "INSTALL_LANGUAGE_LABEL" => "Vælg sprog",
		   "INSTALL_DB_TYPE" => "Databasetype",
		   "INSTALL_DB_TITLE" => "Database",
		   "INSTALL_DB_HOSTNAME" => "Hostnavn",
		   "INSTALL_DB_NAME" => "Databasenavn",
		   "INSTALL_DB_LOGIN"=> "Brugernavn",
		   "INSTALL_DB_PASS" => "Password",
		   "INSTALL_DB_TABLE_PREFIX" => "Databasetabelprefix",
		   "INSTALL_DB_CREATE_DB" => "Opret database?",
		   "INSTALL_DB_CREATE_TABLES" => "Opret tabeller?",
		   "INSTALL_GENERAL_SETTINGS" => "Overordnede indstillinger",
		   "INSTALL_ADMIN_PASS" => "Administratorpassword",
		   "INSTALL_ADMIN_BASEURL"=> "Basis-Url",
		   "INSTALL_ADMIN_PATH_TO_PMNL" => "Sti til phpMyNewsletter",
		   "INSTALL_ADMIN_NAME" => "Administratornavn",
		   "INSTALL_ADMIN_EMAIL" => "Administrators email-adresse",
		   
		   "INSTALL_MESSAGE_SENDING_TITLE" => "Sender meddelelse",
		   "INSTALL_MESSAGE_SENDING_LOOP" => "Nyhedsbrevet sendes %s stk. ad gangen.",
		   "INSTALL_VALIDATION_PERIOD" => "Abonnenter har %s dage til at bekræfte tilmelding.",
		   "INSTALL_SENDING_METHOD" => "Sendemetode",
		   "INSTALL_PHP_MAIL_FONCTION" => "php mail() funktion (standard)",
		   "INSTALL_PHP_MAIL_FONCTION_ONLINE" => "Online.net : e-mail()",

		   "INSTALL_SMTP_HOST" => "SMTP shotnavn",
		   "INSTALL_SMTP_AUTH_NEEDED" => "Godkendelse påkrævet?",
		   "INSTALL_SMTP_USERNAME" => "Brugernavn",
		   "INSTALL_SMTP_PASSWORD" => "Password",
		   
		   "INSTALL_SUBSCRIPTION_TITLE" => "Abonnement / Afslut abonnement",
		   "INSTALL_SUB_CONFIRM" => "Skal abonnenenter bekræfte tilmelding?",
		   "INSTALL_UNSUB_CONFIRM"=>"Skal abonnenenter bekræfte afmelding?",
		   
		   "INSTALL_SAVE_CREATE_DB" => "Opretter database %s",
		   "INSTALL_SAVE_CREATE_TABLE" => "Opretter tabel %s",
		   "INSTALL_SAVE_CONFIG" => "Gem indstillinger",
		   "INSTALL_SAVE_CONFIG_FILE" => "Gemmer indstillinger",
		   "INSTALL_UNABLE_TO_SAVE_CONFIG_FILE" => "Kan ikke gemme indstillinger.",
		   "INSTALL_CONFIG_MANUALLY" => "Klip og klister teksten nedenfor ind i (<em>include/config.php</em>).",
		   "INSTALL_FINISHED" => "Færdig",
		   
		   
		   "LOGOUT_DONE" => "Du er logget ud",
		   "LOGOUT_ERROR"=> "Fejl ved udlogning",
		   "LOGOUT_TITLE" => "Log ud",
		   "LOGOUT_BACK" => "Tilbage",


		   //subscription.php
		   "SUBSCRIPTION_TITLE" => "Tilmelding til nyhedsbrev",
		   "SUBSCRIPTION_SEND_CONFIRM_MESSAGE" => "Bekræftelse sendt via email.",
		   "SUBSCRIPTION_ALREADY_SUBSCRIBER" => "Du er allerede tilmeldt dette nyhedsbrev.",
		   "SUBSCRIPTION_CONFIRMATION" => "Bekræft din tilmelding",
                   "SUBSCRIPTION_FINISHED" => "Tilmelding udført",


		   "SUBSCRIPTION_MAIL_BODY" => "Gå til følgende URL for at bekræfte din tilmelding",
		   "SUBSCRIPTION_UNSUBSCRIBE_LINK" => "Klik på følgende link for at afmelde dit abonnement",
	
		   "SUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS" => "Fejl: Ukendt email-adresse",


		   "UNSUBSCRIPTION_TITLE" => "Afmelding af nyhedsbrev",	
                   "UNSUBSCRIPTION_MAIL_BODY" => "Klik på linket for at bekræfte afmelding",
		   "UNSUBSCRIPTION_SEND_CONFIRM_MESSAGE" => "Bekræftelse sendt via email.",
		   "UNSUBSCRIPTION_CONFIRMATION" => "Bekræft din afmelding",

		   "UNSUBSCRIPTION_FINISHED" => "Afmelding udført",


		   "NEWSLETTER_TITLE" => "Nyhedsbrev",
                   "UNSUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS" => "Fejl: ukendt email-adresse",
			
		   "SEND_UNSUBSCRIPTION_LINK" => "\r\n\r\nAfslut abonnement:\r\n",

		   
		   );



?>
