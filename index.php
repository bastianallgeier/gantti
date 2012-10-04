<?php

require('lib/gantti.php'); 
require('data.php'); 

date_default_timezone_set('UTC');
setlocale(LC_ALL, 'en_US');

$gantti = new Gantti($data, array(
  'title'      => 'Demo',
  'cellwidth'  => 25,
  'cellheight' => 35,
  'today'      => true
));

?>

<!DOCTYPE html>
<html lang="en">
<head>
  
  <title>Mahatma Gantti – A simple PHP Gantt Class</title>
  <meta charset="utf-8" />

  <link rel="stylesheet" href="styles/css/screen.css" />
  <link rel="stylesheet" href="styles/css/gantti.css" />

  <!--[if lt IE 9]>
  <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

</head>

<body>

<header>

<h1>Mahatma Gantti</h1>
<h2>A simple PHP Gantt Class</h2>

</header>

<?php echo $gantti ?>

<article>

<h2>Download</h2>

<p>
  You can download the source for Gantti form Github:<br />
  <a href="https://github.com/bastianallgeier/gantti"><strong>https://github.com/bastianallgeier/gantti</strong></a>
</p>

<h2>Features</h2>

<p>
  <ul>
    <li>Generates valid HTML5</li>
    <li>Very easy to customize with SASS stylesheet</li>
    <li>Works in all major browsers including IE7, IE8 and IE9</li>
    <li>No javascript required</li>
  </ul>
</p>

<h2>Usage</h2>

<p><pre><code><?php $code = "
<?php

require('lib/gantti.php'); 

date_default_timezone_set('UTC');
setlocale(LC_ALL, 'en_US');

\$data = array();

\$data[] = array(
  'label' => 'Project 1',
  'start' => '2012-04-20', 
  'end'   => '2012-05-12'
);

\$data[] = array(
  'label' => 'Project 2',
  'start' => '2012-04-22', 
  'end'   => '2012-05-22', 
  'class' => 'important',
);

\$data[] = array(
  'label' => 'Project 3',
  'start' => '2012-05-25', 
  'end'   => '2012-06-20'
  'class' => 'urgent',
);

\$gantti = new Gantti(\$data, array(
  'title'      => 'Demo',
  'cellwidth'  => 25,
  'cellheight' => 35
));

echo \$gantti;

?>

";

echo htmlentities(trim($code)); ?>
</pre></code></p>

<h2>Data</h2>

<p>Data is defined as an associative array (see the example above).</p>

<p>
  For each project you get the following options: 
  
  <ul>
    <li>label: The label will be displayed in the sidebar</li>  
    <li>start: The start date. Must be in the following format: YYYY-MM-DD</li>  
    <li>end:   The end date. Must be in the following format: YYYY-MM-DD</li>  
    <li>class: An optional class name. (available by default: important, urgent)</li>  
  </ul>

</p>

<h2>Options</h2>

<h3>title (optional, default: false)</h3>
<p>Set an optional title for your gantt diagram here. <br />It will be displayed in the upper left corner. </p>

<h3>cellwidth (optional, default: 40)</h3>
<p>Set the width of all cells. </p>

<h3>cellheight (optional, default: 40)</h3>
<p>Set the height of all cells. </p>

<h3>today (optional, default: true)</h3>
<p>Show or hide the today marker. It will be displayed by default.</p>

<h2>Styles</h2>

<p>
The default stylesheet is available as .scss (<a href="http://sass-lang.com/">SASS</a>)
It includes a set of predefined variables, which you can use to adjust the styles very easily. 
</p>
<p>
You can check out the full SASS file over here: 
<a href="https://github.com/bastianallgeier/gantti/blob/master/styles/scss/gantti.scss">https://github.com/bastianallgeier/gantti/blob/master/styles/scss/gantti.scss</a>
</p>


<h2>Colors</h2>

<p>The default color theme is an adaption of the wonderful <br /><a href="http://ethanschoonover.com/solarized">Solarized color theme by Ethan Schoonover</a></p>

<h2>Author</h2>

<p>
Bastian Allgeier<br />
<a href="http://bastianallgeier.com">http://bastianallgeier.com</a><br />
<a href="http://twitter.com/bastianallgeier">Follow me on Twitter</a>

</p>

<h2>License</h2>

MIT License – <a href="http://www.opensource.org/licenses/mit-license.php">http://www.opensource.org/licenses/mit-license.php</a>

</article>

<a href="https://github.com/bastianallgeier/gantti"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png" alt="Fork me on GitHub"></a>

</body>

</html>
