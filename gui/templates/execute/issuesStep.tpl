<button class='btn btn-default' type="button" id="dropissues" data-toggle="modal" data-target="#Nissues{$step_info.id}">Mensagens Padrão</button>
                <div class="modal fade" id="Nissues{$step_info.id}">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title">Selecione a Mensagem Padrão</h4>
                            </div>
                            <div class="modal-body">
                                <table style="width:100%;">
                                    <tr>
                                        <td>
                                            Categoria
                                        </td>
                                        <td>
                                            <div>
                                                <select class = "chosen-select" id="bulk_tester_div{$step_info.id}">
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
                                            <select class = "chosen-bulk-select" multiple = multiple id="marker-select{$step_info.id}">
                                                {html_options options=$gui->markers}
                                            </select>
                                        </td>
                                    </tr>
                                    <script>
                                        jsonObj2[{$step_info.id}] = 0;
                                        jQuery( document ).ready(function() {
                                            jQuery("#bulk_tester_div{$step_info.id} , #marker-select{$step_info.id}").chosen({ width: "100%", allow_single_deselect: true }).change(
                                                function(desc, selected){
                                                    url = buildURL2(jQuery("#bulk_tester_div{$step_info.id}"),jQuery("#marker-select{$step_info.id}"));
                                                    buildAJAX2(url,jQuery("#errlist{$step_info.id} > *"),{$step_info.id});
                                                    //jQuery('#chkfilter{$step_info.id}').trigger("keyup");
                                                }
                                            );
                                        });
                                    </script>
                                    <tr>
                                        <td>
                                            Buscar Erros
                                        </td>
                                        <td>
                                            <input id="chkfilter{$step_info.id}" style="width:100%">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Erros
                                        </td>
                                        <td>
                                            <div id="errlist{$step_info.id}" style="overflow-y: scroll;height:500px;width:100%">
                                                {foreach key=chave item=issue from=$gui->issues}
                                                    <div id="step{$step_info.id}" data-reference="{$issue.description|escape}" errID="{$issue.id}" style="width:100%">
                                                        <a data-toggle="tooltip" title="{$issue.text_description} ">
                                                            <button type="button" class="btn btn-default" style="width:100%;white-space: normal;"
                                                                    onclick="document.getElementById('step_notes_{$step_info.id}').value+='{$issue.adjusted_text_description|escape} \n';
                                                                        document.getElementById('issx{$issue.id}').checked = true">
                                                                {$issue.description|escape}
                                                            </button>
                                                        </a>
                                                    </div><br errID="{$issue.id}" data-reference="{$issue.description|escape}">

                                                {/foreach}
                                            </div>
                                            <style>
                                                [id^="step{$step_info.id}"]{
                                                    display: inline-block;
                                                }

                                            </style>
                                        </td>
                                    </tr>
                                    <script>
                                        jQuery('#chkfilter{$step_info.id}').on('keyup', function() {
                                            var query = this.value;
                                              //if(typeof jsonObj2[{$step_info.id}] !== 'undefined'){
                                            jQuery('#errlist{$step_info.id} > *').each(function(i, elem) {
                                                //console.log(elem);
                                                if(jsonObj2[{$step_info.id}] == 0){
                                                    if(elem.getAttribute('data-reference').toUpperCase().indexOf(query.toUpperCase()) !== -1){
                                                        elem.style.display = 'inline-block';
                                                    }else{
                                                        elem.style.display = 'none';
                                                    } 
                                                }else if (jsonObj2[{$step_info.id}] == null){
                                                    /*do nothing*/
                                                }else{
                                                    for(i = 0;i<jsonObj2[{$step_info.id}].length;i++){
                                                        if(elem.getAttribute('errID') == jsonObj2[{$step_info.id}][i].id){
                                                            if(elem.getAttribute('data-reference').toUpperCase().indexOf(query.toUpperCase()) !== -1){
                                                                elem.style.display = 'inline-block';
                                                            }else{
                                                                elem.style.display = 'none';
                                                            }
                                                        }
                                                    }
                                                }
                                            });
                                        });
                                    </script>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <style>
                #fixed{$step_info.id}{
                    display:inline-block !important;/*eu sei que isso não deveria existir, mas se tirar isso surge um display none que buga e eu não tive tempo de encontrar a raiz dele.*/
                }
            </style>
            <div class = "dropdown" id = 'fixed{$step_info.id}'  >
                <button class='btn btn-default' type='button' id="dropdownMenu{$step_info.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" >Mensagens Padrão</button>
                
            </div>