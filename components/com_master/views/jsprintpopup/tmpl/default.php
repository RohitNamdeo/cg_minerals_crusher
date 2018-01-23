<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<script>
j(function(){
	j("#printdata").html(window.opener.jspopup_printdata);
    window.print();
});
</script>

<div id="printdata"></div>