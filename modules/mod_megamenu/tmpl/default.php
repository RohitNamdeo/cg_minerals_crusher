<?php
defined('_JEXEC') or die;
?>
<script type="text/javascript">
j(document).ready(function(j){
    j(".mega-menu-children").show();
    j('#mega-menu-3').megamenu({
        rowItems: '5',
        speed: 'fast',
        effect: 'fade',
        openDelay: 0,
        closeDelay: 0,
    });
    
    if( window.opener !== null )
    {
        j(".mega-menu-container").hide();
    }
});                                                                                     
</script>
<? 
//original mega menu sample, not to be rendered to the user, just for reference purposes
if(count($menus) > 0 || JFactory::getUser()->guest == false)
{    
    ?>
        <div class="mega-menu-container" id="mega-menu-container">
            <div class="grey">  
                <ul id="mega-menu-3" class="mega-menu">
                        <?
                            $menu_key = "";
                            function render_sublevel($menu_id, $level, &$menus, &$project_submenus, &$projects)
                            {
                                $menu = @$menus[$menu_id];
                                $child_menu_key = "";
                                
                                $link = ($menu["direct_link"] !="" ? $menu["direct_link"] : "index.php" . ($menu["option"] !="" ? "?option=" . $menu["option"] . ( $menu["view"] != "" ? "&view=" . $menu["view"] : ( $menu["task"] != "" ? "&task=" . $menu["task"] : "" ) ) . ( $menu["additional_params"] != "" ? $menu["additional_params"] : "" ) : "?") . (strpos($menu["additional_params"], "#") > 0 ? "" : "&NavID=" . $menu["id"]));
                                ?>
                                    <li class="<? echo ($menu["active"] ? " mega-active" : "");?> mega-menu-children" style="display: none;">
                                        <a href="<? echo $link; ?>" target="<? echo $menu["target"]; ?>" id="menu<? echo $menu["id"]; ?>" onclick="<? echo ($menu["option"] == "-" || $menu["direct_link"] == "#" ? "return false;" : "");?>" tabindex="-1"><? echo $menu["name"]?></a>
                                        <?
                                            if ($menu["has_children"] == true && count($menu["children"]) > 0)
                                            {
                                                ?>
                                                    <ul>
                                                        <?
                                                            foreach($menu["children"] as $menu_id)
                                                            {
                                                                render_sublevel($menu_id, 1, $menus, $project_submenus, $projects);
                                                            }
                                                        ?>
                                                    </ul>
                                                <?
                                            }
                                        ?>
                                    </li>
                                <?
                            }
                            foreach($menus as $menu_key => $menu)
                            {
                                if ($menu["parent"] == 0)
                                {
                                    $link = ($menu["direct_link"] !="" ? $menu["direct_link"] : "index.php" . ($menu["option"] !="" ? "?option=" . $menu["option"] . ( $menu["view"] != "" ? "&view=" . $menu["view"] : ( $menu["task"] != "" ? "&task=" . $menu["task"] : "" ) ) . ( $menu["additional_params"] != "" ? $menu["additional_params"] : "" ) : "?") . (strpos($menu["additional_params"], "#") > 0 ? "" : "&NavID=" . $menu["id"]));
                                    ?>
                                        <li class="<? echo ($menu["active"] ? " mega-active" : "")?>">
                                            <a href="<? echo $link; ?>" target="<? echo $menu["target"]; ?>" id="menu<? echo $menu["id"]; ?>" onclick="<? echo ($menu["option"] == "-" || $menu["direct_link"] == "#" ? "return false;" : "");?>" tabindex="-1"><? echo $menu["name"]?></a>
                                            <?
                                                if ($menu["has_children"] == true && count($menu["children"]) > 0)
                                                {
                                                    ?>
                                                        <ul>
                                                            <?
                                                            foreach($menu["children"] as $menu_id)
                                                            {
                                                                if (isset($menus[$menu_id]))
                                                                    render_sublevel($menu_id, 1, $menus, $project_submenus, $projects);
                                                            }
                                                            ?>
                                                        </ul>
                                                    <?
                                                }
                                            ?>
                                        </li>
                                    <?
                                }
                            }
                        ?>
<!--                        <li><a href="index.php?option=com_hr&view=changepassword" tabindex="-1"><span>Change Password</span></a></li>
-->                        <!--<li><a href="index.php?option=com_user&task=logout&return=aW5kZXgucGhw"><span>Logout</span></a></li>-->
                </ul>
            </div>
        </div>
    <?
}
?>