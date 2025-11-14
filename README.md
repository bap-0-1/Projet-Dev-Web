# La Banque de Smaug


## Présentation de l'application
Cette application web implémente une fonction de demande de prêts avec connexion par login mot de passe.



## Installation :
        
               $ git clone https://github.com/bap-0-1/Projet-Dev-Web
               $ cd Projet-Dev-Web
               $ sudo docker-compose up --build -d
Il ne reste plus qu'à accéder à la page http://localhost:8080 afin d'accéder à l'application web
## Architecture :
Docker : 
 Serveur PHP
 Database MySQL sur MariaDB avec plugins utilisateurs customisés

 Le formulaire de demande de prêts n'est utilisable que par les utilisateurs déjà enregistrés et sera automatiquement refusé car vous êtes trop pauves.


 Vulnérabilités :
    Vuln easy : blind sqli sur le login (flag a retrouver en exfiltrant la db en blind)
    Vuln Medium : stored xss ou une de ton choix bb
    Vuln hard : rce en blind via sqli sur login


