# xx.css


***
## What is it?

`xx` (pronounced <i>double ex</i>) is a sort of CSS library-- think of it as a way to code your inline styles as shorthand classes. It really makes life easier when you've got the hang of it.

`xx` has been designed to replace a vast majority of use cases for inline style tags. Now, your HTML can be cleaner thanks to over 3,500 classes ready to use in any development project. `xx` includes mobile-specific versions of every class as well, so coding responsive designs is easier than ever.

In some tests, `xx` reduced total style declaration letter count by 70%.

***

## How does it work?

First, you've got to include the script (or a rendered css sheet) in your project like so (DUH):

**Minified:**
    <link rel="stylesheet" type="text/css" href="/path/to/xx/dist/xx.min.css">

**Pretty**
    <link rel="stylesheet" type="text/css" href="/path/to/xx/dist/xx.css">

Now you can start implementing the xx classes into your code, instead of their long-winded `style` attribute counterparts. For example, the following code:

    <span style="font-size:22px;font-weight:600;opacity:0.6;font-style:italic;display:inline-block;margin:10px;margin-top:20px;">
        Cool Internet
    </span>

can be conveniently recreated using these shorthand classes:

    <span class="f22 w600 o6 i inbloc m10 drag20"> Cooler internet! </span>

***


## How the f*ck do I compile the CSS?

If you made added to the generated properties (in `xx.json`) or you lost your copy of the CSS files, you might wanna generate the CSS yourself.

Got PHP installed? I thought so. `cd` to the src directory in terminal, then run `php xx.php`. It will automatically build pretty and minified css files to the `/dist` directory.

***


## Why is it called `xx`?

Come on. You know about variables right? **xx** refers to the fact that MOST of the classes consist of a prefix, followed by a numerical value. xx references the variable nature of this numerical value.

For instance, setting font-size to 15px pixels is written as `f15`, setting margin left to 30px is written as `ml30`, and setting a max-width of 50% is written as `max50p`. These could be visualized simply as `fxx`, `mlxx`, and `maxxXp`. Some classes use numeric values of 1 digit (i.e. `opacity: 0.5` is written as `o5`) and some use 3 digits (for example, `max-width:500px` is `max500`), but the principle remains the same.

***

## Where's the cheatsheet? How do I know what codes to use? This isn't documentation.

Relax. Just take a look at the non-minified CSS file. Cmd+F to find the css property you wanna change. The rest will be obvious. There are a TON of shortcodes.

***

## Bower Install

If you wanna install via bower, just do `bower install xx`. Easy peasy!
