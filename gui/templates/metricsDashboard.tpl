{* 
Testlink Open Source Project - http://testlink.sourceforge.net/ 
@filesource  metricsDashboard.tpl
@internal revisions
@since 1.9.10                
*}
<head>
<style>
#manageHelper {
    height: 100%;
	width: 100%;
	overflow: auto;
	z-index: -1;
}
</style>

</head>

<body>
<div style="width:60%;height:60%; position: absolute;top: 30%;left: 50%;margin-right: -50%;transform: translate(-50%, -50%);resize: vertical; overflow: auto; border: 1px solid black;">
<iframe id="manageHelper" src="lib/results/metricsDashboard2.php" frameborder="0" scrolling="auto">
</iframe>
</div> 
</body>
</html>