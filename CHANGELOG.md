# Release Notes

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
