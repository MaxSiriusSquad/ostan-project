# Routes

## Routes accessibles visiteurs
| URL | Méthode HTTP | Controller | Méthode | Titre | Contenu | Commentaire |
|--|--|--|--|--|--|--|
|`/`|GET|PostController|advicePostList|Page accueil O'Stan|Articles conseils, side bar + lien pour la connexion/inscription|--|
| `/mentions-legales/` | GET | MainController | legalMentions | Mentions Légales | Paragraphes sur les mentions légales | - |
|`/le-concept/`|GET|MainController|concept|Le concept du site|présentation du concept du site - présentation des createurs du site - les points primordiaux dans son utilisation|--|
|`/contact/`|POST|MainController|contact|Contact|Page pour nous contacter|Formulaire de contact|
|`/plan-du-site/`|GET|MainController|sitemap|Plan du site|lien vers les différentes catégories|assez réduit car peu de fonctionnalités ouvertes au visiteur => lien vers page de connexion ?|
|`/advicepost/{slug}/`|GET|PostController|advicePostShow|Détail article conseil| Un article en entier, pas de possibilité de commenter|--|
|`/connexion/`|POST|Backend\SecurityController|login|Connexion|Se connecter|redirection vers `/annonces/`  |
|`/inscription/`|POST|Backend\RegisterController|register|Inscription|S'inscrire|redirection vers `/profil/`|


## Routes accessibles utilisateurs
| URL | Méthode HTTP | Controller | Méthode | Titre | Contenu | Commentaire |
|--|--|--|--|--|--|--|
|`/deconnexion/`|POST|Backend\SecurityController|logout|Déconnexion|Se déconnecter|redirection vers `/`|
|`/profil/{slug}/`|GET|UserController|profile|Profil|Mon profil|page dédié à l'utilisateur (page de son profil) / son "métier", sa gallery, ... |
|`/profil/edit/{slug}/`|GET|UserController|profile|Profil|Mon profil - édition|page pour mise à jour de son profil / formulaire|
|`/`|POST|PostController|home||Liste des annonces|Page d'accueil de l'utilisateur une fois connecté|
|`/annonce/{slug}/`|POST|PostController|adPostShow|Page de détail d'une annonce|possibilité d'y laisser un commentaire |Formulaire pour commentaire |
|`/annonce/edit/{slug}/`|POST|PostController|adEdit|Editer une annonce|Editer son annonce|Formulaire pour éditer une annonce |
|`/annonce/delete/{slug}/`|POST|PostController|adDelete|Supprimer une annonce|Editer son annonce|Formulaire pour éditer une annonce |
|`/annonces/new/`|POST|PostController|adNew||Nouvelle annonce|Formulaire création annonce|
|`/annonces`|GET|PostController|adList|Liste de toutes les annonces|postées par d'autres utilisateurs|-|
|`/profil/{slug}/`|GET|UserController|profileShow|Profil utilisateur|Le profil "public" d'un utilisateur| accessible uniquement par les utilisateurs connectés et non bloqués|
|`/profil/edit/{slug}/`|POST|UserController|edit|Mise à jour de son profil utilisateur|MAJ de son profil d'un utilisateur| - |
|`/profil/delete/{slug}/`|GET|UserController|delete|Possibilité de supprimer son profil|Suppression en cascade de tout ce qu'il a posté| - |
|`/tag/{slug}/`|GET|TagController|tagShow|Page par theme|liste de toutes les annonces et pro liées à ce tag|--|
|`/message/new/`|POST|MessageController|new|Envoi d'un nouveau message|Formulaire|-|
|`/message/{title}/`|POST|MessageController|show|Visualisation d'un ensemble de message, propre à une conversation entre deux utilisateurs|-|-|
|`/users/`|GET|UserController|userList|Visualisation de la liste de tous les users/-|-|



