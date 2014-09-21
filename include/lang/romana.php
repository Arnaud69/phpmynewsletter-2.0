<?

function translate($s, $i="") {
    global $lang_array;

    if(!isset($lang_array['romana'][$s]))
      return ("[Translation required] : $s");

    if($lang_array['romana'][$s]!="") {
	if($i == "") return $lang_array['romana'][$s];
        $sprint = $lang_array['romana'][$s];
	return sprintf("$sprint" , $i);
    }
    else return ("[Translation required] : $s");
}


$lang_array['romana'] = array(
		   //BTN
		   "OK_BTN" => "OK",
		   "YES" => "Da",
		   "NO" => "Nu",
		   
                   "BACK" => "&#206;napoi",

		   //ARCHIVE
		   "ARCHIVE_TITLE" => "Arhive",
		   "ARCHIVE_CHOOSE" => "Alege o ScrisoareNout&#259;&#355;i",
		   "ARCHIVE_SUBJECT" => "Subiect",
		   "ARCHIVE_DATE" => "Dat&#259;",
		   "ARCHIVE_FORMAT" => "Format",
		   "ARCHIVE_DISPLAY" => "Afi&#351;eaz&#259; acest mesaj",
		   "ARCHIVE_BROWSE" => "R&#259;sfoie&#351;te arhive",
		   "ARCHIVE_DELETE" => "Elimin&#259; aceast&#259; arhiv&#259;",
		   "ARCHIVE_DELETE_TITLE" => "Eliminarea arhivei",
		   "ARCHIVE_DELETED" => "Arhiv&#259; eliminat&#259; cu succes",
		   "ARCHIVE_NOT_FOUND" => "Nici o arhiv&#259; g&#259;sit&#259;",
		   
		   //INDEX
		   "PHPMYNEWSLETTER_TITLE" => "ScrisoareNout&#259;&#355;i",
		   "EMAIL_ADDRESS" => "Adres&#259; e-mail",
		   "EMAIL_ADDRESS_NOT_VALID" => "Aceast&#259; adres&#259; de e-mail este invalid&#259;",
		   "NEWSLETTER_SUBSCRIPTION" => "Aboneaz&#259;",
		   "NEWSLETTER_UNSUBSCRIPTION" => "Dezaboneaz&#259;",
		   "AVAILABLE_NEWSLETTER" => "ScrisoriNout&#259;&#355;i existente",
		   
		   //ADMIN LOGIN admin/login.php
		   "LOGIN_TITLE" => "Login",
		   "LOGIN_PLEASE_ENTER_PASSWORD" => "Pentru accesarea panoului de control este necesar&#259; introducerea unei parole.",
		   "LOGIN_PASSWORD" => "Parola",
		   "LOGIN" => "Login!",
		   "LOGIN_BAD_PASSWORD" => "Parol&#259; incorect&#259;!",
		   
		   
		   //MENU
		   "MENU_SUBSCRIBERS"=> "Abona&#355;i",
		   "MENU_COMPOSE" => "Compune",
		   "MENU_ARCHIVES" => "Arhive",
		   "MENU_NEWSLETTER" => "Set&#259;ri ScrisoareNout&#259;&#355;i",
		   "MENU_CONFIG" => "Set&#259;ri globale",
		   "MENU_LOGOUT" => "Logout",
		   
		   "SELECTED_NEWSLETTER" => "ScrisoareNout&#259;&#355;i selectat&#259;",
		   "NEWSLETTER_TOTAL_SUBSCRIBER" => "abonat",
		   "NEWSLETTER_TOTAL_SUBSCRIBERS" => "abona&#355;i",
		   
		   
		   //ADMIN NEWSLETTER
		   "NEWSLETTER_CHOOSE" => "Alege&#355;i o ScrisoareNout&#259;&#355;i",
		   "NEWSLETTER_ACTION" => "Ac&#355;iune ScrisoareNout&#259;&#355;i",
		   "NEWSLETTER_NEW" => "Creaz&#259; o nou&#259; ScrisoareNout&#259;&#355;i",
		   "NEWSLETTER_DEL" => "Elimin&#259; '%s'",
		   "NEWSLETTER_SETTINGS" => "Set&#259;ri ScrisoareNout&#259;&#355;i",
		   "NEWSLETTER_NAME" => "Nume ScrisoareNout&#259;&#355;i",
		   "NEWSLETTER_FROM_ADDR" => "Adres&#259; e-mail expeditor",
		   "NEWSLETTER_FROM_NAME" => "Nume expeditor",
		   "NEWSLETTER_SUBJECT" => "Titlu mesaj",
		   "NEWSLETTER_HEADER" => "Mesaj &#238;nt&#226;mpinare",
		   "NEWSLETTER_FOOTER" => "Mesaj &#238;ncheiere",
		   "NEWSLETTER_SUB_MSG_SUBJECT" => "Subiect mesaj de abonare",
		   "NEWSLETTER_SUB_MSG_BODY" => "Mesaj de abonare",
		   "NEWSLETTER_WELCOME_MSG_SUBJECT" => "Subiect mesaj de bun-venit",
		   "NEWSLETTER_WELCOME_MSG_BODY" => "Mesaj de bun-venit",
		   "NEWSLETTER_UNSUB_MSG_SUBJECT" => "Subiect mesaj dezabonare",
		   "NEWSLETTER_UNSUB_MSG_BODY" => "Mesaj dezabonare",
		   "NEWSLETTER_SAVE_SETTINGS" => "Salveaz&#259; aceste set&#259;ri",
		   "NEWSLETTER_SETTINGS_SAVED" => "Set&#259;ri salvate",
		   "NEWSLETTER_CREATE" => "Creaz&#259; o ScrisoareNout&#259;&#355;i",
		   "NEWSLETTER_SAVE_NEW" => "Creaz&#259; aceast&#259; ScrisoareNout&#259;&#355;i",
		   "NEWSLETTER_DELETED" => "ScrisoareNout&#259;&#355;i eliminat&#259; cu succes",
		   "NEWSLETTER_DELETE_WARNING" => "Elimin&#259; toate datele aferente acestei ScrisoriNout&#259;&#355;i",
		   "NEWSLETTER_SETTINGS_CREATED" => "ScrisoareNout&#259;&#355;i creat&#259; cu succes",
		   "NEWSLETTER_DEFAULT_HEADER" => "========= INTRODUCERE =========\n".
						  "Pune&#355;i aici orice dori&#355;i.\n".
						  "Acest text va fi introdus &#238;naintea oric&#259;rui mesaj",
		   
		   "NEWSLETTER_DEFAULT_FOOTER" => "======== &#206;NCHEIERE =======\n".
						  "Pune&#355;i aici orice dori&#355;i",  
		   "NEWSLETTER_SUB_DEFAULT_SUBJECT" => "V&#259; rug&#259;m s&#259; confirma&#355;i &#238;nscrierea dvs.",
                   "NEWSLETTER_SUB_DEFAULT_BODY" => "Cineva, probabil dvs., a f&#259;cut o cerere de &#238;nscriere la aceast&#259; ScrisoareNout&#259;&#355;i; v&#259; rug&#259;m s&#259; urma&#355;i instruc&#355;iunile de mai jos", 
		   
                   "NEWSLETTER_WELCOME_DEFAULT_SUBJECT" => "Bun venit!",
   		   "NEWSLETTER_WELCOME_DEFAULT_BODY" => "Bun venit...",		   
		   "NEWSLETTER_UNSUB_DEFAULT_SUBJECT" => "V&#259; rug&#259;m s&#259; confirma&#355;i dezabonarea dvs.",
                   "NEWSLETTER_UNSUB_DEFAULT_BODY" => "Cineva, probabil dvs., a f&#259;cut o cerere de dezabonare de la aceasta ScrisoareNout&#259;&#355;i; v&#259; rug&#259;m s&#259; urma&#355;i instruc&#355;iunile de mai jos", 


		   //SUBSCRIBER
		   "SUBSCRIBER_ADD_TITLE" => "Aboneaz&#259; persoan&#259;",
		   "SUBSCRIBER_ADD_BTN" => "Adaug&#259; aceast&#259; adres&#259; e-mail",
		   "SUBSCRIBER_ADDED" => "%s ad&#259;ugat&#259; cu succes",
		   
		   "SUBSCRIBER_IMPORT_TITLE" => "Import&#259; o list&#259; cu adrese e-mail",
		   "SUBSCRIBER_IMPORT_BTN" => "Import&#259;",
		   "SUBSCRIBER_IMPORT_HELP" => "Pute&#355;i importa &#238;n mod direct o list&#259; de adrese e-mail dintr-un fi&#351;ier deja existent.<br />Acest fi&#351;ier trebuie s&#259; se conformeze formatului urm&#259;tor:<br/>adresa1@domeniu.ro<br />adresa2@domeniu.ro<br/>adresa3@domeniu.ro",
		   
		   "SUBSCRIBER_DELETE_TITLE" => "Dezaboneaz&#259; persoan&#259;",
		   "SUBSCRIBER_DELETE_BTN" => "Elimin&#259; aceast&#259; adres&#259; e-mail",
		   "SUBSCRIBER_DELETED" => "Abonat eliminat cu succes",
		   
		   "SUBSCRIBER_EXPORT_TITLE" => "Export&#259; abona&#355;i",
		   "SUBSCRIBER_EXPORT_BTN" => "Export&#259; acum",
		   
		   "SUBSCRIBER_TEMP_TITLE" => "&#206;nscriere &#238;n a&#351;teptare",
		   "SUBSCRIBER_TEMP_BTN" => "Elimin&#259; aceast&#259; adres&#259; e-mail",
		   "SUBSCRIBER_TEMP_DELETED" => "Adres&#259; e-mail eliminat&#259; cu succes",
		   
		   
		   //COMPOSE 
		   "COMPOSE_NEW" => "Compune mesaj nou",
		   "COMPOSE_SUBJECT" => "Subiect",
		   "COMPOSE_FORMAT" => "Format",
		   "COMPOSE_FORMAT_TEXT" => "Text normal",
		   "COMPOSE_FORMAT_HTML" => "HTML",
		   "COMPOSE_FORMAT_HTML_NOTICE" => "(Introduce&#355;i text doar &#238;ntre etichetele <em>&lt;body&gt;&lt;/body&gt;<em>)",
		   "COMPOSE_PREVIEW" => "Vizionare mesaj",
		   "COMPOSE_PREVIEW_TITLE" => "Vizionare mesaj",
		   "COMPOSE_BACK" => "&#206;napoi",
		   "COMPOSE_SEND" => "Trimite acest mesaj",
		   "COMPOSE_SENDING" => "Trimite un mesaj...",
		   "COMPOSE_SENT" => "Mesage trimis cu succes",


		   //GLOBAL CONFIG
		   "GCONFIG_TITLE" => "Set&#259;ri globale",
		   "GCONFIG_DB_TITLE"=> "Set&#259;ri Baz&#259; de date",
		   "GCONFIG_DB_HOST" => "Nume gazd&#259;",
		   "GCONFIG_DB_LOGIN" => "Utilizator",
		   "GCONFIG_DB_DBNAME" => "Nume baz&#259; de date",
		   "GCONFIG_DB_PASSWD" => "Parol&#259; utilizator",
		   "GCONFIG_DB_CONFIG_TABLE" => "Tabela configurare",
		   "GCONFIG_DB_TABLE_MAIL" => "E-mail-uri abona&#355;i re&#355;inute &#238;n",
		   "GCONFIG_DB_TABLE_TEMPORARY" => "Tabel&#259; temporar&#259;",
		   "GCONFIG_DB_TABLE_NEWSCONFIG" => "Tabel&#259; configurare ScrisoareNout&#259;&#355;i",
		   "GCONFIG_DB_TABLE_ARCHIVES" => "Arhivele sunt re&#355;inute &#238;n",
		   "GCONFIG_DB_TABLE_SUBMOD" => "&#206;nscriere &#238;n a&#351;teptare pentru stocare",

		   
		   "GCONFIG_DB_CONFIG_UNWRITABLE" => "Cu permisiunea scris&#259; asupra %s, mai multe set&#259;ri ale bazei de date vor fi disponibile.",
		   "GCONFIG_MISC_TITLE" => "Diferite set&#259;ri",
		   "GCONFIG_MISC_ADMIN_PASSW" => "Parola acces admin",
		   "GCONFIG_MISC_BASE_URL" => "URL baz&#259;",
		   "GCONFIG_MISC_BASE_PATH" => "Cale c&#259;tre ScrisoareNout&#259;&#355;i",
		   "GCONFIG_MISC_LANGUAGE" => "Limb&#259;",

		   "GCONFIG_MESSAGE_HANDLING_TITLE" => "Manipulare mesaje",
		   "GCONFIG_MESSAGE_ADMIN_NAME" => "Nume expeditor mesaj implicit (C&#226;mp <i>De la:</i>)",
		   "GCONFIG_MESSAGE_ADMIN_MAIL" => "Adres&#259; e-mail mesaj implicit",
		   "GCONFIG_MESSAGE_NUM_LOOP" => "Num&#259;r de mesaje trimise &#238;n fiecare sesiune",
		   "GCONFIG_MESSAGE_SEND_METHOD" => "Metod&#259; de trimitere",
                   "GCONFIG_MESSAGE_SEND_METHOD_FUNCTION" => "Func&#355;ie PHP mail()",
		   "GCONFIG_MESSAGE_SMTP_HOST" => "Server gazd&#259; SMTP",
		   "GCONFIG_MESSAGE_SMTP_AUTH" => "Autentificare SMTP necesar&#259;?",
		   "GCONFIG_MESSAGE_SMTP_LOGIN" =>"Nume utilizator SMTP",
		   "GCONFIG_MESSAGE_SMTP_PASSWORD" => "Parol&#259; SMTP",

		   "GCONFIG_SUBSCRIPTION_TITLE" => "&#206;nscriere",
		   "GCONFIF_SUBSCRIPTION_VALIDATION_TIMEOUT" => "Abona&#355;ii au %s zi(le) pentru a-&#351;i confirma &#238;nscrierea",
		   "GCONFIG_SUBSCRIPTION_CONFIRM_SUB" => "Abona&#355;ii trebuie s&#259;-&#351;i confirme &#238;nscrierea?",
		   "GCONFIG_SUBSCRIPTION_CONFIRM_UNSUB" => "Abona&#355;ii trebuie s&#259;-&#351;i confirme anularea &#238;nscrierii?",
		   
		   "GCONFIG_SUBSCRIPTION_MODERATE" => "Abona&#355;ii sunt potrivi&#355;i?",

		   "GCONFIG_SAVE_BTN" => "Salveaz&#259; aceste set&#259;ri",
		   "GCONFIG_SUCCESSFULLY_SAVED" => "Set&#259;ri globale salvate cu succes",
		   //ERROR
		   "ERROR_SQL" => "Eroare baz&#259; de date:<br/>%s<br/>",
		   "ERROR_SQL2" => "Eroare SQL:<br/>%s<br/>",
	
		   "ERROR_DBCONNECT" => "Imposibil de stabilit o conexiune cu baza de date.",
		   "ERROR_DBCONNECT_2" => "Imposibil de stabilit o conexiune cu baza de date: <br />%s.<br />V&#259; rug&#259;m s&#259; verifica&#355;i set&#259;rile bazei dvs. de date.",
		   "ERROR_DBCONNECT_3" => "Eroare la conectare",

		   
		   "ERROR_FLUSHING_TEMP_TABLE" => "Eroare la scoaterea tabelei temporare (%s)",
		   "ERROR_SAVING_SETTINGS" => "Eroare &#238;n timpul salv&#259;rii set&#259;rilor: %s",
		   "ERROR_DELETING_NEWSLETTER" => "Eroare &#238;n timpul &#351;tergerii acestei scrisori nout&#259;&#355;i",
		   "ERROR_CHOOSE_ANOTHER_NEWSLETTER" => "V&#259; rug&#259;m selecta&#355;i o alt&#259; scrisoare nout&#259;&#355;i sau <a href=\"index.php?page=home&action=create\">crea&#355;i una nou&#259;</a>",
		   "ERROR_NO_NEWSLETTER_CREATE_ONE" => "V&#259; rug&#259;m <a href=\"index.php?page=newsletterconf&action=create\">crea&#355;i o nou&#259; scrisoare nout&#259;&#355;i</a>",

		   "ERROR_NO_SUCH_NEWSLETTER" => "Scrisoare nout&#259;&#355;i neg&#259;sit&#259;",
		   "ERROR_ADDING_SUBSCRIBER" => "Eroare la ad&#259;ugarea %s",
		   "ERROR_ALREADY_SUBSCRIBER" => "%s exist&#259; deja &#238;n lista abona&#355;ilor",
		   "ERROR_SUPPLY_VALID_EMAIL" => "V&#259; rug&#259;m s&#259; introduce&#355;i o adres&#259; de e-mail valid&#259;",
		   "ERROR_DELETING_SUBSCRIBER" => "Eroare &#238;n timpul &#351;tergerii acestei adrese: %s",
		   "ERROR_NO_EMAIL_IN_FILE"=>"Nu exista nici o adres&#259; e-mail &#238;n acest fi&#351;ier",
		   "ERROR_IMPORT_FILE_MISSING" => "V&#259; rug&#259;m introduce&#355;i numele unui fi&#351;ier pentru importare",
		   "ERROR_IMPORT_TMPDIR_NOT_WRITABLE" => "Nu exist#259; drept de scriere asupra directorului admin/import!",
		   "ERROR_DELETING_TEMP" => "Eroare &#238;n timpul &#351;tergerii acestei adrese: %s",
		   "ERROR_UNABLE_TO_SEND" => "Nu exist&#259; abona&#355;i, imposibil de compus mesaj nou",
		   "ERROR_ALL_FIELDS_REQUIRED" => "Toate c&#226;mpurile necesare",
		   "ERROR_SENDING" => "Eroare la trimiterea acestui mesaj",
		   "ERROR_DELETING_ARCHIVE" => "Eroare la &#351;tergerea acestei arhive",
		   "ERROR_UNKNOWN" => "Eroare necunoscut&#259;",
                   "ERROR_SENDING_CONFIRM_MAIL" => "Eroare la trimiterea e-mail-ului de confirmare",
		   "ERROR_LOG_CREATE" => "Imposibil de creat fi&#351;ier log.<br/>".
					  "V#259; rug#259;m verifica&#355;i permisiunile directorului admin/logs",

	
		   //MSG
		   "NO_SUBSCRIBER" => "Nu exist&#259; abona&#355;i &#238;n baza de date",
		   "NO_ARCHIVE" => "Nu exista arhiv&#259; pentru aceast&#259; scrisoare nout&#259;&#355;i",
		   "NEWSLETTER_NOT_YET" => "Nu s-a configurat &#238;nc&#259; nici o scrisoare nout&#259;&#355;i.",
		   "BACK" => "&#206;napoi",
		   "EXAMPLE" => "Exemplu",
		   "DONE" => "Terminare",
		   
		   
		   //install
		   "INSTALL_TITLE" => "Instalare ScrisoareNout&#259;&#355;i",
		   "INSTALL_LANGUAGE" => "Limb&#259;",
		   "INSTALL_LANGUAGE_LABEL" => "Alege&#355;i limba dvs. preferat&#259;",
		   "INSTALL_DB_TYPE" => "Tip baz&#259; de date",
		   "INSTALL_DB_TITLE" => "Baz&#259; de date",
		   "INSTALL_DB_HOSTNAME" => "Nume gazd&#259;",
		   "INSTALL_DB_NAME" => "Nume baz&#259; de date",
		   "INSTALL_DB_LOGIN"=> "Login",
		   "INSTALL_DB_PASS" => "Parol&#259;",
		   "INSTALL_DB_TABLE_PREFIX" => "Prefix tabel&#259; baz&#259; de date",
		   "INSTALL_DB_CREATE_DB" => "Creaz&#259; baz&#259; de date?",
		   "INSTALL_DB_CREATE_TABLES" => "Creaz&#259; tabele baz&#259; de date?",
		   "INSTALL_GENERAL_SETTINGS" => "Set&#259;ri obi&#351;nuite",
		   "INSTALL_ADMIN_PASS" => "Parola admin",
		   "INSTALL_ADMIN_BASEURL"=> "URL Baz&#259;",
		   "INSTALL_ADMIN_PATH_TO_PMNL" => "Cale c&#259;tre ScrisoareNout&#259;&#355;i",
		   "INSTALL_ADMIN_NAME" => "Nume admin",
		   "INSTALL_ADMIN_EMAIL" => "Adres&#259; e-mail admin",
		   
		   "INSTALL_MESSAGE_SENDING_TITLE" => "Trimitere mesaj",
		   "INSTALL_MESSAGE_SENDING_LOOP" => "Scrisorile nout&#259;&#355;i trimise &#238;n loop de %s mesaje.",
		   "INSTALL_VALIDATION_PERIOD" => "Abona&#355;ii au %s zile pentru a confirma &#238;nscrierea lor.",
		   "INSTALL_SENDING_METHOD" => "Metod&#259; de trimitere",
		   "INSTALL_PHP_MAIL_FONCTION" => "Func&#355;ia php mail() (implicit)",
		   "INSTALL_PHP_MAIL_FONCTION_ONLINE" => "Online.net : email()",

		   "INSTALL_SMTP_HOST" => "Gazd&#259; SMTP",
		   "INSTALL_SMTP_AUTH_NEEDED" => "Nevoie de autentificare?",
		   "INSTALL_SMTP_USERNAME" => "Nume utilizator",
		   "INSTALL_SMTP_PASSWORD" => "Parol&#259;",
		   
		   "INSTALL_SUBSCRIPTION_TITLE" => "Abonare / Dezabonare",
		   "INSTALL_SUB_CONFIRM" => "Abona&#355;ii trebuie s&#259;-&#351;i confirme abonarea?",
		   "INSTALL_UNSUB_CONFIRM"=>"Abona&#355;ii trebuie s&#259;-&#351;i confirme dezabonarea?",
		   
		   "INSTALL_SAVE_CREATE_DB" => "Creare %s baz&#259; de date",
		   "INSTALL_SAVE_CREATE_TABLE" => "Creare %s tabel&#259;",
		   "INSTALL_SAVE_CONFIG" => "Salvare configura&#355;ie",
		   "INSTALL_SAVE_CONFIG_FILE" => "Salvare fi&#351;ier configura&#355;ie",
		   "INSTALL_UNABLE_TO_SAVE_CONFIG_FILE" => "Imposibil de salvat fi&#351;ier configura&#355;ie.",
		   "INSTALL_CONFIG_MANUALLY" => "V&#259; rug&#259;m copia&#355;i &#351;i lipi&#355;i liniile de mai jos &#238;n fi&#351;ierul configura&#355;ie (<em>include/config.php</em>).",
		   "INSTALL_FINISHED" => "Terminare",
		   
		   
		   "LOGOUT_DONE" => "Sunte&#355;i delogat",
		   "LOGOUT_ERROR"=> "Eroare la delogare",
		   "LOGOUT_TITLE" => "Logout",
		   "LOGOUT_BACK" => "&#206;napoi",


		   //subscription.php
		   "SUBSCRIPTION_TITLE" => "Abonare scrisoare nout&#259;&#355;i",
		   "SUBSCRIPTION_SEND_CONFIRM_MESSAGE" => "Cerere de confirmare trimis&#259; prin e-mail.",
		   "SUBSCRIPTION_ALREADY_SUBSCRIBER" => "Deja v-a&#355;i abonat la aceast&#259; scrisoare de nout&#259;&#355;i.",
		   "SUBSCRIPTION_CONFIRMATION" => "Confirma&#355;i &#238;nscrierea dvs.",
                   "SUBSCRIPTION_FINISHED" => "V-a&#355;i &#238;nscris cu succes",


		   "SUBSCRIPTION_MAIL_BODY" => "Urma&#355;i adresa URL pentru a confirma &#238;nscrierea dvs.",
		   "SUBSCRIPTION_UNSUBSCRIBE_LINK" => "Clic pe urm&#259;toarea leg&#259;tur&#259; pentru a v&#259; dezabona",
	
		   "SUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS" => "Eroare: Adres&#259; e-mail necunoscut&#259;",


		   "UNSUBSCRIPTION_TITLE" => "Dezabonare scrisoare nout&#259;&#355;i",	
                   "UNSUBSCRIPTION_MAIL_BODY" => "Pentru a confirma dezabonarea dvs., clic pe leg&#259;tura urm&#259;toare",
		   "UNSUBSCRIPTION_SEND_CONFIRM_MESSAGE" => "Cerere de confirmare trimis&#259; prin e-mail.",
		   "UNSUBSCRIPTION_CONFIRMATION" => "Confirma&#355;i dezabonarea dvs.",

		   "UNSUBSCRIPTION_FINISHED" => "Dezabonare &#238;nf&#259;ptuit&#259;",


		   "NEWSLETTER_TITLE" => "Scrisori de nout&#259;&#355;i",
                   "UNSUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS" => "Eroare: Adres&#259; e-mail necunoscut&#259;",
			
		   "SEND_UNSUBSCRIPTION_LINK" => "\r\n\r\nDezabonare:\r\n",

		   
		   );



?>
