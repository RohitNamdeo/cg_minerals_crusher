<?php
date_default_timezone_set("Asia/Calcutta");
setlocale(LC_MONETARY, 'en_IN');

function round_2dp($value)
{
    return sprintf("%0.2f", $value); 
}

function in_strtotime($date)
{
    if ($date == "")
    {
        return 0;
    }
    $date = str_replace("/", "-", $date);
    $date = str_replace("\\", "-", $date);
    $date_parts = explode("-", $date);
    if (isset($date_parts[1]))
    {
        if (is_numeric($date_parts[1]))
        {
            $date_parts[1] = date("M", mktime(0, 0, 0, $date_parts[1], 10));
        }
    }
    $date = implode("-", $date_parts);
    return strtotime($date);
}

function compare_object_dates($object1, $object2)
{
        return strtotime($object1->compare_date) - strtotime($object2->compare_date);
}

function is_admin()
{
    $current_user_id = intval(JFactory::getUser()->id);
    
    if($current_user_id == 265 || $current_user_id == 266)
    {
        return true;
    }
    else return false;
}
?>