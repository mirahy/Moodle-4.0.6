<?php


global $CFG, $DB, $COURSE;
require_once('../../../../config.php');
require_once($CFG->libdir. '/coursecatlib.php');

$courseid      = required_param('id', PARAM_INT); 
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}
require_login($course);
$context = context_course::instance($course->id);




     $sql2 = "SELECT i.id,i.itemname, i.idnumber, i.iteminstance, i.itemtype,i.courseid
              FROM  {grade_items} i 
             WHERE i.courseid= ? AND (i.idnumber ='PF' OR i.idnumber ='PS'OR i.idnumber ='E' OR i.itemtype = 'category' ) ORDER BY i.itemname ASC";

           $results2 = $DB->get_records_sql($sql2,array($course->id));


            
foreach ($results2 as $gradeitemid) {  // Verifica e inicia as variaveis fazer função 
    switch ($gradeitemid->idnumber) {
        case 'PF':
            $itemid_prova_final = $gradeitemid->id;
            break;
        
        case 'PS':
            $itemid_prova_substitutiva = $gradeitemid->id;
            break;
        case 'E':
            $itemid_exame_final = $gradeitemid->id;
            break;

        default:
            $sqlgradecat = " SELECT gc.id
                        FROM  {grade_categories} gc 
                       WHERE gc.courseid= ? AND gc.fullname ='AO' AND gc.aggregation='0' AND gc.aggregateonlygraded='0' ";
            $resultgradecat = $DB->get_record_sql($sqlgradecat,array($course->id));
            if ($resultgradecat->id == $gradeitemid->iteminstance){ 
                $itemid_AO = $gradeitemid->id;
            }
            break;
    }
}


$arrayAo = get_notas($course->id, $itemid_AO);
$arrayPf = get_notas($course->id, $itemid_prova_final);
$arrayPs = get_notas($course->id, $itemid_prova_substitutiva);
$arrayEf = get_notas($course->id, $itemid_exame_final);

    $json= new stdClass();
    $json->cpfProfessor = $USER->idnumber;
    $json->loginProfessor = $USER->username;
    $json->nomeDisciplina =  $course->fullname;
    $json->identificadorTurma = $course->idnumber;//vai vir do SIGECAD
    $json->dataExportacao = time();
    
    foreach ($arrayAo as $arrays) {
        $listAlunoNotas = new stdClass();
        $listAlunoNotas->loginAluno = $arrays->username;
        $listAlunoNotas->cpfAluno = $arrays->idnumber;
        $listAlunoNotas->notaAO = $arrayAo[$arrays->username]->finalgrade;
        $listAlunoNotas->notaProvaFinal = $arrayPf[$arrays->username]->finalgrade;
        $listAlunoNotas->notaProvaSubstitutiva = $arrayPs[$arrays->username]->finalgrade;
        $listAlunoNotas->notaExameFinal = $arrays->finalgrade;
        $json->listAlunoNota[] =  $listAlunoNotas;
    }
echo '<pre>';
    print_r($json);
    echo '<br>JSON:<br>';

   $json_data =  json_encode($json);

 //header('Content-Type: application/json');
 //header('Content-Disposition: attachment; filename=myfile.json');
   echo  $json_data;
//echo '<br><br>';
  // $json_datas =  json_decode($json_data);
//print_r($json_datas);
  
  


function get_notas($courseid, $itemid){
    global $DB;
    $sql = "SELECT u.username, u.idnumber, g.itemid,i.itemname, i.itemtype,i.courseid,g.finalgrade 
              FROM {grade_grades} g 
        INNER JOIN {grade_items} i ON g.itemid=i.id INNER JOIN {user} u ON u.id=g.userid 
             WHERE i.courseid= ? AND i.id=?  ORDER BY g.userid ASC";
    if(is_null ($itemid) == 0)
        return $DB->get_records_sql($sql,array($courseid, $itemid));
   // else
     //   return 'Está faltando';
    
}

?>