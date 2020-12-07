# Release Notes

## [v2.0.373 (2020-12-07)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.373...v2.0.373

### Added

- ContainerAwareTrait, BootableTrait, BuildableTrait

## [v2.0.372 (2020-12-06)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.372...v2.0.372

### Added

- LabelsBagTrait + MessagesBagTrait + ParamsBagTrait

### Changed

- Form : Réécriture complète

### Fixed

- Kernel\Notices : Suppression + adaptations collatérales (Form & Template)
- Kernel param.bag : Suppression + adaptations collatérales (helpers ...) 
- Proxy\... : getInstance modif commentaire correspondance inspecteur 

## [v2.0.371 (2020-12-03)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.371...v2.0.371

### Added

- Composant Debug > debugBar + errorHandler 

## [v2.0.370 (2020-12-02)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.370...v2.0.370

### Fixed

- form\_notice.scss : Pb Fond warning
- SelectJs: Passe corrective
- Metabox\Filefeed : Remise en service

## [v2.0.369 (2020-11-29)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.369...v2.0.369

### Fixed

- assets/form : Border checkbox + Radio


## [v2.0.368 (2020-11-29)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.368...v2.0.368

### Fixed

- assets/form : Pb background

## [v2.0.367 (2020-11-27)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.367...v2.0.367)

### Changed

- assets/form : Réécriture affichage variables

### Fixed

- Form : Correctif Handle suppression des messages d'erreurs et session
- Partial\Field : Réf register

## [v2.0.366 (2020-11-26)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.366...v2.0.366)

### Fixed

- Mail : Correctif de l'affichage des infos de contact de get >> param

## [v2.0.365 (2020-11-25)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.365...v2.0.365)

### Fixed

- Console : correctif conflit wp


## [v2.0.364 (2020-11-23)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.364...v2.0.364)

### Fixed

- BurgerButton : Désactivation possible du data-control

## [v2.0.363 (2020-11-22)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.363...v2.0.363)

### Fixed

- Findposts => post_type any > allowed
- Events : trigger sans arguments + suppr [] dans les déclarations


## [v2.0.362 (2020-11-19)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.362...v2.0.362)

### Changed 

- Metabox : Passe de réécriture profonde
- Partial & Field : Modification de la config ['default' => ..., 'driver' => [..., ...]]
- Field MediaImage : Ajout de la spécification value_none pour pb thumbnail_id
- asset/partial/tab : Réécriture complète
- Mail : Réécriture profonde ex. Mail >> Mailable
- Metabox : Réécriture profonde
- Field Tab : Réécriture profonde
- PostType : methode supports
- Form/Addon/Mailer : Réécriture profonde
- Form evenements  wp-admin.form.boot wp-admin.form.booted

### Fixed

- Form : événement de validation non exécutée


## [v2.0.361 (2020-11-18)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.361...v2.0.361)

### Changed

- Partitionnement des styles des formulaires
- Form : champs requis peut être encapsulé dans le label
- Pagination : Passage des styles principaux en variable
- Mise en conformité lié à l'accessibilité
- Form : Modification des classes de Form-xxx > FormXxx

### Fixed

- Correctif Recaptcha selon les nouvelles spécification de field


## [v2.0.360 (2020-11-17)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.360...v2.0.360)

### Changed

- `src/Support/Proxy/Field.php` : Methode config de surcharge de configuration

### Added

- Creation du partial BurgerButton


## [v2.0.359 (2020-11-17)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.359...v2.0.359)

### Changed

- `assets/field/suggest/scss/styles.scss` : Style du picker item Post
- `assets/form/scss/_variables.scss` : Réécriture logique des variables
- `assets/partial/notice/scss/_variables.scss` : Correspondance avec les variables de form
- `src/Support/Proxy/Partial.php` : Methode config de surchage de configuration

### Added

- `assets/form` : Déplacement depuis theme
- `src/Contracts/View/Engine.php` : Ajout de la méthode addPath
- `src/Wordpress/Field/Resources/views/suggest/post-picker_item.php` : Affichage du picker par défaut des recherches suggest post

### Fixed 

- `assets/metabox/driver/postfeed/js/scripts.js` : Réinitialisation de la valeur de recherche à la selection
- `src/Metabox/Resources/views/driver/postfeed/item.php` : Stockage des éléments dans un sous-ensemble items pour permettre l'enregistrement de valeur complémentaires
- `src/Wordpress/Field/Driver/Suggest/Suggest.php` : Correctif de post + test && term non testé + user non testé

## [v2.0.358 (2020-11-16)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.358...v2.0.358)

### Fixed

- Correctifs réécriture Field + Partial


## [v2.0.357 (2020-11-15)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.357...v2.0.357)

### Changed 

- Réécriture Optimisation Field + Partial

## [v2.0.356 (2020-11-14)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.356...v2.0.356)

### Changed

- `assets/field/media-image/js/scripts.js` : value default -1 pour compatibilité \_thumbnail\_id
- `src/Contracts/Metabox/MetaboxDriver.php` : Méthode alias() >> getAlias()
- `src/Metabox/MetaboxServiceProvider.php` : Déclaration du viewer
- `src/Wordpress/Contracts/Query/QueryPost.php` : Suppression des méthodes associé à la composition d'affichage >> ThemeSuite
- `src/Field/FieldServiceProvider.php` : Gestion de la surcharge de config \_default + alias
- `src/Metabox/MetaboxServiceProvider.php` : Gestion de la surcharge de config \_default + alias
- `src/Partial/PartialServiceProvider.php` : Gestion de la surcharge de config \_default + alias

