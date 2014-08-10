<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<!-- Template taken from: interface.php:94 -->

<head>
	<!-- jQuery -->
	<script src="js/jquery-1.10.2.min.js"></script>
	<!-- Bootstrap JavaScript -->
	<script src="js/bootstrap.min.js"></script>		
		<title><?php $this->get("page_title"); //echo getTitle ($pageno); ?></title>
	<!-- This is the bootstrap template -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Bootstrap CSS -->
	<link href="/css/bootstrap.min.css" rel="stylesheet" media="screen">
	<!-- Sidebar stylesheet -->
	<link href="/css/simple-sidebar.css" rel="stylesheet">

	<?php $this->get("Header"); //printPageHeaders(); ?>
</head>
<body>

<<<<<<< HEAD
	


  <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="collapse navbar-collapse">
    	<button type="button" class="btn btn-primary navbar-left nav-menu-trigger-selector" href="javascript:$.pageslide({ direction: 'right', href='#nav-menu-fadeout' })">
    		<i class="glyphicon glyphicon-list"></i>
    	</button>
        <a class="navbar-brand" href="index.php?page=myaccount&tab=default"><?php $this->RemoteDisplayname; ?></a>
        <a class="navbar-brand" href="#"><?php $this->get("Enterprise"); ?></a>
             <ul class="nav navbar-nav">
            	 <li><a href="index.php?page=rackspace">Rackspace</a></li>
            	 <li><a href="index.php?page=depot">Objects</a></li>
            	 <li><a href="index.php?page=ipv4space">IPv4&nbsp;space</a></li>
                 <li><a href="index.php?page=objectlog">Log&nbsp;records</a></li>          
             </ul>
          <form class="navbar-form navbar-right" name="search" method="get">
      			<input type=hidden name=page value=search>
      			<input type=hidden name=last_page value=<?php $this->PageNo; ?>>
				<input type=hidden name=last_tab value=<?php $this->TabNo; ?>>
      			<input class="form-control" type="text" name="q" placeholder="Search" tabindex="1000" value="<?php $this->SearchValue; ?>">
          </form>
        </div><!--/.navbar-collapse -->




        <div style='float: right'>
			<form method=post id=TplSelect name=TplSelect action='?module=redirect&page=myaccount&tab=interface&op=settemplate'>
				<?php $this->getH('TplSelect') ;?>	
				<input class="icon" type="image" border="0" title="set template" src="?module=chrome&uri=pix/tango-document-save-16x16.png" name="submit"></input>
			</form>
=======
	<div class="maintable">
		<div class="container">
	 		<div class="row">
	 			<nav class="navbar navbar-inverse navbar-fixed-top navbar-default" role="navigation">
	  				<div class="container-fluid">
			  			<div class="navbar-header">
					      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar_collapse_element">
					      	<span class="sr-only">Toggle navigation</span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					      </button>
					      <a class="navbar-brand" href="index.php"><?php $this->Enterprise; //echo getConfigVar ('enterprise') ?></a>
					    </div>
					<!-- Collect the nav links, forms, and other content for toggling -->
					    <div class="collapse navbar-collapse" id="navbar_collapse_element">
						    <ul class="nav navbar-nav" style="float:left;">
						      	<li class="hidden-sm"><a href="http://racktables.org" title="Visit RackTables site">RackTables <?php echo CODE_VERSION ?></a></li>
						      	<?php $this->Quicklinks_Table; ?>
						    </ul>
					    	<ul class="nav navbar-nav" style='float:right;'>
					      		<li><a href='index.php?page=myaccount&tab=default'><em><?php $this->RemoteDisplayname; ?></em></a></li>
					    		<li><a href='?logout'><em>[ logout ]</a></em></li>
					    	</ul>
					    </div>
					</div>
				</nav>
	 		</div>
	 	<div class="menubar alert alert-info row" style="padding-top: 75px; max-heigth: 5px;">
	 		<div class="alert-link" style="padding: 0 0 0 0; float: left"><?php $this->Path; ?></div>
	 			<div class='searchbox'  style="padding: 0 0 0 0; float: right">
				<form name=search method=get>
					<input type=hidden name=page value=search>
					<input type=hidden name=last_page value=<?php $this->PageNo; ?>>
					<input type=hidden name=last_tab value=<?php $this->TabNo; ?>>
					<label>Search:
						<input type=text name=q size=20 tabindex=1000 value='<?php $this->SearchValue; ?>'>
					</label>
				</form>
			</div>