## Routes accessibles uniquement administrateur
| URL | Méthode HTTP | Controller | Méthode | Titre | Contenu | Commentaire |
|--|--|--|--|--|--|--|
|`/backend/tag/`|GET|Backend\TagController|tagList|Liste des tags|Liste de tous les tags|--|
|`/backend/tag/edit/{slug}/`|POST|Backend\TagController|tagEdit|Edition du tag|Formulaire d'édition d'un tag|Formulaire pour modifier un tag (son titre par exemple)|
|`/backend/tag/new/`|POST|Backend\TagController|tagNew|Création d'un tag|Formulaire création d'un tag|Formulaire |
|`/backend/tag/delete/{slug}`|POST|Backend\TagController|tagDelete|Suppression d'un tag|Formulaire suppression d'un tag|Formulaire |
|`/backend/user/`|GET|Backend\UserController|userList|Liste de tous les utilisateurs|leur nom, leur rôle, la date de création du profil ? |--|
|`/backend/user/{slug}/`|GET|Backend\UserController|userDetail|Consultation du profil d'un utilisateur|Avec notamment son role, son email|--|
|`/backend/user/{id}/role/{roleId}`|GET|Backend\UserController|updateRole|Mise à jour du role d'un user |utilisateur, admin|-|
|`/backend/user/{id}/role/{roleId}`|GET|Backend\UserController|updateStatus|Mise à jour du statut d'un user |Bloqué / Non bloqué|-|
|`/backend/user/edit/{slug}/`|POST|Backend\UserController|userEdit|Editer profil d'un utilisateur|Page pour éditer un profil|changer son role par exemple, formulaire|
|`/backend/user/delete/{slug}/`|POST|Backend\UserController|userDelete|Supprimer profil d'un utilisateur|Page pour supprimer un profil|changer son role par exemple, formulaire|
|`/backend/`|GET|Backend\PostController|adList|Liste de toutes les annonces| le titre, le créateur de l'annonce, la date de création et le nombre de réponse|--|
|`/backend/annonce/{slug}/`|GET|Backend\PostController|adDetail|Consultation d'une annonce d'un utilisateur|le titre, le contenu, le créateur de l'annonce, la date de création et toutes les réponses|--|
|`/backend/annonce/edit/{slug}/`|POST|Backend\PostController|adEdit|Edition d'une annonce|Page pour éditer une annonce|pour modifier une annonce avec par exemple, un message en rouge "mention supprimée, ne respecte pas la charte"|
|`/backend/annonce/block/{slug}/`|GET|Backend\PostController|annonceBlock|Bloquer une annonce |--|
|`/backend/annonce/delete/{slug}/`|POST|Backend\PostController|adDelete|Suppression d'une annonce|Page pour supprimer une annonce|pour modifier une annonce avec par exemple, un message en rouge "mention supprimée, ne respecte pas la charte"|
|`backend/comment/{id}/status/{statusCode}`|PATCH|Backend\CommentController|Gestion du blocage d'un commentaire|-|
|`/backend/advice-post/`|GET|Backend\PostController|advicepostList|Liste de tous les articles| le titre, le détail, la date de création et de modification|--|
|`/backend/advice-post/new/`|POST|Backend\PostController|advicepostNew|Creation articles|Page creation d'article|formulaire de création d'article|
|`backend/advice-post/{slug}/`|GET|Backend\PostController|advicePostShow|Détail d'un article| Un article en entier|--|
|`backend/advice-post/edit/{slug}/`|POST|Backend\PostController|advicePostEdit|Edition d'un article|Edition d'un article|Formulaire|
|`backend/advice-post/delete/{slug}/`|POST|Backend\PostController|advicePostDelete|Suppression d'un article|Suppression d'un article|Formulaire|
|`backend/post/ad/delete/{slug}/`|POST|Backend\PostController|adDelete|Suppression d'une annonce|Suppression d'une annonce|Formulaire|
|`backend/{slug}/status/{statusCode}/`|POST|Backend\PostController|updateStatus|Modification du status d'un user|possibilité de modifier le statut d'un user|-|
|`backend/blocked`|GET|Backend\BlockendController|index|gestion des blocages|-|-|
|`backend/articles`|GET|Backend\Postcontroller|advicePostList|listes de tous les articles conseils|-|-|
