Please prefer the variable over hardcoded value in Sass files.

*If needed you can copy the color hex value directly by clicking on it.*

<p class="error-msg">
	Can't copy, hit Ctrl+C!
</p>

{{#each colors as |palette key|}}
<div class="color-palette">
	<h3>{{ palette.title }}</h3>
	<div class="colors">
		{{#each values as |value key|}}
			<div class="color" style="color: {{ value }};">
				<div class="color-values">
					<pre>$color-{{@key}}</pre>
					<div>{{value}}</div>
				</div>
			</div>
		{{/each}}
	</div>
</div>
{{/each}}
