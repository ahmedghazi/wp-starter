<?php
echo
'<script type="text/template" id="row-select-view">
    <td>'.LayGridderLocation::get_select_view().'</td><td><input type="checkbox" class="lg-location-hide-editor"></td>
</script>
<script type="text/template" id="row-composite-view">
    <div class="panel panel-default">
        <table class="table font-table table-bordered js-locations-table">
            <thead>
                <tr>
                    <th>Show Gridder in admin panel if</th>
                    <th>Hide Content Editor</th>
                </tr>
            </thead>
            <tbody class="js-locations-table-tbody">
            </tbody>
        </table>
        <div class="panel-footer clearfix">
            <button type="button" class="btn btn-default js-add-location btn-sm"><span class="glyphicon glyphicon-plus"></span> Add Location</button>
        </div>
    </div>
</script>';
?>

<script>
    jQuery(document).ready(function(){
        LGLocation.start();
    });
</script>