<style>
.gradient
{
    border-radius : 5px; 
}
.margin-top10px
{
    margin-top: 10px;
}
 </style>
 <script>
j(function(){
    var height = j(window).height() - 110; 
    j(".inner_table").attr("style","height:"+ height  +"px;");
    
    window.close();
});
 </script>
<!--<table width="99%" border="0">
    <tr>
        <td width="50%" style="background: url('custom/graphics/border.png') repeat-y scroll right bottom transparent; padding-right : 10px;">
            <div class="inner_table" border="0"> 
                <div class="gradient" style="height : 40px;">
                    <img src="custom/graphics/patient.png" style="margin : 5px;"/>
                    <span style="position: absolute; margin-top: 12px;">Patient</span>
                </div>
                <hr class="examplefour">
                <div class="margin-top10px">
                    <button style="height : 90px; width : 90px;" onclick="go('index.php?option=com_ipd&view=available_rooms')">New IPD</button>
                    <button style="height : 90px; width : 90px;" onclick="go('index.php?option=com_opd&view=opd_appointment')">New OPD</button>
                    <button style="height : 90px; width : 90px;" onclick="go('index.php?option=com_ipd&view=payment_history')">IPD Payments</button>
                    <button style="height : 90px; width : 90px;" onclick="go('index.php?option=com_opd&view=opd_payment_history')">OPD Payments</button>
                </div>
                <div class="gradient margin-top10px" style="height : 40px; ">
                    <img src="custom/graphics/management.png" style="margin : 5px;"/>
                    <span style="position: absolute; margin-top: 12px;">History</span>
                </div>
                <hr class="examplefour">
                <div class="margin-top10px">
                    <button style="height : 90px; width : 90px;" onclick="go('index.php?option=com_ipd&view=admissions_history')">Admission History</button>
                    <button style="height : 90px; width : 90px;" onclick="go('index.php?option=com_ipd&view=current_admissions')">Current Admissions</button>
                    <button style="height : 90px; width : 99px;" onclick="go('index.php?option=com_opd&view=opd_appointment_history')">Appointment History</button>
                </div>
                <div class="gradient margin-top10px" style="height : 40px; ">
                    <img src="custom/graphics/ipd.png" style="margin : 5px;"/>
                    <span style="position: absolute; margin-top: 12px;">IPD Services</span>
                </div>
                <hr class="examplefour">
                <div class="margin-top10px">
                    <button style="height : 90px; width : 90px;" onclick="go('index.php?option=com_ipd&view=doctor_visit_history')">Doctor Visits</button>
                </div>
            </div>
        </td>
        <td width="50%" style="padding-left : 10px; 0px;">
             <div class="inner_table" border="0"> 
                <div class="gradient" style="height : 40px; ">
                    <img src="custom/graphics/report.png" style="margin : 5px;"/>
                    <span style="position: absolute; margin-top: 12px;">Reports</span>
                </div>
                <hr class="examplefour">
                <div class="margin-top10px">
                    <button style="height : 90px; width : 90px;" onclick="go('index.php?option=com_reports&view=opd_report')">OPD Report</button>
                    <button style="height : 90px; width : 90px;" onclick="go('index.php?option=com_reports&view=ipd_service_report')">IPD Service Report</button>
                    <button style="height : 90px; width : 90px;" onclick="go('index.php?option=com_reports&view=room_occupancy_chart')">Room Occupancy Chart</button>
                    <button style="height : 90px; width : 90px;" onclick="go('index.php?option=com_reports&view=ipd_admission_report')">IPD Admission Report</button>
                    <button style="height : 90px; width : 90px;" onclick="go('index.php?option=com_reports&view=doctor_visit_report')">Doctor Visit Report</button>
                </div>
                <div class="gradient margin-top10px" style="height : 40px; ">
                    <img src="custom/graphics/management.png" style="margin : 5px;"/>
                    <span style="position: absolute; margin-top: 12px;">Management</span>
                </div>
                <hr class="examplefour">
                <div class="margin-top10px">
                    <button style="height : 90px; width : 90px;" onclick="go('index.php?option=com_twolevelmenu&view=menus')">Menu Manager</button>
                    <button style="height : 90px; width : 90px;" onclick="go('index.php?option=com_master&view=activitylog')">Activity Log</button>
                </div>
            </div>
        </td>
    </tr>
</table>-->
<?
//$skiponce = true;
//$skiponce = false;
//if(count($this->menus) > 0)
//{      
//    foreach($this->menus as $menu)
//    {
//        if($menu["has_children"] == true && $skiponce == false)
//        {
            ?>
                <!--<h3><? //echo $menu["name"]?></h3>-->
                <!--<hr>-->
            <?
//            foreach ($menu["children"] as $childmenu)
//            {
//                if($childmenu["permit"] == "1")
//                {
//                    $childlink = ( $menu["direct_link"] !="" ? $menu["direct_link"] : "index.php" . ($childmenu["option"] !="" ? "?option=" . $childmenu["option"] . ( $childmenu["view"] != "" ? "&view=" . $childmenu["view"] : ( $childmenu["task"] != "" ? "&task=" . $childmenu["task"] : "" ) ) . ( $childmenu["additional_params"] != "" ? $childmenu["additional_params"] : "" ) : ""));
                    ?>
                    <!--<input type="button" onclick="go('<? //echo $childlink?>'); return false;" value="<? //echo $childmenu['name']?>" style="width: 200px; height: 30px;"><br />-->
                    <?
//                }
//            }
//        }
//        $skiponce = false;
//    }
//}
?>