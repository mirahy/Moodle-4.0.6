<?php 
  require_once('config.php');
  require_once('lib.php');

  $courseid      = required_param('courseid', PARAM_INT);

  if (!$course = $DB->get_record('course', array('id' => $courseid))) {
      print_error('nocourseid');
  }

  require_login($course);

 // if (!is_siteadmin()) { // Somente administrador do Moodle acessa essa página
   // redirect("$CFG->wwwroot/");
 // } else {
$context = context_course::instance($course->id);

    if (require_capability('mod/assign:addinstance', $context))
      header('Location: ../../user/index.php?id='.$courseid);

    //require_capability('mod/assign:addinstance', $context);

    $PAGE->set_pagelayout('incourse');

    $pagename = '<b>Notas RD e RT</b>';

    $PAGE->navbar->add($pagename);
    $PAGE->set_title($pagename);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->heading($pagename.' - '.$course->fullname, 2); 

  //  check_provao_config ($course);

estudantes($course);

/*
 //   $quizes = get_quiz ($course->id);
    $quizes = get_quizL (698,693,694,695,696,697);
  //  echo'<pre>';
//print_r($quizes);
    foreach ($quizes as $quiz) {
      
      echo '<hr  align="center" width="30%" color="green" >';
      echo '<h3 align="center">'.$quiz->name.'</h3>';
      echo '<hr  align="center" width="30%" color="green" >';

  //    check_parametros_quiz ($quiz);
    //  check_link_origin((int)$quiz->idnumber,$quiz->name); //verifica se os ids estão correspondentes com a sala origem
      if(!empty($quiz->idnumber)){
      ///  foreach ($quizes as $quiz2) {
         //check_students($quiz->idnumber,$course->id, $quiz->name);// (CursoIDorigem,CursoIDprovao,CursoNome)
       //   check_students($quiz->idnumber,$quiz2->idnumber, $quiz->name,$quiz2->name);// (CursoIDorigem,CursoIDprovao,CursoNome)
            if (!$coursess = $DB->get_record('course', array('id' => 671))) {
      print_error('nocourseid');
  }
 //check_students($quiz2->idnumber,$coursess->id, $coursess->fullname,$quiz2->name);// (CursoIDorigem,CursoIDprovao,CursoNome)
 // check_students01($coursess->id,$quiz->idnumber, $coursess->fullname,$quiz->name);// (CursoIDorigem,CursoIDprovao,CursoNome)

      // }
      /// }
      }
      
    } 
*/
//  }      
  echo $OUTPUT->footer();
?>
