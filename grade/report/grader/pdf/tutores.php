<?php

global  $DB;
require_once('../../../../config.php');

$categoria = required_param('id', PARAM_INT); 
$courseid = required_param('courseid', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}
require_login($course);
$context = context_course::instance($course->id);

$FORMADOR = 3;
$TUTORAD  = 9;

try
{
    include_once 'tutores.class.php';
    
    $registro_notas = new RegistroNotas();


    $sqlUnif =  "SELECT rs.id, u.firstname, u.lastname, u.id as userid, cu.fullname, rs.roleid, cu.id as courseid, cu.category
                   FROM {role_assignments} rs 
             INNER JOIN {user} u ON u.id=rs.userid INNER JOIN {context} c ON rs.contextid=c.id INNER JOIN {course} cu ON c.instanceid=cu.id 
                  WHERE c.contextlevel=50 AND (rs.roleid=$TUTORAD or rs.roleid=$FORMADOR) AND (cu.startdate <= ? AND cu.enddate >=?) AND cu.category =? ORDER BY cu.fullname ASC";//  ORDER BY l.id DESC LIMIT 1";
    
    $sqllog = "SELECT l.id, l.timecreated 
                  FROM {logstore_standard_log} l 
                 WHERE l.userid = ? AND l.courseid = ? ORDER BY l.id DESC LIMIT 1";


    $data = time();

    $cursos = $DB->get_records_sql($sqlUnif,array($data,$data,$categoria));

    $cursos = array_values($cursos);

    $disciplina= new stdClass(); 
    $disciplina->faculdade = 'EAD - FACULDADE DE EDUCAÇÃO A DISTÂNCIA';

    $registro_notas->addDisciplina($disciplina);
    $registro_notas->addCabecalhoProduto();

    foreach ($cursos as $curso){
        if ($curso->category == $categoria){
  
            $log = $DB->get_record_sql($sqllog, array($curso->userid, $curso->courseid));

            $notaObject = new stdClass();
            $notaObject->disciplina = utf8_decode ($curso->fullname);
            $notaObject->firstname = utf8_decode ($curso->firstname);
            $notaObject->lastname = utf8_decode ($curso->lastname);
            $notaObject->timecreated = $log->timecreated ? $log->timecreated : 0;
            $notaObject->roleid = $curso->roleid;
            $registro_notas->addNota($notaObject, $falta);
        }
    }

    $registro_notas->addRodapeNota();
    
   // $registro_notas->addRodapeAssinatura();
    
    $registro_notas->gerar('RelatorioAcesso'.$courseid.'.pdf');
    
} 
catch (Exception $e) 
{ 
    echo $e->getMessage();
}

?>