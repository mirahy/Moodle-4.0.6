<?php

//global $CFG, $DB, $COURSE, $error;
global $CFG, $DB, $COURSE, $SITE, $USER, $error;
require_once('../../../../config.php');
//require_once($CFG->libdir. '/coursecatlib.php');
//require_once($CFG->libdir . '/pdflib.php');
//require_once('relaConfig.php');



$courseid      = required_param('id', PARAM_INT); 
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}

require_login($course);
$context = context_course::instance($course->id);
/*
$PAGE->set_url('/grade/report/grader/pdf/nota_pdf.php', array('id'=>$courseid));
$PAGE->set_title('Sigecad: '.$course->fullname);
$PAGE->set_heading('Sigecad: '.$course->fullname);
$PAGE->set_pagelayout('base');

 echo $OUTPUT->header();
*/






  $error=null;
    $alunosm = get_alunos_matriculados($course->id);
//print_r($alunos); 
    $alunoss = get_alunos_suspensos($course->id);
//print_r($alunoss);

    $alunos = array_diff_key($alunosm, $alunoss);

    $arrayAo = get_notas($course->id, get_itensid($course->id, '', 'category'));//66
    $arrayPf = get_notas($course->id, get_itensid($course->id, 'AF', 'mod'));//63
    $arrayPs = get_notas($course->id, get_itensid($course->id, 'AS', 'mod'));//64
    $arrayEf = get_notas($course->id, get_itensid($course->id, 'EF', 'mod'));//65
    
//print_r($arrayAo);
    $json= new stdClass();
    $json->cpfProfessor = utf8_decode ($USER->idnumber);
    $json->loginProfessor = utf8_decode ($USER->username);
    $json->nomeDisciplina =  utf8_decode ($course->fullname);
    $json->identificadorTurma = $course->idnumber;//vai vir do SIGECAD
    $json->dataExportacao = time();
    
    foreach ($alunos as $aluno) {
        //print_r($arrays);
        $listAlunoNotas = new stdClass();
        $listAlunoNotas->loginAluno = $aluno->username;
        $listAlunoNotas->cpfAluno = $aluno->idnumber;
        $listAlunoNotas->nomeAluno = utf8_decode ($aluno->firstname).' '.utf8_decode ($aluno->lastname);
        $listAlunoNotas->notaAO = $arrayAo[$aluno->id]->finalgrade;
        $listAlunoNotas->notaProvaFinal = $arrayPf[$aluno->id]->finalgrade;
        $listAlunoNotas->notaProvaSubstitutiva = $arrayPs[$aluno->id]->finalgrade;
        $listAlunoNotas->notaExameFinal = $arrayEf[$aluno->id]->finalgrade;
        $json->listAlunoNota[] =  $listAlunoNotas;
    }


