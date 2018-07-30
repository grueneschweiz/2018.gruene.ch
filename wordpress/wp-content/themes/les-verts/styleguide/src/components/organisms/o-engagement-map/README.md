# Notes
Make sure the ids of the svg match the keys of the address (prefixed with 
`m-address-`) and the values of the select options (case sensitive).

## Example
The svg markup:
```svg
<div class="a-map">
    <svg viewBox="0 0 838 542" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <g id="Symbols" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <g id="comp/map/default/_LG" transform="translate(-32.000000, -36.000000)">
                <g transform="translate(32.000000, 36.000000)" id="comp/map">
                    <g>
                        <g id="map-items" transform="translate(0.588997, 3.325623)">
                            <g class="map-item" id="aargau" transform="translate(447.000000, 159.000000)">
                                <path d="........"></path>
                                <text>
                                    <tspan x="........" y="........">AG</tspan>
                                </text>
                            </g>
                            <g class="map-item" id="appenzell-innerhoden" transform="translate(447.000000, 159.000000)">
                                <path d="........"></path>
                                <text>
                                    <tspan x="........" y="........">AG</tspan>
                                </text>
                            </g>
                            ........
                        </g>
                    </g>
                </g>
            </g>
        </g>
    </svg>
</div>
```

The select markup:
```html
<div class="o-engagement-map__selector">
    <div class="a-select">
        <label for="party-selector" class="a-select__label ">
            Kanton auswählen
        </label>
        <div class="a-select__background">
            <select id="party-selector" class="a-select__field " name="party-selector">
                    <option value=""></option>
                    <option value="aargau">Aargau</option>
                    <option value="appenzell-innerhoden">Appenzell innerhoden</option>
                    .......
            </select>
        </div>
    </div>
</div>
```

The dialog markus:
```html
<div class="o-engagement-map__dialog">
    <div class="m-address-dialog" id="m-address-aargau">
        <div class="m-address-dialog__main">
            <div class="m-address-dialog__address">
                <div class="a-address">
                    <p class="a-address__name">Grüne Aargau</p>
                    <p class="a-address__address">Musterstrasse 99<br>0000 Beispielort</p>
                    <a href="tel:+41123456789" class="a-address__phone">012 345 67 89</a>
                </div>
            </div>
            <div class="m-address-dialog__address">
                <a href="https://example.com" class="m-address-dialog__link">Junge Grüne Aargau</a>
            </div>
        </div>
        <button class="m-address-dialog__close" aria-label="Schliessen">
            <svg title="" role="img">
                <use xlink:href="#close"/>
            </svg>
        </button>
    </div>
    <div class="m-address-dialog" id="m-address-appenzell-innerhoden">
        <div class="m-address-dialog__main">
            <div class="m-address-dialog__address">
                <div class="a-address">
                    <p class="a-address__name">Grüne Appenzell Innerhoden</p>
                    <p class="a-address__address">Musterstrasse 99<br>0000 Beispielort</p>
                    <a href="tel:012 345 67 89" class="a-address__phone">012 345 67 89</a>
                </div>
            </div>
            <div class="m-address-dialog__address">
                <a href="https://example.com" class="m-address-dialog__link">Junge Grüne Ostschweiz</a>
            </div>
            </div>
            <button class="m-address-dialog__close" aria-label="Schliessen">
                <svg title="" role="img">
                    <use xlink:href="#close"/>
                </svg>
            </button>
        </div> 
    </div>
    .......
</div>
```