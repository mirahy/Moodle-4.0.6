<?php

function estudantes($courseidOrigem) {
  GLOBAL $DB,$USER;
///*  
//echo'<pre>';
//  */
  $quizes = get_quizL (698,693,694,695,696,697);//697
///*
 //   if (!$courseidOrigem = $DB->get_record('course', array('id' => 671))) {
   //   print_error('nocourseid');
   // } else {
//*/
  $alunosOrigem = get_alunos_matriculados1($courseidOrigem->id);
//var_dump($quizes);
  foreach ($alunosOrigem as $aluno) {
    $a=0;$notas=0;
     if (!$usuario = $DB->get_record('user', array('username' => $aluno))) {
      print_error('nocourseid');
    } else {

        if($USER->id == $usuario->id)
            header('Location: ../../user/index.php?id='.$courseidOrigem->id);

    echo '<b>Aluno(a): '.$usuario->firstname.' '.$usuario->lastname.'</b><br>';   
    foreach ($quizes as $quiz) {    
      $alunosProvao =  get_aluno_matriculado($quiz->idnumber,$aluno);
      if(!empty($alunosProvao)){
        $a++;
        if (!$cursoM = $DB->get_record('course', array('id' => $quiz->idnumber))) {
      print_error('nocourseid');
    } else {
         
          $notaa = buscaNotaProvao($quiz->course,$quiz->idnumber,$aluno);
          $notas += $notaa;

          echo 'Disciplina: '.$cursoM->fullname.' - <b>Nota RD: '.number_format($notaa,1).'</b><br>';  
        }
        }
         // var_dump($notas); 
          //buscaNotaProvao()//quiz->course

      }

    }
echo 'Quantidade de disciplinas Matrículadas: ('.$a.') Soma Notas RD: ('.number_format($notas,1).')<br><b>Média RT: ('.number_format($notas/$a,1).')</b><br> <hr>';     
  }
 ///*
//  }

//echo'</pre>';
  //*/
}
function buscaNotaProvao($courseidProvao,$courseidRD,$aluno) { //
  GLOBAL $DB;

  $arrayA1 = get_notas_A($courseidProvao, get_itensid($courseidProvao, $courseidRD, 'mod'),$aluno);
  //print_r ($arrayA1);
  return (float) $arrayA1->finalgrade;
  //$arrayRDL = get_notas($coursePfIdl , get_itensid($coursePfIdl , $course->id, 'mod',null));//RD


}

// verificar se tem idnumber
function get_notas_A($courseid, $itemid, $username){
    global $DB, $error;
    //"SELECT u.id, u.username, u.idnumber, g.itemid,i.itemname, i.itemtype,i.courseid,g.finalgrade //Debug
    $sql = "SELECT u.id, g.itemid,g.finalgrade
              FROM {grade_grades} g 
        INNER JOIN {grade_items} i ON g.itemid=i.id INNER JOIN {user} u ON u.id=g.userid INNER JOIN {user_enrolments} ue ON ue.userid=g.userid 
             WHERE i.courseid= ? AND i.id=? AND ue.status = '0' AND username=? ORDER BY g.userid ASC";

    return  $DB->get_record_sql($sql,array($courseid, $itemid, $username));  
}

