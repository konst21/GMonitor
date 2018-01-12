<table class="table table-bordered table-striped table-condensed" style="width: auto" id="functions_table">
    <th colspan="5" class="title">
        <b>Functions on Gearman Job server</b>
    </th>
    </tr>
    <tr>
        <th>Function</th>
        <th title="Tasks In Queue">Queue</th>
        <th title="In Progress Now">Now</th>
        <th title="Free Workers for this function">Free<br>Workers</th>
        <th title="Resel all tasks in queue of this function">Reset<br>Queue</th>
    </tr>
    </thead>
    <tbody id="functions_progress">
    <? foreach ($functions as $name => $data) ?>
        <tr>
            <td><?=$name?></td>
            <td id="in_queue"></td>
            <td id="jobs_running"></td>
            <td id="capable_workers"></td>
            <td><i class="fa fa-times"></i></td>
        </tr>
    </tbody>
</table>
