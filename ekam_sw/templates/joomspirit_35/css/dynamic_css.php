<?php
header("Content-type: text/css");
if (isset($_GET['color'])) 
{ $color = $_GET['color'];}
else { $color = '993366'; }
if (isset($_GET['font'])) 
{ $font = $_GET['font'];}
else { $font = 'sans-serif'; }
if (isset($_GET['fontlogo'])) 
{ $fontlogo = $_GET['fontlogo'];}
else { $fontlogo = 'sans-serif'; }
if (isset($_GET['font_content'])) 
{ $font_content = $_GET['font_content'];}
else { $font_content = 'sans-serif'; }
?>

/**		COLOR	**/

a, a:hover, .breadcrumb a:hover,
.user7 .link a:hover, .user8 .link a:hover, .user9 .link a:hover {
color : #<?php echo $color ; ?>;
}




/**		FONT	**/

.logo-text {
font-family: '<?php echo $fontlogo ; ?>', 'Yanone Kaffeesatz';
}

h1, .componentheading, h2.contentheading {
font-family: '<?php echo $font ; ?>', 'Yanone Kaffeesatz';
}

#main_menu li a, #main_menu li span.separator {
font-family: '<?php echo $font ; ?>', 'Yanone Kaffeesatz';
}

.left h3, .right h3, .user1 h3, .user2 h3, .user3 h3, 
.user4 h3, .user5 h3, .user6 h3 {
font-family: '<?php echo $font ; ?>', 'Yanone Kaffeesatz';
}

.submenu li a, .submenu li span.separator {
font-family: '<?php echo $font ; ?>', 'Yanone Kaffeesatz';
}

body {
font-family: '<?php echo $font_content ; ?>';
}