>>>>>>> 4663444... Fixed navbar and image for vanilla
		</div>

		<div id="nav-menu-fadeout">
    		<ul id="nav-menu-list" class="list-group" > 
      	  		<a class="list-group-item" href="index.php?page=rackspace">Rackspace</a>
      	  		<a class="list-group-item" href="index.php?page=depot">Objects</a>
      	  		<a class="list-group-item" href="index.php?page=ipv4space">IPv4&nbsp;space</a>
      	  		<a class="list-group-item" href="index.php?page=ipv6space">IPv6&nbsp;space</a>
      	  		<a class="list-group-item" href="index.php?page=files">Files</a>
      	  		<a class="list-group-item" href="index.php?page=reports">Reports</a>
      	  		<a class="list-group-item" href="index.php?page=ipv4slb">IP&nbsp;SLB</a>
      	  		<a class="list-group-item" href="index.php?page=8021q">802.1Q</a>
      	  		<a class="list-group-item" href="index.php?page=config">Configuration</a>
      	  		<a class="list-group-item" href="index.php?page=objectlog">Log&nbsp;records</a>
      	  		<a class="list-group-item" href="index.php?page=virtual">Virtual&nbsp;Resources</a>
      	  		<a class="list-group-item" href="?logout">Log Out</a>
        	</ul>
    	</div>

    	<div class="row">
        	<div class="col-md-12">
				<ul class="breadcrumb"><input type=hidden name=last_page value=index><input type=hidden name=last_tab value=default><li><span class="divider"><a href="index.php?page=index&tab=default">Main page</a></li></li><div style="float: right" class=greeting><a href="index.php?page=myaccount&tab=default"><i class="glyphicon glyphicon-user"></i>&nbsp;RackTables Administrator</a></div></ul>        
			</div>
   		</div>

  		<div class="row">
        		<div class="col-md-12">
        		</div>
   		</div>

    	<div class="row">
        		<div class="col-md-12">
                </div>
   		</div>




      </div>
    </div>  




	
		<!-- Sidebar -->
	<!-- 	<div class="sidebar-wrapper" id="sideBarMenu" data-spy="affix" style="display: none;"> 
			<ul class="nav navbar-nav navbar-right">
				<li><a href='index.php?page=myaccount&tab=default'><?php $this->DisplayName; ?></a></li>
				<li><a class="navbar-brand" href="#"><?php $this->get("Enterprise"); ?></a>	</li>
			</ul>
			<ul class="sidebar-nav" style="height: 100%;">
				<li><a href="#">Rackspace</a></li>
				<li><a href="#">Objects</a></li>
				<li><a href="#">IPv4 space</a></li>
				<li><a href="#">IPv6 space</a></li>
				<li><a href="#">Files</a></li>
				<li><a href="#">Reports</a></li>
				<li><a href="#">IP SLB</a></li>
				<li><a href="#">802.1Q</a></li>
				<li><a href="#">Configuration</a></li>
				<li><a href="#">Log records</a></li>
				<li><a href="#">Virtual Resources</a></li>
				<li role="presentation" class="divider"></li>
				<li><a href="?logout"> <strong> Logout</strong> </a></li>
			</ul>
		</div> -->

		<!-- Navigation bar -->
		<!-- <nav class="navbar navbar-inverse navbar-fixed-top" style="width: 100%;">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#mainNavbar">
						<span class="sr-only">Toggle navigation</span>					

						<span class="icon-bar"></span>
					</button>


				</div>

				<div class="collapse navbar-collapse" id="mainNavbar">

					<form class="navbar-form navbar-left" >
						<button id="showSideMenuButton" type="button" class="btn btn-navbar btn-primary" data-toggle="collapse" 
					data-target="#sideBarMenu"> 
					<button id="showSideMenuButton" type="button" class="btn btn-navbar btn-primary">
						<span class='glyphicon glyphicon-align-justify'></span></button>	
					</form>

					<ul class="nav navbar-nav">
						<li><a class="navbar-brand" href="#"><?php $this->get("Enterprise"); ?></a>	</li>
					</ul>
					<ul class="nav navbar-nav">
						<li><a href="http://racktables.org" title="Visit RackTables site"><?php echo CODE_VERSION ?></a></li>
					</ul>
					<?php $this->get("QuicklinksTable") ?>

					
					<ul class="nav navbar-nav navbar-right">
						<li><a href='index.php?page=myaccount&tab=default'><?php $this->Displayname; ?></a></li>
						<li><a href='?logout'> <strong>Logout</strong></a></li>
					</ul> 
					<form class="navbar-form navbar-right" role="search">

						<div class="form-group">
							<input type=hidden name=page value=search>
							<input type=hidden name=last_page value=<?php $this->PageNo; ?>>
							<input type=hidden name=last_tab value=<?php $this->TabNo; ?>>
							<input type="text" class="form-control" name=q placeholder="Search" value='<?php $this->SearchValue; ?>'>
						</div>
						
						<button type="submit" class="btn btn-default"><span class='glyphicon glyphicon-search'></span></button>
					</form>

				</div> 
			</div>
		</nav>
 -->



		<div class="container" style="margin-top: 100px; margin-bottom: 50px;">



			<ul   id=foldertab class="nav nav-tabs">
				<?php $this->Tabs; ?>
			</ul>


			<div class="msgbar" ><?php $this->get("Message"); //showMessageOrError(); ?></div>
			<div class="pagebar" style="padding-top: 20px;"><?php $this->get("Payload"); //echo $payload; ?></div>
		</div>
	

	<!-- Doing Initalscripts here --> 
	<script type="text/javascript">
		$("#showSideMenuButton").click(function (){
			if($('#sideBarMenu').css('display') == "none"){
				$('#sideBarMenu').show();
			}else{
				$('#sideBarMenu').hide();
			}

		})
		$(".sidebar-nav").height($(document).height());


	</script>

</body>
</html>
