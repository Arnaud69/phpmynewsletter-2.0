<?php
    /*
    * https://svn.apache.org/repos/asf/spamassassin/branches/b2_4_0/rules/20_anti_ratware.cf
    * https://mail.python.org/pipermail/mailman-developers/2011-November/021593.html
    * http://www.greenend.org.uk/rjk/spoolstats/agents.html
    * http://www.l0d.org/user_agent
    */
    $user_agent = array(
    
    //User-Agent =~ /^Mozilla\/5\.\d+ \(.*\) Gecko\/\d{8}(?: |$)/
        'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:38.0) Gecko/20100101 Thunderbird/38.5.1',        
        'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:38.0) Gecko/20100101 Thunderbird/38.5.0',
        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Thunderbird/38.5.1',
        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Thunderbird/38.5.0',
        'Mozilla/5.0 (X11; Linux x86_64; rv:38.0) Gecko/20100101 Thunderbird/38.5.1',
        'Mozilla/5.0 (X11; Linux i686; rv:38.0) Gecko/20100101 Thunderbird/38.4.0',
        'Mozilla/5.0 (X11; Linux x86_64; rv:31.0) Gecko/20100101 Thunderbird/31.5.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:38.0) Gecko/20100101 Thunderbird/38.5.1',
        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:42.0) Gecko/20100101 Firefox/42.0 SeaMonkey/2.39',
        'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:42.0) Gecko/20100101 Firefox/42.0 SeaMonkey/2.39',
        'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:38.0) Gecko/20100101 Thunderbird/38.5.1',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:38.0) Gecko/20100101 Thunderbird/38.5.0',
        'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070728 Thunderbird/2.0.0.6 Mnenhy/0.7.6.666',
        'Mozilla/5.0 (X11; Linux i686 on x86_64; rv:42.0) Gecko/20100101 Firefox/42.0 SeaMonkey/2.39',
        
    //user-Agent =~ /^Mutt\/\d(?:\.\d+){1,4}/
        'Mutt/1.2.5.1i',
        'Mutt/1.2.5i',
        'Mutt/1.3.25i',
        'Mutt/1.3.28i',
        'Mutt/1.4.1i',
        'Mutt/1.4.2.1i',
        'Mutt/1.4i',
	'Mutt/1.5.24 (2015-08-30)',
        'Mutt/1.5.3i',
        'Mutt/1.5.4i',
        'Mutt/1.5.5.1+cvs20040105i',
        'Mutt/1.5.5.1i',
        'Mutt/1.5.6i',
    
    //X-Mailer =~ /^Mozilla 4\.\d{2} \[[a-z]{2}\]/
        'Mozilla 4.8 [en] (Windows NT 5.0; U)',
        'Mozilla 4.73 [en]C-CCK-MCD VERIZON473 (Win98; U)',
        'Mozilla 4.75 [de] (Win98; U)',
        'Mozilla 4.79 [en] (Win98; U)',
    	'Mozilla 4.08 [en] (Win16; U)',
    //User-Agent  =~ /^Microsoft[ -]Outlook[ -]Express[ -]Macintosh[ -]Edition/
        'Microsoft-Outlook-Express-Macintosh-Edition/5.02.2022',
    
    //X-Mailer =~ /^Microsoft Outlook [A-Z]{3}, Build [89]\.0\.[1-3]\d{3} \([89]\.0\.[1-3]\d{3}\.0\)$/
        'Microsoft Outlook Express 6.00.3790.3959',
        'Microsoft Outlook Express 6.00.2900.5931',
        'Microsoft Outlook Express 6.00.2900.5843',
        'Microsoft Outlook Express 6.00.2900.5512',
        'Microsoft Outlook Express 6.00.2900.2180',
        'Microsoft Outlook Express 6.00.2900.3664',
        'Microsoft Outlook Express 5.00.2615.200',
        'Microsoft Outlook 14.0',
        'Microsoft Outlook 15.0',
        
    // X-Mailer =~ /^Microsoft Windows Live Mail 16.4.3522.110
        'Microsoft Windows Live Mail 16.4.3522.110',
        'Microsoft Windows Live Mail 16.4.3505.912',
        
    // X-Mailer=~ /^Microsoft Office Outlook
        'X-Mailer: Microsoft Office Outlook 12.0',
        
    //User-Agent  =~ /^Microsoft-Entourage\/\d{1,2}(?:\.\d){1,2}\.\d{4}$/
        'Microsoft-Entourage/11.4.0.080122',
        'Microsoft-Entourage/12.30.0.110427',
    
    //User-Agent =~ /^KMail\/1\.\d\.\d+$/
        'KMail/1.4.1',
        'KMail/1.4.3',
        'KMail/1.5',
        'KMail/1.5.1',
        'KMail/1.5.2',
        'KMail/1.5.3',
        'KMail/1.5.4',
        'KMail/1.5.93',
        'KMail/1.6',
        'KMail/1.6.1',
        'KMail/1.6.2',
        'KMail/1.9.10 (enterprise35 0.20100827.1168748)',
    
    //User-Agent  =~ /^Internet Messaging Program \(IMP\) [34]\.\d/
        'Internet Messaging Program (IMP) 3.0',
        'Internet Messaging Program (IMP) 3.1',
        'Internet Messaging Program (IMP) 3.1 / FreeBSD-4.6',
        'Internet Messaging Program (IMP) 3.2',
        'Internet Messaging Program (IMP) 3.2.1',
        'Internet Messaging Program (IMP) 3.2.1 / FreeBSD-5.0 ',
        'Internet Messaging Program (IMP) 3.2.2',
        'Internet Messaging Program (IMP) 3.2.2-cvs',
        'Internet Messaging Program (IMP) 3.2.3',
        'Internet Messaging Program (IMP) 3.2.3-cvs',
        'Internet Messaging Program (IMP) 4.0-cvs',
    
    //X-Mailer =~ /^T-Online (?:e|Web)Mail \d\.\d+$/
        
    
    //X-Mailer    =~ /^Apple Mail \(\d\.\d+\)$/
        'Apple Mail (2.3112)',
        'Apple Mail (2.1510)',
        'Apple Mail (2.1283)',
        'Apple Mail (2.1085)',
        
    //User-Agent =~ /^Gnus\/\d\.\d+ /
        'Gnus/5.13 (Gnus v5.13) Emacs/24.3 (gnu/linux)',
        'Gnus/5.13 (Gnus v5.13) Emacs/24.5 (gnu/linux)',
        'Gnus/5.13 (Gnus v5.13) Emacs/23.4 (gnu/linux)',
        'Gnus/5.13 (Gnus v5.13) Emacs/24.4 (gnu/linux)',
        'Gnus/5.13 (Gnus v5.13) Emacs/23.1 (gnu/linux)',
        'Gnus/5.13 (Gnus v5.13) Emacs/24.5 (darwin)',
        'Gnus/5.13 (Gnus v5.13) Emacs/24.4 (darwin)',
        'Gnus/5.130014 (Ma Gnus v0.14) Emacs/24.5 (x86_64-pc-linux-gnu)',
        'Gnus/5.13 (Gnus v5.13) Emacs/23.2 (gnu/linux)',
        'Gnus/5.13 (Gnus v5.13) Emacs/24.3 (windows-nt)',
        'Gnus/5.09 (Gnus v5.9.0) Emacs/21.2',
        'Gnus/5.1002 (Gnus v5.10.2) Emacs/21.2 (gnu/linux)',
        'Gnus/5.1002 (Gnus v5.10.2) Emacs/21.3 (berkeley-unix)',
        'Gnus/5.1003 (Gnus v5.10.3) Emacs/21.3 (gnu/linux)',
        'Gnus/5.1008 (Gnus v5.10.8) Emacs/21.3 (irix)',
        'Gnus/5.1008 (Gnus v5.10.8) XEmacs/21.4.22 (darwin)',
        
    //X-Mailer =~ /^Gnus v\d(?:\.\d+){1,2}\/X?Emacs \d+\.\d+/
        'Gnus v5.7/Emacs 20.7',
        


    
    
    
    );