if (!is_null($error)){
    $PAGE->set_url('/grade/report/grader/pdf/nota_pdf.php', array('id'=>$courseid));
    $PAGE->set_title('Sigecad: '.$course->fullname);
    $PAGE->set_heading('Sigecad: '.$course->fullname);
    $PAGE->set_pagelayout('base');

    echo $OUTPUT->header();
    echo 'A disciplina: '.$course->fullname.' não está de acordo com a RESOLUÇÃO Nº 104 DE 09 DE SETEMBRO DE 2020, é necessário ajustar os seguintes parametros:<br><br>';
    echo $error;

    echo '<br>Assista o vídeo tutorial de como ajustar os parametros:<br><br>';
    echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/Xq4ArvtJdSM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
} else{
    /*header('Content-Type: application/json');
    //header('Content-Disposition: attachment; filename='.$course->fullname.'.json');
    print_r($json);

    $json_data =  json_encode($json);
    echo  $json_data;
*/

    try
    {

       // $userdata = "SELECT fieldid, data FROM {user_info_data} WHERE userid = ?";
        $academico= '';//$DB->get_records_sql($userdata,array($id));// user

        $user = "SELECT firstname,lastname, username FROM {user} WHERE id = ?";
        $nome= $DB->get_record_sql($user,array($id));// user

        include_once 'relatorioNotas.class.php';
        
        $registro_notas = new RegistroNotas();

        $professores = get_professores($courseid);

        foreach ($professores as $professor) {
            if ($professor->roleid == 3)
                $formador .= $professor->firstname.' '.$professor->lastname.', ';
            if ($professor->roleid == 9)
                $tutor .= $professor->firstname.' '.$professor->lastname.', ';
        }

        $disciplina= new stdClass(); 
        $disciplina->faculdade = 'EAD - FACULDADE DE EDUCAÇÃO A DISTÂNCIA';//$result[4]->finalgrade;//.$result[25]->itemname; //n
        $disciplina->curso= get_categoria($course->category);////////////////////utf8_decode ($SITE->fullname);//n
        $disciplina->disciplina= $course->fullname;//pegar n
        $disciplina->professor= substr($formador, 0, -2);//pegar n
        $disciplina->tutor= substr($tutor, 0, -2);//pegar n
//echo '<pre>';

        $registro_notas->addDisciplina2($disciplina);
    
        // adiciona uma seção contendo o cabeçalho dos produtos
        $registro_notas->addCabecalhoProduto();

        foreach ($json->listAlunoNota as  $listAlunoNot){
          //  print_r($listAlunoNot);
            $count++;
            $notaObject= new stdClass();
            $notaObject->nome = $listAlunoNot->nomeAluno;
            $notaObject->AO = $listAlunoNot->notaAO;
            $notaObject->AF = $listAlunoNot->notaProvaFinal;
            $notaObject->AS = $listAlunoNot->notaProvaSubstitutiva;
            $notaObject->EF = $listAlunoNot->notaExameFinal;
            $notaObject->count = $count;
            $registro_notas->addNota($result, $notaObject, $statusObject);
        }
//die;
       // $registro_notas->addNota($result, $notaObject, $statusObject);

        $chTotal=0;
        $registro_notas->addRodapeNota($chTotal);

     $registro_notas->addRodapeNota2($chTotal);

        //addRodapeNota2($nota)
        
        // adiciona o rodapé da nota
       // $registro_notas->addRodapeAssinatura();
        //print_r($CFG);
        // salva a nota fiscal em um arquivo
        $registro_notas->gerar('Histórico-.pdf');
    
    } 
    catch (Exception $e) 
    { 
        echo $e->getMessage(); // exibe a mensagem de erro 
    }

}



function get_categoria($categoryID){
    global $DB;

     $sql = "SELECT c.path
                    FROM {course_categories} c 
                   WHERE c.id=? ";

    $path = $DB->get_record_sql($sql,array($categoryID));

    $raiz = explode('/',$path->path);

    $sql2 = "SELECT c.name
                    FROM {course_categories} c 
                   WHERE c.id=? ";

    $result = $DB->get_record_sql($sql2,array($raiz[2]));
    
    return $result->name;
  
}

function get_alunos_matriculados($courseid){
    global $DB;

     $sql = "SELECT u.id, u.username,u.idnumber, u.firstname, u.lastname
                    FROM {role_assignments} rs 
              INNER JOIN {user} u ON u.id=rs.userid INNER JOIN {context} c ON rs.contextid=c.id
                   WHERE c.contextlevel=50 AND rs.roleid=5 AND c.instanceid=?  ORDER BY u.firstname ASC";

        return $DB->get_records_sql($sql,array($courseid));
        // $alunoss = get_alunos_suspensos($courseid);

         //print_r( array_diff($alunos, $alunoss) );


   
}

