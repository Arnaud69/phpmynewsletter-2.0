<?
// ===============================
//  Translated by AleX <DarkSide>
//        WWW.DANOSSE.COM
//   alexandrearaujo1@gmail.com
// ===============================

function translate($s, $i="") {
    global $lang_array;

    if(!isset($lang_array['portuguesbr'][$s]))
      return ("[Translation required] : $s");

    if($lang_array['portuguesbr'][$s]!="") {
	if($i == "") return $lang_array['portuguesbr'][$s];
        $sprint = $lang_array['portuguesbr'][$s];
	return sprintf("$sprint" , $i);
    }
    else return ("[Translation required] : $s");
}


$lang_array['portuguesbr'] = array(
		   //BTN
		   "OK_BTN" => "OK",
		   "YES" => "Sim",
		   "NO" => "N&atilde;o",
		   
                   "BACK" => "Voltar",

		   //ARCHIVE
		   "ARCHIVE_TITLE" => "Arquivo",
		   "ARCHIVE_CHOOSE" => "Escolha uma newsletter",
		   "ARCHIVE_SUBJECT" => "Assunto",
		   "ARCHIVE_DATE" => "Data",
		   "ARCHIVE_FORMAT" => "Formato",
		   "ARCHIVE_DISPLAY" => "Mostrar esta mensagem",
		   "ARCHIVE_BROWSE" => "Vasculhar arquivos",
		   "ARCHIVE_DELETE" => "Deletar este aquivo",
		   "ARCHIVE_DELETE_TITLE" => "Arquivo Deletado",
		   "ARCHIVE_DELETED" => "Arquivo Deletado com Sucesso!",
		   "ARCHIVE_NOT_FOUND" => "Arquivo n&atilde;o localizado!",
		   
		   //INDEX
		   "PHPMYNEWSLETTER_TITLE" => "phpMyNewsletter",
		   "EMAIL_ADDRESS" => "Endere&ccedil;o de E-mail",
		   "EMAIL_ADDRESS_NOT_VALID" => "Este e-mail n&atilde;o &eacute; valido.",
		   "NEWSLETTER_SUBSCRIPTION" => "Inscrever",
		   "NEWSLETTER_UNSUBSCRIPTION" => "Cancelar Inscri&ccedil;&atilde;o",
		   "AVAILABLE_NEWSLETTER" => "Newsletters Disponivel",
		   
		   //ADMIN LOGIN admin/login.php
		   "LOGIN_TITLE" => "Login",
		   "LOGIN_PLEASE_ENTER_PASSWORD" => "Area restrita para Administradores",
		   "LOGIN_PASSWORD" => "Password",
		   "LOGIN" => "Login !",
		   "LOGIN_BAD_PASSWORD" => "Password errado !",
		   
		   
		   //MENU
		   "MENU_SUBSCRIBERS"=> "Inscrever",
		   "MENU_COMPOSE" => "Novo",
		   "MENU_ARCHIVES" => "Arquivo",
		   "MENU_NEWSLETTER" => "Config do Newsletter",
		   "MENU_CONFIG" => "Config Global",
		   "MENU_LOGOUT" => "Logout",
		   
		   "SELECTED_NEWSLETTER" => "Selecionar Newsletter",
		   "NEWSLETTER_TOTAL_SUBSCRIBER" => "Inscritos",
		   "NEWSLETTER_TOTAL_SUBSCRIBERS" => "inscritos",
		   
		   
		   //ADMIN NEWSLETTER
		   "NEWSLETTER_CHOOSE" => "Escolha uma newsletter",
		   "NEWSLETTER_ACTION" => "Newsletter ativa",
		   "NEWSLETTER_NEW" => "Crie uma nova newsletter",
		   "NEWSLETTER_DEL" => "Deletar '%s'",
		   "NEWSLETTER_SETTINGS" => "Config Newsletter",
		   "NEWSLETTER_NAME" => "Nome da Newsletter",
		   "NEWSLETTER_FROM_ADDR" => "Email de envio",
		   "NEWSLETTER_FROM_NAME" => "Nome de envio",
		   "NEWSLETTER_SUBJECT" => "Assunto da Mensagem",
		   "NEWSLETTER_HEADER" => "Titulo da Mensagem",
		   "NEWSLETTER_FOOTER" => "Rodap&eacute; da Mensagem",
		   "NEWSLETTER_SUB_MSG_SUBJECT" => "Titulo do Cadastro",
		   "NEWSLETTER_SUB_MSG_BODY" => "Mensagem do Cadastro",
		   "NEWSLETTER_WELCOME_MSG_SUBJECT" => "Titulo de Boas Vindas",
		   "NEWSLETTER_WELCOME_MSG_BODY" => "Mensagem de Boas Vindas",
		   "NEWSLETTER_UNSUB_MSG_SUBJECT" => "Titulo de cancelamento do cadastro",
		   "NEWSLETTER_UNSUB_MSG_BODY" => "Mensagem do cancelamento",
		   "NEWSLETTER_SAVE_SETTINGS" => "Salvar estas config",
		   "NEWSLETTER_SETTINGS_SAVED" => "Settings saved",
		   "NEWSLETTER_CREATE" => "Criar uma newsletter",
		   "NEWSLETTER_SAVE_NEW" => "Criar esta newsletter",
		   "NEWSLETTER_DELETED" => "Newsletter deletada com sucesso.",
		   "NEWSLETTER_DELETE_WARNING" => "Deletar todas as newsletter",
		   "NEWSLETTER_SETTINGS_CREATED" => "Newsletter criadas com sucesso!",
		   "NEWSLETTER_DEFAULT_HEADER" => "========= Cabe&ccedil;alho =========\n".
						  "Digite aqui a sua mensagem!",
		   
		   "NEWSLETTER_DEFAULT_FOOTER" => "======== Rodap&eacute; =======\n".
						  "Escreva o rodap&eacute; aqui!",  
		   "NEWSLETTER_SUB_DEFAULT_SUBJECT" => "Por favor confirme sua Inscri&ccedil;&atilde;o",
                   "NEWSLETTER_SUB_DEFAULT_BODY" => "Algu&eacute;m, provavelmente voc&ecirc;, cadastrou este e-mail na nossa lista!", 
		   
                   "NEWSLETTER_WELCOME_DEFAULT_SUBJECT" => "Bem Vindo a este newsletter",
   		   "NEWSLETTER_WELCOME_DEFAULT_BODY" => "Bem Vindo a este newsletter !",		   
		   "NEWSLETTER_UNSUB_DEFAULT_SUBJECT" => "Por Favor confirme o cancelamento",
                   "NEWSLETTER_UNSUB_DEFAULT_BODY" => "Algu&eacute;m, provavelmente voc&ecirc;, pediu para ser excluido da nossa lista!", 


		   //SUBSCRIBER
		   "SUBSCRIBER_ADD_TITLE" => "Adicionar Inscri&ccedil;&atilde;o",
		   "SUBSCRIBER_ADD_BTN" => "Adicionar este e-mail",
		   "SUBSCRIBER_ADDED" => "%s adicionado com sucesso!",
		   
		   "SUBSCRIBER_IMPORT_TITLE" => "Importar esta lista de emails",
		   "SUBSCRIBER_IMPORT_BTN" => "Importar",
		   "SUBSCRIBER_IMPORT_HELP" => "Voc&ecirc; pode importar uma lista de e-mails direto de um arquivo.<br />Este arquivo precisa conter este formato:<br/>endereco1@domain.com<br />endereco2@domain.com<br/>endereco3@domain.com",
		   
		   "SUBSCRIBER_DELETE_TITLE" => "Deletar esta Inscri&ccedil;&atilde;o",
		   "SUBSCRIBER_DELETE_BTN" => "Deletar este e-mail",
		   "SUBSCRIBER_DELETED" => "Inscri&ccedil;&atilde;o deletada com sucesso!",
		   
		   "SUBSCRIBER_EXPORT_TITLE" => "Exportar Inscri&ccedil;&otilde;es",
		   "SUBSCRIBER_EXPORT_BTN" => "Exportar agora",
		   
		   "SUBSCRIBER_TEMP_TITLE" => "Inscri&ccedil;&otilde;es Pendentes",
		   "SUBSCRIBER_TEMP_BTN" => "Delete this email address",
		   "SUBSCRIBER_TEMP_DELETED" => "Email deletado com sucesso!",
		   
		   
		   //COMPOSE 
		   "COMPOSE_NEW" => "Escrever uma nova mensagem",
		   "COMPOSE_SUBJECT" => "T&iacute;tulo",
		   "COMPOSE_FORMAT" => "Formato",
		   "COMPOSE_FORMAT_TEXT" => "Apenas Texto",
		   "COMPOSE_FORMAT_HTML" => "HTML",
		   "COMPOSE_FORMAT_HTML_NOTICE" => "(Forne&ccedil;a somente o c&oacute;digo inclu&iacute;do entre <em>&lt;body&gt;&lt;/body&gt;<em> tags)",
		   "COMPOSE_PREVIEW" => "Visualizar Mensagem",
		   "COMPOSE_RESET" => "Resetar",
		   "COMPOSE_PREVIEW_TITLE" => "Visualizar Mensagem",
		   "COMPOSE_BACK" => "Voltar",
		   "COMPOSE_SEND" => "Enviar esta mensagem",
		   "COMPOSE_SENDING" => "Enviando uma mensagem ...",
		   "COMPOSE_SENT" => "Mensagem enviada com sucesso!",


		   //GLOBAL CONFIG
		   "GCONFIG_TITLE" => "Config Global",
		   "GCONFIG_DB_TITLE"=> "Config do Banco de Dados",
		   "GCONFIG_DB_HOST" => "Hostname",
		   "GCONFIG_DB_LOGIN" => "Login do Usu&aacute;rio",
		   "GCONFIG_DB_DBNAME" => "Nome do Banco de Dados",
		   "GCONFIG_DB_PASSWD" => "Password do Usu&aacute;rio",
		   "GCONFIG_DB_CONFIG_TABLE" => "Config da Tabela",
		   "GCONFIG_DB_TABLE_MAIL" => "Os emails ser&atilde;o guardados na",
		   "GCONFIG_DB_TABLE_TEMPORARY" => "Tabela Temporaria",
		   "GCONFIG_DB_TABLE_NEWSCONFIG" => "Tabela de Config do Newsletter",
		   "GCONFIG_DB_TABLE_ARCHIVES" => "Arquivos ser&atilde;o guardado na",
		   "GCONFIG_DB_TABLE_SUBMOD" => "Inscri&ccedil;&otilde;es aguardaram na",

		   
		   "GCONFIG_DB_CONFIG_UNWRITABLE" => "Com escreva permiss&otilde;es em %s, mais ajustes da base de dados estaria dispon&iacute;vel.",
		   "GCONFIG_MISC_TITLE" => "Ajustes Variados",
		   "GCONFIG_MISC_ADMIN_PASSW" => "Password do Admin",
		   "GCONFIG_MISC_BASE_URL" => "Endere&ccedil;o Base",
		   "GCONFIG_MISC_BASE_PATH" => "pasta do phpMyNewsletter",
		   "GCONFIG_MISC_LANGUAGE" => "Linguaguem",

		   "GCONFIG_MESSAGE_HANDLING_TITLE" => "Manipula&ccedil;&atilde;o de mensagem",
		   "GCONFIG_MESSAGE_ADMIN_NAME" => "Nome do Administrador(<i>Para:</i> campo)",
		   "GCONFIG_MESSAGE_ADMIN_MAIL" => "E-mail do Administrador",
		   "GCONFIG_MESSAGE_NUM_LOOP" => "Número das mensagens emitidas em cada ciclo",
		   "GCONFIG_MESSAGE_SEND_METHOD" => "Emitindo o m&eacute;todo",
                   "GCONFIG_MESSAGE_SEND_METHOD_FUNCTION" => "PHP mail() function",
		   "GCONFIG_MESSAGE_SMTP_HOST" => "SMTP - Nome do server",
		   "GCONFIG_MESSAGE_SMTP_AUTH" => "SMTP - Precisa de autentica&ccedil;&atilde;o ?",
		   "GCONFIG_MESSAGE_SMTP_LOGIN" =>"SMTP - Usu&aacute;rio",
		   "GCONFIG_MESSAGE_SMTP_PASSWORD" => "SMTP - Password",

		   "GCONFIG_SUBSCRIPTION_TITLE" => "Inscri&ccedil;&otilde;es",
		   "GCONFIF_SUBSCRIPTION_VALIDATION_TIMEOUT" => "Os inscritos t&ecirc;m %s dias para confirmar suas subscri&ccedil;&otilde;es",
		   "GCONFIG_SUBSCRIPTION_CONFIRM_SUB" => "O inscrito precisa confirmar a inscri&ccedil;&atilde;o ?",
		   "GCONFIG_SUBSCRIPTION_CONFIRM_UNSUB" => "O inscrito precisa confirmar o cancelamento ?",
		   
		   "GCONFIG_SUBSCRIPTION_MODERATE" => "Inscritos podem moderar ?",

		   "GCONFIG_SAVE_BTN" => "Salvar estas configura&ccedil;&otilde;es",
		   "GCONFIG_SUCCESSFULLY_SAVED" => "Config Global salvas com sucesso!",
		   //ERROR
		   "ERROR_SQL" => "Erro do BD :<br/>%s<br/>",
		   "ERROR_SQL2" => "Erro do SQL :<br/>%s<br/>",
	
		   "ERROR_DBCONNECT" => "Incapaz de conectar ao BD.",
		   "ERROR_DBCONNECT_2" => "Incapaz de conectar ao BD: <br />%s.<br />Por Favor verificar as configuca&ccedil;&otilde;es.",
		   "ERROR_DBCONNECT_3" => "Erro na conex&atilde;o!",

		   
		   "ERROR_FLUSHING_TEMP_TABLE" => "Erro ao nivelar a tabela temporaria (%s)",
		   "ERROR_SAVING_SETTINGS" => "Erro ao salvar as config: %s",
		   "ERROR_DELETING_NEWSLETTER" => "Erro ao suprimir este boletim de newsletter",
		   "ERROR_CHOOSE_ANOTHER_NEWSLETTER" => "Escolha por favor um outro newsletter, ou <a href=\"index.php?page=home&action=create\">crie um novo</a>",
		   "ERROR_NO_NEWSLETTER_CREATE_ONE" => "Favor, <a href=\"index.php?page=newsletterconf&action=create\">crie um novo newsletter</a>",

		   "ERROR_NO_SUCH_NEWSLETTER" => "Newsletter n&atilde;o localizado",
		   "ERROR_ADDING_SUBSCRIBER" => "Erro ao adicionar %s",
		   "ERROR_ALREADY_SUBSCRIBER" => "%s est&aacute; j&aacute; na lista dos inscristos",
		   "ERROR_SUPPLY_VALID_EMAIL" => "Forne&ccedil;a por favor um email v&aacute;lido!",
		   "ERROR_DELETING_SUBSCRIBER" => "Erro ao deletar este endere&ccedil;o: %s",
		   "ERROR_NO_EMAIL_IN_FILE"=>"N&atilde;o h&aacute; email nesse arquivo",
		   "ERROR_IMPORT_FILE_MISSING" => "Forne&ccedil;a por favor um arquivo para a importa&ccedil;&atilde;o",
 	           "ERROR_IMPORT_TMPDIR_NOT_WRITABLE" => "N&atilde;o pode escrever em admin/import!",
		   "ERROR_DELETING_TEMP" => "Erro ao deletar o endere&ccedil;o: %s",
		   "ERROR_UNABLE_TO_SEND" => "Nenhum iscrito, incapaz de compôr uma nova mensagem",
		   "ERROR_ALL_FIELDS_REQUIRED" => "Todos os campos preenchidos",
		   "ERROR_SENDING" => "Erro ao enviar esta mensagem",
		   "ERROR_DELETING_ARCHIVE" => "Erro ao deletar este arquivo",
		   "ERROR_UNKNOWN" => "Erro Desconhecido",
                   "ERROR_SENDING_CONFIRM_MAIL" => "Erro ao enviar a confirma&ccedil;&atilde;o!",
		   "ERROR_LOG_CREATE" => "Incapaz de criar arquivo de sistema.<br/>".
					  "Cheque as permi&ccedil;&otilde;es da pasta admin/logs",

		   //MSG
		   "NO_SUBSCRIBER" => "Ningu&eacute;m inscrito no bando de dados",
		   "NO_ARCHIVE" => "N&atilde;o h&aacute; arquivo no newsletter",
		   "NEWSLETTER_NOT_YET" => "N&atilde;o h&aacute; NewsLetter configurado ainda.",
		   "BACK" => "Voltar",
		   "EXAMPLE" => "Exemplo",
		   "DONE" => "Pronto",
		   
		   
		   //install
		   "INSTALL_TITLE" => "phpMyNewsletter instala&ccedil;&atilde;o",
		   "INSTALL_LANGUAGE" => "Linguagem",
		   "INSTALL_LANGUAGE_LABEL" => "Escolhar uma linguagem",
		   "INSTALL_DB_TYPE" => "Tipo de Banco de Dados",
		   "INSTALL_DB_TITLE" => "Banco de Dados",
		   "INSTALL_DB_HOSTNAME" => "Hostname",
		   "INSTALL_DB_NAME" => "Nome do BD",
		   "INSTALL_DB_LOGIN"=> "Login",
		   "INSTALL_DB_PASS" => "Password",
		   "INSTALL_DB_TABLE_PREFIX" => "Prefixo das tabelas do BD",
		   "INSTALL_DB_CREATE_DB" => "Criar Banco de Dados ?",
		   "INSTALL_DB_CREATE_TABLES" => "Criar tabelas do BD ?",
		   "INSTALL_GENERAL_SETTINGS" => "Ajustes Comuns",
		   "INSTALL_ADMIN_PASS" => "Password do Administrador",
		   "INSTALL_ADMIN_BASEURL"=> "URL Base",
		   "INSTALL_ADMIN_PATH_TO_PMNL" => "Pasta do phpMyNewsletter",
		   "INSTALL_ADMIN_NAME" => "Nome do Administrador",
		   "INSTALL_ADMIN_EMAIL" => "Email do Administrador",
		   
		   "INSTALL_MESSAGE_SENDING_TITLE" => "Mensagem Enviada",
		   "INSTALL_MESSAGE_SENDING_LOOP" => "O boletim de notícias &eacute; emitido em %s ciclos de mensagens.",
		   "INSTALL_VALIDATION_PERIOD" => "Os inscritos t&ecirc;m %s dias para confirmar suas inscri&ccedil;&atilde;o.",
		   "INSTALL_SENDING_METHOD" => "Enviado metodo",
		   "INSTALL_PHP_MAIL_FONCTION" => "php mail() function (default)",
		   "INSTALL_PHP_MAIL_FONCTION_ONLINE" => "Online.net specific email() php function",

		   "INSTALL_SMTP_HOST" => "SMTP - Host",
		   "INSTALL_SMTP_AUTH_NEEDED" => "Precisa de autentica&ccedil;&atilde;o ?",
		   "INSTALL_SMTP_USERNAME" => "Usu&aacute;rio",
		   "INSTALL_SMTP_PASSWORD" => "Password",
		   
		   "INSTALL_SUBSCRIPTION_TITLE" => "Inscri&ccedil;&atilde;o / Cancelamento",
		   "INSTALL_SUB_CONFIRM" => "Os inscritos precisam autorizar a inscri&ccedil;&atilde;o ?",
		   "INSTALL_UNSUB_CONFIRM"=>"Os inscritos precisam autorizar o cancelamento ?",
		   
		   "INSTALL_SAVE_CREATE_DB" => "Criando %s banco de dados",
		   "INSTALL_SAVE_CREATE_TABLE" => "Criando %s tabela",
		   "INSTALL_SAVE_CONFIG" => "Salvando configura&ccedil;&atilde;o",
		   "INSTALL_SAVE_CONFIG_FILE" => "Salvando configura&ccedil;&atilde;o do arquivo",
		   "INSTALL_UNABLE_TO_SAVE_CONFIG_FILE" => "Incapaz de salvar config nos arquivos.",
		   "INSTALL_CONFIG_MANUALLY" => "Por favor copie e cole a linha abaixo nos configs do arquivo(<em>include/config.php</em>).",
		   "INSTALL_FINISHED" => "Finalizado",
		   
		   
		   "LOGOUT_DONE" => "Saindo do sistema",
		   "LOGOUT_ERROR"=> "Erro ao sair do sistema",
		   "LOGOUT_TITLE" => "Logout",
		   "LOGOUT_BACK" => "Voltar",


		   //subscription.php
		   "SUBSCRIPTION_TITLE" => "Inscri&ccedil;&atilde;o do Newsletter",
		   "SUBSCRIPTION_SEND_CONFIRM_MESSAGE" => "Pedido da confirma&ccedil;&atilde;o emitido pelo email.",
		   "SUBSCRIPTION_ALREADY_SUBSCRIBER" => "Voc&ecirc; ja esta inscrito no nosso newsletter.",
		   "SUBSCRIPTION_CONFIRMATION" => "Confirme sua incri&ccedil;&atilde;o",
                   "SUBSCRIPTION_FINISHED" => "Inscri&ccedil;&atilde;o efetuada com sucesso!",


		   "SUBSCRIPTION_MAIL_BODY" => "Entre na URL sitada e confirme a sua inscri&ccedil;&atilde;o para completar o processo",
		   "SUBSCRIPTION_UNSUBSCRIBE_LINK" => "Click para cancelar a inscri&ccedil;&atilde;o com a nossa newsletter",
	
		   "SUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS" => "Erro: Email desconhecido",


		   "UNSUBSCRIPTION_TITLE" => "Cancelamento da Newsletter",	
                   "UNSUBSCRIPTION_MAIL_BODY" => "Confirme o seu cancelamento clicando no link",
		   "UNSUBSCRIPTION_SEND_CONFIRM_MESSAGE" => "Pedido de confirma&ccedil;&atilde;o emitido pelo email.",
		   "UNSUBSCRIPTION_CONFIRMATION" => "Confirme o seu cancelamento",

		   "UNSUBSCRIPTION_FINISHED" => "Cancelamento realizado com sucesso!",


		   "NEWSLETTER_TITLE" => "Newsletters",
                   "UNSUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS" => "Erro: Email desconhecido",
			
		   "SEND_UNSUBSCRIPTION_LINK" => "\r\n\r\nCancelamento :\r\n",

		   
		   );



?>
