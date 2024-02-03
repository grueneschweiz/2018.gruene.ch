# Contributing ...

... is cool and helps to make the 🌍 a better place 🤩

## Installation

### Requirements

* [node](https://nodejs.org/) >= 10.16.0
* [yarn](https://yarnpkg.com) >= 1.7.0
* [docker](https://www.docker.com/) >= 23.0.0
* [docker compose](https://docs.docker.com/compose/) >= 2.16.0

### Setup

#### Styleguide

1. `yarn` - Install all dependencies
1. `yarn start` - start the styleguide & live assets compilation.
1. View the styleguide [http://localhost:4000](http://localhost:4000)

#### WordPress

1. Add the required proprietary plugins `polylang-pro`, `advanced-custom-fields-pro`, `searchwp`, `searchwp-polylang`
   into `wordpress/wp-content/plugins`.
1. `yarn wp:init` - Install WordPress with all dependencies
1. View your Site [http://localhost](http://localhost)
1. Site backend [http://localhost/wp-admin](http://localhost/wp-admin/) - login: **admin**, password: **admin**

> Contact us if you need help installing [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/pro/),
[Polylang Pro](https://polylang.pro/) and [SearchWP](https://searchwp.com/).

## Use

1. `yarn start` - start the styleguide & live assets compilation
2. `yarn wp:start` - start WordPress

### Access

* Website: [http://localhost](http://localhost)
* Website backend [http://localhost/wp-admin](http://localhost/wp-admin/) - login: **admin**, password: **admin**
* Phpmyadmin: [http://localhost:8181](http://localhost:8181)
* Styleguide: [http://localhost:4000](http://localhost:4000)
* Mailhog: [http://localhost:8025](http://localhost:8025)

_That's probably all you need to know!_ 🍻

## Detailed Guide

### Debug

XDEBUG is enabled by default.

- Setting the PHP constant `SUPT_FORM_ASYNC` to false (e.g. in `wp-config.php`) processes form mails and crm saving
  synchronously and thus facilitates debugging.
- Setting the PHP constant `LES_VERTS_FORM_DEBUG_LOG` to true (e.g. in `wp-config.php`) will log all form submissions
  and crm saving to `./form.log`.

### Theming

#### Templating

We use [Timber](http://upstatement.com/timber/) to handle the templating.
The theme "les-verts" is based on [Timber Starter Theme](https://github.com/timber/starter-theme).

#### Assets / styleguide

The assets are generated with our standard styleguide stack (see `./wordpress/wp-content/themes/les-verts/styleguide`).
To keep it as light and simple as possible, the docker image doesn't take care of the styleguide and assets pipeline.

#### Develop

The styleguide (fractal) and WordPress share the same assets build folder, which is in
`./wordpress/wp-content/themes/les-verts/static`.
Check `./wordpress/wp-content/themes/les-verts/README.md` for more details.

### WordPress tools

#### WP-CLI

You can run any command with `docker exec`, for example:
`docker exec wp_docker_les_verts wp theme install Akismet`

_Official docs: [https://wp-cli.org/commands/](https://wp-cli.org/commands/)_

##### List installed plugins

* `docker exec wp_docker_les_verts wp plugin list`

##### Update plugins

* List needed updates:
  `docker exec wp_docker wp plugin update --all --dry-run`
* Update all plugins:
  `docker exec wp_docker_les_verts wp plugin update --all`
* Update a single plugin:
  `docker exec wp_docker_les_verts wp plugin update <pluginname>`

##### Install a plugin

* `docker exec wp_docker_les_verts wp plugin install <pluginname> --activate`
* OR add a submodule in `./wordpress/wp-content/plugins` if needed. If you know what you're doing.

##### More

See [https://wp-cli.org/commands/plugin/](https://wp-cli.org/commands/plugin/)

### ACF & Custom Post Types

We use ACF to add custom fields on post types (& elsewhere).

### Add a post type

1. Register a new post type class that extends the class `\SUPT\Model` located in `theme/lib/post-types/Model.php`
1. Include it in the loader file: `theme/lib/_loader.php`

### ACF - Advanced Custom Fields

To version, share and deploy the ACF fields we created, we use the feature
called [Synchronized JSON](https://www.advancedcustomfields.com/resources/synchronized-json/).
Basically, it means we have nothing to take care of manually. A typical flow is:

#### Add/edit fields

1. Edit the fields as you want in the backend
1. They are automatically saved in the folder `theme/acf-json`, nothing to do here
1. When you commit, don't forget to commit these files as well

#### Sync all fields

By default, when the acf-json folder is present, WordPress loads the ACF config from there, so it's always in sync.
If you want to edit them in the backend though, wp will show "Available for sync". You can click manually "sync".

## Useful commands

* `yarn start` - Start the Styleguide
* `yarn wp:start` - Start WordPress
* `yarn start:all` - Start both the styleguide and WordPress
* `yarn build` - Build the styleguide assets for production.
* `yarn fractal:build` - Build the styleguide as a static site in
  _`./wordpress/wp-content/themes/les-verts/styleguide/dist/build`_
* `yarn fractal sass:generate` - auto-update the _loader.scss file with existing components
* `yarn wp:init` - Initialise the local WordPress instance
* `yarn component:add type component_name` - Create a new component for the styleguide.

## L10n

Localization is a mess:

- We use [Polylang Pro](https://polylang.pro/) for content translation
- Follow these 99 simple steps for translating the theme 😅
  - Respect [WordPress Internationalization](https://developer.wordpress.org/apis/internationalization/) on development
  - If you changed some ACF fields:
    [Export all ACF field groups as PHP](http://localhost/wp-admin/edit.php?post_type=acf-field-group&page=acf-tools)
    and store them in
    [wordpress/wp-content/themes/les-verts/acf-translate.php](wordpress/wp-content/themes/les-verts/acf-translate.php).
    Make sure you don't overwrite the `die();` statement on top.
  - Download and install [Poedit Pro](https://poedit.net/pro) on your local machine. You need the pro version because of
    twig.
  - Open
    [wordpress/wp-content/themes/les-verts/languages/theme.pot](wordpress/wp-content/themes/les-verts/languages/theme.pot)
    with Poedit Pro and choose _Update from code_. Save the updated `theme.pot` file.
  - Commit the `theme.pot` file and push it to the `dev` branch on github.
  - The new strings are pushed to [crowdin](https://crowdin.com/project/2018gruenech) by
    the [l10n.yml](.github/workflows/l10n.yml)
    workflow.
  - Translate the strings on [crowdin](https://crowdin.com/project/2018gruenech).
  - Trigger the [l10n.yml](.github/workflows/l10n.yml) workflow again. This will download the translated strings from
    [crowdin](https://crowdin.com/project/2018gruenech) and create a new PR with the translated files. If no
    translations changed, no PR is created.
  - Merge the PR.
  
