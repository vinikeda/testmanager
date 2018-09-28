{* TestLink Open Source Project - http://testlink.sourceforge.net/ *}
{* $Id: frmInner.tpl,v 1.4 2005/10/09 18:13:48 schlundus Exp $ *}
{* Purpose: smarty template - inner frame for workarea *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$pageCharset}" />
	<meta http-equiv="Content-language" content="en" />
	<meta http-equiv="expires" content="-1" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta name="generator" content="testlink" />
	<meta name="author" content="Martin Havlat" />
	<meta name="copyright" content="GNU" />
	<meta name="robots" content="NOFOLLOW" />
	<base href="{$basehref}" />
	<title>TestLink Inner Frame</title>
	<style media="all" type="text/css">@import "{$css}";</style>
        <script src="vendor/bootstrap-3.3.7/js/jquery-3.2.0.min.js"></script>
        <script>
    jQuery(document).ready(function(){
        jQuery(document.getElementById("frame1").contentDocument).on("load",function(){
        
    
    });
        jQuery("#frame1 window").on("load",function(){
            jQuery("#frame1").on("load",function(){
                console.log(jQuery("body").html());
                console.log(this);
            });
        //console.log(jQuery("#frame1").html());
        jQuery("#frame1").contents().on("mousedown, mouseup, click", function(){
            console.log("test");
            window.parent.document.getElementById("sidebar").click();
            jQuery("#sidebar").addClass("active");
            jQuery("#frame1").trigger("click");
        });
    });
    jQuery("#frame2").on("load",function(){
        
        jQuery(this).contents().on("mousedown, mouseup, click", function(){
            jQuery("#sidebar").addClass("active");
            jQuery("#frame1").trigger("click");
        });
    });
});
function dostuff(){
    console.log("batatã");
    console.log(jQuery("#frame1").contents().length);
    jQuery("#frame1").contents().on("mousedown, mouseup, click", function(){
            console.log("test");
            window.parent.document.getElementById("sidebar").click();
            jQuery("#sidebar").addClass("active");
            jQuery("#frame1").trigger("click");
        });
}
</script>
</head>
        
<frameset cols="{$treewidth|default:"30%"},*" border="5" frameborder="10" framespacing="1">
	<frame src="{$treeframe}" name="treeframe" scrolling="auto" id="frame1"/>
	<frame src="{$workframe}" name="workframe" scrolling="auto" id="frame2" style="background:#FFF;" />
</frameset>

</html>
