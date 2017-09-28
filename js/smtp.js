    function checkSMTP(){
        var QC = document.global_config.elements['sending_method'].selectedIndex;
        if(QC==02){ // SMTP
            document.global_config.elements['smtp_host'].disabled = false;
            document.global_config.elements['smtp_host'].value = "";
            document.global_config.elements.smtp_auth[0].checked = "checked";
            document.global_config.elements.smtp_auth[1].checked = "";
            document.global_config.elements['smtp_login'].disabled = false;
            document.global_config.elements['smtp_login'].value = "";
            document.global_config.elements['smtp_pass'].disabled = false;
            document.global_config.elements['smtp_pass'].value = "";
            document.global_config.elements['smtp_port'].disabled = false;
            document.global_config.elements['smtp_port'].value = "";
        }
        if(QC==1){ // SMTP TLS
            document.global_config.elements['smtp_host'].disabled = false;
            document.global_config.elements['smtp_host'].value = "";
            document.global_config.elements.smtp_auth[0].checked = "";
            document.global_config.elements.smtp_auth[1].checked = "checked";
            document.global_config.elements['smtp_login'].disabled = false;
            document.global_config.elements['smtp_pass'].disabled = false;
            document.global_config.elements['smtp_port'].disabled = true;
            document.global_config.elements['smtp_port'].value = "587";
        }
        if(QC==2){ // SMTP SSL
            document.global_config.elements['smtp_host'].disabled = false;
            document.global_config.elements['smtp_host'].value = "";
            document.global_config.elements.smtp_auth[0].checked = "";
            document.global_config.elements.smtp_auth[1].checked = "checked";
            document.global_config.elements['smtp_login'].disabled = false;
            document.global_config.elements['smtp_pass'].disabled = false;
            document.global_config.elements['smtp_port'].disabled = true;
            document.global_config.elements['smtp_port'].value = "465";
        }
        if (QC==3){ // LBSMTP
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "";
            document.global_config.elements.smtp_auth[0].checked = "checked";
            document.global_config.elements.smtp_auth[1].checked = "";
            document.global_config.elements['smtp_login'].disabled = true;
            document.global_config.elements['smtp_login'].value = "";
            document.global_config.elements['smtp_pass'].disabled = true;
            document.global_config.elements['smtp_pass'].value = "";
            document.global_config.elements['smtp_port'].disabled = true;
            document.global_config.elements['smtp_port'].value = "";
        }
        if (QC==4){ // SMTP GMAIL TLS
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "smtp.gmail.com";
            document.global_config.elements.smtp_auth[0].checked = "";
            document.global_config.elements.smtp_auth[1].checked = "checked";
            document.global_config.elements['smtp_login'].disabled = false;
            document.global_config.elements['smtp_pass'].disabled = false;
            document.global_config.elements['smtp_port'].disabled = true;
            document.global_config.elements['smtp_port'].value = "587";
        }
        if (QC==5){ // SMTP GMAIL SSL
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "smtp.gmail.com";
            document.global_config.elements.smtp_auth[0].checked = "";
            document.global_config.elements.smtp_auth[1].checked = "checked";
            document.global_config.elements['smtp_login'].disabled = false;
            document.global_config.elements['smtp_pass'].disabled = false;
            document.global_config.elements['smtp_port'].disabled = true;
            document.global_config.elements['smtp_port'].value = "465";
        }
        if(QC==6){ // PHP_MAIL, INFOMANIAK
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "";
            document.global_config.elements.smtp_auth[0].checked = "checked";
            document.global_config.elements.smtp_auth[1].checked = "";
            document.global_config.elements['smtp_login'].disabled = true;
            document.global_config.elements['smtp_pass'].disabled = true;
            document.global_config.elements['smtp_port'].disabled = true;
            document.global_config.elements['smtp_port'].value = "";
        }
        if(QC==7){ // SMTP MUTU OVH
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "ssl0.ovh.net";
            document.global_config.elements.smtp_auth[0].checked = "";
            document.global_config.elements.smtp_auth[1].checked = "checked";
            document.global_config.elements['smtp_login'].disabled = false;
            document.global_config.elements['smtp_pass'].disabled = false;
            document.global_config.elements['smtp_port'].disabled = true;
            document.global_config.elements['smtp_port'].value = "587";
        }
        if(QC==8){ // SMTP MUTU 1&1
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "auth.smtp.1and1.fr";
            document.global_config.elements.smtp_auth[0].checked = "";
            document.global_config.elements.smtp_auth[1].checked = "checked";
            document.global_config.elements['smtp_login'].disabled = false;
            document.global_config.elements['smtp_pass'].disabled = false;
            document.global_config.elements['smtp_port'].disabled = true;
            document.global_config.elements['smtp_port'].value = "465";
        }
        if(QC==9){ // SMTP MUTU GANDI
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "mail.gandi.net";
            document.global_config.elements.smtp_auth[0].checked = "";
            document.global_config.elements.smtp_auth[1].checked = "checked";
            document.global_config.elements['smtp_login'].disabled = false;
            document.global_config.elements['smtp_pass'].disabled = false;
            document.global_config.elements['smtp_port'].disabled = true;
            document.global_config.elements['smtp_port'].value = "587";
        }
        if(QC==10){ // SMTP MUTU ONLINE
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "smtpauth.online.net";
            document.global_config.elements.smtp_auth[0].checked = "";
            document.global_config.elements.smtp_auth[1].checked = "checked";
            document.global_config.elements['smtp_login'].disabled = false;
            document.global_config.elements['smtp_pass'].disabled = false;
            document.global_config.elements['smtp_port'].disabled = true;
            document.global_config.elements['smtp_port'].value = "587";
        }
        if(QC==11){ // SMTP MUTU INFOMANIAK
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "mail.infomaniak.ch";
            document.global_config.elements.smtp_auth[0].checked = "";
            document.global_config.elements.smtp_auth[1].checked = "checked";
            document.global_config.elements['smtp_login'].disabled = false;
            document.global_config.elements['smtp_pass'].disabled = false;
            document.global_config.elements['smtp_port'].disabled = true;
            document.global_config.elements['smtp_port'].value = "587";
        }
        if(QC==12){ // SMTP ONE.COM
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "mailout.one.com";
            document.global_config.elements.smtp_auth[0].checked = "checked";
            document.global_config.elements.smtp_auth[1].checked = "";
            document.global_config.elements['smtp_login'].disabled = true;
            document.global_config.elements['smtp_pass'].disabled = true;
            document.global_config.elements['smtp_port'].disabled = true;
            document.global_config.elements['smtp_port'].value = "25";
        }
        if(QC==13){ // SMTP SSL ONE.COM
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "send.one.com";
            document.global_config.elements.smtp_auth[0].checked = "";
            document.global_config.elements.smtp_auth[1].checked = "checked";
            document.global_config.elements['smtp_login'].disabled = false;
            document.global_config.elements['smtp_pass'].disabled = false;
            document.global_config.elements['smtp_port'].disabled = true;
            document.global_config.elements['smtp_port'].value = "465";
        }
    }