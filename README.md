 Ce README, destiné
au groupe chargé de réaliser le test d'intrusion, devra expliquer de manière claire comment installer
et configurer l'application. Il devra également décrire son fonctionnement et la logique sous-jacente.
En complément, un document devra être rédigé pour présenter le fonctionnement de chaque
vulnérabilité présente dans l'application. Chaque vulnérabilité devra être expliquée en détail, avec une
description de son fonctionnement et de la manière dont elle peut être exploitée.



               La Banque de Smaug


Présentation de l'application :
Cette application web implémente une fonction de demande de prêts avec connexion par login mot de passe.



Installation :
$ git clone https://github.com/bap-0-1/Projet-Dev-Web
$ cd Projet-Dev-Web
$ sh ./start.sh

Architecture :
Docker : 
 Serveur PHP
 Database MySQL sur MariaDB avec plugins utilisateurs customisés


 Vulnérabilités :
 
    Vuln easy : blind sqli sur le login (flag a retrouver en exfiltrant la db en blind)
    Vuln Medium : stored xss ou une de ton choix bb
    Vuln hard : rce en blind via sqli sur login


