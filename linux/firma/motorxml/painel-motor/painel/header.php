<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="SHORTCUT ICON" href="./img/favicon.png">
<title>Ti Labs &raquo; Motor Xml</title>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/interface.js"></script>

<!--[if lt IE 7]>
 <style type="text/css">
 div, img { behavior: url(iepngfix.htc) }
 </style>
<![endif]-->

<link href="./css/style.css" rel="stylesheet" type="text/css" />
<link href="./css/itunes.css" rel="stylesheet" type="text/css" />
<link href="./css/grid.css" rel="stylesheet" type="text/css" />
<link href="./css/slide.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="dock" id="dock">
  <div class="dock-container">
  <a class="dock-item" href="index.php"><img src="img/home.png" alt="home" /><span>Home</span></a> 
  <a class="dock-item" href="http://www.nfe.fazenda.gov.br/portal/principal.aspx"><img src="img/portfolio.png" alt="portfolio" /><span>Portal NFe</span></a> 
  <a class="dock-item" href="remove.php"><img src="img/rss.png" alt="rss" /><span>Remover NF-e</span></a> 
  <a class="dock-item" href="sobre.php"><img src="img/video.png" alt="video" /><span>Sobre</span></a>
  <a class="dock-item" href="http://128.1.0.156/painel-motor/"><img src="img/link.png" alt="video" /><span>Sair</span></a>
</div>
</div>
<script type="text/javascript">
	
	$(document).ready(
		function()
		{
			$('#dock').Fisheye(
				{
					maxWidth: 50,
					items: 'a',
					itemsText: 'span',
					container: '.dock-container',
					itemWidth: 40,
					proximity: 90,
					halign : 'center'
				}
			)
		}
	);

</script>
    
