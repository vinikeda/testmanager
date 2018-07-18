<div class="resultBox" style="height: 350px;">
<table style="width:100%;">
    <tr style="/*display:none*/">
        <td>
            Categoria
        </td>
        <td>
            <select class = "chosen-select" id="bulk_tester_div">
                <option value ="0" selected>todos</option>
                {html_options options=$gui->Categories}{*selected=$gui->SelectedCategory*}
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
            <input style="width:100%;" id="chkfilter">
        </td>
    </tr>
    <tr>
        <td>
            Erros
        </td>
        <td>
            <div id="errorlist"  style="overflow-y: scroll;height:180px;border-style:solid;border-width:0.5px;border-color:#a6a6a6;">
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
	<td><br></td>
	</tr>
    <tr>
        <td></td>		
        <td>
            
            
            <button class='btn btn-default' type="button" id="dropissues" data-toggle="modal" data-target="#Nissues">Criar novos erros</button>
            
            
            <!--button style="color: black;">Criar novos erros</button--></td>
    </tr>
    <script>
        
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
                    //console.log(result)
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
            jQuery(".modal-chosen-select").chosen({ width: "100%", allow_single_deselect: true });
            jQuery(".chosen-select").chosen().change(
                function(desc, selected){
                    selectedCategory = selected.selected;
                    buildAJAX();
                }
            );
            jQuery(".chosen-bulk-select").chosen({ width: "100%", allow_single_deselect: true });
            jQuery(".chosen-bulk-select").chosen().change(
                function(desc, selected){
                    buildAJAX();
                }
            );
        });
        
        
        
        function chkfilter(query){
            if(jsonObj == 0){
                jQuery('[id^="issr"]').each(function(i, elem) {
                     //sem json de filtro
                    if (elem.getAttribute('data-reference').toUpperCase().indexOf(query.toUpperCase()) !== -1) {
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
                     
                    if (elem.getAttribute('data-reference').toUpperCase().indexOf(query.toUpperCase()) !== -1) {
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
<div class="modal fade " id="Nissues">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Criar Novo Erro</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="Nname">Nome:</label>
                    <input id ="Nname" class ="form-control">
                </div>
                <div class="form-group">
                    <label for="Nname">Categoria:</label>
                    <select class = "modal-chosen-select" id="category-select">
                        {html_options options=$gui->Categories selected = 0}
                    </select>
                </div>
                <div class="form-group">
                    <label for="tproject-select">Disponível para:</label>
                    <select class = "modal-chosen-select" id="tproject-select" multiple = multiple >
                        {html_options options=$gui->projectLists selected = 0}
                    </select>
                </div>
                <div class="form-group">
                    <label for="marker-select">Marcador</label>
                    <select class = "modal-chosen-select" multiple = multiple id="marker-select">
                        {html_options options=$gui->markers selected=$gui->selectedMarkers}
                    </select>
                </div>
                <div class="form-group">
                    <label for="Nname">Descrição</label>
                    <textarea class="form-control" id="description_text"></textarea>
                </div>
                <div class="form-group text-center">
                    <input type="button" class="btn btn-default" value="Criar" onclick="createIssue()">
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    /*$('#Nissues').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);});*/
    function createIssue(){
        issueName   = document.getElementById("Nname").value;
        category    = jQuery("#category-select").chosen().val();
        availableTo = jQuery("#tproject-select").chosen().val();
        markers     = jQuery("#marker-select").chosen().val();
        description = document.getElementById("description_text").value;
        markerstrings = "";
        for(var i = 0;i < markers.length; i++){
            markerstrings += "&markersID[]="+markers[i];
        }
        availablestrings = "";
        for(var i = 0;i < markers.length; i++){
            availablestrings += "&projectsID[]="+availableTo[i];
        }
        url = "lib/issue/issuesEdit.php?"+"subadiq_name="+issueName+"&category="+category+"&descText="+description+"&do_create=Create&do_action=do_create"+markerstrings+availablestrings;
        //console.log(url);
        jQuery.ajax({
                url:url, success: function(result){
                    updateIssuesList();
                }
            });
    }
    function updateIssuesList(){
        jQuery.ajax({
                url:'lib/issue/issuesList.php', success: function(result){
                    //console.log(result)
                    jsonObj = JSON.parse(result);
                    //console.log(jsonObj)
                    eList = jQuery('#errorlist');
                    eList.empty();
                    var v = "";
                    for(i=0;i<jsonObj.length;i++){
                        v+='<div id="issr'+jsonObj[i]['id']+'" data-reference="'+jsonObj[i]['description']+'"><input type="checkbox" name="issue['+jsonObj[i]['id']+']">'+jsonObj[i]['description']+'</div><br id = "issr'+jsonObj[i]['id']+'" data-reference="'+jsonObj[i]['description']+'">';
                    }
                    //console.log(v);
                    //chkfilter(jQuery('#chkfilter').value);
                    eList.append(v);
                    buildAJAX();
                }
            });
    }
</script>