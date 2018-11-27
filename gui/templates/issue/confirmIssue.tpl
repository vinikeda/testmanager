<button class='btn btn-default' type="button" id="dropissues" data-toggle="modal" data-target="#Nissues">Consultar Sínteses</button>
<div class="modal fade" id="Nissues">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Selecione a Mensagem Padrão</h5>
                <button type="button" class="close btn-noback" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table style="width:100%;">
                    <tr>
                        <td>
                            Categoria
                        </td>
                        <td>
                            <div>
                                <select class = "chosen-select confirmer" id="category">
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
                            <select class = "chosen-bulk-select confirmer" multiple = multiple id="marker">
                                {html_options options=$gui->testers}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Buscar Erros
                        </td>
                        <td>
                            <input id="chkfilter" style="width:100%">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Erros
                        </td>
                        <td>
                            <div id="errlist" style="overflow-y: scroll;height:500px;width:100%">
                                {foreach key=chave item=issue from=$gui->issues}
                                    <div id="step" class="issue" data-reference="{$issue.description|escape}" issid="{$issue.id}" style="width:100%">
                                        <a data-toggle="tooltip" title="{$issue.text_description|escape} ">
                                            <button type="button" class="btn btn-default" style="width:100%;white-space: normal;"
                                                    onclick="document.getElementById('step_notes').value += '{$issue.adjusted_text_description|escape} \n';">
                                                {$issue.description|escape}
                                            </button>
                                        </a>{$issue.text_description|escape}
                                    </div>
                                            <br class="issue" issid="{$issue.id}" data-reference="{$issue.description|escape}">

                                {/foreach}
                            </div>
                        </td>
                    </tr>

                </table>
            </div>
        </div>
    </div>
</div>
<script>
    function filtrar(){
        chkfilter(document.getElementById('chkfilter').value);
    }
    jQuery(".confirmer").chosen({ width: "85%", allow_single_deselect: true }).change(
                function(desc, selected){
                    buscar();
                }
            );
    
    jQuery('#chkfilter').on('keyup', function() {
            var query = this.value;
            buscar();
        });
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
                    
                    filtrar();
                    {/literal}
                }
            });
        
    }
    
    function chkfilter(query){
        jQuery('.issue').each(function(i, elem) {
             //sem json de filtro
            if (elem.getAttribute('data-reference').toUpperCase().indexOf(query.toUpperCase()) !== -1) {
            }else{
                elem.style.display = 'none';
            }

        });
    }
</script>