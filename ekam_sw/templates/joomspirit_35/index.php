<?php
/**
* @copyright	Copyright (C) 2011 - JoomSpirit. All rights reserved.
*/
defined('_JEXEC') or die('Restricted access');
$path = $this->baseurl.'/templates/'.$this->template;
$website_name = $this->params->get('website_name');
$website_slogan = $this->params->get('website_slogan');
$width_right = $this->params->get('width_right');
$width_left = $this->params->get('width_left');
$width = $this->params->get('width');
$user1_width = $this->params->get('user1_width');
$user3_width = $this->params->get('user3_width');
$user4_width = $this->params->get('user4_width');
$user6_width = $this->params->get('user6_width');
$user7_width = $this->params->get('user7_width');
$user9_width = $this->params->get('user9_width');
$shadow = $this->params->get('shadow');
$color = $this->params->get('color');
$font = $this->params->get('font');
$fontlogo = $this->params->get('fontlogo');
$font_content = $this->params->get('font_content');
$show_tooltips = $this->params->get('show_tooltips');
/*$js='<div class="js" ><a id="jslink" target="_blank" href="http://www.joomspirit.com" >free template joomla</a></div>';*/
if ($this->params->get('fontSize') == '') 
	{ $fontSize ='12px'; } 
else { $fontSize = $this->params->get('fontSize'); }

JHTML::_('behavior.mootools');
include_once(JPATH_ROOT . "/templates/" . $this->template . '/lib/php/layout.php');
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
    
<link href="http://fonts.googleapis.com/css?family=Skranji&subset=latin,latin-ext" rel="stylesheet" type="text/css"/>
    
<jdoc:include type="head" />

<!--	Google fonts	-->
<?php if ( $font != 'JosefinSansStdLight' ) : ?>
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=<?php echo $font ; ?>" />
<?php endif; ?>
<?php if ( $fontlogo != 'JosefinSansStdLight' ) : ?>
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=<?php echo $fontlogo ; ?>" />
<?php endif; ?>

<!-- style sheet links -->
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/main.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/nav.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/moomenuh.css" type="text/css" />
<link rel="stylesheet" media="screen" type="text/css" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/dynamic_css.php&#63;font=<?php echo $font ; ?>&amp;font_content=<?php echo $font_content ; ?>&amp;fontlogo=<?php echo $fontlogo ; ?>&amp;color=<?php echo substr($color, 1) ; ?>" />

<?php if( strstr($_SERVER['HTTP_USER_AGENT'],'Android') || strstr($_SERVER['HTTP_USER_AGENT'],'webOS') || strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPod') ) : ?>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/mobile.css" type="text/css" />
<?php endif; ?>

<!--[if lte IE 8]>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ie8.css" type="text/css" />
<![endif]-->
<!--[if lte IE 7]>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ie7.css" type="text/css" />
<![endif]-->
<!--[if lt IE 7]>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ie6.css" type="text/css" />
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/lib/js/iepngfix_tilebg.js"></script>
<style type="text/css">
* { behavior: url(<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/lib/js/iepngfix.htc) }
</style>
<![endif]-->

<!-- MOOMENU HORIZONTAL-->
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/lib/js/UvumiDropdown.js"></script>
<script type="text/javascript">
	var menu = new UvumiDropdown('main_menu');
</script>

<?php if ($show_tooltips == '1') : ?>
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/lib/js/tooltips.js"></script>
<?php endif; ?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $(".left .moduletable.tab").click(function(){
            $(".left").animate({left:"-15px"},"slow");
        });
            $(".left").mouseleave(function(){
            $(".left").animate({left:"-216px"},"slow");
        });
    });
</script>

</head>

