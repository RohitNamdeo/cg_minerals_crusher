<?php
    defined('_JEXEC') or die( 'Restricted access' );
?>
<style>
.hasmenu, .hasmenu2 {
    border: 1px solid #008;
    margin: 3px;
    padding: 5px;
    width: 30px;
}

/* Optionally define a fixed width for menus */
.ui-menu {
    width: 220px;
}
/* Allow to use <kbd> elements inside the title to define shortcut hints. */
.ui-menu kbd {
    padding-left: 1em;
    float: right;
}

/* Define a custom icon */
.ui-icon.custom-icon-firefox {
    background-image: url(application_firefox.gif);
    background-position: 0 0;
}
</style>
<script>
var CLIPBOARD;

j(document).contextmenu({
    delegate: ".hasmenu",
    autoFocus: true,
    preventContextMenuForPopup: true,
    preventSelect: true,
    taphold: true,
    menu: [{
        title: "Cut <kbd>Ctrl+X</kbd>",
        cmd: "cut",
        uiIcon: "ui-icon-scissors"
    }, {
        title: "Copy <kbd>Ctrl+C</kbd>",
        cmd: "copy",
        uiIcon: "ui-icon-copy"
    }, {
        title: "Paste <kbd>Ctrl+V</kbd>",
        cmd: "paste",
        uiIcon: "ui-icon-clipboard",
        disabled: true
    }, {
        title: "----"
    }, {
        title: "More",
        children: [{
            title: "Sub 1 (callback)",
            action: function (event, ui) {
                alert("action callback sub1");
            }
        }, {
            title: "Edit <kbd>[F2]</kbd>",
            cmd: "sub2",
            tooltip: "Edit the title"
        }, ]
    }],
    // Handle menu selection to implement a fake-clipboard
    select: function (event, ui) {
        var $target = ui.target;
        switch (ui.cmd) {
            case "copy":
                CLIPBOARD = $target.text();
                break
            case "paste":
                CLIPBOARD = "";
                break
        }
        alert("select " + ui.cmd + " on " + $target.text());
        // Optionally return false, to prevent closing the menu now
    },
    // Implement the beforeOpen callback to dynamically change the entries
    beforeOpen: function (event, ui) {
        var $menu = ui.menu,
            $target = ui.target,
            extraData = ui.extraData; // passed when menu was opened by call to open()

        // console.log("beforeOpen", event, ui, event.originalEvent.type);

        ui.menu.zIndex(j(event.target).zIndex() + 1);

        j(document)
        //                .contextmenu("replaceMenu", [{title: "aaa"}, {title: "bbb"}])
        //                .contextmenu("replaceMenu", "#options2")
        //                .contextmenu("setEntry", "cut", {title: "Cuty", uiIcon: "ui-icon-heart", disabled: true})
        .contextmenu("setEntry", "copy", "Copy '" + $target.text() + "'")
            .contextmenu("setEntry", "paste", "Paste" + (CLIPBOARD ? " '" + CLIPBOARD + "'" : ""))
            .contextmenu("enableEntry", "paste", (CLIPBOARD !== ""));

        // Optionally return false, to prevent opening the menu now
    }
});
</script>
<h1>jquery.ui-contextmenu.js</h1>

<p>Right-click in an element to open the context menu:</p>
<br />
<div>
    <span class="hasmenu" tabindex="0">AAA</span>
    <span class="hasmenu" tabindex="0">BBB</span>
    <span class="hasmenu" tabindex="0">CCC</span>
</div>