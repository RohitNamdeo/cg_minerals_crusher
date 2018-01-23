<?php
    defined('_JEXEC') or die; 
?>
<style>
    /*#notes td{
        padding: 0;
    }*/
</style>
<script>
    j(function(){
        window.print();
    });
</script>
<div align="center">
    <h1>Notes</h1>
    <br />
    <table class="clean centreheadings" id="notes">
        <tr>
            <!--<th>#</th>-->
            <th>Date</th>
            <th width="500">Note</th>
        </tr>
        <?
            if(count($this->notes) > 0)
            {
                $x = 1;
                $note_type = 0;
                foreach($this->notes as $note)
                {
                    if($note_type != $note->note_type)
                    {
                        ?>
                        <tr>
                            <th colspan="2" style="font-size:medium;"><? echo ($note->note_type == SPECIFIC ? "Specific Note" : "General Note"); ?></th>
                        </tr>
                        <?
                        $note_type = $note->note_type;
                    }
                    ?>
                    <tr>
                        <!--<td align="center"><? //echo $x++; ?></td>-->
                        <td align="center"><? echo date("d-M-Y", strtotime($note->date_of_note)); ?></td>
                        <td style="font-size:medium;"><? echo $note->note; ?></td>
                    </tr>
                    <?
                }
            }
        ?>
    </table>
</div>