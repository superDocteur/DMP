# DMP
Dossier Médical Personnel

Ce petit site internet tout-en-un sert à gérer des données médicales (pour le moment sans gestion de metadatas,
peut-être un jour mais ca n'est pas la priorité) dans un but d'être une compilation de ressources en cas d'urgence médicale.
Il suffit d'avoir l'adresse de consultation sur soi (éventuellement via un QRCode généré dans l'interface d'administration et
permettant l'autologin dans la partie visualisation).

Dépendances :
- PHP (>5.4)
  - Module SQLite3
  - Module ImageMagik
  - librairie Smarty pour les templates (pour le moment inclus dans l'arborescence pendant le développement)
  - librairie phpqrcode (idem)
  
Les avantages de la version de base :
- simple de déploiement (autodéploiement)
- PHP donc trouvable partout
- SQLite (donc pas besoin de déployer ou de dépendre d'une base MySQL/PgSQL ou autre)
- Inclusion de tous documents entièrement dans la base : 1 seul fichier, pas de gestion compliquée d'arborescence
- un seul script faisant point d'accès (dmp.php), tout le reste est ensuite inclus depuis ce script
- autentification en lecture avec un mot de passe non stocké en clair dans la base (mais pour le moment transmis en clair)
- autentification en administration avec un mot de passe non stocké en clair (mais pour le moment transmis en clair)
- Cryptage des données (pour le moment via une clé symétrique elle-même cryptée par les clés ci-dessus)

Les désavantages d'une illusoire autre version :
- elle n'existe pas
