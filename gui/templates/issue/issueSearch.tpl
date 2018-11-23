<table style="width:100%;">
    <tr>
        <td>
            Categoria
        </td>
        <td>
            <div>
                <select class = "chosen-select" id="category">
                    <option value ="0" selected>todos</option>
                    {html_options options=$gui->Categories}
                </select>
            </div>
        </td>
    </tr>
    <tr style="/*display:none*/">
        <td>
            Marcadores
        </td>
        <td>
            <select class = "chosen-select" multiple = multiple id="marker">
                {html_options options=$gui->markers}
            </select>
        </td>
    </tr>
    <tr style="/*display:none*/">
        <td>
            <input type="button" value="buscar" onclick="buscar()">
        </td>
    </tr>
</table>
<script>
    jQuery(".chosen-select").chosen({ width: "85%", allow_single_deselect: true });
    function buscar(){
        category = jQuery("#category").chosen().val();
        markers = (jQuery("#marker").chosen().val());
        markerstrings = '';
        for(var i = 0;i < markers.length; i++){
            markerstrings += "&markersID[]="+markers[i];
        }
        link = "lib/issue/searchIssue.php?category="+category+markerstrings;
        
        jQuery.ajax({
                url:link, success: function(result){
                    jsonObj = JSON.parse(result);
                    ids=[];
                    for(i = 0;i<jsonObj.length;i++){
                        ids.push(jsonObj[i].id);
                    }
                    {literal}
                    jQuery(".issue").css({"display":"none"});
                    for(i = 0;i<jsonObj.length;i++){
                        jQuery(".issue[issid="+ids[i]+"]").css({"display":"table-row"});
                    }
                    {/literal}
                }
            });
        
    }
</script>
                    