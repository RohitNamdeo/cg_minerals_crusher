<?php
    defined('_JEXEC') or die;
?>
<script>
j(function(){
     j("#credit,#mobile_no").on("keypress",function(e){
           if(is_character(e))
           {
               e.preventDefault();
           }
     });
});   
//var setting_to_create = <? //echo $this->setting_to_create; ?>;  
var setting_to_create = 0;

function create_all_button(missing_settings)
{
    setting_to_create = missing_settings;
    if(setting_to_create > 0)
    {
        j("#create_all_button").html("<input type='button' value='Create All Settings' onclick='create_all_settings();'><br /><br />");
    }
}

function create_all_settings()
{
    var keys = new Array();
    j(".missing_settings").each(function(){
        keys.push(j(this).parent().attr("id"));
    });
    
    var p = j.when(1); // empty promise
    keys.forEach(function(key,index){
        p = p.then(function(){
            default_value = j("#" + key).find("img").attr("default_value");
            value_type = j("#" + key).find("img").attr("value_type");
            return create_setting(key,default_value,value_type,'all');
        });
    }); 
}

function create_setting(key, default_value, value_type, mode="")
{   
     
    j("#" + key).html('<img src="custom/graphics/icons/basic-loader.gif"/>');
    if(mode == "")
    {
        j.get("index.php?option=com_settings&task=create_setting&tmpl=xml&key=" + key + "&default_value=" + default_value + "&value_type=" + value_type, function(data){
            if(data != "")
            {
                data = j.parseJSON(data);
                j("#" + key).html('<img src="custom/graphics/icons/16x16/tick.png"/>');
                if(value_type == 1) { j("#" + key + "_numeric").html(data); }
                else if(value_type == 0) { j("#" + key + "_string").html(data); }
                
                if(--setting_to_create == 0) { j("#create_all_button").hide(); }
            }
            else
            {
                j("#" + key).html(j("#" + key + "_image").html()); 
                j("#" + key).find("img").attr("class", "missing_settings");
            } 
        });
    }
    else
    {
        return j.get("index.php?option=com_settings&task=create_setting&tmpl=xml&key=" + key + "&default_value=" + default_value + "&value_type=" + value_type, function(data){
            if(data != "")
            {
                data = j.parseJSON(data);
                j("#" + key).html('<img src="custom/graphics/icons/16x16/tick.png"/>');
                if(value_type == 1) { j("#" + key + "_numeric").html(data); }
                else if(value_type == 0) { j("#" + key + "_string").html(data); }
                
                if(--setting_to_create == 0) { j("#create_all_button").hide(); } 
            }
            else
            {
                j("#" + key).html(j("#" + key + "_image").html()); 
                j("#" + key).find("img").attr("class", "missing_settings");
            } 
        });
    }   
}  
</script>
<h1>Raw Settings Manager</h1>
<div id='create_all_button'></div>
<?
    /*if($this->setting_to_create > 0)
    {
        echo "<div id='create_all_button'><input type='button' value='Create All Settings' onclick='create_all_settings();'><br /><br /></div>";
    }*/
?>
<table class="clean centreheadings" id="settings">
    <thead>
        <tr>
            <th width="20">#</th>
            <th>Action</th>
            <th>Key</th>
            <th>Value String</th>
            <th>Value Numeric</th>
            <th>Value Type</th>
        </tr>
    </thead>
    <?  
        if(count($this->settings) > 0)
        {
            $x = 1;
            $missing_settings = 0;
            foreach($this->settings as $key=>$setting)
            {     
                ?>
                <tr>
                    <td width="20" align="center"><? echo $x++; ?></td>
                    <?
                        if($setting["found"] == 0)
                        {
                            $missing_settings++;
                            ?>
                            <td align="center">
                                <span id="<? echo $key; ?>"><img src="custom/graphics/icons/16x16/critical.png" class="missing_settings" onclick="create_setting('<? echo $key; ?>','<? echo $setting["default_value"]; ?>',<? echo $setting["value_type"]; ?>);" style="cursor:pointer;" default_value="<? echo $setting["default_value"]; ?>" value_type="<? echo $setting["value_type"]; ?>" /></span>
                                <span id="<? echo $key; ?>_image" style="display:none;"><img src="custom/graphics/icons/16x16/critical.png" onclick="create_setting('<? echo $key; ?>','<? echo $setting["default_value"]; ?>',<? echo $setting["value_type"]; ?>);" style="cursor:pointer;" default_value="<? echo $setting["default_value"]; ?>" value_type="<? echo $setting["value_type"]; ?>" /></span>
                            </td>
                            <? 
                        }
                        elseif($setting["found"] == 1)
                        {  
                            ?>
                            <td align="center"><img src="custom/graphics/icons/16x16/tick.png"/></td>
                            <?
                        }
                    ?>
                    <td><?echo @$key ;?></td>
                    <td id="<? echo $key; ?>_string"><?echo @$setting["value_string"];?></td>
                    <td  id="<? echo $key; ?>_numeric" align="center"><?echo @$setting["value_numeric"];?></td>
                    <td align="center">
                        <?
                            if(@$setting["value_type"] == "0")
                            {
                                echo "String";
                            }
                            else if(@$setting["value_type"] == "1")
                            {
                                echo "Numeric";
                            }
                            else
                            {
                                echo "";
                            }
                        ?>
                    </td>
                </tr>  
                <? 
            }
            echo '<script type="text/javascript">', 'create_all_button(' . $missing_settings . ');', '</script>';  
        }  
    ?>
</table>