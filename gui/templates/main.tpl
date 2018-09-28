{* Testlink Open Source Project - http://testlink.sourceforge.net/ *}
{* $Id: main.tpl,v 1.8 2009/06/29 10:45:24 havlat Exp $ *}
{* Purpose: smarty template - main frame *}
{*******************************************************************}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
{include file="inc_head.tpl" openHead="yes"}
	<meta http-equiv="Content-Type" content="text/html; charset={$pageCharset}" />
	<meta http-equiv="Content-language" content="en" />
	<meta name="generator" content="testlink" />
	<meta name="author" content="TestLink Development Team" />
	<meta name="copyright" content="TestLink Development Team" />
	<meta name="robots" content="NOFOLLOW" />
	<title>TestLink {$tlVersion|escape}</title>
	<meta name="description" content="TestLink - {$gui->title|default:"Main page"}" />
	<link rel="icon" href="{$basehref}{$smarty.const.TL_THEME_IMG_DIR}favicon.ico" type="image/x-icon" />
	<style>
	html,body{
  height: 100%;
}
#mainframe{
	background-image:url({$smarty.const.TL_THEME_IMG_DIR}ArgotechnoIcon.png);
	background-position:center;
	background-repeat:no-repeat;
	background-size:auto 80%;
}

</style>


</head>
<body>

{include file="navBar2.tpl"}

<iframe src="{$gui->mainframe}" scrolling='auto' name='mainframe' allowtransparency="true" id="mainframe" style="width:100%;"></iframe>
</body>
<!--frameset rows="100,*" frameborder="0" framespacing="0" id="mainframeset">
	<frame src="{$gui->titleframe}" id="navframe" style="/*margin-bottom:30px;*/z-index:999;" name="titlebar" scrolling="no" noresize="noresize" allowtransparency="true" />
	<frame src="{$gui->mainframe}" scrolling='auto' name='mainframe' allowtransparency="true" id="mainframe"/>
	<noframes>
		<body>TestLink required a frames supporting browser.</body>
	</noframes>
</frameset-->

<script>
    jQuery(document).ready(function(){
    jQuery("#mainframe").on("load",function(){
        
        jQuery(this).contents().on("mousedown, mouseup, click", function(){
            jQuery("#sidebar").addClass("active");
            jQuery("#sidebar").trigger("click");
        });
    });
});
//script criado para redimensionar o tamanho da navbar de acordo com a necesidade evitando problemas de tamanho
var frame7 = document.getElementById("mainframe");
var tamanho = function(event){
var navbar = document.getElementById("mainnavbar");
	frame7.style.height="calc(100% - "+navbar.getDimensions().height+"px)";
	};
window.addEventListener('load',tamanho );
window.addEventListener('resize', tamanho);

</script>

</html>