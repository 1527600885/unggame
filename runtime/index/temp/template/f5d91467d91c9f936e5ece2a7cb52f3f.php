<?php /*a:3:{s:61:"/www/wwwroot/www.unicgm.com/public/themes/template/index.html";i:1663548936;s:69:"/www/wwwroot/www.unicgm.com/public/themes/template/common/header.html";i:1663320458;s:69:"/www/wwwroot/www.unicgm.com/public/themes/template/common/footer.html";i:1663548490;}*/ ?>

<!DOCTYPE html>
<html>
<head> 
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php echo htmlentities($seo_title); ?></title>
	<meta name="keywords" content="<?php echo htmlentities($seo_keywords); ?>">
	<meta name="description" content="<?php echo htmlentities($seo_description); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="baidu-site-verification" content="code-09qLlhlH1O" />
	<link rel="shortcut icon" href="/upload/favicon.ico">
	<link rel="stylesheet" type="text/css" href="/themes/template/static/css/bootstrap.min.css"/>
	<link rel="stylesheet" type="text/css" href="/themes/template/static/css/onekey.min.css"/>
	<link rel="stylesheet" type="text/css" href="/themes/template/static/css/animates.css"/>
	<link rel="stylesheet" type="text/css" href="/themes/template/static/css/font-awesome.min.css"/>
	<link rel="stylesheet" type="text/css" href="/themes/template/static/css/jquery.mmenu.all.css"/>
	<link rel="stylesheet" type="text/css" href="/themes/template/static/css/swiper.min.css"/>
	<script type="text/javascript" src="/themes/template/static/js/jquery.min.js"></script>
	<script type="text/javascript" src="/themes/template/static/js/jquery.mmenu.all.js"></script>
	<script type="text/javascript" src="/themes/template/static/js/masonry.pkgd.min.js"></script>
	<script type="text/javascript" src="/themes/template/static/js/swiper.animate.min.js"></script>
	<script type="text/javascript" src="/themes/template/static/js/swiper.min.js"></script>
	<script type="text/javascript" src="/themes/template/static/js/wow.min.js"></script>
	<script type="text/javascript" src="/themes/template/static/js/common.js"></script>