function get_professores($courseid){
    global $DB;

     $sql = "SELECT u.id, u.username,u.idnumber, rs.roleid, u.firstname, u.lastname
                    FROM {role_assignments} rs 
              INNER JOIN {user} u ON u.id=rs.userid INNER JOIN {context} c ON rs.contextid=c.id
                   WHERE c.contextlevel=50 AND (rs.roleid=3 OR rs.roleid=9)AND c.instanceid=?  ORDER BY u.firstname ASC";

        return $DB->get_records_sql($sql,array($courseid));
        // $alunoss = get_alunos_suspensos($courseid);

         //print_r( array_diff($alunos, $alunoss) );


   
}
// verificar se existe PP, PS, E (com grdemax =10.000, gradetype=1)
// se AO   (aggregation='0' média das notas) AND (aggregateonlygraded='0' não descartar vazias) (droplow = '0' não remover menores notas)
function get_itensid($courseid, $idnumber, $itemtype){
    global $DB, $error;
    $sql = "SELECT i.id,i.itemname, i.idnumber, i.iteminstance, i.itemtype,i.courseid,i.grademax
              FROM  {grade_items} i 
             WHERE i.courseid= ? AND i.idnumber = ? AND i.itemtype = ? AND i.gradetype = '1' ";
        // WHERE i.courseid= ? AND i.idnumber =? AND i.itemtype = ? AND i.gradetype = '1' AND i.grademax = '10.000' ";
    $gradeitemid = $DB->get_record_sql($sql,array($courseid, $idnumber, $itemtype));
//print_r($gradeitemid);
    if($idnumber=='')
        $idnumber='AO';
    if(is_null($gradeitemid->id)){
        $error .= '<div class="alert alert-danger" role="alert">O item: '.$idnumber.' não foi encontrado, verifcar o Número de identificação do módulo(idnumber)</div>';
    }else{
        if($gradeitemid->grademax != 10.0){
            $error .= '<div class="alert alert-danger" role="alert">O item: '.$idnumber.' está com Nota máxima(grademax) diferente de 10.000</div>';
        }
    }
    if ($gradeitemid->itemtype == 'category'){
        $sqlGradeCategories = "   SELECT gc.id, gc.aggregation, gc.aggregateonlygraded, gc.droplow
                                    FROM  {grade_categories} gc 
                                   WHERE gc.courseid= ? AND gc.fullname ='AO'";
                                   // WHERE gc.courseid= ? AND gc.fullname ='AO' AND gc.aggregation='0' AND gc.aggregateonlygraded='0' AND gc.droplow = '0' ";

        $resultgradecat = $DB->get_record_sql($sqlGradeCategories,array($courseid));
        
        if(!is_null($resultgradecat->id)){
             

             if($resultgradecat->aggregation != 0){
                $error .= '<div class="alert alert-danger" role="alert">O item: '.$idnumber.' está com Forma de agregação das notas(aggregation) diferente de Média das notas</div>';
            }
             if($resultgradecat->aggregateonlygraded != 0){
                $error .= '<div class="alert alert-danger" role="alert">O item: '.$idnumber.' está com Desconsiderar notas vazias(aggregateonlygraded): selecionado</div>';
            }
             if($resultgradecat->droplow != 0){
                $error .= '<div class="alert alert-danger" role="alert">O item: '.$idnumber.' está com Descartar as menores (droplow) diferente de 0 </div>';
            }

        } else $error .= '<div class="alert alert-danger" role="alert">O item: '.$idnumber.' está com Forma de agregação das notas(aggregation) diferente de Média das notas</div>';

        if ($resultgradecat->id == $gradeitemid->iteminstance){ 
            return $gradeitemid->id;
        }
    } else return $gradeitemid->id;
            
   
}
// verificar se tem idnumber
function get_notas($courseid, $itemid){
    global $DB;
    //"SELECT u.id, u.username, u.idnumber, g.itemid,i.itemname, i.itemtype,i.courseid,g.finalgrade //Debug
    $sql = "SELECT u.id, g.itemid,g.finalgrade 
              FROM {grade_grades} g 
        INNER JOIN {grade_items} i ON g.itemid=i.id INNER JOIN {user} u ON u.id=g.userid INNER JOIN {user_enrolments} ue ON ue.userid=g.userid 
             WHERE i.courseid= ? AND i.id=? AND ue.status = '0' ORDER BY g.userid ASC";

    return $DB->get_records_sql($sql,array($courseid, $itemid));
}

function get_alunos_suspensos($courseid){
    global $DB;

     $sql = "SELECT u.id, u.username
                    FROM {user_enrolments} ue 
              INNER JOIN {user} u ON u.id=ue.userid INNER JOIN {enrol} e ON ue.enrolid=e.id
                   WHERE ue.status=1 AND e.courseid=? AND e.enrol='manual' ORDER BY u.firstname ASC";

        return $DB->get_records_sql($sql,array($courseid));
   
}
?>