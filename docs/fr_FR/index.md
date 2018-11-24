Description
===

Plugin permettant de piloter des objets connectés TP-Link via la plateforme
du fabriquant : [Kasa](https://www.tp-link.com/us/kasa-smart/kasa.html).
A ce jour, seule la prise connectée HS110 sont traités.

Avertissements
===

Je n'ai pas la vocation d'assurer une maintenance long terme de ce plugin.
Je l'ai avant tout créé pour moi-même. Une fois stable, je l'ai partagé sur le
market afin d'en faire profiter d'autres. Cependant :

-   J'ai rarement le temps de me mettre sur des développements persos. Donc si
    vous rencontrez un problème, n'hésitez pas à participer au sujet dédié
    sur le forum. Avec un peu de chance j'aurai le temps et la motivation pour
    vous aider... mais je ne m'engage en rien.

-   Comme indiqué précédemment, ce plugin ne prend en charge que la prise HS110.
    La raison est simple : c'est tout ce que j'ai chez moi. Je serai ravi de
    traiter d'autres périphériques... mais il faudra pour cela me les offrir ;)
    Cependant, si vous tentez l'utilisation du plugin avec un autre matériel,
    n'hésitez pas à m'informer sur le forum si ça fonctionne correctement ou
    pas.

-   Ce développement est en Open Source (AGPL) et disponible sur github. Je
    serais ravi de donner la main à d'autres contributeurs. N'hésitez pas à me
    faire signe si vous êtes intéressés.

Prérequis
===
Pour le moment, le plugin nécessite que vous ayez correctement installé votre
équipement et configuré sa connexion avec Kasa (avec l'application mobile Kasa).


Installation
===
Suite à l'installation et activation du plugin, rendez-vous sur la page de
configuration pour installer les dépendances.

P.S. : pour une raison inconnue, il arrive parfois que les dépendances soient
marquées "OK" alors qu'elles ne le sont pas. Dans le doute appuyez toujours sur
"Relancer".

Puis renseigner vos identifiants Kasa.

Sauvegardez, puis rendez-vous sur la page du plugin (dans la catégorie "objets
connectés").

Appuyez sur "Synchroniser avec Kasa". Une fois la synchronisation réussie, il
vous faut actualiser la page pour voir apparaitre vos équipements.

Et voila !

Encore un avertissement !
===
La mise à jour des valeurs (état, puissance, consommation) se fait sur demande
avec la commande "rafraîchir", à la sauvegarde de votre équipement, à l'appel
d'une action (switch On/Off) et via le cron toutes les 15min.
Cela signifie donc qu'une modification de l'état directement sur l'équipement
ou via l'application Kasa ne sera prise en compte qu'après 15min (ou par action
manuellement de votre part sur Jeedom).
