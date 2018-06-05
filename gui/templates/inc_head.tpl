<!-- começa aqui o inc_head.tpl-->
{*
Testlink Open Source Project - http://testlink.sourceforge.net/
$Id: inc_head.tpl,v 1.36 2010/08/28 14:47:54 franciscom Exp $
Purpose: smarty template - HTML Common Header


Critic Smarty Global Variables expected

editorType: used to understand if code for tinymce need to be loaded 

*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$pageCharset}" />
	<meta http-equiv="Content-language" content="en" />
	<meta http-equiv="expires" content="-1" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta name="author" content="Martin Havlat" />
	<meta name="copyright" content="GNU" />
	<meta name="robots" content="NOFOLLOW" />
	
	
	<base href="{$basehref}"/>
	<title>TestManager</title>
	<link rel="icon" href="{$basehref}{$smarty.const.TL_THEME_IMG_DIR}favicon.ico" type="image/x-icon" />
	
{* ----- load CSS ------------------------------------------------------------------- *} 
	<!-- css padrão do testlink-->
	<style media="all" type="text/css">@import "{$css}";</style>

	{if $use_custom_css}
	<style media="all" type="text/css">@import "{$custom_css}";</style>
	{/if}
	
	{if $testproject_coloring eq 'background'}
  	<style type="text/css"> body {ldelim}background: {$testprojectColor};{rdelim}</style>
	{/if}
  
	<style media="print" type="text/css">@import "{$basehref}{$smarty.const.TL_PRINT_CSS}";</style>
<!--fim do css padrão-->
	<link rel="stylesheet" href="vendor/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="vendor/bootstrap-3.3.7/css/bootstrap.css">
	
	<script src="vendor/bootstrap-3.3.7/js/jquery-3.2.0.min.js"></script>
	<script src="vendor/bootstrap-3.3.7/js/bootstrap.js"></script>
{* ----- load javascripts libraries -------------------------------------------------- *} 
	<script type="text/javascript" src="{$basehref}gui/javascript/testlink_library.js" language="javascript"></script>
	<script type="text/javascript" src="{$basehref}gui/javascript/test_automation.js" language="javascript"></script>
	
	{if $jsValidate == "yes"} 
	<script type="text/javascript" src="{$basehref}gui/javascript/validate.js" language="javascript"></script>
    {include file="inc_jsCfieldsValidation.tpl"}
	{/if}
   
	{if $editorType == 'tinymce'}
    <script type="text/javascript" language="javascript"
    	src="{$basehref}third_party/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
    {include file="inc_tinymce_init.tpl"}
	{/if}

	{if $smarty.const.TL_SORT_TABLE_ENGINE == 'kryogenix.org'}
	<script type="text/javascript" src="{$basehref}gui/javascript/sorttable.js" 
		language="javascript"></script>
	{/if}


  	<link rel="stylesheet" href="{$basehref}third_party/chosen/chosen.css">
	<script type="text/javascript" src="{$basehref}third_party/jquery/{$smarty.const.TL_JQUERY}" language="javascript"></script>
	<script type="text/javascript" src="{$basehref}third_party/chosen/chosen.jquery.js"></script>

	<script type="text/javascript" src="{$basehref}third_party/prototype/prototype.js" language="javascript"></script>
	<script type="text/javascript" src="{$basehref}vendor/Mask/jquery.mask.js" language="javascript"></script>
	<script>
		jQuery(document).ready(function(){
			jQuery('.date').mask('00/00/0000');
		});
	</script>
	<script type="text/javascript" language="javascript">
	<!--
	var fRoot = '{$basehref}';
	var menuUrl = '{$menuUrl}';
	var args  = '{$args}';
	var additionalArgs  = '{$additionalArgs}';
	
	// To solve problem diplaying help
	var SP_html_help_file  = '{$SP_html_help_file}';
	
	//attachment related JS-Stuff
	var attachmentDlg_refWindow = null;
	var attachmentDlg_refLocation = null;
	var attachmentDlg_bNoRefresh = false;
	
	// bug management (using logic similar to attachment)
	var bug_dialog = new bug_dialog();

	// for ext js
	var extjsLocation = '{$smarty.const.TL_EXTJS_RELATIVE_PATH}';
	
	//-->
	</script> 
	<link rel="stylesheet" href="vendor/Argo/ArgoCustomizations.css">
{if $openHead == "no"} {* 'no' is default defined in config *}
</head>
{/if}
<!-- termina aqui o inc_head.tpl-->