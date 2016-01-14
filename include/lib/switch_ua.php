<?php
    /*
    * https://svn.apache.org/repos/asf/spamassassin/branches/b2_4_0/rules/20_anti_ratware.cf
    */
    $user_agent = array(
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; rv:38.0) Gecko/20100101 Thunderbird/38.5.1',//User-Agent =~ /^Mozilla\/5\.\d+ \(.*\) Gecko\/\d{8}(?: |$)/
        , //USER_AGENT_PINE  Message-Id         =~ /^<Pine\.[A-Z]{3}\.\d\.[0-9A-Z]+\./
        , //USER_AGENT_MUTT user-Agent          =~ /^Mutt\/\d(?:\.\d+){1,4}/
        , //USER_AGENT_MOZILLA_UAuser-Agent     =~ /^Mozilla\/5\.\d+ \(.*\) Gecko\/\d{8}(?: |$)/
        , //USER_AGENT_MOZILLA_XM	X-Mailer    =~ /^Mozilla 4\.\d{2} \[[a-z]{2}\]/
        , //USER_AGENT_MACOE		User-Agent  =~ /^Microsoft[ -]Outlook[ -]Express[ -]Macintosh[ -]Edition/
        'X-Mailer: Microsoft Outlook 15.0',//USER_AGENT_OUTLOOK	X-Mailer        =~ /^Microsoft Outlook [A-Z]{3}, Build [89]\.0\.[1-3]\d{3} \([89]\.0\.[1-3]\d{3}\.0\)$/
        , //USER_AGENT_ENTOURAGE	User-Agent  =~ /^Microsoft-Entourage\/\d{1,2}(?:\.\d){1,2}\.\d{4}$/
        , //USER_AGENT_KMAIL		User-Agent  =~ /^KMail\/1\.\d\.\d+$/
        , //USER_AGENT_IMP		User-Agent      =~ /^Internet Messaging Program \(IMP\) [34]\.\d/
        , //USER_AGENT_TONLINE	X-Mailer        =~ /^T-Online (?:e|Web)Mail \d\.\d+$/
        , //USER_AGENT_APPLEMAIL	X-Mailer    =~ /^Apple Mail \(\d\.\d+\)$/
        , //USER_AGENT_GNUS_UA	User-Agent      =~ /^Gnus\/\d\.\d+ /
          //USER_AGENT_GNUS_XM	X-Mailer        =~ /^Gnus v\d(?:\.\d+){1,2}\/X?Emacs \d+\.\d+/


    
    
    
    );