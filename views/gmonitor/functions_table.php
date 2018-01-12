<table class="table table-bordered table-striped table-condensed" style="width: auto" id="functions_table">
    <th colspan="5" class="title" style="text-align: center;">
        <b>Functions on Gearman Job server</b>
    </th>
    </tr>
    <tr">
        <th style="width: 200px;">Function</th>
        <th title="Tasks In Queue">Queue</th>
        <th title="In Progress Now">Now</th>
        <th title="Free Workers for this function">Workers</th>
        <th title="Resel all tasks in queue of this function">Reset</th>
    </tr>
    </thead>
    <tbody id="functions_progress">
    <? foreach ($functions as $name => $data) ?>
        <tr>
            <td><?=$name?></td>
            <td><?=$data['in_queue']?></td>
            <td><?=$data['jobs_running']?></td>
            <td><?=$data['capable_workers']?></td>
            <td class="reset" data-function="<?=$name?>"><i class="fa fa-times"></td>
        </tr>
    </tbody>
</table>
