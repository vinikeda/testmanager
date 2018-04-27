<div class="resultBox">
<table>
    <tr style="/*display:none*/">
        <td>
            Categoria
        </td>
        <td>
            <select class = "chosen-select" id="bulk_tester_div">
                {html_options options=$gui->Categories selected = 0}{*selected=$gui->SelectedCategory*}
            </select>
        </td>
    </tr>
    <tr style="/*display:none*/">
        <td>
            Marcadores
        </td>
        <td>
            <select class = "chosen-bulk-select" multiple = multiple id="bulk_tester_div2">
                {html_options options=$gui->markers selected=$gui->selectedMarkers}
            </select>
        </td>
    </tr>
    
    <tr>
        <td>
            Buscar Erros
        </td>
        <td>
            <input id="chkfilter">
        </td>
    </tr>
    <tr>
        <td>
            Erros
        </td>
        <td>
            <div id="errorlist"  style="overflow-y: scroll;height:180px">
                {foreach key=chave item=issue from=$gui->issues}
                    <div id="issr{$issue.id}" data-reference="{$issue.description}"><input type="checkbox" name="issue[{$issue.id}]" {if $gui->selectedIssues[$issue.id] == 1}checked{/if}>{$issue.description}</div><br id = "issr{$issue.id}" data-reference="{$issue.description}">
                {/foreach}
            </div>
            <style>
                [id^="issr"]{
                    display: inline-block;
                }

            </style>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><a onclick><button>criar novos erros</button></a></td>
    </tr>
    <script>
        function createInExecution(){
            creationWindow = window.open("http://localhost/testlink/lib/issue/issuesEdit.php?do_action=create");
            creationWindow.getElementById()
        }
        
        selectedCategory = 0;
        jsonObj = 0;
        function buildURL(){
            selectedMarkers = jQuery("#bulk_tester_div2").chosen().val();
            //selectedCategory = jQuery("#bulk_tester_div").val(); //não funciona como deveria
            markerstrings = "";
            for(var i = 0;i < selectedMarkers.length; i++){
                markerstrings += "&markersID[]="+selectedMarkers[i];
            }
            return "lib/issue/searchIssue.php?category="+selectedCategory+markerstrings;
        }
        function buildAJAX(){
            jQuery.ajax({
                url:buildURL(), success: function(result){
                    console.log(result)
                    jsonObj = JSON.parse(result);
                    jQuery('[id^="issr"]').each(function(i, elem) {
                        elem.style.display = 'none';
                        if(jsonObj != null){
                            for(j=0;j< jsonObj.length;j++){
                                if(elem.id == 'issr'+jsonObj[j].id)elem.style.display = 'inline-block';
                            }
                        }
                    });
                    //chkfilter(jQuery('#chkfilter').value);
                }
            });
        }
        jQuery( document ).ready(function() {
            jQuery(".chosen-select").chosen({ width: "85%", allow_single_deselect: true });
            jQuery(".chosen-select").chosen().change(
                function(desc, selected){
                    selectedCategory = selected.selected;
                    buildAJAX();
                }
            );
            jQuery(".chosen-bulk-select").chosen({ width: "100%", allow_single_deselect: true });
            jQuery(".chosen-bulk-select").chosen().change(
                function(desc, selected){
                    buildAJAX()
                }
            );
        });
        
        
        
        function chkfilter(query){
            if(jsonObj == 0){
                jQuery('[id^="issr"]').each(function(i, elem) {
                     //sem json de filtro
                    if (elem.getAttribute('data-reference').indexOf(query) != -1) {
                        elem.style.display = 'inline-block';//console.log(elem);
                    }else{
                        elem.style.display = 'none';
                    }

                });
            }
            else if (jsonObj == null){
            /*do nothing !!!!*/
            }
            else{
                jQuery('[id^="issr"]').each(function(i, elem) {
                     
                    if (elem.getAttribute('data-reference').indexOf(query) != -1) {
                        //elem.style.display = 'none';
                        for(j=0;j< jsonObj.length;j++){
                            if('issr'+jsonObj[j].id == elem.getAttribute('id'))elem.style.display = 'inline-block';
                        }//console.log(elem);
                    }else{
                        elem.style.display = 'none';
                    }

                });
                
                
                /*for(i = 0;i< jsonObj.length;i++){
                    elem = jQuery("#issr"+jsonObj[i].id);
                    if (elem.getAttribute('data-reference').indexOf(query) != -1) {
                        elem.style.display = 'inline-block';//console.log(elem);
                    }else{
                        elem.style.display = 'none';
                    }
                }*/
            }
        }
        jQuery('#chkfilter').on('keyup', function() {
            var query = this.value;
            chkfilter(query)
        });
    </script>
</table>
</div>