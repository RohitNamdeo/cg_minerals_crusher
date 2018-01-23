<script>
j(function() {
	var icons = {
		header: "ui-icon-circle-arrow-e",
		headerSelected: "ui-icon-circle-arrow-s"
	};
	j( "#leftmenu" ).accordion({
		icons: icons,
		autoHeight: false,
		navigation: true
	});
});
</script>
<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td align="center">
<?
if(count($menus) > 0)
{    
    ?>
    <div id="leftmenu" style="width: 95%" align="left">
    <?
        foreach($menus as $menu)
        {
            $link = ( $menu["direct_link"] !="" ? $menu["direct_link"] : "index.php" . ($menu["option"] !="" ? "?option=" . $menu["option"] . ( $menu["view"] != "" ? "&view=" . $menu["view"] : ( $menu["task"] != "" ? "&task=" . $menu["task"] : "" ) ) . ( $menu["additional_params"] != "" ? $menu["additional_params"] : "" ) : ""));
            if($menu["has_children"] == true)
            {
                ?>
                <h3><a href="#"><? echo $menu["name"]?></a></h3>
                <div>
                    <?
                        foreach ($menu["children"] as $childmenu)
                        {
                            $childlink = ( $menu["direct_link"] !="" ? $menu["direct_link"] : "index.php" . ($childmenu["option"] !="" ? "?option=" . $childmenu["option"] . ( $childmenu["view"] != "" ? "&view=" . $childmenu["view"] : ( $childmenu["task"] != "" ? "&task=" . $childmenu["task"] : "" ) ) . ( $childmenu["additional_params"] != "" ? $childmenu["additional_params"] : "" ) : ""));
                            ?>
	                            <div style="clear: both; margin-left: -10px; width: 120%;">
	                            <span class="ui-icon ui-icon-circle-arrow-e" style="float: left"></span>
	                            <a href="<? echo $childlink; ?>" target="<? echo $menu["target"]; ?>"><? echo $childmenu["name"]; ?></a>
                            </div>
                            <?
                        }
                    ?>
                </div>
                <?
            }
            elseif(false)
            {
                ?>
                <li><a href="<? echo $link; ?>" target="<? echo $menu["target"]; ?>"><? echo $menu["name"]?></a></li>
                <?
            }
        }
    ?>
    </div>
    <?
}
?>
</td></tr></table>