<?php

global  $DB, $USER, $error;
require_once('../../../../config.php');


$courseid      = required_param('id', PARAM_INT); 
//$userid      = required_param('userid', PARAM_INT); 
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}

require_login($course);
$context = context_course::instance($course->id);


    

  $error=null;

    include_once 'relatorioNotas90.class.php';
        
    $registro_notas = new RegistroNotas();


    $professores = get_professores($courseid);

    $disciplina= new stdClass(); 
    $disciplina->faculdade = 'EAD - FACULDADE DE EDUCAÇÃO A DISTÂNCIA';
    $disciplina->curso = get_categoria($course->category);
    $disciplina->disciplina= $course->fullname;
    $disciplina->professor = strtoupper($professores->formadores);
    $disciplina->tutor = strtoupper($professores->tutores);


    $registro_notas->addCabecalhoDisciplina($disciplina);

    $registro_notas->addCabecalhoNotas();


    $arrayAo = get_notas($course->id, get_itensid($course->id, '', 'category'));
    $arrayPf = get_notas($course->id, get_itensid($course->id, 'AF', 'mod'));
    $arrayPs = get_notas($course->id, get_itensid($course->id, 'AS', 'mod'));
    $arrayEf = get_notas($course->id, get_itensid($course->id, 'EF', 'mod'));

    $arrayA1 = get_notas($course->id, get_itensid($course->id, 'A1', 'mod'));
    $arrayA2 = get_notas($course->id, get_itensid($course->id, 'A2', 'mod'));
    $arrayA3 = get_notas($course->id, get_itensid($course->id, 'A3', 'mod'));
    $arrayA4 = get_notas($course->id, get_itensid($course->id, 'A4', 'mod'));

    $arrayA5 = get_notas($course->id, get_itensid($course->id, 'A5', 'mod'));
    $arrayA6 = get_notas($course->id, get_itensid($course->id, 'A6', 'mod'));


    $alunosm = get_matriculados($course->id, 5);
    $alunoss = get_alunos_suspensos($course->id);
    $alunos = array_diff_key($alunosm, $alunoss);


    $c=0;
    foreach ($alunos as $aluno) {
        $count++;

        if($USER->id == $aluno->id)
            header('Location: ../../user/index.php?id='.$courseid);
        $listAlunoNotas = new stdClass();

        $listAlunoNotas->nome = utf8_decode (strtoupper($aluno->firstname)).' '.utf8_decode (strtoupper($aluno->lastname));
        $listAlunoNotas->AO = $arrayAo[$aluno->id]->finalgrade;
        $listAlunoNotas->AF = $arrayPf[$aluno->id]->finalgrade;
        $listAlunoNotas->AS = $arrayPs[$aluno->id]->finalgrade;
        $listAlunoNotas->EF = $arrayEf[$aluno->id]->finalgrade;

        $listAlunoNotas->A1 = $arrayA1[$aluno->id]->finalgrade;
        $listAlunoNotas->A2 = $arrayA2[$aluno->id]->finalgrade;
        $listAlunoNotas->A3 = $arrayA3[$aluno->id]->finalgrade;
        $listAlunoNotas->A4 = $arrayA4[$aluno->id]->finalgrade;

        $listAlunoNotas->A5 = $arrayA5[$aluno->id]->finalgrade;
        $listAlunoNotas->A6 = $arrayA6[$aluno->id]->finalgrade;

        $somaNotas = ($listAlunoNotas->A1 + $listAlunoNotas->A2 + $listAlunoNotas->A3 + $listAlunoNotas->A4 + $listAlunoNotas->A5 + $listAlunoNotas->A6)/6;

       // if ((number_format($somaNotas, 2) != number_format($listAlunoNotas->AO,2)) AND $c == 0){
        if ((round($somaNotas, 1) != round($listAlunoNotas->AO,1)) AND $c == 0){
             $error .= '<div class="alert alert-danger" role="alert">'.round($somaNotas, 1).' A '.round($listAlunoNotas->AO,1).'média AO está divergente do padrão de 6 atividades, confira o relatório de notas</b>;</div>';
             $c++;
         }
        $listAlunoNotas->count = $count;

        $registro_notas->addNota($result, $listAlunoNotas, $statusObject);

    }


