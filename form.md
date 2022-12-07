# Création des formulaires en Symfony

La création des formulaire en symfony est un peu particulière.

- 1) Créer le type du formulaire  
    -- Préparer la construction du formulaire en se basant sur une entité si besoin
    -- Grâce à la commande symfony console make:form SongFormType 
- 2) Vérifier et filtrer les propriétés qui serviront à la création des inputs
- 3) Dans la méthode create de SongController, demander au SongController de créer le formulaire en se basant sur son type et la nouvelle instance du nouveau son, en paramètres. 
- 4) Toujours dans la méthode create, passer la partie visible du formulaire créée à la vue.  
- 5) Dans la vue, utiliser les fonctions form_start, form_end, form_widget
https://symfony.com/doc/current/form/form_customization.html#form-start-form-view-variables