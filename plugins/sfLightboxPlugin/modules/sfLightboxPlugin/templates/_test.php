
<?php use_helper('Lightbox'); ?>

<h1>EXAMPLE</h1>
<h2>SINGLE IMAGE</h2>

<?php

// Test light_image
echo light_image(
    'http://www.huddletogether.com/projects/lightbox2/images/thumb-1.jpg', 
    'http://www.huddletogether.com/projects/lightbox2/images/image-1.jpg', 
    array()
);

$image_options = array('title' => 'Optional caption.');    
echo light_image(
    'http://www.huddletogether.com/projects/lightbox2/images/thumb-2.jpg', 
    'http://www.huddletogether.com/projects/lightbox2/images/image-2.jpg', 
    $image_options
);

?>

<h2>IMAGE SET</h2>

<?php

// To display a slide show of several images
$images[] = array(
    'thumbnail' => 'http://www.huddletogether.com/projects/lightbox2/images/thumb-3.jpg',
    'image'     => 'http://www.huddletogether.com/projects/lightbox2/images/image-3.jpg',
    'options'   => array('title' => 'Roll over and click right side of image to move forward.')
);

$images[] = array(
    'thumbnail' => 'http://www.huddletogether.com/projects/lightbox2/images/thumb-4.jpg',
    'image'     => 'http://www.huddletogether.com/projects/lightbox2/images/image-4.jpg',
    'options'   => array('title' => 'Alternatively you can press the right arrow key.')
);

$images[] = array(
    'thumbnail' => 'http://www.huddletogether.com/projects/lightbox2/images/thumb-5.jpg',
    'image'     => 'http://www.huddletogether.com/projects/lightbox2/images/image-5.jpg',
    'options'   => array('title' => 'The script preloads the next image in the set as you\'re viewing.')
);

$images[] = array(
    'thumbnail' => 'http://www.huddletogether.com/projects/lightbox2/images/thumb-6.jpg',
    'image'     => 'http://www.huddletogether.com/projects/lightbox2/images/image-6.jpg',
    'options'   => array('title' => 'Press Esc to close')
);

$link_options = array(
    'title'     => 'Lightbox2',
    'slidename' => 'lightbox',
);    


echo light_slideshow($images, $link_options);

?>

<h2>MODAL BOX</h2>

<?php 

// Modal Lightbox plugin test
$link_options = array(
    'title' => 'sfLightboxPlugin',
    'size'  => '450x180',
    'speed' => '5'
);    

// or
//$link_options='title=sfLightboxPlugin size=450x180 speed=5';
//$link_options='title="sfLightboxPlugin" class=resizespeed_5 blocksize_450x180';
 
echo light_modallink('<h3>&raquo; Link to test the modal box &laquo;</h3>', 'sfLightboxPlugin/modal', $link_options);
  
?>