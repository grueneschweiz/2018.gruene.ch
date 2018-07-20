
This is a living documentation built to illustrate all the elements available for the whole family of Les Verts Website.

## Usage

Basically, you just need to include these two files in your products:

```
<link rel="stylesheet" href="dist/static/style.min.css">
```
```
<script src="dist/static/js/app.min.js"></script>
```

The CSS depends on a couple of other assets (fonts, images…); you will find them in the `dist/static` folder once your project has been built (after running `yarn && yarn build`).

If you just want to integrate some specific components, and/or want a deeper understanding of how to use the current web interface, please have a look at the detailed [Web UI documentation](docs/how-to-use-this-interface).

## Design principles and conventions

The architecture of this project is based on the [atomic web design](docs/atomic-design) principles.

[CSS class names](docs/class-names) are prefixed based on these principles and also follow the [BEM convention](http://getbem.com/naming/) _(in short, BEM stands for **B**lock **E**lement **M**odifier)_.


## Code

The code is available the [GitLab repo](https://gitlab.com/superhuit/les-verts-stack).

This project (including the documentation you are currently browsing) has been built with [Fractal](https://fractal.build), which should, by design, be flexible enough to be integrated into any environment.


## Contact

Questions, suggestions? Feel free to [mail superhuit](mailto:tech+lesverts@superhuit.ch)

_[superhuit](https://www.superhuit.ch)_
