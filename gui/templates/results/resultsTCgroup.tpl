{* 
TestLink Open Source Project - http://testlink.sourceforge.net/

show Test Results and Metrics
@filesource	resultsTC.tpl

@internal revisions
@since 1.9.6 
20130203 - franciscom - TICKET 0005516
*}

{lang_get var="labels"
          s="title,date,printed_by,title_test_suite_name,platform,builds,
             title_test_case_title,version,generated_by_TestLink_on, priority,
             info_resultsTC_report,elapsed_seconds,export_as_spreadsheet"}

{include file="inc_head.tpl" openHead="yes"}


</head>
<body>
<script type="text/javascript">
  function iframeLoaded(iFrameID) {
      //var iFrameID = document.getElementById('idIframe{$gui->tplan_id}');
      console.log(iFrameID);//"{$gui->tplan_id}");
      if(iFrameID) {
            // here you can make the height, I delete it first, then I make it again
            iFrameID.height = "";console.log(iFrameID.contentWindow.document.body.scrollHeight);
            iFrameID.height = iFrameID.contentWindow.document.body.scrollHeight + "px";
      }   
  }
</script> 
<table>
	<tr>
            <td>{lang_get s="testproject"}</td><td>{$smarty.const.TITLE_SEP}</td>
            <td>
                    <span style="color:black; font-weight:bold; text-decoration: underline;">{$gui->tproject_name|escape}</span>
            </td>
	</tr>
        <tr>
            <td>Sub-Adquirente</td><td>{$smarty.const.TITLE_SEP}</td>
            <td>
                <span style="color:black; font-weight:bold; text-decoration:underline;">
                    <form method="get">
                        <input type="hidden" value="0" name="format">
                    <select name="sub" id="selectSubs" onchange="this.form.submit()">
                        <option value = "0" select >Todos</option>
                        {html_options options=$gui->subs selected=$gui->sub}
                    </select>
                    </form> 
                </span>
            </td>
        </tr>
	<tr>
            <td>{lang_get s="testplan"}</td><td>{$smarty.const.TITLE_SEP}</td>
            <td> 
                <span style="color:black; font-weight:bold; text-decoration:underline;">
                    <form method="get" action="lib/results/resultsTC.php">
                        <input type="hidden" value="0" name="format">
                        <select name="tplan_id" id="selectTestplan" onchange="this.form.submit()">
                            <option value = "0" select >Todos</option>
                            {html_options options=$gui->tplanIDS}
                        </select>
                    </form> 

                </span>
            </td>
	</tr>
        <tr>
            <td>
                <br>
            </td>
        </tr>
    </table>
{foreach from = $gui->tplanIDS key=idx item=value}
    <b>{$value}</b><br>
    {*<iframe onscroll =" iframeLoaded(this)" id='idIframe{$idx}' src = "lib/results/tableresultsTC.php?format=0&tplan_id={$idx}" width="100%">*}
        
    </iframe><br>

{/foreach}

</body>
</html>