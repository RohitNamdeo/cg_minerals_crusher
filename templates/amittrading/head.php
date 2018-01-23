<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<?
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once (JPATH_ROOT.DS.'custom'.DS.'phpincludes'.DS.'functions.php');
?>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.10.1/js/jquery.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.10.1/js/jquery-ui-1.10.3.custom.min.js"></script>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/custom/packages/jquery1.10.1/css/flick/jquery-ui-1.10.3.custom.min.css" type="text/css" />
<script>
    j=jQuery.noConflict();
</script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/jquery.customplugins.compiled.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/minified/jquery.customplugins.compiled.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/minified/jquery.tablesorter.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/minified/jquery.floatheader.min.js"></script>       
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/minified/jquery.ui-contextmenu.min.js"></script>       

<script src="<?php echo $this->baseurl ?>/custom/pdf/FileSaver.js"></script>       
<script src="<?php echo $this->baseurl ?>/custom/pdf/jspdf.js"></script>       
<script src="<?php echo $this->baseurl ?>/custom/pdf/jspdf.plugin.table.js"></script>

      
<script src="<?php echo $this->baseurl ?>/custom/pdf/cwdialog.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo $this->baseurl ?>/custom/css/cwdialog.css" />      


<link type="text/css" rel="stylesheet" href="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/css/jquery.dropdown.css" /> 
<link type="text/css" rel="stylesheet" href="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/css/jquery.treeview.css" /> 
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/css/colorbox.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/css/chosen.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/custom/css/megamenu/megamenu.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/custom/css/megamenu/skins/grey.css" type="text/css" />
<!--<link rel="stylesheet" href="<?php //echo $this->baseurl ?>/custom/css/jquery.dropdown.css" type="text/css" /> -->
<script>
    j(function() {
        j("body").bind("contextmenu", function(){
            //return false;
        });
        j("abbr.timeago").timeago();
        j( "input:submit,input:reset,input:button, button").button();
        j(".tablelist tr td:first-child").attr("align", "right");
        j(".floatheader").floatHeader();
        
        if(typeof String.prototype.trim !== 'function') {
          String.prototype.trim = function() {
            return this.replace(/^\s+|\s+$/g, ''); 
          }
        }
        (function(j) {
            j.fn.getCursorPosition = function() {
                var input = this.get(0);
                if (!input) return; // No (input) element found
                if ('selectionStart' in input) {
                    // Standard-compliant browsers
                    return input.selectionStart;
                } else if (document.selection) {
                    // IE
                    input.focus();
                    var sel = document.selection.createRange();
                    var selLen = document.selection.createRange().text.length;
                    sel.moveStart('character', -input.value.length);
                    return sel.text.length - selLen;
                }
            }
        })(jQuery);
        
        /*
        * plugin to make most of the reports & history keyboard friendly
        * by keyboard up-down arrow keys, user can navigate to next or previous row
        * by default 1st row is highlighted
        * enter click triggers the action ie. displaying item details in invoice history, opening supplier, customer, transporter account from respective views
        * whenever up-down arrow click navigates to row which is not in display due to screen height then scroll is performed by code
        * callTaskOnNavigation works when up & down arrow triggers the task instead of enter click. It is used in pending sales orders
        */
        
        (function(j){
            j.fn.scrollIntoView = function(options){
                var params = j.extend({ 
                    rowSelector : null,
                    rowAttribute : null,
                    task : null,
                    callTaskOnNavigation : 0
                }, options);
                
                j(".scrollIntoView").find("tr." + params.rowSelector).first().addClass("clickedRow");
                
                if(params.callTaskOnNavigation == 1)
                {
                    var task_param = parseFloat(j(".scrollIntoView tr.clickedRow").attr(params.rowAttribute));
            
                    if(task_param == "" || isNaN(task_param))
                    {
                        task_param = 0;
                    }
                    if(task_param > 0)
                    {
                        var action = params.task + "(" + task_param + ");";
                        eval(action);
                    }
                }
                 
                j(document).on("keydown", function(e) {
                    switch(e.keyCode) {
                        case 38: // up
                            if (j(".scrollIntoView tr.clickedRow").prev().length) {
                                j(".scrollIntoView tr.clickedRow").removeClass("clickedRow").prev().addClass("clickedRow");
                                
                                if(params.callTaskOnNavigation == 1)
                                {
                                    var task_param = parseFloat(j(".scrollIntoView tr.clickedRow").attr(params.rowAttribute));
                            
                                    if(task_param == "" || isNaN(task_param))
                                    {
                                        task_param = 0;
                                    }
                                    if(task_param > 0)
                                    {
                                        var action = params.task + "(" + task_param + ");";
                                        eval(action);
                                    }
                                }
                            }
                            break;
                        case 40: // down
                            if (j(".scrollIntoView tr.clickedRow").next().length) {
                                j(".scrollIntoView tr.clickedRow").removeClass("clickedRow").next().addClass("clickedRow");   
                                
                                if(params.callTaskOnNavigation == 1)
                                {
                                    var task_param = parseFloat(j(".scrollIntoView tr.clickedRow").attr(params.rowAttribute));
                            
                                    if(task_param == "" || isNaN(task_param))
                                    {
                                        task_param = 0;
                                    }
                                    if(task_param > 0)
                                    {
                                        var action = params.task + "(" + task_param + ");";
                                        eval(action);
                                    }
                                }
                            }
                            break;
                        case 13: // enter
                            var task_param = parseFloat(j(".scrollIntoView tr.clickedRow").attr(params.rowAttribute));
                            
                            if(task_param == "" || isNaN(task_param))
                            {
                                task_param = 0;
                            }
                            if(task_param > 0)
                            {
                                var action = params.task + "(" + task_param + ");";
                                eval(action);
                            }
                            break;
                    }
                    
                    if (e.keyCode == 38 || e.keyCode == 40)
                    {
                        if (j(".scrollIntoView tbody tr.clickedRow").offset().top < window.scrollY)
                        {
                            j("html, body").scrollTop(j(".scrollIntoView tbody tr.clickedRow").offset().top);
                        }
                        else if (j(".scrollIntoView tbody tr.clickedRow").offset().top + j(".scrollIntoView tbody tr.clickedRow").height() > window.scrollY + (window.innerHeight || document.documentElement.clientHeight) - 50)
                        {
                            j("html, body").scrollTop(j(".scrollIntoView tbody tr.clickedRow").offset().top + j(".scrollIntoView tbody tr.clickedRow").height() - (window.innerHeight || document.documentElement.clientHeight) + 50);
                        }
                        else
                        {
                            e.preventDefault();
                        }
                    }
                });
            }
        }(jQuery));
    });
    
    function colorbox(element_id)
    {
        j("#" + element_id).colorbox({maxWidth: "80%", maxHeight: "100%", width : "960"});
    }
    
    function go(place)
    {
        document.location.href = place;
    }
    
    function isemail(email)
    {
       var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
       return reg.test(email);
    }
    
    function isnumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
    var jspopup_printdata = "";
    function popup_print(htmldata)
    {
        jspopup_printdata=htmldata;
        window.open('index.php?option=com_master&view=jsprintpopup&tmpl=print');
    }
    
    function prevent_char(keycode,e)
    {
        if(!(keycode>=48 && keycode<=57))
        {
            if(!((keycode == 0) || (keycode==8) || keycode == 46))
            e.preventDefault();    
        }                            
    }
    
    function strict_numbers(keycode,e)
    {
        if(!(keycode>=48 && keycode<=57))
        {
            if(!((keycode == 0) || (keycode==8)))
            e.preventDefault();    
        }
    }
    
    function numeric(e)
    {
        if(!(e.which>=48 && e.which<=57 ))
        {
            if(!((e.which == 0) || (e.which==8)))
            e.preventDefault();    
        }
    }
    
    function alpha_numeric(e)
    {
        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str) || e.which == 8 || e.which == 9 || e.which == 32 || e.which == 13)   
        {
            return true;
        }
        e.preventDefault();
        return false;
    }
    
    
    
    function formatDate(date)
    {
        var monthNames = new Array("Jan", "Feb", "Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep","Oct", "Nov", "Dec");
        
        var d = new Date(date),
            //month = '' + (d.getMonth() + 1),
            month = '' + d.getMonth(),
            day = '' + d.getDate(),
            year = d.getFullYear();

        //if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;
        
        return [day, monthNames[month], year].join('-');
    }
    
    j(document).on("keydown", function(e){
        // shortcuts which opens the link
        // 1st shortcut triggers the submit task in most of the forms
        
        if (e.altKey && e.which == 90) 
        {
            e.preventDefault();
            validateForm();
        }
        
        if (e.ctrlKey && e.which == 89) /*Ctrl + Y*/
        {
            e.preventDefault();
            go("index.php?option=com_master&view=manage_categories");
        }
        else if (e.ctrlKey && e.which == 69) /*Ctrl + E*/
        {
            e.preventDefault();
            go("index.php?option=com_master&view=manage_customers");
        }
        else if (e.ctrlKey && e.which == 83) /*Ctrl + S*/
        {
            e.preventDefault();
            go("index.php?option=com_master&view=manage_suppliers");
        }
        else if (e.ctrlKey && e.which == 76) /*Ctrl + L*/
        {
            e.preventDefault();
            go("index.php?option=com_master&view=manage_locations");
        }
        else if (e.ctrlKey && e.which == 81) /*Ctrl + Q*/
        {
            e.preventDefault();
            go("index.php?option=com_master&view=manage_customer_categories");
        }
        else if (e.ctrlKey && e.which == 85) /*Ctrl + U*/
        {
            e.preventDefault();
            go("index.php?option=com_amittrading&view=add_purchase_order");
        }
        else if (e.ctrlKey && e.which == 80) /*Ctrl + P*/
        {
            e.preventDefault();
            go("index.php?option=com_amittrading&view=purchase_invoice");
        }
        else if (e.ctrlKey && e.which == 72) /*Ctrl + H*/
        {
            e.preventDefault();
            go("index.php?option=com_amittrading&view=purchase_return");
        }
        else if (e.ctrlKey && e.which == 79) /*Ctrl + O*/
        {
            e.preventDefault();
            go("index.php?option=com_amittrading&view=sales_order");
        }
        else if (e.ctrlKey && e.which == 66) /*Ctrl + B*/
        {
            e.preventDefault();
            go("index.php?option=com_amittrading&view=sales_invoice");
        }
        else if (e.ctrlKey && e.which == 87) /*Ctrl + W*/
        {
            e.preventDefault();
            go("index.php?option=com_amittrading&view=sales_return");
        }
        else if (e.ctrlKey && e.which == 75) /*Ctrl + K*/
        {
            e.preventDefault();
            go("index.php?option=com_amittrading&view=stock_transfer");
        }
        else if (e.ctrlKey && e.which == 77) /*Ctrl + M*/
        {
            e.preventDefault();
            go("index.php?option=com_amittrading&view=customer_payment");
        }
        else if (e.ctrlKey && e.which == 78) /*Ctrl + N*/
        {
            e.preventDefault();
            go("index.php?option=com_amittrading&view=supplier_payment");
        }
        else if (e.ctrlKey && e.which == 68) /*Ctrl + D*/
        {
            e.preventDefault();
            go("index.php?option=com_amittrading&view=collection_report");
        }
        else if (e.ctrlKey && e.which == 73) /*Ctrl + I*/
        {
            e.preventDefault();
            go("index.php?option=com_amittrading&view=itemwise_sales_stats");
        }
        else if (e.ctrlKey && e.which == 71) /*Ctrl + G*/
        {
            e.preventDefault();
            go("index.php?option=com_amittrading&view=stock_report");
        }
       // else if (e.ctrlKey && e.which == 74) /*Ctrl + J*/
//        {
//            e.preventDefault();
//            go("index.php?option=com_amittrading&view=marketing_list");
//        }
        else if (e.ctrlKey && e.which == 82) /*Ctrl + R*/
        {
            e.preventDefault();
            go("index.php?option=com_master&view=manage_vehicles");
        }
    });
    
    function convert_number(number)
    {
        if ((number < 0) || (number > 999999999)) 
        { 
            //return "NUMBER OUT OF RANGE!";
            return 0;
        }
        var Gn = Math.floor(number / 10000000);  /* Crore */ 
        number -= Gn * 10000000; 
        var kn = Math.floor(number / 100000);     /* lakhs */ 
        number -= kn * 100000; 
        var Hn = Math.floor(number / 1000);      /* thousand */ 
        number -= Hn * 1000; 
        var Dn = Math.floor(number / 100);       /* Tens (deca) */ 
        number = number % 100;               /* Ones */ 
        var tn= Math.floor(number / 10); 
        var one=Math.floor(number % 10); 
        var res = ""; 

        if (Gn>0) 
        { 
            res += (convert_number(Gn) + " CRORE"); 
        } 
        if (kn>0) 
        { 
                res += (((res=="") ? "" : " ") + 
                convert_number(kn) + " LAKH"); 
        } 
        if (Hn>0) 
        { 
            res += (((res=="") ? "" : " ") +
                convert_number(Hn) + " THOUSAND"); 
        } 

        if (Dn) 
        { 
            res += (((res=="") ? "" : " ") + 
                convert_number(Dn) + " HUNDRED"); 
        } 


        var ones = Array("", "ONE", "TWO", "THREE", "FOUR", "FIVE", "SIX","SEVEN", "EIGHT", "NINE", "TEN", "ELEVEN", "TWELVE", "THIRTEEN","FOURTEEN", "FIFTEEN", "SIXTEEN", "SEVENTEEN", "EIGHTEEN","NINETEEN"); 
        var tens = Array("", "", "TWENTY", "THIRTY", "FOURTY", "FIFTY", "SIXTY","SEVENTY", "EIGHTY", "NINETY"); 

        if (tn>0 || one>0) 
        { 
            if (!(res=="")) 
            { 
                res += " AND "; 
            } 
            if (tn < 2) 
            { 
                res += ones[tn * 10 + one]; 
            } 
            else 
            { 

                res += tens[tn];
                if (one>0) 
                { 
                    res += ("-" + ones[one]); 
                } 
            } 
        }

        if (res=="")
        { 
            res = "zero"; 
        } 
        return res;
    }
    
    j(document).ready(function(){
        j(document).bind("contextmenu",function(e){
            //e.preventDefault();
        });
    });
    
    /*function generatefromtable() {
        var data = [], fontSize = 7, height = 0, doc;
        doc = new jsPDF('p', 'pt', 'a4', true);
        doc.setFont("times", "normal");
        doc.setFontSize(fontSize);
        doc.text(20, 20, ""); 
        data = [];
        data = doc.tableToJson('vehicles');
         height = doc.drawTable(data, {
            xstart : 10,
            ystart : 10,
            tablestart : 10,
            //marginleft : 10,
            xOffset : 10,
            yOffset : 15,
        });
        doc.text(50, height + 20, '');
        doc.save("some-file.pdf"); 
    }  */  
    
    
    var tableToExcel = (function() {
    var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
    return function(table, name) {
    if (!table.nodeType) table = document.getElementById(table)
    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
    window.location.href = uri + base64(format(template, ctx))
    }
    })()
</script>
<style>
body
{
  /*-moz-user-select: none;
  -khtml-user-select: none;
  -webkit-user-select: none;
  user-select: none;*/
}
</style>
<script type="text/javascript" src="<?php echo $this->baseurl; ?>/custom/js/flashobject.js"></script>
<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/custom/css/template.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/custom/css/graphics.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/system.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/general.css" type="text/css" />
<!--[if IE 6]>
    <script type="text/javascript" src="<?php echo $this->baseurl; ?>/custom/js/ie_png.js"></script>
    <script type="text/javascript">
       ie_png.fix('img');
   </script>
<![endif]-->
<script>
    j(function(){
        j("#page-loader").hide();
        j("#content").show();
    });
</script>