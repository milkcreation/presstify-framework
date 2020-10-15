# Release Notes

## [v2.0.345 (2020-10-15)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.345...v2.0.345)

### Added

- `src/Console/Commands/UpdateCore20345.php` : Script de mise à jour vers la 2.0.345
- `src/Contracts/Form/FactorySession` : Prise en charge des sessions de formulaire, possibilié de désactivation au niveau des champs

### Changed

- `assets/field/_variables.scss` : Ajout de la couleur transaprente
- `assets/field/datepicker/scss/styles.scss` : Transposition des styles inclus dans le thème
- `assets/theme/scss/_variables.scss` : Déplacement de wp-admin-colors
- `assets/theme/scss/styles.scss` : Désactivation du thème des champs
- `package.json` : Prise en charge de la librairie hamburgers CSS
- `src/Contracts/Form/FactoryField.php` : Passage en typage strict
- `src/Contracts/Form/FactoryHandle.php` : En remplacement de FactoryRequest + Réorganisation du code
- `src/Contracts/Mail/Mailer` : Methode getDefault de methode static à methode d'instance + parseAttachment && parseContact en static >> réécriture incluse parseContact prend en compte \['email' => xxx, 'name' => xxx\]
- `src/Contracts/PostType/PostTypeFactory` : Modification de la gestion des labels permet de récuperer un objet labelBag et plural+singular
- `src/Contracts/Session/Store` : Méthode put passage array possible + putOne créée
- `src/Form/Addon/Mailer/Mailer` : Adaptation request >> handle
- `src/Form/Addon/Record/Record` : Table 'tify_forms_record' col session de 32 à 255 car. 
- `src/Form/Addon/User/User` : Adaptation request >> handle
- `src/Form/Factory/Addons.php` : Typage strict __construct
- `src/Form/Factory/Buttons.php` : Typage strict __construct
- `src/Form/Factory/Events.php` : Typage strict __construct
- `src/Form/Factory/Fields.php` : Typage strict __construct
- `src/Form/Factory/Group.php` : Typage strict __construct
- `src/Form/Factory/Groups.php` : Typage strict __construct
- `src/Form/Factory/Notices.php` : Typage strict __construct
- `src/Form/Factory/Options.php` : Typage strict __construct
- `src/Form/Field/Recaptcha/Recaptcha.php` : Adaptation request >> handle
- `src/Form/FieldController.php` : Support session et réorganisation alpha
- `src/Taxonomy/TaxonomyTermMeta.php` : Correctif bug unserialize value null
- `src/User/UserMeta.php` : Correctif bug unserialize value null
- `src/Template/Templates/PostListTable/ServiceProvider.php` : Modification de la surcharge 
- `src/Template/Templates/UserListTable/ServiceProvider.php` : Modification de la surcharge 
- `src/Wordpress/Template/Templates/PostListTable/DbBuilder.php` : Permet la selection de Builder Lara ou WpQuery
- `src/Wordpress/Template/Templates/UserListTable/DbBuilder.php` : Permet la selection de Builder Lara ou WpUserQuery

## [v2.0.344 (2020-10-07)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.344...v2.0.344)

### Added

- `assets/field/_variables.scss` : Gestion des variables communes des champs
- `assets/field/select/scss/_variables.scss` : Gestion des variables field **select**
- `assets/field/select-js/scss/_variables.scss` : Gestion des variables field **select-js**
- `assets/partial/notice/scss/_variables.scss`: Gestion des variables partial **notice**

### Changed

- `assets/template/file-manager/js/scripts.js` : Adapation assets template **file-manager**
- `assets/theme/scss/` : Réorganisation du theme

### Fixed

- `assets/partial/pdfviewer/js/scripts.js`: Modification de la dépendance pdfjs
- `src/Filesystem/StaticCacheManager.php` : Correctif de l'initialisation
- `src/Filesystem/StorageManager.php` : Suppression de prise en charge de realpath($root)
- `src/Template/Templates/FileManager` : Adaptation code nouveaux enjeux template ActionsFactory 

### Removed

- `assets/form/scss`: Gestion des styles formulaires vers le thème @todo remettre en place et surcharger avec theme
- `assets/wp-admin`: Suppression à la faveur de `assets/wordpress/admin`

## [v2.0.343 (2020-10-04)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.343...v2.0.343)

### Added

- `package.json`: Ajout de la librarie lodash

## [v2.0.342 (2020-09-23)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.342...v2.0.342)

### Changed

- `assets/field/select-js/scss/styles.scss`: Adaptation des styles
- `assets/field/toggle-switch/scss/styles.scss` : Adaptation des styles
- `src/Field/Driver/Checkbox/Checkbox.php` : attributs checked="checked" >> checked
- `src/Field/Driver/Radio/Radio.php`: attributs checked="checked" >> checked

### Added

