<?php
	require "db.php";

	function create_db_tree($name,$type,$parent_name,$describtion,$max_length,$weight,$required_before,$required_prev,$required_next,$required_after,$required_lexems,$is_required){
		$point=R::dispense('dictionary');
		$point->name=$name;
		$point->type=$type;
		if($parent_name!=0){
			$point->parent_id=R::findOne('name = ?',array($parent_name))->id;
		}
		$point->describtion=$describtion;
		$point->max_length=$max_length;
		$point->weight=$weight;
		$point->required_before=$required_before;
		$point->required_prev=$required_prev;
		$point->required_next=$required_next;
		$point->required_after=$required_after;
		$point->required_lexems=$required_lexems;
		$point->is_required=$is_required;
		R::store($point);
	}

	create_db_tree('identifier','identifier',0,'identifier',4,0,0,0,0,0,0,0);
	//create_db_tree('.386','identifier',0,'First word in program',4,0,0,0,0,0,0,0,0);
	create_db_tree('macro name','identifier','identifier','identifier of macros',4,0,0,'MACRO',0,0,0,0);
		create_db_tree('MACRO','directive','macro name','macros directive',5,0,0,'macro name','parametr','ENDM',0,0);
			create_db_tree('parametr','identifier','MACRO','macros parametr',4,0,0,'MACRO',0,0,0,0);
		create_db_tree('ENDM','directive','MACRO','macros parametr',4,0,'MACRO',0,0,0,0,0);

	create_db_tree('mov','command',0,'command move',3,0,'cs/macro',0,'register/memory','register/memory/const/identifier',0,0);
	create_db_tree('adc','command',0,'command adc',3,0,'cs/macro',0,'register/memory','register/memory/const/variable',0,0);
	create_db_tree('cmp','command',0,'command compare',3,0,'cs/macro',0,'register/memory/const/variable','register/memory/const/variable',0,0);
	create_db_tree('or','command',0,'command or',2,0,'cs/macro',0,'register/memory','register/memory/const/variable',0,0);
	create_db_tree('neg','command',0,'command neg',3,0,'cs/macro',0,'register/memory',0,0,0);
	create_db_tree('dec','command',0,'command neg',3,0,'cs/macro',0,'register/memory',0,0,0);
	create_db_tree('and','command',0,'command move',3,0,'cs/macro',0,'register/memory','register/memory/const/variable',0,0);

	create_db_tree('segment name','identifier',0,'identifier of segment',4,0,0,0,'segment','ASSUME','cs/ds',0);
		create_db_tree('segment','directive',0,'starts segment',7,0,0,'segment name',0,'ENDS',0,0);
	create_db_tree('ENDS','directive',0,'ends segment',4,0,'segment','segment name',0,0,0,0);
	create_db_tree('END','directive',0,'ends program',3,0,0,0,0,0,0,0);

	create_db_tree('ASSUME','directive',0,'attaches segments',6,0,0,0,'cs:segment name/ds:segment name','cs:segment name/ds:segment name',0,0);

	create_db_tree('Undefined lexem','undefined',0,'this lexem is unexpected',255,0,0,0,0,0,0,0);
	create_db_tree('Call','command',0,'calling the macro',4,0,'cs/macro',0,'macro name',0,'macro name',0);

	//create_db_tree('const','group of consts',0,'special row for all  consts',255,0,0,0,0,0,0,0);
		create_db_tree('bin const','const','const','constant',255,0,0,0,0,0,0,0);
		create_db_tree('dec const','const','const','constant',255,0,0,0,0,0,0,0);
		create_db_tree('hex const','const','const','constant',255,0,0,0,0,0,0,0);
		create_db_tree('str const','const','const','constant',255,0,0,0,0,0,0,0);
	create_db_tree('pointer','memory',0,'adress to point in memory',255,0,0,0,0,0,0,0);


	create_db_tree('register','register',0,'register',3,0,0,0,0,0,0,0);
		create_db_tree('register32','register','register','register',3,0,0,0,0,0,0,0);
		create_db_tree('register16','register','register','register',3,0,0,0,0,0,0,0);
		create_db_tree('register08','register','register','register',3,0,0,0,0,0,0,0);
		create_db_tree('sp','register','register','stack pointer',2,0,0,0,0,0,0,0);
		create_db_tree('bp','register','register','base pointer',2,0,0,0,0,0,0,0);
		create_db_tree('si','register','register','source index',2,0,0,0,0,0,0,0);
		create_db_tree('di','register','register','destination index',2,0,0,0,0,0,0,0);
		create_db_tree('cs','register','register','code segment register',2,0,0,0,0,0,0,0);
		create_db_tree('ds','register','register','data segment register',2,0,0,0,0,0,0,0);
		create_db_tree('ss','register','register','stack segment register',2,0,0,0,0,0,0,0);
		create_db_tree('es','register','register','additional segment register',2,0,0,0,0,0,0,0);


	create_db_tree('variable','identifier',0,'user variable',4,0,0,0,0,0,0,0);
	create_db_tree(',','char',0,'one char lexem',4,0,0,0,0,0,0,0);
	create_db_tree('met','identifier',0,'point in code',4,0,'cs/macro',0,0,0,'met name',0);
	create_db_tree('jc','command',0,'jump to point in code',2,0,'cs/macro',0,'met name',0,'met',0);
	create_db_tree('jmp','command',0,'jump to point in code',3,0,'cs/macro',0,'met name',0,'met',0);
	create_db_tree('far','directive',0,'declarated jump to other segment',3,0,0,0,0,0,0,0);
	create_db_tree('near','directive',0,'declarated jump to this segment',3,0,0,0,0,0,0,0);

	
	create_db_tree('DB','directive',0,'type of data',2,1,'ds','identifier','bin const/hex constr/dec const/string const',0,0,0);
	create_db_tree('DW','directive',0,'type of data',2,2,'ds','identifier','bin const/hex constr/dec const',0,0,0);
	create_db_tree('DD','directive',0,'type of data',2,4,'ds','identifier','bin const/hex constr/dec const',0,0,0);

	// create_db('register','reg','eax,ax,ah,al,ebx,bx,bh,bl');
	// create_db('sp','reg','stack pointer','sp');
	// create_db('bp','reg','base pointer','sp');
	// create_db('si','reg','source index','si');
	// create_db('di','reg','destination index','di');
	// create_db('cs','reg','code segment register','cs');
	// create_db('ds','reg','data segment register','ds');
	// create_db('ss','reg','stack segment register','ss');
	// create_db('es','reg','additional segment register','es');
	// create_db('identifier','identifier','variable,first symbol==letter,a-zA-Z0-9,4 symbols max, uppercase==lowercase,','a-z/a-z0-9/a-z0-9/a-z0-9',4);

	// function create_db($name,$type,$describtion,$masks,$max_length){

	// }
	// create_db('mov','command','=','command/char/reg/char/reg,command/char/reg/char/var,command/char/reg/char/mem,command/char/var/char/reg,command/char/ver/char/var,command/char/var/char/mem,command/char/mem/char/reg,command/char/mem/char/var,command/char/mem/char/mem',3);
	// create_db('adc','command','hz','command/char/reg/char/reg,command/char/reg/char/var,command/char/reg/char/mem,command/char/var/char/reg,command/char/ver/char/var,command/char/var/char/mem,command/char/mem/char/reg,command/char/mem/char/var,command/char/mem/char/mem',3);
	// create_db('cmp','command','?','command/char/reg/char/reg,command/char/reg/char/var,command/char/reg/char/mem,command/char/var/char/reg,command/char/ver/char/var,command/char/var/char/mem,command/char/mem/char/reg,command/char/mem/char/var,command/char/mem/char/mem',3);
	// create_db('or','command','||','command/char/reg/char/reg,command/char/reg/char/var,command/char/reg/char/mem,command/char/var/char/reg,command/char/ver/char/var,command/char/var/char/mem,command/char/mem/char/reg,command/char/mem/char/var,command/char/mem/char/mem',3);
	// create_db('and','command','+','command/char/reg/char/reg,command/char/reg/char/var,command/char/reg/char/mem,command/char/var/char/reg,command/char/ver/char/var,command/char/var/char/mem,command/char/mem/char/reg,command/char/mem/char/var,command/char/mem/char/mem',3);
	// create_db('neg','command','+','command/char/reg,command/char/var,command/char/mem',3);
	// create_db('dec','command','+','command/char/reg,command/char/var,command/char/mem',3);

	// create_db('register','reg','eax,ax,ah,al,ebx,bx,bh,bl');
	// create_db('sp','reg','stack pointer','sp');
	// create_db('bp','reg','base pointer','sp');
	// create_db('si','reg','source index','si');
	// create_db('di','reg','destination index','di');
	// create_db('cs','reg','code segment register','cs');
	// create_db('ds','reg','data segment register','ds');
	// create_db('ss','reg','stack segment register','ss');
	// create_db('es','reg','additional segment register','es');
	// create_db('identifier','identifier','variable,first symbol==letter,a-zA-Z0-9,4 symbols max, uppercase==lowercase,','a-z/a-z0-9/a-z0-9/a-z0-9',4);

	// create_db('END','directive','','',3);

	
	// create_db('.386','directive','','',4);
	// create_db('SEGMENT','directive','','',7);
	// create_db('ENDS','directive','','',4);
	// create_db('ASSUME','directive','','',6);

	// create_db('MACRO','directive','','',5);
	// create_db('ENDM','directive','','',4);
	
	// create_db('DB','directive','','',2);
	// create_db('DW','directive','','',2);
	// create_db('DD','directive','','',2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Bootstrap 101 Template</title>
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
<form enctype="multipart/form-data" action="processing.php" style="padding-left: 20px;padding-right: 20px;" method="POST">
    <!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла -->
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    <!-- Название элемента input определяет имя в массиве $_FILES -->
    <pre>Отправить этот файл: </pre><input class="btn btn-primary" name="userfile" type="file" />
    </br>
    <input class="btn btn-primary" type="submit" value="Send File" />
</form>
    <script src="js/bootstrap.js"></script>
  </body>
</html>