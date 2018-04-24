{foreach key=chave item=issue from=$gui->issues}
    <div id="issr{$issue.description}"><input id="issr2{$issue.description}" type="checkbox" name="issue[{$issue.id}]" {if $gui->selectedIssues[$issue.id] == 1}checked{/if}>{$issue.description}</div><br id = "issr{$issue.description}">
{/foreach}