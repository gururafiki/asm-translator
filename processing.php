<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Зарах | E | 60</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <link href="css/bootstrap.css" rel="stylesheet">
    <!--<link href="assets/css/styles.css" rel="stylesheet">-->
    <!-- Bootstrap -->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
  <body>
<form enctype="multipart/form-data" action="processing.php" style="padding-left: 20px;padding-right: 20px;" method="POST">
    <!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла -->
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    <!-- Название элемента input определяет имя в массиве $_FILES -->
    <pre>Отправить этот файл: </pre><input class="btn btn-primary" name="userfile" type="file" />
    </br>
    <input class="btn btn-primary" type="submit" value="Send File" />
</form>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="msg"></div>
            <div id="main" class="row-fluid">
                <div class="table">
<?php
    require "db.php";

	function check_hex_const($lexem){
        if($lexem[0]=='$'){
        	for($i=1;$i<strlen($lexem);$i++){
				$ascii_value=ord($lexem[$i]);
				if(!(($ascii_value>=97 && $ascii_value<=102) || ($ascii_value>=65 && $ascii_value<=70) || ($ascii_value>=48 && $ascii_value<=57))){
					return false;
				}
        	}
        }
        elseif( $lexem[strlen($lexem)-1]=='h' || $lexem[strlen($lexem)-1]=='H'){
        	if(!(ord($lexem[0])>=48 && ord($lexem[0])<=57)){
        		return false;
        	}
			for($i=1;$i<(strlen($lexem)-1);$i++){
				$ascii_value=ord($lexem[$i]);
				if(!(($ascii_value>=97 && $ascii_value<=102) || ($ascii_value>=65 && $ascii_value<=70) || ($ascii_value>=48 && $ascii_value<=57))){
        			return false;
				}
        	}
        }
        else{
        	return false;
        }
        return true;
	}
	function check_bin_const($lexem){
        if( $lexem[strlen($lexem)-1]=='b' || $lexem[strlen($lexem)-1]=='B'){
			for($i=0;$i<(strlen($lexem)-1);$i++){
				$ascii_value=ord($lexem[$i]);
				if(!($ascii_value==48 || $ascii_value==49)){
        			return false;
				}
        	}
        }
        else{
        	return false;
        }
        return true;
	}
	function check_dec_const($lexem){
		for($i=0;$i<strlen($lexem);$i++){
			$ascii_value=ord($lexem[$i]);
			if(!($ascii_value>=48 && $ascii_value<=57)){
    			return false;
			}
    	}
        return true;
	}
	function check_identificator($lexem){
        if( strlen($lexem)<=4){
        	if(!( (ord($lexem[0])>=65 && ord($lexem[0])<=90) || (ord($lexem[0])>=97 && ord($lexem[0])<=122) ) ){
        		return false;
        	}
			for($i=1;$i<strlen($lexem);$i++){
				$ascii_value=ord($lexem[$i]);
				if(!( ($ascii_value>=48 && $ascii_value<=57) || ($ascii_value>=65 && $ascii_value<=90) || ($ascii_value>=97 && $ascii_value<=122) ) ){
        			return false;
				}
        	}
        }
        else{
        	return false;
        }
        return true;
	}

    function check_pointer($lexem){
        if(strstr($lexem ,'ptr')){
            $lexem=trim(substr($lexem,strpos($lexem,'ptr')+3));
        }
        if(strstr($lexem ,':') ){
            $register=trim(substr($lexem,0,strpos($lexem,':')));
            if($register[1]=='s')
                $reg=R::findOne('dictionary','name = ?',array(trim($register)));
            if(!isset($reg))
                return false;
            $lexem=trim(substr($lexem,strpos($lexem,':')+1));
        }
        if( $lexem[0]=='[' && $lexem[strlen($lexem)-1]==']' && strlen($lexem)-1!=1){
            $buf=substr($lexem,1,strlen($lexem)-2);
            $arr=explode('+', $buf);
            $base=0;
            foreach ($arr as $key) {
                if($base==0 && check_register_32($key) ){
                    $base=32;
                }
                elseif($base==0 && check_register_16($key) ){
                    $base=16;
                }
                elseif($base==0 && check_register_8($key) ){
                    $base=8;
                }
                elseif($base==0 && trim($key)=='bp' ){
                    $base=='bp';
                }
                elseif($base!=32 && check_register_32($key) ){
                    return false;
                }
                elseif($base!=16 && check_register_16($key) ){
                    return false;
                }
                elseif($base!=8 && check_register_8($key) ){
                    return false;
                }
                elseif($base!='bp' && trim($key)=='bp' ){
                    return false;
                }
                elseif($base==32 && check_register_32($key) ){
                    continue;
                }
                elseif($base==16 && check_register_16($key) ){
                    continue;
                }
                elseif($base==8 && check_register_8($key) ){
                    continue;
                }
                else{
                    $reg=R::findOne('dictionary','type = ? AND name = ?',array('register',trim($key) ) );
                    if( ($reg->name=='si' || $reg->name=='di' ) && $base==8){
                        return false;
                    }

                    if(!( isset($reg) || check_dec_const($key) || check_hex_const($key) || check_bin_const($key) ) )
                    return false;
                }
            }    
        }
        else{
            return false;
        }
        return true;
    }
    function check_met($lexem){
        if( $lexem[strlen($lexem)-1]!=':' || strlen($lexem)>5)
            return false;
        return true;
    }   
    function check_string($lexem){
        if(!( $lexem[0]=='"' && $lexem[strlen($lexem)-1]=='"')){
           return false;
        }
        return true;
    }
    function check_register($lexem){
        $row=R::findOne('dictionary','type = ? AND name = ?',array('register',$lexem));
        if(!isset($row))
            if(!check_register_32(trim($lexem)))
                if(!check_register_16(trim($lexem)))
                    if(!check_register_8(trim($lexem)))
                        return false;
        return true;
    }
    function check_register_32($lexem){
        if($lexem[0]=='e'){
            if(ord($lexem[1])>=97 && ord($lexem[1])<=100 && $lexem[2]=='x'){
                return true;
            }
        }
        return false;
    }

    function check_register_16($lexem){
        if(ord($lexem[0])>=97 && ord($lexem[0])<=100 && $lexem[1]=='x'){
            return true;
        }
        return false;
    }
    
    function check_register_8($lexem){
        if(ord($lexem[0])>=97 && ord($lexem[0])<=100 && ($lexem[1]=='h' || $lexem[1]=='l'))
                return true;
        return false;
    }

    function to_array_by_pos($string,$delimer_pos){
        $pre_str=substr($string,0,$delimer_pos-1);
        $str=explode(' ', $pre_str);
        $counter=0;
        foreach ($str as $key) {
            if((strlen($key)==2 && ord($key[0])==13 && ord($key[1])==10)|| $key=='' || $key==' ' || strlen($key)==0 || ord($key)==0){
                unset($str[$counter]);
            }
            $counter++;
        }
        array_push($str,substr($string,$delimer_pos));
        return $str;
    }

    function to_array_by_pos_include_pos($string,$delimer_pos){
        $pre_str=substr($string,0,$delimer_pos);
        $str=explode(' ', $pre_str);
        $counter=0;
        foreach ($str as $key) {
            if((strlen($key)==2 && ord($key[0])==13 && ord($key[1])==10) || $key=='' || $key==' ' || strlen($key)==0 || ord($key)==0){
                unset($str[$counter]);
            }
            $counter++;
        }
        array_push($str,substr($string,$delimer_pos,1));
        array_push($str,substr($string,$delimer_pos+1));
        return $str;
    }

    function detect_lexem($lexem){
        $lexem_define=R::FindOne('dictionary','name = ?',array(trim($lexem)));
        if(!isset($lexem_define))
            if(!check_register_32($lexem))
                if(!check_register_16($lexem))
                    if(!check_register_8($lexem))
                        if(!check_hex_const(trim($lexem)))
                            if(!check_bin_const(trim($lexem)))
                                if(!check_dec_const(trim($lexem)))
                                    if(!check_pointer(trim($lexem)))
                                        if(!check_string(trim($lexem)))
                                            if(!check_met(trim($lexem)))
                                                if(!check_identificator(trim($lexem)))
                                                    $lexem_define=R::findOne('dictionary','name = ?',array('Undefined lexem'));
                                                else
                                                    $lexem_define=R::findOne('dictionary','name = ?',array('identifier'));
                                            else
                                                $lexem_define=R::findOne('dictionary','name = ?',array('met'));
                                        else
                                            $lexem_define=R::findOne('dictionary','name = ?',array('str const'));
                                    else
                                        $lexem_define=R::findOne('dictionary','name = ?',array('pointer'));
                                else
                                    $lexem_define=R::findOne('dictionary','name = ?',array('dec const'));
                            else
                                $lexem_define=R::findOne('dictionary','name = ?',array('bin const'));
                        else
                            $lexem_define=R::findOne('dictionary','name = ?',array('hex const'));
                    else
                        $lexem_define=R::findOne('dictionary','name = ?',array('register08'));
                else
                    $lexem_define=R::findOne('dictionary','name = ?',array('register16'));
            else
                $lexem_define=R::findOne('dictionary','name = ?',array('register32'));

        return $lexem_define;
    }
    
    function check_for_spaces($string){
        $buf_arr=explode(' ', $string);
        $counter=0;
        foreach ($buf_arr as $key) {
            if((strlen($key)==2 && ord($key[0])==13 && ord($key[1])==10) || $key=='' || $key==' ' || strlen($key)==0 || ord($key)==0){
                unset($buf_arr[$counter]);
            }
            $counter++;
        }
        return $buf_arr;
    }
    function echo_code($rows){
       echo '<div class="col-lg-12"><table class="table table-striped table-condensed table-bordered table-hover"><tbody><thead>
                    <tr>
                    </tr>
                    <tr>
                      <th style="width:5%">row number</th>
                      <th style="width:15%">first word</th>
                      <th style="width:15%">second word</th>
                      <th style="width:10%">third word</th>
                      <th style="width:20%">fourth word</th>
                      <th style="width:10%">fifth word</th>
                    </tr>
                  </thead>';
        $row_number=0;          
        foreach ($rows as $row) {            
            echo '<tr class="clickable">';
            echo '<td>'.$row_number.'</td>';
            foreach ($row as $word) {
                echo '<td>'.$word.'</td>';
            }
            $row_number++;
            echo '</tr>';
        }
        echo "</tbody></table></div>"; 
        return $row_number;
    }

    function lexical_analisation($rows){
       $row_number=0;
        foreach ($rows as $row) {
            $word_number=0;
            foreach ($row as $word) {
                        $lexem_obj=detect_lexem($word);
                        $rows['code'][$row_number][$word_number]=$word;
                        $rows['type'][$row_number][$word_number]=$lexem_obj->type;
                        $rows['name'][$row_number][$word_number]=$lexem_obj->name;
                        $rows['required_after'][$row_number][$word_number]=$lexem_obj->required_after;
                        $rows['required_before'][$row_number][$word_number]=$lexem_obj->required_before;
                        $rows['required_prev'][$row_number][$word_number]=$lexem_obj->required_prev;
                        $rows['required_next'][$row_number][$word_number]=$lexem_obj->required_next;
                        $rows['required_lexems'][$row_number][$word_number]=$lexem_obj->required_lexems;
                        $rows['max_length'][$row_number][$word_number]=$lexem_obj->max_length;
                        $rows['weight'][$row_number][$word_number]=$lexem_obj->weight;
                        $word_number++;
            }
            $row_number++;
        }
        return $rows; 
    }
    function table($row_number,$rows){
        for ($i=0;$i<$row_number;$i++){
        echo "<div class=\"col-lg-12\"><table class=\"table table-striped table-condensed table-bordered table-hover\"><tbody>
                   <thead>
                    <tr>
                    </tr>
                    <tr>
                      <th style=\"width:5%\">row number</th>
                      <th style=\"width:5%\">word number</th>
                      <th style=\"width:15%\">code</th>
                      <th style=\"width:15%\">name</th>
                      <th style=\"width:10%\">type</th>
                      <th style=\"width:10%\">max_length</th>
                      <th style=\"width:10%\">weight</th>
                      <th style=\"width:10%\">required_prev</th>
                      <th style=\"width:10%\">required_next_elem</th>
                      <th style=\"width:10%\">required_before</th>
                      <th style=\"width:10%\">required_after</th>
                      <th style=\"width:10%\">required_lexems</th>
                    </tr>
                  </thead>";
            $j=0;
            $row='';
            if( !isset($rows['errors'][$i][0]) )
                $rows['errors'][$i][0]='';
            while(isset($rows['code'][$i][$j])){
                echo '<tr class=\"clickable\">';
                    echo '<td>'.$i.'</td>';
                    echo '<td>'.$j.'</td>';
                    echo '<td>'.$rows['code'][$i][$j].'</td>';
                    echo '<td>'.$rows['name'][$i][$j].'</td>';                
                    if ($rows['type'][$i][$j]=='undefined'){
                        $rows['errors'][$i][0].='unexpected lexem "'.$rows['code'][$i][$j].'" on line '.$i.' on position '.$j. PHP_EOL;
                    }
                    echo '<td>'.$rows['type'][$i][$j].'</td>';
                    echo '<td>'.$rows['max_length'][$i][$j].'</td>';
                    echo '<td>'.$rows['weight'][$i][$j].'</td>';                    
                    if (strlen($rows['required_prev'][$i][$j])>1 && $rows['required_prev'][$i][$j]!='true'){
                        echo '<td>'.$rows['required_prev'][$i][$j].'</td>';
                        $rows['errors'][$i][0].='"'.$rows['required_prev'][$i][$j].'" is required at the previous position of lexem on line '.$i.' on position '.$j. PHP_EOL;
                    }
                    else{
                        echo '<td></td>';
                    }
                    if (strlen($rows['required_next'][$i][$j])>1 && $rows['required_next'][$i][$j]!='true'){
                        if($rows['name'][$i][$j]=='MACRO'){
                            echo '<td></td>';
                            $rows['required_next'][$i][$j]='true';
                        }
                        else{
                        echo '<td>'.$rows['required_next'][$i][$j].'</td>';
                        $rows['errors'][$i][0].='"'.$rows['required_next'][$i][$j].'" is required at the next position of lexem on line '.$i.' on position '.$j. PHP_EOL;
                        }
                    }
                    else{
                        echo '<td></td>';
                    }
                    if (strlen($rows['required_before'][$i][$j])>1 && $rows['required_before'][$i][$j]!='true'){
                        echo '<td>'.$rows['required_before'][$i][$j].'</td>';
                        $rows['errors'][$i][0].='"'.$rows['required_before'][$i][$j].'" is required before this row by lexem on line '.$i.' on position '.$j. PHP_EOL;
                    }
                    else{
                        echo '<td></td>';
                    }
                    if (strlen($rows['required_after'][$i][$j])>1 && $rows['required_after'][$i][$j]!='true'){
                        echo '<td>'.$rows['required_after'][$i][$j].'</td>';
                        $rows['errors'][$i][0].='"'.$rows['required_after'][$i][$j].'" is missed after lexem on line '.$i.' on position '.$j. PHP_EOL;
                    }
                    else{
                        echo '<td></td>';
                    }
                    if (strlen($rows['required_lexems'][$i][$j])>1 && $rows['required_lexems'][$i][$j]!='true'){
                        echo '<td>'.$rows['required_lexems'][$i][$j].'</td>';
                        $rows['errors'][$i][0].='Can not find in code "'.$rows['required_lexems'][$i][$j].'" that is required by lexem on line '.$i.' on position '.$j. PHP_EOL;
                    }
                    else{
                        echo '<td></td>';
                    }



                echo '</tr>';
                $row.=$rows['code'][$i][$j].' ';
                $j++;
            }

         echo '<h3>'.$row.'</h3></tbody></table></div>';
         if(!empty($rows['errors'][$i][0]))
            echo '<pre>'.$rows['errors'][$i][0].'</pre>';
        }
        return $rows;
    }

    function syntax_analisation($row_number,$rows){
        $array_required[][]='';
        $array_segments[][][]='';
        $stack_first=0;
        $stack_second=0;
        $required_identifiers[][]='';

        for ($i=0;$i<$row_number;$i++){
            $j=0;
            while(isset($rows['code'][$i][$j])){
                //проверка на соответсвие елемента позади текущего
                $checked_prev=R::findOne('dictionary','name = ? OR type = ?',array($rows['required_prev'][$i][$j],$rows['required_prev'][$i][$j]));
                if($checked_prev->type==$rows['type'][$i][$j-1]){
                    $rows['required_prev'][$i][$j]='true';
                    $rows['name'][$i][$j-1]=$checked_prev->name;
                    //запись массива границ сегмента
                    if($rows['name'][$i][$j-1]=='segment name' || $rows['name'][$i][$j-1]=='macro name'){
                        if($rows['name'][$i][$j-1]!='macro name')
                            $array_required[$stack_first]['code']='s:'.$rows['code'][$i][$j-1];
                        $array_required[$stack_first]['required_prev']=$rows['code'][$i][$j-1];
                        $array_required[$stack_first]['required_before']=$rows['name'][$i][$j];
                        $array_required[$stack_first]['required_name']=$rows['required_after'][$i][$j];
                        $array_required[$stack_first]['pos_i']=$i;
                        $array_required[$stack_first]['pos_j']=$j;
                        $stack_first++;
                    }
                }
                //проверка на соответсвие елемента впереди текущего
                $arr=explode('/', $rows['required_next'][$i][$j]);
                foreach ($arr as $key) {
                    $checked_next=R::findOne('dictionary','name = ? OR type = ?',array($key,$key));
                    if($checked_next->type==$rows['type'][$i][$j+1]){
                        $rows['required_next'][$i][$j]='true';
                        break;
                    }
                }

                if($rows['type'][$i][$j]=='command'){
                    if($rows['type'][$i][$j+1]=='directive' && $rows['code'][$i][$j+2]=='ptr' && $rows['type'][$i][$j+3]=='identifier'){
                        $required_identifiers[$req_iden_count]['code']=$rows['code'][$i][$j+3];
                        $required_identifiers[$req_iden_count]['position']=$rows['code'][$i][$j+1];
                        $required_identifiers[$req_iden_count]['i']=$i;
                        $required_identifiers[$req_iden_count]['j']=$j+3;
                        $req_iden_count++;

                    }
                    if($rows['type'][$i][$j+1]=='identifier'){
                        $required_identifiers[$req_iden_count]['code']=$rows['code'][$i][$j+1];
                        $required_identifiers[$req_iden_count]['i']=$i;
                        $required_identifiers[$req_iden_count]['j']=$j+1;
                        $req_iden_count++;
                    }
                    if($rows['type'][$i][$j+3]=='identifier'){
                        $required_identifiers[$req_iden_count]['code']=$rows['code'][$i][$j+3];
                        $required_identifiers[$req_iden_count]['i']=$i;
                        $required_identifiers[$req_iden_count]['j']=$j+3;
                        //echo $required_identifiers[$req_iden_count]['code']. PHP_EOL;
                        $req_iden_count++;
                    }
                }
                //проверка на наличее закрывающего елемента впереди текущего
                $arr1=explode('/', $rows['required_after'][$i][$j]);
                foreach ($arr1 as $key) {
                    $checked_end_of_row=R::findOne('dictionary','type = ?',array($key));
                    if($checked_end_of_row->type==$rows['type'][$i][$j+3] && ord($checked_end_of_row->type)!=0){
                        //проверка на соответствие операндов команды
                        if($rows['type'][$i][$j]=='command' && $rows['name'][$i][$j+1]!=$rows['name'][$i][$j+3])
                            if($rows['type'][$i][$j+1] == 'register' && $rows['type'][$i][$j+3] == 'register')
                                if( substr($rows['name'][$i][$j+1],-2) != substr($rows['name'][$i][$j+3],-2) )
                                    $rows['errors'][$i][0]='Operand types must match on line '.$i. PHP_EOL;

                        if($rows['type'][$i][$j+2]=='char'){
                            $rows['required_after'][$i][$j]='true';
                            $rows['required_before'][$i][$j+3]='true';
                            break;
                        }
                        else{
                            $rows['errors'][$i][$j]='unexpected lexem on line '.$i;
                            $rows['required_after'][$i][$j]='true';
                            $rows['required_before'][$i][$j+3]='true';
                            break;
                        }
                    }
                }
                //проверка на наличее открывающего елемента позади текущего
                $arr_before=explode('/', $rows['required_before'][$i][$j]);
                foreach ($arr_before as $req_before) {
                    $req_counter=0;
                    foreach ($array_required as $required) {
                        if(substr(trim($rows['code'][$i][$j]),1)==$required['code']){
                            if($rows['code'][$i][$j][0]=='c')
                                $buf='code segment';
                            elseif(trim($rows['code'][$i][$j])[0]=='d')
                                $buf= 'data segment';
                            if($j==1){
                                $rows['required_next'][$i][$j-1]='true';
                                $rows['name'][$i][$j]=$buf;
                                $rows['type'][$i][$j]='register:'.$buf.' identifier';
                                $array_required[$req_counter]['segment_type']=$buf;
                            }
                            elseif($j==3){
                                $rows['required_after'][$i][$j-3]='true';
                                $rows['name'][$i][$j]=$buf;
                                $rows['type'][$i][$j]='register:'.$buf.' identifier';
                                $array_required[$req_counter]['segment_type']=$buf;
                            }
                        }
                        if($req_before == $required['required_before']){
                           if($rows['code'][$i][$j-1] == $required['required_prev'] || ($req_before=='MACRO' && empty($array_required[$req_counter]['end_pos_i']) ) ){
                                $rows['required_before'][$i][$j]='true';
                                $rows['required_after'][$required['pos_i']][$required['pos_j']]='true';
                                $array_required[$req_counter]['end_pos_i']=$i;
                            }
                        }
                        $req_counter++;
                    }
                }
                $j++;
            }
        }
        $buf_index=0;
        foreach ($array_required as $key) {
            //Проверка правильности заполнения сегмента
            // if($rows['required_lexems'][$key['pos_i']][1]=='macro name'){
            //     $p=0;
            //     foreach ($required_identifiers as $variable) {
            //         if( trim($variable['code'])==trim($rows['code'][$key['pos_i']][0]) && $rows['name'][$variable['i']][0]=='Call'){
            //             $rows['required_lexems'][$key['pos_i']][1]='true';
            //             $rows['required_next'][$variable['i']][0]='true';
            //             $rows['required_lexems'][$variable['i']][0]='true';
            //         }
            //         $p++;
            //     }
            // }
            for($i=$key['pos_i']+1;$i<$key['end_pos_i'];$i++){
                if(($key['segment_type']=='code segment' || $key['required_before']=='MACRO') && ($rows['type'][$i][2]=='const' && $rows['type'][$i][0]=='identifier' && $rows['type'][$i][1]=='directive')){
                    $rows['errors'][$i][0] = 'In '.$key['segment_type'].' is finded unexpected type of lexems on line '.$i. PHP_EOL;
                }
                elseif($key['segment_type']=='code segment' || $key['required_before']=='MACRO'){
                    foreach ($required_identifiers as $variable) {
                        if(($rows['required_lexems'][$i][0]=='met name' || $rows['required_lexems'][$i][0]='true') && substr(trim($rows['code'][$i][0]),0,strlen(trim($rows['code'][$i][0]))-1)==trim($variable['code'])){

                            if($rows['required_next'][$variable['i']][$variable['j']-3]=='met name' && $rows['name'][$variable['i']][$variable['j']-3]=='jmp' && $variable['position']=='far' && ($key['pos_i'] > $variable['i'] || $variable['i'] > $key['end_pos_i'] ) ){
                                $rows['required_lexems'][$i][0]='true';
                                $rows['required_next'][$variable['i']][$variable['j']-3]='true';
                                $rows['required_lexems'][$variable['i']][$variable['j']-3]='true';
                            }
                            elseif( $rows['required_lexems'][$variable['i']][$variable['j']-3]=='met' && ( $rows['name'][$variable['i']][$variable['j']-3]=='jc' && $variable['position']=='near' && $key['pos_i']<$variable['i'] && $variable['i'] < $key['end_pos_i']  ) ){
                                $rows['required_lexems'][$i][0]='true';
                                $rows['required_next'][$variable['i']][$variable['j']-3]='true';
                                $rows['required_lexems'][$variable['i']][$variable['j']-3]='true';
                            }
                            elseif( $rows['required_lexems'][$variable['i']][$variable['j']-1]=='met' && ($rows['name'][$variable['i']][$variable['j']-1]=='jc' && $key['pos_i']<$variable['i'] && $variable['i'] < $key['end_pos_i'] )){
                                $rows['required_lexems'][$i][0]='true';
                                $rows['required_next'][$variable['i']][$variable['j']-1]='true';
                                $rows['required_lexems'][$variable['i']][$variable['j']-1]='true';
                            }
                        }
                    }
                    if($rows['required_before'][$i][0]=='cs/macro')
                        $rows['required_before'][$i][0]='true';
                }
                elseif($key['segment_type']=='data segment' && $rows['type'][$i][2]!='const' && $rows['type'][$i][0]!='identifier' && $rows['type'][$i][1]!='directive')
                    $rows['errors'][$i][0] = 'In '.$key['segment_type'].' is finded unexpected type of lexems on line '.$i. PHP_EOL;
                elseif($key['segment_type']=='data segment'){
                    for($p=$i+1;$p<$key['end_pos_i'];$p++){
                        if(trim($rows['code'][$p][0])==trim($rows['code'][$i][0]))
                            $rows['errors'][$p][0]='Unexpected repeated declaration of a constant on line '.$p.'. "'.$rows['code'][$i][0].'" has been already declarated on line '.$i.PHP_EOL;
                    }
                    $rows['name'][$i][0]='variable';
                    foreach ($required_identifiers as $variable) {
                        if($rows['required_before'][$i][1]=='ds')
                            $rows['required_before'][$i][1]='true';
                        if(trim($variable['code'])==trim($rows['code'][$i][0]) ){
                            $rows['name'][$variable['i']][$variable['j']]='variable';
                            if($variable['j']==3){
                                $finded=explode('/',$rows['required_after'][$variable['i']][$variable['j']-3]);
                                foreach ($finded as $find) {
                                    if($find=='variable'){
                                        $rows['required_after'][$variable['i']][$variable['j']-3]='true';
                                        break;
                                    }
                                }
                            }
                            elseif($variable['j']==1){
                                $finded=explode('/',$rows['required_after'][$variable['i']][$variable['j']-1]);
                                foreach ($finded as $find) {
                                    if($find=='variable'){
                                        $rows['required_after'][$variable['i']][$variable['j']-1]='true';
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //Проверка правильности открытия и закрытия сегментов
            $buf_index++;
            for($k=$buf_index;$k<=$req_counter;$k++){
                if(substr(trim($key['required_name']),0,3)!='END' || ord($key['end_pos_i'])==0 )
                    break;
                elseif(substr(trim($array_required[$k]['required_name']),0,3)!='END' )
                    continue;
                elseif(($key['pos_i']<$array_required[$k]['pos_i'] && $array_required[$k]['pos_i']<$key['end_pos_i'] ) || ($key['pos_i']<$array_required[$k]['end_pos_i'] && $array_required[$k]['end_pos_i']<$key['end_pos_i'] ) ){
                    $rows['errors'][$key['pos_i']][0].= 'You did not close "'.$key['required_prev'].'" but started "'.$array_required[$k]['required_prev'].'" between lines '.$key['pos_i'].' and '.$key['end_pos_i']. PHP_EOL;
                }
            }
        } 
        return $rows;
    }
    function check_errors($row_number,$rows,$fn){  
        $fp = fopen($fn.date("_Y-m-d_H-i-s").'.lst', 'a');      
        $file_text='Marchecko KV-51 v11 listing';
        $detected_errors='';
        fwrite($fp, $file_text . PHP_EOL);
        $errors_counter=0;
        for($i=0;$i<=$row_number;$i++) {
            $errors='';
            $j=0;
            $file_text='';
            $errors_out='';
            while(isset($rows['code'][$i][$j]) ){
                $file_text.=$rows['code'][$i][$j].' ';
                if(strlen($rows['errors'][$i][$j])>5){
                    $errors_counter++;
                    $errors.=$rows['errors'][$i][$j];
                    $arr_err=explode(PHP_EOL,$errors);
                    foreach ($arr_err as $key) {
                        if(!empty($key))
                            $errors_out.='<pre>'.$key.'</pre>';
                    }
                }
                $j++;
            }
            if(isset($rows[$i][0]))
                fwrite($fp, $i.' : '.$file_text . PHP_EOL);
            else
                fwrite($fp, 'Find '.$errors_counter .' errors'. PHP_EOL);
            if($errors!=''){
                $file_text= 'Error near line '.$i.':'.$errors;
                $detected_errors.='Error near line '.$i.':'.$errors_out;
                fwrite($fp, $file_text . PHP_EOL);
            }
        }
        echo $detected_errors;
        fclose($fp);
        return $file_text;
    }

    function start($fn){
        $fn=substr($fn,0,-4);
        $fp = fopen($fn.'.asm', "r"); // Открываем файл в режиме чтения
        if ($fp){
            $i=0;
            while (!feof($fp)){
                $mytext = fgets($fp, 999);
                $pos=strpos($mytext,'"');//отлов строки
                if($pos===false){
                    $pos_com=strpos($mytext,',');//отлов запятых
                    if($pos_com===false){
                        $buf_arr=check_for_spaces($mytext);
                        if(!empty($buf_arr))
                            $rows[$i++]=$buf_arr;
                    }
                    else{
                        $rows[$i++]=to_array_by_pos_include_pos($mytext,$pos_com);
                    }
                }
                else{
                    $rows[$i++]=to_array_by_pos($mytext,$pos);
                }
                if(trim($mytext)=='END'){
                    $flag=true;
                    break;
                }
            }
        }
        else 
            echo "Ошибка при открытии файла";
        fclose($fp);
        echo '<button class="btn btn-primary" id="Code" href="#">Открыть код</button><button class="btn btn-success" id="Lexical" href="#">Открыть лексический анализ</button><button class="btn btn-danger" id="Errors" href="#">Открыть Блок ошибок</button>';
        echo '<div id="Assmebler code" style="display: none;">';
        $row_number=echo_code($rows);
        echo '</div>';
        $rows=lexical_analisation($rows);

        $rows=syntax_analisation($row_number,$rows);
        echo '<div id="Lexical analisation" style="display: none;">';
        $rows=table($row_number,$rows);
        echo '</div>';
        if(!$flag){
            $rows['errors'][0][0].= 'Can not find end of programm.Last lexem in the code must be "END"'. PHP_EOL;
        }
        echo '<div id="Detected errors" style="display: none;">';        
        $file_text=check_errors($row_number,$rows,$fn);
        echo '</div>';

    }


    $uploaddir = 'uploaded/';
    $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
    $fn = 'uploaded/'.$_FILES['userfile']['name'];
    echo '<pre>';
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        echo $_FILES['userfile']['name']." has been uploaded.\n";
    } else {
        echo "Attack!\n";
    }

    // echo 'Other info:';
    // print_r($_FILES);

    print "</pre>";
    start($fn);

?>
</div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        window.onload= function() {
            document.getElementById('Code').onclick = function() {
                openbox('Assmebler code', this);
                return false;
            };

            document.getElementById('Lexical').onclick = function() {
                openbox('Lexical analisation', this);
                return false;
            };

            document.getElementById('Errors').onclick = function() {
                openbox('Detected errors', this);
                return false;
            };
        };
        function openbox(id, toggler) {
            var div = document.getElementById(id);
            if(div.style.display == 'block') {
                div.style.display = 'none';
                toggler.innerHTML = 'Show '+id;
            }
            else {
                div.style.display = 'block';
                toggler.innerHTML = 'Close '+id;
            }
        }
    </script>
<script src="js/bootstrap.js"></script>
  </body>
</html>