</head>
<body>
<div id="page">
<header id="header">
	<div class="container">
		<div class="logo"><a href="/"><img src="<?php echo htmlentities($system['logo']); ?>" alt="" /></a></div>
		<div class="tel"><i class="fa fa-phone"></i> <span><?php echo htmlentities($system['telephone']); ?></span></div>
		<nav class="nav">
			<ul>
				<?php if(is_array($catalogHeader) || $catalogHeader instanceof \think\Collection || $catalogHeader instanceof \think\Paginator): $i = 0; $__LIST__ = $catalogHeader;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item1): $mod = ($i % 2 );++$i;?>
				<li>
					<a href="<?php echo htmlentities($item1['url']); ?>"><?php echo htmlentities($item1['title']); ?></a>
					<ul>
						<?php if(is_array($item1['children']) || $item1['children'] instanceof \think\Collection || $item1['children'] instanceof \think\Paginator): $i = 0; $__LIST__ = $item1['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item2): $mod = ($i % 2 );++$i;?>
						<li><a href="<?php echo htmlentities($item2['url']); ?>" ><?php echo htmlentities($item2['title']); ?></a></li>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</li>
				<?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</nav>
		<a href="#menu" class="mm_btn">
			<div class="menu_bar">
				<div class="menu_bar_item top">
					<div class="rect top"></div>
				</div>
				<div class="menu_bar_item mid">
					<div class="rect mid"></div>
				</div>
				<div class="menu_bar_item bottom">
					<div class="rect bottom"></div>
				</div>
			</div>
		</a> 
	</div>
</header>
<nav id="menu" class="mm-menu_offcanvas">
	<div id="panel-menu">
		<ul>
			<?php if(is_array($catalogHeader) || $catalogHeader instanceof \think\Collection || $catalogHeader instanceof \think\Paginator): $i = 0; $__LIST__ = $catalogHeader;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item1): $mod = ($i % 2 );++$i;?>
			<li>
				<a href="<?php echo htmlentities($item1['url']); ?>"><?php echo htmlentities($item1['title']); ?></a>
				<ul>
					<?php if(is_array($item1['children']) || $item1['children'] instanceof \think\Collection || $item1['children'] instanceof \think\Paginator): $i = 0; $__LIST__ = $item1['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item2): $mod = ($i % 2 );++$i;?>
					<li><a href="<?php echo htmlentities($item2['url']); ?>" ><?php echo htmlentities($item2['title']); ?></a></li>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</li>
			<?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
	</div>
</nav>	
<section class="col-index-banner col-banner" id="100001">
	<div class="swiper-container banner-container">
		<div class="swiper-wrapper">
			<div class="swiper-slide">
				<div style="background:url(<?php echo htmlentities($catalog['cover']); ?>);" class="img slide-img"></div>
				<div class="slide-content i1">
					<h1><?php echo htmlentities($system['frame']); ?></h1>
				</div>
			</div>
		</div>
		<div class="swiper-page">
			<div class="swiper-pagination"></div>
		</div>
	</div>
</section>
<section class="col-product-wrap">
	<div class="container">
		<div class="row col-product-1">
			<div class="col-md-8 col-pad-0">
				<div class="col-web-design">
					<div class="inner-container">
						<h2 class="title-head wow fadeInLeft"><?php echo htmlentities($label['characteristic']); ?></h2>
						<div class="main-content wow fadeInLeft" data-wow-delay=".2s"><?php echo htmlentities($system['characteristic']); ?></div>
						<a class="wow fadeInLeft" data-wow-delay=".4s" href="">Read More<i class="fa fa-long-arrow-right"></i></a>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="col-service"></div>
			</div>
			<div class="fl-layer wow fadeInUp"><img src="/themes/template/static/images/pc_img.png"></div>
		</div>	
		<div class="row col-product-2">
			<div class="col-md-4">
				<div class="col-mobile">
					<h2 class="title-head wow fadeInLeft"><?php echo htmlentities($label['frame_list']); ?></h2>
					<div class="main-content">
						<ul>
							<?php if(is_array($system['frame_list']) || $system['frame_list'] instanceof \think\Collection || $system['frame_list'] instanceof \think\Paginator): $i = 0; $__LIST__ = $system['frame_list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
							<li class="wow fadeInUp"><?php echo htmlentities($item['title']); ?><i class="fa fa-long-arrow-right"></i></li>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
						<div class="s-img"><img src="/themes/template/static/images/mobile_img.jpg"></div>
					</div>
				</div>
			</div>
			<div class="col-md-8 col-pad-0">
				<div class="col-marketing">
					<div class="col-md-6">
						<div class="item-left">
							<h2 class="title-head wow fadeInRight"><?php echo htmlentities($label['project_desc']); ?></h2>
							<div class="main-content wow fadeInRight" data-wow-delay=".2s"><?php echo htmlentities($system['project_desc']); ?></div>
							<a class="wow fadeInRight" data-wow-delay=".4s" href="#">Read More<i class="fa fa-long-arrow-right"></i></a>
						</div>
					</div>
					<div class="col-md-6">
						<div class="item-right">
							<div class="main-content">
								<ul>
									<?php if(is_array($system['project_list']) || $system['project_list'] instanceof \think\Collection || $system['project_list'] instanceof \think\Paginator): $i = 0; $__LIST__ = $system['project_list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
									<li class="wow fadeInRight" data-wow-delay=".2s">
										<div class="s-list"><strong><?php echo htmlentities($item['title']); ?></strong><p><?php echo htmlentities($item['content']); ?></p></div>
									</li>
									<?php endforeach; endif; else: echo "" ;endif; ?>
								</ul>
								<div class="s-content wow fadeInRight" data-wow-delay=".8s"><?php echo htmlentities($system['project']); ?></div>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</section>
<footer class="footer">
	<div class="copy">
		<div class="container">
			<div class="col-md-6">
				<div><?php echo htmlentities($system['copyright']); ?></div>
			</div>
			<div class="col-md-6"><?php echo $system['icp']; ?></div>
		</div>
	</div>
</footer>
</div>
<div id="gotoTop" title="返回顶部"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
</body>
</html>