if (!is_null($error)){
    $PAGE->set_url('/grade/report/grader/pdf/nota_pdf.php', array('id'=>$courseid));
    $PAGE->set_title('Sigecad: '.$course->fullname);
    $PAGE->set_heading('Sigecad: '.$course->fullname);
    $PAGE->set_pagelayout('standard');

    echo $OUTPUT->header();
    //echo 'A disciplina: <b>'.$course->fullname.'</b> não está de acordo com a <b>RESOLUÇÃO Nº 104 DE 09 DE SETEMBRO DE 2020</b>, é necessário ajustar os seguintes parametros:<br><br>';
    
    //echo $error;

    //echo '<br>Assista o vídeo tutorial de como ajustar os parametros:<br><br>';
   // echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/Xq4ArvtJdSM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
     echo 'A disciplina: <b>'.$course->fullname.'</b> não está com as atividades configuradas, é necessário ajustar os seguintes parametros:<br><br>';
    
    echo $error;
} else{
   
    $registro_notas->addRodapeNota();

    // salva a nota em um arquivo
    $registro_notas->gerar('Nota-'.$courseid.'.pdf');

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


// verificar se existe PP, PS, E (com grdemax =10.000, gradetype=1)
// se AO   (aggregation='0' média das notas) AND (aggregateonlygraded='0' não descartar vazias) (droplow = '0' não remover menores notas)
function get_itensid($courseid, $idnumber, $itemtype){
    global $DB, $error;

    $sql = "SELECT i.id,i.itemname, i.idnumber, i.iteminstance, i.itemtype,i.courseid,i.grademax, gc.fullname
              FROM  {grade_items} i JOIN  {grade_categories} gc ON gc.courseid=i.courseid
             WHERE i.courseid= ? AND i.idnumber = ? AND i.itemtype = ? AND i.gradetype = '1' AND (gc.id=i.categoryid OR i.iteminstance = gc.id  )";
        // WHERE i.courseid= ? AND i.idnumber =? AND i.itemtype = ? AND i.gradetype = '1' AND i.grademax = '10.000' ";
    $gradeitemid = $DB->get_record_sql($sql,array($courseid, $idnumber, $itemtype));


    if($idnumber=='')
        $idnumber='AO';
    if(is_null($gradeitemid->id) AND $idnumber!='AO' ){
         $error .= '<a href="https://docs.google.com/document/d/1oX0qnYwaBJEEIIeBoozSzHsd22DsdxntlAe6B968z6I/edit" target="_blank"><div class="alert alert-danger" role="alert">A atividade: '.$idnumber.' não foi encontrada, verifique o <b>"Número de identificação do módulo"</b> que deve ser: <b>'.$idnumber.'</b> - [ <b>Clique aqui para ver o Tutorial de correção!</b> ]</div></a>';//(idnumber)
    }else{
        if($gradeitemid->grademax != 10.0 AND $idnumber!='AO'){
            $error .= '<a href="https://docs.google.com/document/d/1RQygCyiIQ33Td8ZiVXJsakfLpnQRVSbwGklbXU1b2oI/edit" target="_blank"><div class="alert alert-danger" role="alert">A atividade: '.$idnumber.' está com <b>"Nota máxima"</b> diferente de <b>10.00</b> - [ <b>Clique aqui para ver o Tutorial de correção!</b> ]</div>';//(grademax)
        }

         if($gradeitemid->fullname != 'AO' AND $idnumber!='AF'AND $idnumber!='AS'AND $idnumber!='EF' AND $idnumber!='AO' ){
                $error .= '<a href="https://docs.google.com/document/d/1jzO-B0O3YOLJfJldib2SBGDo8Za1spPicKdGJWgPUGQ/edit" target="_blank"><div class="alert alert-danger" role="alert">A atividade: '.$idnumber.', não está na categoria <b>AO</b> - [ <b>Clique aqui para ver o Tutorial de correção!</b> ]</div>';
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
                $error .= '<a href="https://docs.google.com/document/d/1-SGuFCibCy0bkiODEeW2RyrSihQebK9_DoZGD8z8NUU/edit" target="_blank"><div class="alert alert-danger" role="alert">A categoria: '.$idnumber.' está com <b>"Forma de agregação das notas"</b> diferente de <b>Média das notas</b> - [ <b>Clique aqui para ver o Tutorial de correção!</b> ]</div>';//(aggregation)
            }
             if($resultgradecat->aggregateonlygraded != 0){
                $error .= '<a href="https://docs.google.com/document/d/1Mc0MLNypqtX14dHtGLvVpVxJEtOgrx_67HiYXhS6pZY/edit" target="_blank"><div class="alert alert-danger" role="alert">A categoria: '.$idnumber.' está com o checkbox <b>"Desconsiderar notas vazias"</b> diferente de <b>desmarcado</b> - [ <b>Clique aqui para ver o Tutorial de correção!</b> ]</div>';//(aggregateonlygraded)
            }
             if($resultgradecat->droplow != 0){
                $error .= '<a href="https://docs.google.com/document/d/1WMLzJlVP3rJ6gwUPG60-GK43QsdWJAOmZW4dkKXqTv8/edit" target="_blank"><div class="alert alert-danger" role="alert">A categoria: '.$idnumber.' está com <b>"Descartar as menores" </b> diferente de <b>0</b> - [ <b>Clique aqui para ver o Tutorial de correção!</b> ]</div>';//(droplow)
            }

        } else $error .= '<a href="" target="_blank"><div class="alert alert-danger" role="alert">A categoria: '.$idnumber.' não foi encontrada - [ <b>Clique aqui para ver o Tutorial de correção!</b> ]</div>';

        
        if ($resultgradecat->id == $gradeitemid->iteminstance){ 
            return $gradeitemid->id;
        }
    } else return $gradeitemid->id;
            
   
}
// verificar se tem idnumber
function get_notas($courseid, $itemid){
    global $DB, $error;
    //"SELECT u.id, u.username, u.idnumber, g.itemid,i.itemname, i.itemtype,i.courseid,g.finalgrade //Debug
    $sql = "SELECT u.id, g.itemid,g.finalgrade
              FROM {grade_grades} g 
        INNER JOIN {grade_items} i ON g.itemid=i.id INNER JOIN {user} u ON u.id=g.userid INNER JOIN {user_enrolments} ue ON ue.userid=g.userid 
             WHERE i.courseid= ? AND i.id=? AND ue.status = '0' ORDER BY g.userid ASC";

    return  $DB->get_records_sql($sql,array($courseid, $itemid));  
}

function get_professores($courseid){

    $professores = get_matriculados($courseid, 3);

    foreach ($professores as $professor) {
        $formador .= $professor->firstname.' '.$professor->lastname.', ';
    }

    $formador = substr($formador, 0, -2);

    $tutores = get_matriculados($courseid, 1);//1

    foreach ($tutores as $tutorA) {
        $tutor .= $tutorA->firstname.' '.$tutorA->lastname.', ';
    }
    $tutor = substr($tutor, 0, -2);

    $professores = new stdClass();
    $professores->formadores = $formador;
    $professores->tutores = $tutor;

    return $professores;
}

function get_matriculados($courseid, $roleid){
    global $DB;

     $sql = "SELECT u.id, u.username,u.idnumber, u.firstname, u.lastname, rs.roleid
               FROM {role_assignments} rs 
         INNER JOIN {user} u ON u.id=rs.userid INNER JOIN {context} c ON rs.contextid=c.id
              WHERE c.contextlevel=50 AND c.instanceid=? AND rs.roleid=? ORDER BY u.firstname ASC";

    return $DB->get_records_sql($sql,array($courseid,$roleid));
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