function get_itensid($courseid, $idnumber, $itemtype){
    global $DB, $error;

    $sql = "SELECT i.id,i.itemname, i.idnumber, i.iteminstance, i.itemtype,i.courseid,i.grademax, gc.fullname
              FROM  {grade_items} i JOIN  {grade_categories} gc ON gc.courseid=i.courseid
             WHERE i.courseid= ? AND i.idnumber = ? AND i.itemtype = ? AND i.gradetype = '1' AND (gc.id=i.categoryid OR i.iteminstance = gc.id  )";
        // WHERE i.courseid= ? AND i.idnumber =? AND i.itemtype = ? AND i.gradetype = '1' AND i.grademax = '10.000' ";
    $gradeitemid = $DB->get_record_sql($sql,array($courseid, $idnumber, $itemtype));


    if ($gradeitemid->itemtype == 'category'){
        $sqlGradeCategories = "   SELECT gc.id, gc.aggregation, gc.aggregateonlygraded, gc.droplow
                                    FROM  {grade_categories} gc 
                                   WHERE gc.courseid= ? AND gc.fullname ='AO'";
                                   // WHERE gc.courseid= ? AND gc.fullname ='AO' AND gc.aggregation='0' AND gc.aggregateonlygraded='0' AND gc.droplow = '0' ";

        $resultgradecat = $DB->get_record_sql($sqlGradeCategories,array($courseid));


        
        if ($resultgradecat->id == $gradeitemid->iteminstance){ 
            return $gradeitemid->id;
        }
        return $gradeitemid->id;
    } else return $gradeitemid->id;
            
   
}
  
  function check_students($courseidOrigem,$courseidProvao, $quizname, $quizname2) {
  
    GLOBAL $DB;

    $alunosOrigem = get_alunos_matriculados($courseidOrigem);
//print_r($alunosOrigem);
    $alunosProvao = get_alunos_matriculados($courseidProvao);


  //  get_matricula_aluno($alunoid,$courseidOrigem);

    $result = array_diff_key($alunosOrigem,$alunosProvao);

    if(!empty($result)){
        echo'<div style="color:red;">(Verificar) --- Aluno de ('.$quizname.') <b> e não Matrículado em '.$quizname2.'</b> :</div><br>';
        $cont = 0;
        foreach ($result as $students) {
          $cont++;
          echo $cont.' - '.$students->fullname.'<br>';
        }
        //print_r($result);
        echo '<hr>';
    }
  }

  function get_aluno_matriculado($courseidProvao,$alunosOrigem) {
  
    GLOBAL $DB;

     $sql = "SELECT u.username
              FROM {role_assignments} rs 
        INNER JOIN {user} u ON u.id=rs.userid INNER JOIN {context} c ON rs.contextid=c.id
             WHERE c.contextlevel=50 AND rs.roleid=5 AND c.instanceid=? AND u.username=?  ORDER BY u.firstname ASC";

    return (array) $DB->get_fieldset_sql($sql,array($courseidProvao,$alunosOrigem));

//echo'<pre>';
 //   $alunosOrigem = get_alunos_matriculados1($courseidOrigem);
//print_r($alunosOrigem);
   // $alunosProvao = (array) get_alunos_matriculados1($courseidProvao);
//return $alunosProvao;
  //  print_r($alunosProvao);


//    get_matricula_aluno($alunoid,$courseidOrigem);

    //$result = array_diff_key($alunosOrigem,$alunosProvao);

 /*   if(!empty($result)){
        echo'<div style="color:red;">(Verificar) --- Aluno de ('.$quizname.') <b> e não Matrículado em '.$quizname2.'</b> :</div><br>';
        $cont = 0;
        foreach ($result as $students) {
          $cont++;
          echo $cont.' - '.$students->fullname.'<br>';
        }
        //print_r($result);
        echo '<hr>';
    }
    */
  //  echo'</pre>';
  }


  function check_link_origin($origincurseid,$quizname) {
    global $DB;
    $percent = 0;

    if (!$origincourse = $DB->get_record('course', array('id' => $origincurseid))) {
      echo '<div style="color:orange;">(Verificar)--- O id do questionário<b>('.$quizname.')</b> do provão <b>NÃO</b> corresponde ao curso origem <b>('.$origincurseid.')</b></div>';
      echo '<hr>';
    } else {
     // $origincoursefullname = substr($origincourse->fullname, 0, -16);
      similar_text($origincourse->fullname, $quizname, $percent);
     // echo $percent;
      if ($percent<60){
        echo '<div style="color:orange;">(Verificar) --- O id do questionário<b> ('.$quizname.')</b> do provão <b>NÃO</b> corresponde ao curso origem <b>('.$origincourse->fullname.')</b></div>'; 
        echo '<hr>';
      }
    }
  }

  function check_provao_config ($course) {
    //print_r($course);
    if($course->visible != 1)
       echo '<div style="color:red;">(Verificar) --- Os parametros do PROVÃO estão diferentes dos padrões estabelecidos. <b>(O provão está OCULTO)</b></div><br>';  
    if($course->startdate != 1654488000)
       echo '<div style="color:red;">(Verificar)> --- Os parametros do PROVÃO estão diferentes dos padrões estabelecidos. <b>(Data de INÍCIO da sala provão)</b></div><br>';  
    if($course->enddate != 1655697540)
       echo '<div style="color:red;">(Verificar) --- Os parametros do PROVÃO estão diferentes dos padrões estabelecidos. <b>(Data de FECHAMENTO da sala provão)</b></div><br>';
  }

  function get_alunos_matriculados($courseid){
    global $DB;

    $sql = "SELECT u.username, CONCAT( u.firstname,' ', u.lastname) as Fullname
              FROM {role_assignments} rs 
        INNER JOIN {user} u ON u.id=rs.userid INNER JOIN {context} c ON rs.contextid=c.id
             WHERE c.contextlevel=50 AND rs.roleid=5 AND c.instanceid=?  ORDER BY u.firstname ASC";

    return $DB->get_records_sql($sql,array($courseid));
  }

   function get_alunos_matriculados1($courseid){
    global $DB;

    $sql = "SELECT u.username
              FROM {role_assignments} rs 
        INNER JOIN {user} u ON u.id=rs.userid INNER JOIN {context} c ON rs.contextid=c.id
             WHERE c.contextlevel=50 AND rs.roleid=5 AND c.instanceid=?  ORDER BY u.firstname ASC";

    return (array) $DB->get_fieldset_sql($sql,array($courseid));
  }

   function get_matricula_aluno($alunoid,$courseid){
    global $DB;

    $sql = "SELECT u.id, CONCAT( u.firstname,' ', u.lastname) as Fullname
              FROM {role_assignments} rs 
        INNER JOIN {user} u ON u.id=rs.userid INNER JOIN {context} c ON rs.contextid=c.id
             WHERE c.contextlevel=50 AND rs.roleid=5 AND c.instanceid=? AND u.id=?  ORDER BY u.firstname ASC";

    return $DB->get_records_sql($sql,array($courseid,$alunoid));
  }

   function check_parametros_quiz ($quiz) {
  // print_r($quiz);
    $mdl_quiz = array('timeclose' => 1655006340, 'timeopen' => 1654488000, 'timelimit' => '3600','overduehandling' => 'autosubmit','graceperiod' => '0','preferredbehaviour' => 'deferredfeedback','attempts' => '1','attemptonlast' => '0','grademethod' =>'1','decimalpoints' => '1','questiondecimalpoints' => '-1','reviewattempt' => '65552','reviewcorrectness' => '16','reviewmarks' => '16','reviewspecificfeedback' => '16','reviewgeneralfeedback' => '16','reviewrightanswer' => '16','reviewoverallfeedback' => '16','questionsperpage' => '1','navmethod' => 'free', 'shuffleanswers' => '1','sumgrades' => '5.00000','grade' => '10.00000','password' => '','subnet' => '','browsersecurity' => '-','delay1' => '0','delay2' => '0','showuserpicture' => '0','showblocks' => '0') ;

    $mdl_quiz_help = array('timeclose' => 'Data de Fechamento', 'timeopen' => 'Data de Abertura','timelimit' => 'Limite de tempo <> 60 minutos','overduehandling' => 'Quando o tempo expirar <> a tentativa é enviada automaticamente ','graceperiod' => 'Período de carência de envio Ativado','preferredbehaviour' => 'Como se comporta as questões <> Feedback adiado','attempts' => 'Tentativas permitidas <> 1','attemptonlast' => 'Cada tentativa se baseia na última <> Não','grademethod' => 'Método de avaliação <> Nota mais alta','decimalpoints' => 'Casas decimais nas avaliações <> 2','questiondecimalpoints' => 'Casas decimais nas avaliações da pergunta <> O mesmo que para as avaliações em geral','reviewattempt' => 'Depois do fechamento do questionário "todos selecionados"','reviewcorrectness' => 'Depois do fechamento do questionário "todos selecionados"','reviewmarks' => 'Mais tarde enquanto ainda estiver aberto "Notas"','reviewspecificfeedback' => 'Depois do fechamento do questionário "todos selecionados"','reviewgeneralfeedback' => 'Depois do fechamento do questionário "todos selecionados"','reviewrightanswer' => 'Depois do fechamento do questionário "todos selecionados"','reviewoverallfeedback' => 'Depois do fechamento do questionário "todos selecionados"','questionsperpage' => 'Nova página <> Cada pergunta','navmethod' => 'Método de navegaçãoElemento Avançado <> Livre', 'shuffleanswers' => 'Misturar entre as questões <> Sim','password' => 'Está com Senha','subnet' => 'Requer endereço de rede','browsersecurity' => 'Segurança do navegador <> Nenhum','delay1' => 'Força demora entre a primeira e a segunda tentativas <> 0','delay2' => 'Força demora entre tentativas posteriores <> 0','showuserpicture' => 'Mostrar a fotografia do usuário <> Nenhuma imagem','showblocks' => 'Mostrar blocos durante as tentativas do questionário <> Não');
    
      $result =   array_diff_assoc((array)$mdl_quiz,(array)$quiz); // Verifica se os parametros estão correspondentes com o "padrão"

      $resultHelp =  array_intersect_key((array)$mdl_quiz_help,(array)$result); // Insere a descrição nos parametros inconsistentes

      if(!empty($resultHelp)){
        echo '<div style="color:brown;">(Verificar) --- Os parametros do questionário <b>'.$quiz->name.'</b> estão diferentes dos padrões estabelecidos.</div><br><pre>';
        print_r($resultHelp);
        echo '</pre><hr>';
      }
  }


   function get_quiz ($course) {
    GLOBAL $DB;
     
    $sql = "SELECT  q.*, cm.id AS moduleid, cm.idnumber, cm.section, cm.instance
                        FROM {quiz} q INNER JOIN  {course_modules} cm ON cm.instance = q.id 
                       WHERE q.course= ? AND cm.module = 16  ORDER BY q.timeopen ASC";

    $quizes = $DB->get_records_sql($sql,array($course));
  
    return array_values($quizes);
  }

     function get_quizL ($course,$course1,$course2,$course3,$course4,$course5) {
    GLOBAL $DB;
     
    $sql = "SELECT  q.*, cm.id AS moduleid, cm.idnumber, cm.section, cm.instance
                        FROM {quiz} q INNER JOIN  {course_modules} cm ON cm.instance = q.id 
                       WHERE (q.course= ? OR q.course= ? OR q.course= ? OR q.course= ? OR q.course= ? OR q.course= ?) AND cm.module = 16  ORDER BY q.timeopen ASC";

    $quizes = $DB->get_records_sql($sql,array($course,$course1,$course2,$course3,$course4,$course5));
  
    return array_values($quizes);
  }

?>
