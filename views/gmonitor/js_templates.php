<script type="text/html" id="functions_table_template">
    <table class="table table-bordered table-striped table-condensed" style="width: auto" id="functions_table">
        <thead>
        <tr>
            <th colspan="5" class="title">
                <b>Functions on Gearman Job server</b>
            </th>
        </tr>
        <tr>
            <th style="width: 200px;">Function</th>
            <th title="Tasks In Queue">Queue</th>
            <th title="In Progress Now">Now</th>
            <th title="Free Workers for this function">Free<br>Workers</th>
            <th title="Resel all tasks in queue of this function">
                <span id="reset_all_functions">
                    Reset<br>Queue
                </span>
            </th>
        </tr>
        </thead>
        <tbody id="functions_tbody">
        </tbody>
    </table>
</script>
<script type="text/html" id="function_rows">
    <% $.each (rdata, function (index, data) { %>
    <tr>
        <td>  <%= data.function_name %> </td>
        <td>  <%= data.in_queue %> </td>
        <td>  <%= data.jobs_running %> </td>
        <td>  <%= data.capable_workers %> </td>
        <td class="reset" data-function="<%= data.function_name %>"><i class="fa fa-times"></td>
        </tr>;
    <% }); %>

</script>
