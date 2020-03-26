<!DOCTYPE HTML>

<html>

<head>
    <meta charset="UTF-8">
  <title>Untitled</title>
</head>

<body>
<?php
$url = "http://bnb.bg/Statistics/StExternalSector/StExchangeRates/StERForeignCurrencies/index.htm";
$content = file_get_contents($url) ;


  $table_open_tag = "<tbody>";
$table_beginning_position = strpos($content , $table_open_tag  ) ;


    $table_close_tag = "</tbody>";
$table_closing_position = strpos($content , $table_close_tag , $table_beginning_position)  ;

 $table_length =   $table_closing_position -   $table_beginning_position ;
$table_content = substr($content , $table_beginning_position ,$table_length  ) ;

$row = '<td class="first">';
$currency_total = substr_count($table_content , $row )   ;


 $offset = 0 ;
 $masiv = explode("</tr>", $table_content)   ;
  mb_internal_encoding("UTF-8");
 function get_specific_currency_params($string , $div_class , $beginning=0)
 {
          $td_openning_tag_start_pos =  mb_strpos($string , $div_class , $beginning   )    ;
          $offset = $td_openning_tag_start_pos ;

         $td_openning_tag_final_pos =  mb_strpos($string , '>',  $offset )    ;
          $offset = $td_openning_tag_final_pos ;

          $td_closing_tag_first_position  = mb_strpos($string , '</' , $offset) ;

          $specific_param  = mb_substr($string ,$td_openning_tag_final_pos + 1 ,$td_closing_tag_first_position -$td_openning_tag_final_pos-1 ) ;
          return array($specific_param , $offset);
 }
    $final_array = array();
  for($i = 0 ; $i < $currency_total ; $i++ , $offset)
  {
      $individual_currency = $masiv[$i];
       $div_class = '<td class="first">';
       list($name , $offset ) =    get_specific_currency_params($individual_currency , $div_class );

       $div_class = '<td class="center">';
       list($name_abbr, $offset ) =   get_specific_currency_params($individual_currency , $div_class );

        $div_class = '<td class="right">';
        list($unit , $offset)  =    get_specific_currency_params($individual_currency , $div_class );

          $div_class = '<td class="center">';
        list($value , $offset )=    get_specific_currency_params($individual_currency , $div_class , $offset );

         $div_class = '<td class="last center">';
        list($return_value , $offset) =    get_specific_currency_params($individual_currency , $div_class );

              $final_array[$i]= array($name , $name_abbr, $unit , $value);
             $final_array_assoc[$name_abbr]= array($name , $name_abbr, $unit , $value) ;


  }
  echo "<table>";
    for($i = 0 ; $i < $currency_total ; $i++ )
  {
        echo "<tr>";
        echo "<td>" ;
        echo  $final_array[$i][0]  ;
        echo "</td>" ;
        echo "<td>" ;
        echo  $final_array[$i][1]  ;
        echo "</td>" ;
        echo "<td>" ;
        echo  $final_array[$i][2]  ;
        echo "</td>" ;
        echo "<td>" ;
        echo  $final_array[$i][3]  ;
        echo "</td>" ;
        echo "</tr>";
  }
  echo "</table>";
  echo "<br>";
  echo '<form method="post" id="form">';
  echo "<p>Конвертирай от :</p>";
    echo '<select id="from" name="select_from">';
    for($i=0 ; $i < $currency_total ; $i++)
    {
          echo '<option value="'.$final_array[$i][1].'">'.$final_array[$i][0]."(".$final_array[$i][1].")</option>";
    }
    echo '</select>';
    echo "<p>Сума :</p>";
    echo '<input type="text" name="txt_from">';
    echo "<p>Конвертирай към : </p>";
     echo '<select id="to" name="select_to">';
    for($i=0 ; $i < $currency_total ; $i++)
    {
        echo '<option value="'.$final_array[$i][1].'">'.$final_array[$i][0]."(".$final_array[$i][1].")</option>";

    }
    echo '</select>';
    echo "<p>";

    echo '<input type="submit" value="Сметни" name="button" id="buttona">';
      echo "</p>";
    echo '</form>';
    if(isset($_POST['button']))
    {


      $select_from =    $_POST['select_from'] ;
       $select_to=    $_POST['select_to'] ;
       $quantity =     $_POST['txt_from'] ;


       if(array_key_exists($select_from,$final_array_assoc)      )
       {

           $currency_from_unit = $final_array_assoc[$select_from][2];
           $currency_from_value=  $final_array_assoc[$select_from][3];
           if($currency_from_unit!=1)
           {
                 $currency_from_value = $currency_from_value / $currency_from_unit ;
           }
       }
         if(array_key_exists($select_to,$final_array_assoc)      )
       {

           $currency_to_unit = $final_array_assoc[$select_to][2];
           $currency_to_value=  $final_array_assoc[$select_to][3];
           if($currency_to_unit!=1)
           {
                 $currency_to_value = $currency_to_value / $currency_to_unit ;
           }
       }
       $amount = $currency_from_value * $quantity ;
         $result =   $amount / $currency_to_value ;

              echo " С $quantity $select_from можете да купите $result $select_to";
  }


?>

</body>

</html>