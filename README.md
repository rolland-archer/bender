Bender is a class that does a simple but very useful thing: it combines your CSS / Javascripts into one file (one for CSS and one for Javascript),
then minimizes these files on the fly. It makes your site load faster due to reduced number of HTTP requests. It also reduces server load and traffic.
Bender is written in pure PHP and can be used even on very restricted shared hostings. It does not require any other technology, such as Java or Python.

<h3>Changelog</h3>

Friday, November 1, 2013
- Changed a way to check if recombination / minification is required. Now it recombines / minimizes css and javascript files only if one of original
  files were changes, instead of forced compilation of all scripts based on time-to-live

Saturday, February 1, 2014
- library Now monitors not only the modification date of the file list, but also the composition and the paths to the files. So disabling file or connecting a new one or replacing js/main.js with main.js you will get a new cache file, and the old deleted automatically.

- At some point in the resulting files should start to break up into several. For example, preferably three files by 200kb than one of 600. So now can specify namespaces to pack files (see next point).

- Introduced namespaces collected files. This is done to remove old when regeneration + allows you to break a conclusion similar to several files. For example, code
```php
$bender->enqueue(array(
  'file-1.js'
  '/dir1/file-1.js' 
  '/dir2/file-1.js'
));

echo $bender->output_js('scripts', 'cache-1 ');

$bender->enqueue(array(
  'file-1.js'
  '/dir1/file-1.js' 
  '/dir2/file-1.js'
));

echo $bender->output_js ('scripts', 'cache-2');
```
will output
```html
<script type="text/javascript" src = "/scripts/cache-1_[hash file].js?v=[generation timestamp]" ></script>
<script type="text/javascript" src = "/scripts/cache-2_[hash file].js?v=[generation timestamp]" ></script>
```
4) I came across problems with packaging css, when they contains @import(...) and relative paths to resources - fonts or images. Since css can be also placed in different folders to structure, the level of the output directory for some is not the same level of input, and relative paths are violated. 
So library recursively replaces @import to content of imported files, and rewrites resource's paths, appending or removing unwanted segments.
