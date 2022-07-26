<?php

global $CFG, $DB;
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
$CATIDUAB = 3;   

try
{
    $id_venda=1;
    include_once 'tutores.class.php';
    
    $registro_notas = new RegistroNotas($id_venda);

     $sqlCursos = "SELECT u.id as courseid, u.fullname, u.category, c.path
                    FROM {course} u 
                    INNER JOIN {course_categories} c ON u.category=c.id 
                   WHERE u.startdate <= ? AND u.enddate >=?  ORDER BY u.startdate ASC";
//AND u.enddate >=?

    $sqlLogs = "SELECT l.id, l.userid, l.courseid, l.timecreated, u.firstname, u.lastname
                    FROM {logstore_standard_log} l
                    INNER JOIN {user} u ON u.id = l.userid
                    WHERE  l.courseid = ?
                    AND l.userid = ?
                    ORDER BY l.id DESC LIMIT 1";

    $sqlTutores = "SELECT u.id, u.firstname,u.lastname, u.username, rs.roleid, u.firstname, u.lastname
                    FROM {role_assignments} rs 
              INNER JOIN {user} u ON u.id=rs.userid INNER JOIN {context} c ON rs.contextid=c.id 
                   WHERE c.contextlevel=50 AND (rs.roleid=$TUTORAD or rs.roleid=$FORMADOR) AND c.instanceid=?";

$data = time();
//print_r($data[0]);
//die();
    $cursos = $DB->get_records_sql($sqlCursos,array($data,$data));
  //  $cursos = $DB->get_records_sql($sqlCursos,array($data));
    $cursos = array_values($cursos);


    $disciplina= new stdClass(); 
    $disciplina->faculdade = 'EAD - FACULDADE DE EDUCAÇÃO A DISTÂNCIA';

    $registro_notas->addDisciplina($disciplina);
    $registro_notas->addCabecalhoProduto();
  //  echo'<pre>';
   // print_r($cursos);

    foreach ($cursos as $curso){ 
     //   $categorias = explode('/', $curso->path);
       
        //if ($categorias[1] == 3 AND ($categorias[2] != 7 OR $categorias[2] != 6 OR $categorias[2] != 144)){

            $students = $DB->get_records_sql($sqlTutores,array($curso->courseid));

            foreach ($students as $student){// passa por tutor dentro do curso
                $logs = $DB->get_record_sql($sqlLogs,array( $curso->courseid, $student->id));
                if($student) {
                    $notaObject = new stdClass();
                    $notaObject->disciplina = utf8_decode ($curso->fullname);
                    $notaObject->firstname = utf8_decode ($student->firstname);
                    $notaObject->lastname = utf8_decode ($student->lastname);
                    $notaObject->timecreated = $logs->timecreated ? $logs->timecreated : 0;
                    $notaObject->roleid = $student->roleid;
                 //   print_r($notaObject);
                    $registro_notas->addNota($notaObject, $falta);
                }
            } 
        }
 //   }
    
    // adiciona a seo contendo o rodap dos produtos
    $registro_notas->addRodapeNota();
    
    // adiciona o rodap da nota
   // $registro_notas->addRodapeAssinatura();
    
    // salva a nota fiscal em um arquivo
    $registro_notas->gerar('RelatorioAcesso'.$courseid.'.pdf');
    

} 
catch (Exception $e) 
{ 
    echo $e->getMessage(); // exibe a mensagem de erro 
}

?>