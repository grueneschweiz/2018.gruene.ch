---
title: Grid System
---

A responsive, mobile first fluid grid system, based on *CSS Grid layout*, that appropriately scales up to 14 columns as the device or viewport size increases.

Drag & use this <a href="javascript: (function () {var el = document.body;var className = 'grid-debugger';if (el.classList) {el.classList.toggle(className);} else {var classes = el.className.split(' ');var existingIndex = classes.indexOf(className);if (existingIndex >= 0)classes.splice(existingIndex, 1);elseclasses.push(className);el.className = classes.join(' ');}})();">grid debugger</a> bookmarklet to toggle a developpement helper for the grid.

***

> _**NOTE**_
>
> *To change the number of columns (or other grid parameters), edit the sass variables*
>
> <i>**`$grid-xxx`** in **`src/scss/_variables.scss`** file to your desired grid system setup.</i>

***

Basic example
----------

The example below shows six elements aligned on the columns of the grid system. Items have been placed onto the grid using line-based placement.

<div class="grid-wrapper-for-doc">
<section id="basic" class="grid">
  <div class="col one">First</div>
  <div class="col two">Second</div>
  <div class="col three">Third</div>
  <div class="col four">Fourth</div>
  <div class="col five">Fith</div>
  <div class="col six">Sixth</div>
</section>
<style>
  #basic .one { grid-column: 1 / span 3; }
  #basic .two { grid-column: 4 / span 8; }
  #basic .three { grid-column: 12 / span 3; }
  #basic .four { grid-column: 1 / span 4; }
  #basic .five { grid-column: 5 / span 6; }
  #basic .six { grid-column: 11 / span 4; }
</style>
</div>

### HTML
```
<section class="wrapper">
  <div class="one">First</div>
  <div class="two">Second</div>
  <div class="three">Third</div>
  <div class="four">Fourth</div>
  <div class="five">Fith</div>
  <div class="six">Sixth</div>
</section>
```

### SCSS
```
.wrapper {
  @extend .grid;
}

.one {
  grid-column: 1 / span 3;
}
.two {
  grid-column: 4 / span 8;
}
.three {
  grid-column: 12 / span 3;
}
.four {
  grid-column: 1 / span 4;
}
.five {
  grid-column: 5 / span 6;
}
.six {
  grid-column: 11 / span 4;
}
```

Responsive
----------

To enable a responive layout, set the `gri-column` property in a media query.

<div class="grid-wrapper-for-doc">
<section id="responsive" class="grid">
  <div class="col one">First</div>
  <div class="col two">Second</div>
  <div class="col three">Third</div>
  <div class="col four">Fourth</div>
  <div class="col five">Fith</div>
  <div class="col six">Sixth</div>
</section>
<style>
  #responsive .one,
  #responsive .two,
  #responsive .three,
  #responsive .four,
  #responsive .five,
  #responsive .six { grid-column: 1 / span 14; }
  @media (min-width: 1024px) {
    #responsive .one,
    #responsive .three,
    #responsive .five { grid-column: 1 / span 7 }
    #responsive .two,
    #responsive .four,
    #responsive .six { grid-column: 8 / span 7; }
  }
  @media (min-width: 1280px) {
    #responsive .one { grid-column: 1 / span 3; }
    #responsive .two { grid-column: 4 / span 8; }
    #responsive .three { grid-column: 12 / span 3; }
    #responsive .four { grid-column: 1 / span 4; }
    #responsive .five { grid-column: 5 / span 6; }
    #responsive .six { grid-column: 11 / span 4; }
  }
</style>
</div>

### SCSS
```
.wrapper {
  @extend .grid;
}
.one {
  grid-column: 1 / span 14;

  @include media(768px) {
    grid-column: 1 / span 7
  }
  @include media(1024px) {
    grid-column: 1 / span 3;
  }
}
.two {
  grid-column: 1 / span 14;

  @include media(768px) {
    grid-column: 8 / span 7;
  }
  @include media(1024px) {
    grid-column: 4 / span 8;
  }
}
.three {
  grid-column: 1 / span 14;

  @include media(768px) {
    { grid-column: 1 / span 7 }
  }
  @include media(1024px) {
    grid-column: 12 / span 3;
  }
}
.four {
  grid-column: 1 / span 14;

  @include media(768px) {
    grid-column: 8 / span 7;
  }
  @include media(1024px) {
    grid-column: 1 / span 4;
  }
}
.five {
  grid-column: 1 / span 14;

  @include media(768px) {
    { grid-column: 1 / span 7 }
  }
  @include media(1024px) {
    grid-column: 5 / span 6;
  }
}
.six {
  grid-column: 1 / span 14;

  @include media(768px) {
    grid-column: 8 / span 7;
  }
  @include media(1024px) {
    grid-column: 11 / span 4;
  }
}
```

***

> _**TO GO DEEPER**_
>
> *Learn more about **CSS Grid layout** in a complete and well done guide at **CSS-Tricks***
>
> _https://css-tricks.com/snippets/css/complete-guide-grid/_

***
⤺ _[back to docs homepage](overview)_
