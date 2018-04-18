<div class="resultBox">
<table>
    <tr style="display:none">
        <td>
            Categoria
        </td>
        <td>
            <select name="category" class = "chosen-select" id="bulk_tester_div">
                {html_options options=$gui->Categories selected=$gui->SelectedCategory}
            </select>
        </td>
    </tr>
    <tr style="display:none">
        <td>
            Marcadores
        </td>
        <td>
            <select name="markersID[]" class = "chosen-bulk-select" multiple = multiple id="bulk_tester_div">
                {html_options options=$gui->markers selected=$gui->selectedMarkers}
            </select>
        </td>
    </tr>
    <script>
        jQuery( document ).ready(function() {
        jQuery(".chosen-select").chosen({ width: "85%", allow_single_deselect: true });
        jQuery(".chosen-bulk-select").chosen({ width: "100%", allow_single_deselect: true });
        });
    </script>
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
            <div style="overflow-y: scroll;height:180px">
                {foreach key=chave item=issue from=$gui->issues}
                    <div id="issr{$issue.description}"><input id="issr2{$issue.description}" type="checkbox" name="issue[{$issue.id}]" {if $gui->selectedIssues[$issue.id] == 1}checked{/if}>{$issue.description}</div><br id = "issr{$issue.description}">
                {/foreach}
            </div>
            <style>
                [id^="issr"]{
                    display: inline-block;
                }

            </style>
        </td>
    </tr>
    <script>
        jQuery('#chkfilter').on('keyup', function() {
            var query = this.value;

            jQuery('[id^="issr"]').each(function(i, elem) {

                  if (elem.id.indexOf(query) != -1) {
                      elem.style.display = 'inline-block';console.log(elem);
                  }else{
                      elem.style.display = 'none';
                  }
            });
        });
    </script>
</table>
</div>