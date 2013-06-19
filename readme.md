## Overview
This is a quick command line CSS & JS minifier. Designed to be run during the build phase of a web app's deployment. It creates a second version of every .js and .css file (with the extension .min.js and .min.css). It's left up to the dev to decide how to reference these minified versions in production.

It uses the [Closure Compilier](https://developers.google.com/closure/compiler/) (by Google) to minify the JavaScript. It uses the [CssMin PHP project](https://code.google.com/p/cssmin/) (maintained via [composer](http://getcomposer.org/)) to minify the css.

## Usage
- Requires PHP >= 5.4 (earlier versions untested)
- Requires Java (for Closure)

### Command Line Format
Specify any options and then specify the files or directories that you'd like the minifier to work through.

`php minify.php (options) (file1 (file2 (...)))`

### Command Line Options
- `-r` : Recurse through any sub-directories that may be present.

### Example
`php minify.php -r static/css/ static/js/`

### License

- The Closure Compilier is liscensed under [Apache 2](http://www.apache.org/licenses/LICENSE-2.0)
- Everything else is liscensed under [MIT](http://opensource.org/licenses/MIT)
