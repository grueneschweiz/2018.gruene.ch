# GREEN Websites

The GREENS of Switzerland's website with a living styleguide made with Fractal.build, based on WordPress 
(Multisite ready) and with the fancy design and the latest technologies by 
[superhuit.ch](https://superhuit.ch) 🚀, coded by [gruene.ch](https://gruene.ch) 🤓

* [Get the latest version of the theme](https://grueneschweiz.github.io/2018.gruene.ch/theme/les-verts.zip) 📦
* [Report a bug or request a feature](https://github.com/grueneschweiz/2018.gruene.ch/issues/new) 🕷
* [Contribute](.github/CONTRIBUTING.md) 💻

Your not a webmonkey and you just want a **website ready to go**?
* [Turnkey Ready Website](https://extern18.gruene.ch/musterperson/angebot) 🤩
* [Manual - Help Pages](https://docs.gruene.ch) 🚨

> As member of the GREEN party, feel free to use it for your personal website. As party, please contact us 
([gruene@gruene.ch](mailto:gruene@gruene.ch)).

Support the development with a [donation](https://gruene.ch/spenden) 💚

---

# How to install the Theme

1. Download the [latest theme package](https://grueneschweiz.github.io/2018.gruene.ch/theme/les-verts.zip)
1. Install and activate it in WordPress
1. Install the suggested plugins (as announced in the WordPress' admin notices). **Note**: You must buy and manually 
install the premium plugin [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/pro/).
1. Configure your site to use a static homepage (else your homepage will stay blank): `Settings` > `Reading` > 
`Your homepage displays` > `A static page`

## Using Sanuk Font
Since Sanuk has a very strict licence, we've bundled the free alternative Passion One Bold. 
To use Sanuk, upload it into the `wp-content` folder as follows:
```
wp-content/
  sanuk/
    font.eot
    font.ttf
    font.woff
    font.woff2
```
Verify, you name the files exactly as above, and you provide all those file formats.

---

# Hints

* The expensive tasks after form submission are processed asynchronously (using wp cron). Use the WP_CLI to manage the
  jobs `wp form`.
* Dedicated docs:
  * [SSO setup](docs/sso.md)
  * [Progressbar](docs/progressbar.md)
* The upload of PNGs and JPEGs can be limited in their dimensions (to prevent imagemagick memory issues). To do so, add
  the following constants to your `wp-config.php`:
  ```php
  define( 'SUPT_UPLOAD_MAX_PX_PNG', 1920 * 1920 ); // max area of 1920px * 1980px
  define( 'SUPT_UPLOAD_MAX_PX_JPEG', 4096 * 4096 ); // max area of 4096px * 4096px
  ```
