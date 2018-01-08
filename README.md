INSTRUCTIONS D'UTILISATION DU BUNDLE "AutoDbBundle"
===================================================

Bundle développé sous Symfony 3

Versions conseillées : 

* Symfony = 3.3.x
* Twig = 1.35.0
* Php : >= 5.3.3
* Doctrine 2.5.12

> JavaScript activé

(Bien évidemment fonctionnel sous Symfony 3.0.0 et Twig 1.23.1 via une installation normale, voir : https://symfony.com/doc/current/setup.html )

Installation : 
==============

_**Cette étape peut être sautée, l'installation de ce bundle est basique.**_

- Insérez le "Vendor" Guillaume avec son bundle "AutoDbBundle" dans le dossier src/ de votre projet Symfony.

- Ajoutez le bundle dans le fichier composer.json qui se trouve à la racine dans la partie psr-4, si une erreur "Class not found" s'affiche, remplacez les bundles par :

<pre>
<code>
"psr-4": {
    "": "src/"
 },
</code>
</pre>

- Ajoutez cette ligne dans le fichier AppKernel.php : 

<pre>
<code>
new Guillaume\AutoDbBundle\GuillaumeAutoDbBundle(),
</code>
</pre>

- Modifiez votre fichier routing.yml dans votre dossier config :

<pre>
<code>
guillaume_auto_db:
    resource: "@GuillaumeAutoDbBundle/Resources/config/routing.xml"
    prefix:   /
</code>
</pre>

- Mettez à jour votre autoloader pour inclure vos nouvelles Class :
<pre><code>composer dump-autoload</code></pre>

Vous trouverez les différentes routes de votre projet via la commande :
<pre><code>php bin/console debug:router</code></pre>

Guide d'utilisation : 
=============

**Attention aux droits d'écritures, un mauvais paramétrage pourrait empêcher le bon fonctionnement de ce bundle**

**Seul les associations unidirectionnelles sont disponibles sur cette version.**

Ce projet est une première version sous Symfony 3, il permet de créer des entités fonctionnant sous Doctrine.

Les différentes routes du bundle : 

* /autodb_p1
* /autodb_p2?nbEntite=n
* /autodb_p3
* /autodb_p4
* /autodb_p5

/autodb_p1
==========

Page permettant de choisir le nombre d'entités.

/autodb_p2?nbEntite=n
=====================

Choix du nom de chaque entité.

/autodb_p3
==========

Partie attributs et associations.

Chaque entité possède des attributs et associations, pour l'instant seul les types integer, string, text et boolean sont disponibles, javascript doit être activé pour profiter des différentes fonctionnalités de la page.

Pour ajouter des attributs à une entité, il suffit de cliquer sur le bouton "+", à chaque nouvelle balise input et checkbox, le numéro à la fin de chaque attribut name s'additionne au précédent, exemple :
- name="EntiteA_x0" <- par défaut lors du chargement de la page
- name="EntiteA_x1" <- ajout via javascript
- name="EntiteA_x2" <- ajout via javascript

Ce type de nommage servira pour les associations.

Une fois les différents attributs sélectionnés, il faut choisir une association, s'il n'en existe pas, laissez la balise select sur "Aucune"
S'il en existe une, il faut choisir l'un des types d'association existant.

Une association contient un choix d'entité à sélectionner, un "nameForeignKey" et un "referencedColumnName", tous 3 obligatoires, "nameForeignKey" et "referencedColumnName" pointent vers la clé primaire (qui est sélectionnable via les attributs "name" des inputs ajoutés précédemment).

Le "nameForeignKey" est le nom qui sera enregistré dans la base de données et "referencedColumnName" est la clé primaire de l'entité choisie, sélectionnez la clé primaire souhaitée.

Un bouton "Nouvelle association" est disponible.

Exemple : 
* Choix = EntiteB
* nameForeignKey = EntiteB_x0
* referencedColumnName = EntiteB_x0

Résultat final dans la base de données :

> Select : ManyToOne

* EntiteA
* -id
* -EntiteB_id _null_
-------------------
* EntiteB
* -id

/autodb_p4
==========

C'est la partie qui créait les différents fichiers avec l'archive zip.

/autodb_p5
==========

Cette page permet de télécharger l'archive zip qui est disponible par défaut dans le dossier web du projet.

Bugs connus :
=============

Si la précédente archive n'a pas été supprimée ou renommée du dossier web, un message d'erreur s'affiche, lorsque l'on relance la création des entités, cependant les anciens fichiers sont encore en mémoire et une erreur : <pre><code>Parse error: syntax error, unexpected '<', expecting end of file</code></pre> s'affichera lors de la mise à jour de la base de données, supprimez le dernier zip crée dans votre dossier /web et relancer le formulaire de la page /autodb_p3.
