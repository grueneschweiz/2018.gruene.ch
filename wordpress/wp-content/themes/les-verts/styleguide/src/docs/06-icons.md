To add an icon in the icon system, create the icon svg file in `src/icons` folder.

All icons will be included in the `static/icons.svg` SVG sprite.

To include an icon, copy and paste the markup below

*Make sure you replace the identifier `home` (the word just after the `#`) with the desired icon name chosen from the list below and the `Accueil` with the wanted title*

***

**Example:**

Here is the full rendered markup for the **home** icon:

```
<svg role="img">
    <title>Give me a title</title>
	<use xlink:href="#home"></use>
</svg>
```

***

Available icons
---------------

<div class="doc-icons">
	{{#each groups }}
	<h3>{{ name }}</h3>
	<ul>
		{{#each icons }}
		<li>
			<div class="doc-icons__icon">
				<svg role="img">
					<use xlink:href="#{{ . }}" />
				</svg>
				<h4>{{ . }}</h4>
			</div>
		</li>
		{{/each }}
	</ul>
	{{/each }}
</div>
{{inline_svg 'icons.svg' style="display:none;" }}

***
⤺ _[back to docs homepage](overview)_
