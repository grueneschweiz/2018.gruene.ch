Les Verts Stack
====================

Les Verts (Die Gruene) new website with living styleguide made with Fractal.build, hosted on WordPress Multisite and with the latest technologies by superhuit.ch.

Living Styleguide: https://superhuit.gitlab.io/les-verts-stack/

Installation
------------

### Requirements

* [node](https://nodejs.org/) 8.11.0 <= node < 10 (Do not use node v10 as it breaks build process)
* [yarn](https://yarnpkg.com) >= 1.7.0

### Getting Started

**Styleguide**

1. `yarn` - Install all dependencies
2. `yarn start` - start the styleguide & live assets compilation.
3. View the styleguide [http://localhost:4000](http://localhost:4000)

That's probably all you need to know!

### Useful commands

* `yarn build` - build the styleguide assets & optimize them.
* `yarn fractal sass:generate` - auto-update the _loader.scss file with existing components

@frctl/fractal @frctl/handlebars @frctl/mandelbrot
