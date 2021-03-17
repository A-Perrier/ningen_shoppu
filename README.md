# NingenShoppu

## NingenShoppu ?
Ne cherchez pas bien loin pour l'origine de ce nom, j'écoute beaucoup le groupe de musique Ningen Isu (人間椅子) tout en développant, et shoppu (ショップ) vient du japonais que l'on traduit comme "magasin" (shop).

## Pourquoi ?
NingenShoppu est un projet d'exercice dans le cadre de mon apprentissage du développement web en autodidacte.
J'ai eu l'occasion de mettre en place différents projets, allant du simple blog à la plateforme de gestion de projet (qui est en cours de reconception).
Une plateforme de e-commerce est un projet complet techniquement et personnalisable dans bien des directions et c'était une porte ouverte vers de nouveaux apprentissages (et pour les portes fermées, je coderai un générateur de clefs).

## Tout seul ?
Le projet a été créé en autonomie totale, quoi qu'évidemment appuyé par des forums de temps à autres au gré des problématiques rencontrées. 

- Le breadcrumb a été récupéré et adapté depuis - [l'original](https://codepen.io/Shyam-Chen/pen/WvYYLK) 
- Le HTML/CSS de la notation par étoiles a été prise - [ici](https://codepen.io/neilpomerleau/pen/wzxzQr)
- Le switch provient de - [cette source](https://www.w3schools.com/howto/howto_css_switch.asp)

Le design a été intégralement réalisé par moi-même sur Figma.

## Environnement
NingenShoppu est développé sur Symfony 5.2.3 (PHP 7.4.5) et avec le soutien de jQuery 3.5.1 et de SASS
- IDE : Visual Studio Code
- BDD : MySQL 5.7

## Fonctionnalités

### Utilisateurs classiques
- [x] Connexion / Inscription avec envoi d'email
- [x] Modification de l'email / mot de passe avec envoi d'email
- [x] Breadcrumb
  #### Produits
  - [x] Visibilité des produits en stock
  - [x] Ajout / Suppression du panier, avec choix des quantités
  - [x] Panier
  - [x] Ajout d'avis sur les produits achetés (commentaire et note)
  - [x] Tri par catégorie
  - [x] Barre de recherche
  #### Commandes
  - [x] Validation du panier sécurisée (blocage des quantités en fonction du stock restant)
  - [x] Paiement par l'API de Stripe
  - [x] Mail de récapitulatif de commande
  - [x] Consultation des commandes passée
  - [x] Annulation de commande possible si encore impayée
  #### Contact
  - [x] Envoi du formulaire de contact

### Administration
- [x] Layout significatif
  #### Produits
  - [x] Accès aux produits retirés de la vente
  - [x] Panneau de gestion des commandes clients
  - [x] Gestion du CRUD des produits
  - [x] Gestion du CRUD des photos attachées au produit
  - [x] Photo par défaut pour les produits sans photo uploadées
  - [x] Gestion de la mise en ligne ou non des produits
  - [x] Visibilité accrue pour les produits dont la quantité en stock est faible
  - [x] Panneau d'accès rapide aux produits retirés de la vente
  - [ ] Intégration d'un système de labels certifiants
  #### Catégories
  - [x] Gestion du CRUD des catégories
  - [x] Retrait de la vente des produits dont la catégorie est supprimée, avec alerte à l'administration
  #### Livraisons
  - [x] Intégration d'une livraison
      * A l'aide de la liste de tous les produits, on dispose de boutons permettant de modifier la quantité de produits reçus (par exemple repris d'un bordereau de livraison). Un récapitulatif des produits rentrés est injecté en temps réel en bas de la page. On peut alors soumettre cette liste et les stocks seront incrémentés d'autant
  - [x] Historique des livraisons
  - [x] Champs triables
  - [ ] Barre de recherche spécifique aux livraisons

### Securité
- [x] Redimensionnement des photos bloqué côté utilisateur avec Glide
- [x] Le paiement est sécurisé avec Stripe
- [x] Les requêtes HTTP indésirables sont bloquées côté serveur

### Infrastructure
- [x] Sitemap