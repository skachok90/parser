<?php echo $this->doctype() ?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php 
			echo $this->headTitle();
		?>
		
		<?php 
			echo $this->headLink()
			->appendStylesheet($this->urlCss . 'main.css')
		?>
		<?php 
		echo $this->headScript()
		->appendFile($this->urlJs . 'jquery-1.9.0.min.js');
		?>
	</head>
	<body>
		<?php echo $this->layout()->content ?>
	</body>
</html>