<body <?php echo ('style="font-size:'.$fontSize.';"');?> >


	<?php if( ($this->countModules('breadcrumb')) || ($this->countModules('top_menu')) || ($this->countModules('translate')) || ($this->countModules( 'search' )) || ($this->params->get('twitter') != '') || ($this->params->get('blogger') != '') || ($this->params->get('delicious') != '') || ($this->params->get('yahoo') != '') || ($this->params->get('flickr') != '') || ($this->params->get('facebook') != '') || ($this->params->get('rss') != '') || ($this->params->get('linkedin') != '') || ($this->params->get('myspace') != '') || ($this->params->get('youtube') != '') || ($this->params->get('vimeo') != '') ) : ?>
	<div id="header-site">
		<div class="wrapper-site" style="width:<?php echo $width ; ?>;">
		

			<!--	SOCIAL LINKS	-->
			<?php if( ($this->params->get('twitter') != '') || ($this->params->get('blogger') != '') || ($this->params->get('delicious') != '') || ($this->params->get('flickr') != '') || ($this->params->get('facebook') != '') || ($this->params->get('yahoo') != '') || ($this->params->get('rss') != '') || ($this->params->get('linkedin') != '') || ($this->params->get('myspace') != '') || ($this->params->get('youtube') != '') || ($this->params->get('vimeo') != '') ) : ?>
			<div id="social-links">
			<ul>
				<?php if ($this->params->get('twitter') != '') : ?>
				<li><a target="_blank" id="twitter" title="Twitter" href="<?php echo $this->params->get('twitter') ?>">Twitter</a></li>
				<?php endif; ?>		
				<?php if ($this->params->get('blogger') != '') : ?>
				<li><a target="_blank" id="blogger" title="Blogger" href="<?php echo $this->params->get('blogger') ?>">Blogger</a></li>
				<?php endif; ?>
		
				<?php if ($this->params->get('delicious') != '') : ?>
				<li><a target="_blank" id="delicious" title="Delicious" href="<?php echo $this->params->get('delicious') ?>">Delicious</a></li>
				<?php endif; ?>
				<?php if ($this->params->get('facebook') != '') : ?>
				<li><a target="_blank" id="facebook" title="Facebook" href="<?php echo $this->params->get('facebook') ?>">Facebook</a></li>
				<?php endif; ?>
				<?php if ($this->params->get('rss') != '') : ?>
				<li><a target="_blank" id="rss" title="RSS" href="<?php echo $this->params->get('rss') ?>">RSS</a></li>
				<?php endif; ?>
				<?php if ($this->params->get('linkedin') != '') : ?>
				<li><a target="_blank" id="linkedin" title="Linkedin" href="<?php echo $this->params->get('linkedin') ?>">Linkedin</a></li>
				<?php endif; ?>
				<?php if ($this->params->get('myspace') != '') : ?>
				<li><a target="_blank" id="myspace" title="Myspace" href="<?php echo $this->params->get('myspace') ?>">Myspace</a></li>
				<?php endif; ?>
		
				<?php if ($this->params->get('flickr') != '') : ?>
				<li><a target="_blank" id="flickr" title="Flickr" href="<?php echo $this->params->get('flickr') ?>">Flickr</a></li>
				<?php endif; ?>
				<?php if ($this->params->get('youtube') != '') : ?>
				<li><a target="_blank" id="youtube" title="Youtube" href="<?php echo $this->params->get('youtube') ?>">Youtube</a></li>
				<?php endif; ?>
				<?php if ($this->params->get('vimeo') != '') : ?>
				<li><a target="_blank" id="vimeo" title="Vimeo" href="<?php echo $this->params->get('vimeo') ?>">Vimeo</a></li>
				<?php endif; ?>
				<?php if ($this->params->get('yahoo') != '') : ?>
				<li><a target="_blank" id="yahoo" title="Yahoo" href="<?php echo $this->params->get('yahoo') ?>">Yahoo</a></li>
				<?php endif; ?>				
				
				
			</ul>
			</div>
			<?php endif; ?>
			
			
			<?php if ($this->countModules( 'search' )) : ?>
			<div id="search">
				<jdoc:include type="modules" name="search" style="xhtml" />
			</div>	
			<?php endif; ?>

			<?php if($this->countModules('translate')) : ?>
			<div id="translate">
				<jdoc:include type="modules" name="translate" style="xhtml" />
			</div>	
			<?php endif; ?>
			
			<?php if($this->countModules('top_menu')) : ?>
			<div id="top_menu">
				<jdoc:include type="modules" name="top_menu" style="xhtml" />
			</div>	
			<?php endif; ?>			
			
			<?php if($this->countModules('breadcrumb')) : ?>
			<div class="breadcrumb">
				<jdoc:include type="modules" name="breadcrumb" style="xhtml" />
			</div>	
			<?php endif; ?>
			

		</div>		<!-- end of wrapper-site		-->
	</div>			<!-- end of header-site		-->
	<?php endif; ?>



	<div class="wrapper-site-content <?php echo $shadow ; ?>" style="width:<?php echo $width ; ?>;">
	
		<div id="header">
			
			<?php if($this->countModules('logo')) : ?>
			<div id="logo" >
				<jdoc:include type="modules" name="logo" style="xhtml" />
			</div>
			<?php else : ?>
			<div id="logo" class="logo-text">
				<?php echo $website_name ; ?>
				<?php if ($website_slogan != "") : ?>
				<span class="slogan"><?php echo $website_slogan ; ?></span>
				<?php endif; ?>
			</div>		
			<?php endif; ?>
	
			
			<?php if($this->countModules('menu')) : ?>
			<div id="menu">
				<jdoc:include type="modules" name="menu" style="xhtml" />
			</div>
			<?php endif; ?>
			
		</div>	<!--	END OF HEADER	-->
	
		
		<?php if($this->countModules('image')) : ?>
		<div id="image">
			<jdoc:include type="modules" name="image" style="xhtml" />
		</div>
		<?php endif; ?>
	
	
		<?php if(!$this->countModules('home_page')) : ?>
	
					
			<div id="content-area">	
			
				<div id="content">
				
					<!--  USER 1, 2, 3 -->
					<?php if($this->countModules('user1') || $this->countModules('user2') || $this->countModules('user3')) : ?>
					<div id="users_top">
																
						<?php if($this->countModules('user1')) : ?>
						<div class="user1" <?php echo ('style="width:'.$user1_width.';"');?>>
							<jdoc:include type="modules" name="user1" style="joomspirit" />
						</div>
						<?php endif; ?>
											
						<?php if($this->countModules('user3')) : ?>
						<div class="user3" <?php echo ('style="width:'.$user3_width.';"');?>>
							<jdoc:include type="modules" name="user3" style="joomspirit" />
						</div>
						<?php endif; ?>
					
						<?php if($this->countModules('user2')) : ?>
						<div class="user2">
							<jdoc:include type="modules" name="user2" style="joomspirit" />
						</div>
						<?php endif; ?>
					
						<div class="clr"></div>
											
					</div>
					<?php endif; ?>  <!--	END OF USERS TOP	-->
					
					<div id="main_component" >
					
						<?php if($this->countModules('right')) : ?>
						<div class="right" style="width:<?php echo $width_right ; ?>;">
							<jdoc:include type="modules" name="right" style="joomspirit" />
						</div>
						<?php endif; ?>				
					
						<div class="main-content">
							<!--  MAIN COMPONENT -->
							<jdoc:include type="message" />
							<jdoc:include type="component" />
						</div>	
							
					</div>
					
					<!--  USER 4, 5, 6 -->
					<?php if($this->countModules('user4') || $this->countModules('user5') || $this->countModules('user6')) : ?>
					<div id="users_bottom">
																
						<?php if($this->countModules('user4')) : ?>
						<div class="user4" <?php echo ('style="width:'.$user4_width.';"');?>>
							<jdoc:include type="modules" name="user4" style="joomspirit" />
						</div>
						<?php endif; ?>
											
						<?php if($this->countModules('user6')) : ?>
						<div class="user6" <?php echo ('style="width:'.$user6_width.';"');?>>
							<jdoc:include type="modules" name="user6" style="joomspirit" />
						</div>
						<?php endif; ?>
					
						<?php if($this->countModules('user5')) : ?>
						<div class="user5">
							<jdoc:include type="modules" name="user5" style="joomspirit" />
						</div>
						<?php endif; ?>
					
						<div class="clr"></div>
											
					</div>
					<?php endif; ?>  <!--	END OF USERS BOTTOM	-->
			
				</div>	<!-- END OF CONTENT	-->
				
				<div class="clr"></div>
			
			</div>	<!-- end of content area		-->
                        
                        <?php if($this->countModules('left')) : ?>
				<div class="left" style="width:<?php echo $width_left ; ?>;">
					<jdoc:include type="modules" name="left" style="joomspirit" />
				</div>
			<?php endif; ?>
                        
			
		<?php endif; ?>		<!-- end of condition for module "home_page" 	-->
	
	</div>		<!-- end of wrapper-site	-->
	
		
			
	<div class="wrapper-site" style="width:<?php echo $width ; ?>;">
	
		<div id="footer">
					
					<!--  USER 7, 8, 9 -->
					<?php if($this->countModules('user7') || $this->countModules('user8') || $this->countModules('user9')) : ?>
					<div id="users_footer">
																
						<?php if($this->countModules('user7')) : ?>
						<div class="user7" <?php echo ('style="width:'.$user7_width.';"');?>>
							<jdoc:include type="modules" name="user7" style="joomspirit" />
						</div>
						<?php endif; ?>
											
						<?php if($this->countModules('user9')) : ?>
						<div class="user9" <?php echo ('style="width:'.$user9_width.';"');?>>
							<jdoc:include type="modules" name="user9" style="joomspirit" />
						</div>
						<?php endif; ?>
					
						<?php if($this->countModules('user8')) : ?>
						<div class="user8">
							<jdoc:include type="modules" name="user8" style="joomspirit" />
						</div>
						<?php endif; ?>
					
						<div class="clr"></div>
											
					</div>
					<?php endif; ?>  <!--	END OF USERS FOOTER	-->			
					
					<?php if($this->countModules('address')) : ?>
					<div id="address">
						<jdoc:include type="modules" name="address" style="xhtml" />
					</div>
					<?php endif; ?>
					
					<?php if($this->countModules('bottom_menu')) : ?>
					<div id="bottom_menu">
						<jdoc:include type="modules" name="bottom_menu" style="xhtml" />
					</div>
					<?php endif; ?>
		
		</div>	<!-- end of footer		-->
	
	</div>		<!-- end of wrapper-site	-->

</body>
</html>