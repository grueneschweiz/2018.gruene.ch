Les Verts Stack
===============

Les Verts (Grüne Schweiz) new website with living styleguide made with Fractal.build, hosted on WordPress Multisite and with the latest technologies by superhuit.ch.

Living Styleguide: https://styleguide.2018.gruene.ch/

Installation
============

Requirements
------------

* [node](https://nodejs.org/) 8.11.0 <= node < 10 (Do not use node v10 as it breaks build process)
* [yarn](https://yarnpkg.com) >= 1.7.0
* [docker](https://www.docker.com/) >= 18.0.0
* [docker compose](https://docs.docker.com/compose/) >= 1.21.0

Getting Started
===============

Setup
----------

**Styleguide**

1. `yarn` - Install all dependencies
2. `yarn start` - start the styleguide & live assets compilation.
3. View the styleguide [http://localhost:4000](http://localhost:4000)

**WordPress**

1. `docker-compose up -d` - Create docker containers & launch them
2. `yarn wp:init` - Install WordPress with all dependencies
3. `yarn && yarn build` - install styleguide dependencies & build the assets (CSS, JS) for production
4. View your Site [http://localhost](http://localhost)
5. Site backend [http://localhost/wp-admin](http://localhost/wp-admin/) - login: **admin**, password: **admin**


> _**Optional**_
> * [http://localhost/wp-admin/admin.php?page=mlang_settings](http://localhost/wp-admin/admin.php?page=mlang_settings) - Add _Polylang-Pro_ license if used in the website
> * [http://localhost/wp-admin/admin.php?page=mlang_settings](http://localhost/wp-admin/admin.php?page=mlang_settings) - check the option "Media > Automatically duplicate media in all languages when uploading a new file."
> * [http://localhost/wp-admin/admin.php?page=mlang](http://localhost/wp-admin/admin.php?page=mlang) - add languages for each website
>

Use
---

1. `yarn start` - start the styleguide & live assets compilation
2. `docker-compose up -d` - start WordPress

Access
------

* Website: [http://localhost](http://localhost)
* Website backend [http://localhost/wp-admin](http://localhost/wp-admin/) - login: **admin**, password: **admin**
* Phpmyadmin: [http://localhost:8181](http://localhost:8181)
* Styleguide: [http://localhost:4000](http://localhost:4000)
* Mailhog: [http://localhost:8025](http://localhost:8025)

_That's probably all you need to know!_ 🍻

Detailed Guide
==============

Debug
-----
You can enable WordPress & php debugs tools to help develop.

1. Set 1 to `WORDPRESS_DEBUG` environment variable in `./docker-compose.yml` file.
2. Enable xdebug by uncommenting line 13 in `./scripts/php.ini` file
3. Rebuild the container by running the command `docker-compose down && docker-compose build && docker-compose up -d`

Theming
-------
### Templating
We use [Timber](http://upstatement.com/timber/) to handle the templating.
The theme "les-verts" is based on [Timber Starter Theme](https://github.com/timber/starter-theme).

### Assets / styleguide
The assets are generated with our standard styleguide stack (see `./styleguide`).
To keep it as light and simple as possible, the docker image doesn't take care of the styleguide and assets pipeline.

#### Develop
The styleguide (fractal) and WordPress share the same assets build folder, which is in `./theme/static`.
Check `./theme/README.md` for more details.

WordPress tools
---------------
### WP-CLI
You can run any command with `docker exec`, for example:
`docker exec wp_docker_les_verts wp theme install Akismet`

_Official docs: [https://wp-cli.org/commands/](https://wp-cli.org/commands/)_

#### List installed plugins
* `docker exec wp_docker_les_verts wp plugin list`

#### Update plugins
* List needed updates:
	`docker exec wp_docker wp plugin update --all --dry-run`
* Update all plugins:
  `docker exec wp_docker_les_verts wp plugin update --all`
* Update a single plugin:
  `docker exec wp_docker_les_verts wp plugin update <pluginname>`

#### Install a plugin
* `docker exec wp_docker_les_verts wp plugin install <pluginname> --activate`
* OR add a submodule in `./wordpress/wp-content/plugins` if needed. If you know what you're doing.

### Import all ACF configs
* `docker exec package install git@github.com:superhuit-ch/wp-cli-acf-json.git` - if not already installed
* `docker exec wp_docker_les_verts wp acf-json sync --all_sites` - imports all the JSON ACF configs in the database

#### More
See [https://wp-cli.org/commands/plugin/](https://wp-cli.org/commands/plugin/)

ACF & Custom Post Types
-----------------------
We use ACF to add custom fields on post types (& elsewhere).

### Add a post type
1. Register a new post type class that extends the class `\SUPT\Model` located in `theme/lib/post-types/Model.php`
2. Include it in the loader file: `theme/lib/_loader.php`

### ACF - Advanced Custom Fields
To version, share and deploy the ACF fields we created, we use the feature called [Synchronized JSON](https://www.advancedcustomfields.com/resources/synchronized-json/).
Basically, it means we have nothing to take care of manually. A typical flow is:

#### Add/edit fields
1. Edit the fields as you want in the backend
2. They are automatically saved in the folder `theme/acf-json`, nothing to do here
3. When you commit, don't forget to commit these files as well

#### Sync all fields
By default, when the acf-json folder is present, WordPress loads the ACF config from there, so it's always in sync.
If you want to edit them in the backend though, wp will show "Available for sync". You can click manually "sync", or easier:

* `wp acf-json sync --all_sites` - import all the JSON ACF configs in the database

Deploy
======

* `yarn build` - Build assets
* `./deploy.sh` - Launch our automagically disruptive rsync-flavoured deploy script
* Enjoy 🌈

Useful commands
===============

* `yarn start` - Start the Styleguide
* `yarn wp:start` - Start WordPress
* `yarn start:all` - Start both the styleguide and WordPress
* `yarn build` - Build the styleguide assets for production.
* `yarn fractal:build` - Build the styleguide as a static site in _`./styleguide/dist/build`_
* `yarn fractal sass:generate` - auto-update the _loader.scss file with existing components
* `yarn wp:init` - Initialise the local WordPress instance
