<?
$SkipOnce = false;
if(count($this->menus) > 0)
{
    foreach($this->menus as $menu)
    {
        if (@$menu["permit"] == 1)
        {
            if($menu["has_children"] == true)
            {
                if ($SkipOnce)
                {
                    ?>
                        <br style="clear: both;"/>
                        <br style="clear: both;"/>
                    <?
                }
                $SkipOnce = true;
                ?>
                    <h3><? echo $menu["name"]?></h3>
                    <hr>
                <?
                foreach ($menu["children"] as $childmenu)
                {
                    if($childmenu["permit"] == "1")
                    {
                        $childlink = ( $childmenu["direct_link"] !="" ? $childmenu["direct_link"] : "index.php" . ($childmenu["option"] !="" ? "?option=" . $childmenu["option"] . ( $childmenu["view"] != "" ? "&view=" . $childmenu["view"] : ( $childmenu["task"] != "" ? "&task=" . $childmenu["task"] : "" ) ) . ( $childmenu["additional_params"] != "" ? $childmenu["additional_params"] : "" ) : ""));
                        ?>
                        <input type="button" onclick="go('<? echo $childlink?>');return false;" value="<? echo $childmenu['name']?>" style="width: 170px; height: 60px; float: left; margin-right: 20px; margin-bottom: 20px; ">
                        <?
                    }
                }
            }
        }
    }
}
?>