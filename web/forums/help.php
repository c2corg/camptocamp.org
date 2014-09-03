<?php
// this is not efficient (we have a first redirection, and short article url will cause another one but
// - i don't want to change every link to help.php in punbb code
// - not sure this page is called often anyway
header('Location: /articles/526013');