### Fixed

- `src/Wordpress/Query/QueryPost.php` : Gestion du subtitle déléguée à theme-suite

### Added

- Librairies spatie/menu en vue de l'utilisation pour les partials Breadcrumb Pagination et Menu

## [v2.0.355 (2020-11-12)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.355...v2.0.355)

### Added

- `src/Support/Arr.php` : methode insertAfter à tester ...

### Fixed 

- `assets/in-viewport/js/scripts.js` : Gestion des viewport parent de target et externe
- QueryPost && QueryTerm && QueryUser : buildIn 'any'


## [v2.0.354 (2020-11-11)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.354...v2.0.354)

### Fixed

- `assets/partial/pdfviewer/js/scripts.js`: pdfjs >> pdfjsLib
- `src/Field/Driver/Suggest/Suggest.php` : Gestion des attributs ajax + timeout 5s par défaut
- `src/Template/Templates/ListTable/Item.php` : BadMethodCall sans __()
- `src/Wordpress/Query/QueryPost.php` : Evite boucle infinie fetchFromIds >> test exist $ids + count($ids)

## [v2.0.353 (2020-11-10)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.353...v2.0.353)

### Changed

- `src/Asset/AssetServiceProvider.php` : rewrite_base >> scope
- `src/Contracts/Form/FormFactory.php` : Mode de fonctionnement en session pour les formulaires non "controllés" + successed >> isSuccessed + partitionnement de la construction du rendu
- `src/Contracts/Routing/UrlFactory.php` : Gestion du fragment d'URL
- `src/Contracts/Session/Store.php` : Réécriture sans ParamsBag
- `src/Field/Driver/TextRemaining/TextRemaining.php` : selector >> type
- `src/Form/Factory/Handle.php` : Mode de fonctionnement en session 

### Fixed

- `assets/field/text-remaining/scss/styles.scss`: Correctif CSS
- `assets/field/tinymce/js/scripts.js` : Correctif compat tinyMce
- `assets/partial/pdfviewer/js/scripts.js`: rewrite_base >> scope


## [v2.0.352 (2020-11-05)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.352...v2.0.352)

### Fixed

- Suppression des oldies
- Test de remplacement de fzaninotto/faker par fakerphp/faker

### Added

- `src/Support/Filesystem.php` : Gestionnaire de système de fichiers

## [v2.0.351 (2020-11-04)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.351...v2.0.351)

### Fixed

- Suppression de l'appel de assets/theme en dépendance pour éviter les doublons

### Added

- Pagination du partial Suggest
- Gestion des variables partial

## [v2.0.350 (2020-10-31)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.350...v2.0.350)

### Fixed

- Rétropédalage JQueryMigrate

## [v2.0.349 (2020-10-30)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.349...v2.0.349)

### Added

- Librairie PHP fakerphp/faker
- Librarie JS axios
- `src/Filesystem/LocalFilesystem.php` : Méthode rel de récupération du chemin relatif
- `src/Routing/Url.php` : Méthode scope
- `src/Routing/UrlFactory.php` : Methode path
- `src/Support/Env.php` : Création du gestionnaire d'environnement
- 
### Changed 

- Mise à jour des librairies PHP : illuminate/... : ^7.0 > ^8.0 && jgrossi/corcel: ^4.0 >> ^5.0 && composer/composer ^1.6 > ^2.0
- Remplacement des occurences getenv par env

### Fixed 

- `src/Filesystem/Filesystem.php` : headers personnalisées prioritaires pour la réponse
- `src/Filesystem/LocalFilesystem.php` : headers personnalisées prioritaires pour la réponse
- `src/helpers.php` : Passe de réécriture des commentaires + methode env()

## [v2.0.348 (2020-10-20)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.348...v2.0.348)

### Changed 

- Ajout de la dépendance JQueryMigrate aux assets intégrants jQueryUi Widget 

### Added

- `package.json` : Ajout de la librairie jQueryMigrate 

## [v2.0.347 (2020-10-16)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.347...v2.0.347)

### Changed 

- `src/Contracts/Form/FormFactory.php` : isSuccessed >> successed
- `src/Contracts/Form/FormFactory.php` : Gestion du support de session
- `src/Form/Factory/Field` : Gestion du support de session
- `src/Form/Factory/Handle.php` : Gestion du support de session
- `src/Contracts/Form/FormFactory.php` : setOnSuccess >> setSuccessed
- `src/Contracts/Template/TemplateFactory` : méthode provider ajout d'un arg de valeur par défaut
- `src/Wordpress/Template/Templates/PostListTable` : Modification dans l'adapteur Wordpress
- `src/Wordpress/Template/Templates/UserListTable` : Modification dans l'adapteur Wordpress

### Added 

- `src/Contracts/Template/FactoryDbBuilder` : methodes -> getColumns + getKeyName + setColumns + setKeyName + hasColumn
- `src/Form/Factory/Handle.php` : Gestion du message de succes en version flashbag


## [v2.0.346 (2020-10-15)](https://svn.tigreblanc.fr/presstify-framework/tags/2.0.346...v2.0.346)

### Fixed

- `assets/anim/js/scripts.js` : Déclenchement à l'ouverture

### Removed

- `assets/theme/scss/field/datepicker.scss` : Gestion déléguée au field

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