- `assets/field/select/js/scripts.js` : Gestion des listes de selection (en attente de résolution)
- `assets/field/select/scss/styles.scss` : Modification des styles d'une liste de selection
- `assets/form/scss/_theme.scss` : Gestion des liste de selection 
- `assets/theme/scss/_variables.scss` : Variables liste de selection
- `src/Field/Driver/Select/Select`: Gestion d'encapsulation 'wrapper'
- `src/Field/Resources/views/select/index.php`: wrapper en layout
- `src/Field/Resources/views/select/wrapper.php` : template d'encapsulation
- `src/Form/Resources/views/addon/mailer/mail/confirmation/html/body.php`: Template dédiée au mail de confirmation
- `src/Form/Resources/views/addon/mailer/mail/notification/html/body.php`: Template dédiée au mail de notification
- `src/Support/Arr.php`: methode stripslashes

### Fixed

- `assets/field/toggle-switch/js/scripts.js` : Zone commentée
- `src/Contracts/PostType/PostTypePostMeta.php`: Remplacement methode wp_unslash >> Arr::stripslashes
- `src/Contracts/Taxonomy/TaxonomyTermMeta.php` : Remplacement methode wp_unslash >> Arr::stripslashes
- `src/Contracts/User/UserMeta.php` : Remplacement methode wp_unslash >> Arr::stripslashes
- `src/Field/Driver/SelectJs/SelectJs.php`: Remplacement methode wp_unslash >> Arr::stripslashes
- `src/Form/Addon/Record/Record.php`: Remplacement methode wp_unslash >> Arr::stripslashes
- `src/PostType/PostTypePostMeta.php`: Remplacement methode wp_unslash >> Arr::stripslashes
- `src/Taxonomy/TaxonomyTermMeta.php`: Remplacement methode wp_unslash >> Arr::stripslashes
- `src/User/Metadata/Metadata.php`: Remplacement methode wp_unslash >> Arr::stripslashes
- `src/User/Metadata/Option.php`: Remplacement methode wp_unslash >> Arr::stripslashes
- `src/User/UserMeta.php`: Remplacement methode wp_unslash >> Arr::stripslashes
- `src/Wordpress/Form/Form.php`: Remplacement methode wp_unslash >> Arr::stripslashes
- `src/Wordpress/Field/Driver/Findposts/Findposts.php`: Remplacement methode wp_unslash >> Arr::stripslashes
- `src/Wordpress/Session/Session.php`: Remplacement methode wp_unslash >> Arr::stripslashes
- `src/Field/Resources/views/toggle-switch/index.php`: Force le typage string des valeurs
- `src/Form/Addon/Mailer/Mailer.php` : Possibilité de personnaliser le mail via viewer 
- `src/Form/Factory/Field.php`: Réécriture de la méthode renderPrepare if: endif; >> if {} 
- `src/Form/Factory/Field.php`: Correctif passage de label personnalisé
- `src/Form/Factory/Group.php`: Correctif position pour tabindex
- `src/Form/FormFactory.php` : Gestion wrapper
- `src/Form/FormFactory.php` : Anchor basée sur wrapper
- `src/Form/Resources/views/index.php`: Prise en compte de l'encapsulation du formulaire
- `src/Wordpress/Metabox/Metabox.php`: dépréciation WP 'whitelist_options' >> 'whitelist_options'

## [v2.0.341 (2020-09-18)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.341...v2.0.341)

### Changed

- `src/Console/Command.php` : Portage des méthodes de journalisation et d'affichage des message de notification de `Wordpress/Database/Command/WpBuilderCommand.php`


## [v2.0.340 (2020-09-15)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.340...v2.0.340)

### Added

- `assets/wordpress/js/admin-bar-pos.js` : Création du gestionnaire de barre d'administration Wordpress

### Changed

- `src/Partial/Driver/Sidebar/Sidebar.php` : items >> body + posibilité de Closure

### Fixed

- `assets/field/media-image/js/scripts.js` : Modification de la déclaration des couleurs
- `assets/theme/scss/_buttons.scss` : Modification de la déclaration des couleurs
- `assets/theme/scss/_variables.scss` : Modification de la déclaration des couleurs
- `assets/wp-admin/scss/_variables.scss` : Modification de la déclaration des couleurs
- `assets/wp-admin/scss/field/datepicker.scss` : Modification de la déclaration des couleurs

## [v2.0.339 (2020-09-12)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.339...v2.0.339)

### Added

- `assets/form/scss/_theme.scss` : Prise en compte du Champs datepicker
- `assets/theme/scss/_variables.scss` : Variable de gestion des champs par défaut et jeu de variables dédiée à select-js
- `package.json` : Ajout de la librairie jQueryBlockUi

### Changed

- `assets/field/select-js/js/scripts.js` : Déplacement de la mise en lumière de selection au niveau du conteneur côté JS
- `assets/field/select-js/scss/styles.scss` : Déplacement de la mise en lumière de selection au niveau du conteneur côté CSS
- `src/View/Factory/PlatesFactory.php` : Affectation de valeur par défaut de la méthode htmlAttrs === $this->get('attrs', [])

### Fixed

- `src/Filesystem/ImgFilesystem.php` : Récupération d'image lorsque les attrs ne sont pas définis
