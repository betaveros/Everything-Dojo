<?php
  $title = '404';
  include("include/include.php");
  $extra_style = "<style>
      .not-found-img.centre {
        display: block;
        margin: 0 auto; /* Centers the image */
        width: 80vh;
      }

      .email::after {
        content: \" \\2709\"; /* Adds an envelope after emails */
        font-size: 18px;
      }
    </style>";
  get_header();
?>

<img class="not-found-img centre" src="data:image/png;base64,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" />
<h1 class="error-header">404 Not Found</h1>
<p class="error-explain">Sorry, we seem to have misplaced the page or document you requested. Either that, or we killed it (with fire!), or it didn't exist in the first place. Anywho, you can try a couple of things:</p>
<ul class="error-action">
  <li><a class="back-link" href="javascript:history.back()">Go back</a> from whence you came!</li>
  <li>Go to the <a class="index-link" href="index.php">index</a>.</li>
  <li>Take a look at all the fancy-schmancy styles available to you on the <a class="db-link" href="themedb.php">Theme Database</a> page.</li>
  <li>Build your own theme with the <a class="themizer-link" href="themizer.php">Themizer</a>.</li>
  <li>If you want to test out a theme before using it on your blog, <a class="tryit-link" href="tryit.php">Try-it</a> is the place for you.</li>
  <li>Talk about stuff (like themes, HTML, and rants on 404 pages) on the <a class="discussion-link" href="discussion.php">fora</a> of this site.</li>
  <li>Report this page to us at <a class="email" href="mailto:error@everythingdojo.com">error@evverythingdojo.com</a></li>
<ul>

<?php get_footer(); ?>
