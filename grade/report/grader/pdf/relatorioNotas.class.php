<?php
/**
 * Classe Registro de notas
 * Encapsula o Relaatório de notas em PDF
 */
class RegistroNotas
{
    private $pdf;            // objeto PDF
    private $produtos;       // Vetor de Produtos
    private $total_produtos; // Valor total de produtos
    private $count_produtos; // Quantidade de produtos

    /**
     * Método construtor
     * Instancia o objeto FPDF
     * @param $numero numero da nota fiscal
     * @param $data data de emissão
     */
    public function __construct()
    {
        // Define o diretório das fontes
        define('FPDF_FONTPATH', getcwd() . '/app.util/pdf/font/');
        
        // Carrega a biblioteca FPDF
        include_once 'app.util/pdf/fpdf.php';
        
        // Cria um novo documento PDF
        $this->pdf = new FPDF('P', 'pt');//L||p
        $this->pdf->SetMargins(30,30,30); // define margens


       
        // Adiciona uma página
        $this->pdf->AddPage();
        $this->pdf->Ln();
        
        // acrescenta a imagem de logo nestas coordenadas
        $image  = 'header_logo.gif';
        $this->pdf->Image($image, 25, 20, 50);
       
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetFont('Arial','',6);
        $this->pdf->SetXY(80,30);
        $this->pdf->Cell(70, 6, 'Ministério da Educação -', 0, 0, 'L');
        $this->pdf->SetFont('Arial','B',6);
        $this->pdf->Cell(50, 6, 'Universidade Federal da Grande Dourados', 0, 0, 'L');
        $this->pdf->SetXY(80,40);
        $this->pdf->SetFont('Arial','',6);
        $this->pdf->Cell(100, 6, 'PROGRAD - Pró-Reitoria de Ensino de Graduação', 0, 0, 'L');
        $this->pdf->SetXY(80,50);
        $this->pdf->Cell(100, 6, 'EAD - Faculdade de Educação a Distância', 0, 0, 'L');
        
        $this->pdf->SetAutoPageBreak(true,30); 
        // inicializa variáveis
        $this->total_produtos = 0;
        $this->count_produtos = 0;

 

    }

    /**
     * Método addCliente
     * Adiciona um cliente na nota
     * @param $cliente Objeto contendo os atributos do cliente
     */
    public function addDisciplina($disciplina, $academico, $nome)//cabeçalho turma
    {
        $this->pdf->SetY(80);
        
        // exibe o título da seção
        $this->pdf->SetFont('Arial','B',8);
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 12, 'Histórico Escolar - ', 0, 0, 'C');
        $this->pdf->SetFont('Arial','',8);

        $this->pdf->Ln(16);

        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, '', 'LTR', 0, 'C');
        $this->pdf->Ln(12);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 10, " Faculdade: ".utf8_decode ($disciplina->faculdade), 'LR', 0, 'L');
        $this->pdf->Ln(10);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 10, ' Curso: '.$disciplina->curso.'        Polo: '.$disciplina->polo, 'LR', 0, 'L');
        $this->pdf->Ln(10);

        //$this->pdf->SetX(20);
      //  $this->pdf->Cell(550, 10, ' Reconhecimento: '.'PORTARIA SERES/MEC nº286 de 21/12/2012 - D.O.U. nº249 de 27/12/2012, p. 13', 'LR', 0, 'L');
        //$this->pdf->Ln(10);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 10, ' RGA: '.utf8_decode ($academico[2]->data).'       Acadêmico: '.utf8_decode ($nome->firstname).' '.utf8_decode ($nome->lastname), 'LR', 0, 'L');
        $this->pdf->Ln(10);



        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, '', 'LBR', 0, 'C');
        $this->pdf->Ln(12);


        //*--* Dados Ingresso

        $this->pdf->Ln(16);

        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, '', 'LTR', 0, 'C');

        $this->pdf->Ln(12);

        $this->pdf->SetX(20);
        $this->pdf->SetFont('Arial','B',8);
        $this->pdf->Cell(550, 10, " Dados do Ingresso ", 'LR', 0, 'L');
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->Ln(8);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, '', 'LR', 0, 'C');

        $this->pdf->Ln(8);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 10, " Ingresso: ".utf8_decode ($academico[25]->data)."      Ano de Ingresso: ".utf8_decode ($academico[26]->data)."      Semestre: ".utf8_decode ($academico[27]->data), 'LR', 0, 'L');
        $this->pdf->Ln(10);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 10, ' Vestibular: '.utf8_decode ($academico[28]->data).'       Pontuação: '.utf8_decode ($academico[29]->data), 'LR', 0, 'L');
        $this->pdf->Ln(10);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 10, ' Disciplinas: '.utf8_decode ($academico[24]->data), 'LR', 0, 'L');//.$disciplina->periodo, 'LR', 0, 'L');//****************
        $this->pdf->Ln(10);

   
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, '', 'LBR', 0, 'C');
        $this->pdf->Ln(12);

//*--* Dados Pessoais

        $this->pdf->Ln(16);

        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, '', 'LTR', 0, 'C');

        $this->pdf->Ln(12);

        $this->pdf->SetX(20);
        $this->pdf->SetFont('Arial','B',8);
        $this->pdf->Cell(550, 10, " Dados do Pessoais ", 'LR', 0, 'L');
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->Ln(8);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, '', 'LR', 0, 'C');

        $this->pdf->Ln(8);

        $this->pdf->SetX(20); 
        $this->pdf->Cell(550, 10, " Estado Civil: ".utf8_decode ($academico[15]->data)."      Sexo: ".utf8_decode ($academico[6]->data)."      Nascimento: ".utf8_decode ($academico[3]->data), 'LR', 0, 'L');
        $this->pdf->Ln(10);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 10, ' Naturalidade:    '.utf8_decode ($academico[8]->data).'            Nascionalidade: '.utf8_decode ($academico[7]->data), 'LR', 0, 'L');
        $this->pdf->Ln(10);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 10, ' Filiação(PAI): '.utf8_decode ($academico[4]->data).'         Filiação(MÃE): '.utf8_decode ($academico[5]->data), 'LR', 0, 'L');
        
        $this->pdf->Ln(10);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 10, ' Identidade: '.utf8_decode ($academico[9]->data).' '.utf8_decode ($academico[10]->data).'/'.utf8_decode ($academico[11]->data).'        Doc. Militar: '.utf8_decode ($academico[12]->data), 'LR', 0, 'L');
        $this->pdf->Ln(10);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 10, ' Título de Eleitor: '.utf8_decode ($academico[13]->data).'           Estado: '.utf8_decode ($academico[14]->data), 'LR', 0, 'L');
        $this->pdf->Ln(10);

   
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, '', 'LBR', 0, 'C');
        $this->pdf->Ln(12);

        //*--* Conclusão ensino médio

        $this->pdf->Ln(16);

        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, '', 'LTR', 0, 'C');

        $this->pdf->Ln(12);

        $this->pdf->SetX(20);
        $this->pdf->SetFont('Arial','B',8);
        $this->pdf->Cell(550, 10, " Conclusão do Ensino Médio/Ensino Superior ", 'LR', 0, 'L');
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->Ln(8);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, '', 'LR', 0, 'C');

        $this->pdf->Ln(8);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 10, " Instituição: ".utf8_decode ($academico[16]->data), 'LR', 0, 'L');
        $this->pdf->Ln(10);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 10, ' Cidade: '.utf8_decode ($academico[17]->data).' / '.utf8_decode ($academico[18]->data).'          Ano de Conclusão: '.utf8_decode ($academico[19]->data), 'LR', 0, 'L');
        $this->pdf->Ln(10);

   
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, '', 'LBR', 0, 'C');
        $this->pdf->Ln(12);

        //*--* Diplomação

        $this->pdf->Ln(16);

        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, '', 'LTR', 0, 'C');

        $this->pdf->Ln(12);

        $this->pdf->SetX(20);
        $this->pdf->SetFont('Arial','B',8);
        $this->pdf->Cell(550, 10, " Diplomação do Acadêmico ", 'LR', 0, 'L');
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->Ln(8);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, '', 'LR', 0, 'C');

        $this->pdf->Ln(8);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 10, " Conclusão do Curso: ".utf8_decode ($academico[20]->data)."                                    Colação de Grau: ".utf8_decode ($academico[21]->data)."                                    Expedição do Diploma: ".utf8_decode ($academico[22]->data), 'LR', 0, 'L');
        $this->pdf->Ln(10);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 10, ' Enade: '.utf8_decode ($academico[23]->data), 'LR', 0, 'L');
        $this->pdf->Ln(10);

   
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, '', 'LBR', 0, 'C');
        $this->pdf->Ln(12);

        $this->pdf->AddPage();

        //--------

       

    }


      public function addDisciplina2($disciplina)//cabeçalho turma
    {
         $this->pdf->Ln();
         
/*        // acrescenta a imagem de logo nestas coordenadas
        $image  = 'header_logo.gif';
        $this->pdf->Image($image, 25, 20, 50);
       

     //   $this->pdf->SetLineWidth(1);
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetFont('Arial','',6);
        $this->pdf->SetXY(80,30);
        $this->pdf->Cell(70, 6, 'Ministério da Educação -', 0, 0, 'L');
        $this->pdf->SetFont('Arial','B',6);
        $this->pdf->Cell(50, 6, 'Universidade Federal da Grande Dourados', 0, 0, 'L');
        $this->pdf->SetXY(80,40);
        $this->pdf->SetFont('Arial','',6);
        $this->pdf->Cell(100, 6, 'PROGRAD - Pró-Reitoria de Ensino de Graduação', 0, 0, 'L');
        $this->pdf->SetXY(80,50);
         $this->pdf->Cell(100, 6, 'FACED - Faculdade de Educação a Distância', 0, 0, 'L');
*/
        //*******

         $this->pdf->SetY(70);
        
        // exibe o título da seção
        $this->pdf->SetFont('Arial','B',8);
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 12, 'Notas Moodle (Para Simples Conferência)', 0, 0, 'C');
        $this->pdf->SetFont('Arial','',8);

        $this->pdf->Ln(16);

        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  10, '', 'LTR', 0, 'C');
        $this->pdf->Ln(10);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 12, " Faculdade: ".utf8_decode ($disciplina->faculdade), 'LR', 0, 'L');
        $this->pdf->Ln(12);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 12, ' Curso: '.$disciplina->curso, 'LR', 0, 'L');

        $this->pdf->SetFont('Arial','B',8);
        $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 12, ' Disciplina: '.utf8_decode ($disciplina->disciplina), 'LR', 0, 'L');
        $this->pdf->SetFont('Arial','',8);

        $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550, 12, ' Professor(a) Formador(a): '.utf8_decode ($disciplina->professor), 'LR', 0, 'L');
        if(strlen($disciplina->tutor)>0){
            $this->pdf->Ln(12);
            $this->pdf->SetX(20);
            $this->pdf->Cell(550, 12, ' Tutor(a): '.utf8_decode ($disciplina->tutor), 'LR', 0, 'L');
        }

        $this->pdf->Ln(10);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  10, '', 'LBR', 0, 'C');
        $this->pdf->Ln(12);
        //$this->pdf->Ln(12);
    }



    /**
     * Adiciona a linha de cabeçalho para os produtos
     */
    public function addCabecalhoProduto()
    {
      //  $this->pdf->SetY(185);
        
        // exibe o título da seção
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(300, 12, ' ', 0, 0, 'L');
        
        // exibe os títulos das colunas
        $this->pdf->Ln(10);
        $this->pdf->SetX(20);
        //$this->pdf->SetFillColor(230,230,230);
        //$this->pdf->Cell(50,  12, 'PERÍODO',     1, 0, 'C', 0);
        $this->pdf->Cell(360, 14, 'NOME',  1, 0, 'C', 0);
       /* $this->pdf->Cell(35, 12, 'A1',  1, 0, 'C', 0);
        $this->pdf->Cell(35, 12, 'A2',  1, 0, 'C', 0);
        $this->pdf->Cell(35, 12, 'A3',  1, 0, 'C', 0);
        $this->pdf->Cell(35, 12, 'A4',  1, 0, 'C', 0);
        $this->pdf->Cell(35, 12, 'A5',  1, 0, 'C', 0);
        $this->pdf->Cell(35, 12, 'A6',  1, 0, 'C', 0);
*/
        $this->pdf->Cell(40,  14, 'AO', 1, 0, 'C', 0);
        $this->pdf->Cell(35,  14, 'AF', 1, 0, 'C', 0);
        $this->pdf->Cell(45,  14, 'B', 1, 0, 'C', 0);
        $this->pdf->Cell(35,  14, 'AS', 1, 0, 'C', 0);
        $this->pdf->Cell(35,  14, 'EF', 1, 0, 'C', 0);

        $this->pdf->SetFont('Arial','',9);
       
    }


    /**
     * Adiciona a linha de cabeçalho para os produtos
     */
    public function addCabecalhoProduto2()
    {
      //  $this->pdf->SetY(185);
        
        // exibe o título da seção
        $this->pdf->SetFont('Arial','B',12);
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(300, 14, ' ', 0, 0, 'L');
        
        // exibe os títulos das colunas
        $this->pdf->Ln(14);
        $this->pdf->SetX(20);
        //$this->pdf->SetFillColor(230,230,230);
        $this->pdf->Cell(50,  14, 'PERÍODO',     0, 0, 'C', 0);
        $this->pdf->Cell(00, 14, 'DISCIPLINA CURSADA',  0, 0, 'C', 0);
        $this->pdf->Cell(50, 14, 'C.H.',  0, 0, 'C', 0);

        $this->pdf->Cell(50,  14, 'NOTA', 0, 0, 'C', 0);
        $this->pdf->Cell(50,  14, 'OBS', 0, 0, 'C', 0);
        $this->pdf->Cell(50,  14, 'TIPO', 0, 0, 'C', 0);
        $this->pdf->SetFont('Arial','',12);
       
    }
    

      public function addNota($students, $res, $statusObject)
    {
       // if(!strpos(utf8_decode ($students->fullname),'Meta_Alunos')){
       if(!preg_match("/(Meta_Alunos|ouvidoria|tp|email|discente|secretaria|prova|matr|comunicado)/i",utf8_decode ($students->fullname))){
            if(($statusObject->status=='') || ($res->resultado === 'DS') ){
                $res->resultado;
            }else {
                $res->resultado = 'MA';
            }
            if($res->resultado==='RN')
            $this->pdf->SetTextColor(255,0,0);
        //if($res->resultado=='RN' && $res->ex==0 && $res->mFinal >=40)
          //  $this->pdf->SetTextColor(255,127,0);

        $this->pdf->Ln(14);
        $this->pdf->SetX(20);

     //   $this->pdf->Cell(50,  12, $res->datastart, '', 0, 'C');
        $nomedisciplina = explode('-', utf8_decode ($students->fullname));

        $estudantedp = "";
if($students->roleid ==='15'){
    $estudantedp = "- (DP)";
}

        if (($res->count % 2) === 0)
            $this->pdf->SetTextColor(100,100,100);
       // $this->pdf->SetFillColor(200,220,255);
        else
        $this->pdf->SetTextColor(0,0,0);
        
        $disciplina->Nomedisciplina = $nomedisciplina[1] ? $nomedisciplina[1] : utf8_decode ($students->fullname);

        if(strlen($nomedisciplina[1])>0){
            $this->pdf->Cell(360, 14, $res->nome.' '.$estudantedp , 'LB', 0, 'R');
         }else{ $this->pdf->Cell(360, 14, ' '.$res->nome.' '.$estudantedp , 'LB', 0, 'R'); }

      /*  $this->pdf->Cell(35,  12, number_format($res->A1, 2), '', 0, 'C');

      // if($statusObject->status=='1'){
            if(($res->resultado === 'MA')|| ($res->resultado === 'DS')){
                 $this->pdf->Cell(35,  12, '-', '', 0, 'C');
             } else
            $this->pdf->Cell(35,  12, number_format($res->A2, 2), '', 0, 'C');
        //} else $this->pdf->Cell(50,  12, '-', '', 0, 'C');

       // if(($statusObject->status=='1') || ($res->resultado === 'DS') ){
            $this->pdf->Cell(35,  12, number_format($res->A3, 2), '', 0, 'C');
     //  } else $this->pdf->Cell(50,  12, 'MA', '', 0, 'C');
      
        $this->pdf->Cell(35,  12, number_format($res->A4, 2), '', 0, 'C');


        $this->pdf->Cell(35,  12, number_format($res->A5, 2), '', 0, 'C');
        $this->pdf->Cell(35,  12, number_format($res->A6, 2), '', 0, 'C');
        */
       

         if(strlen($res->AO)>0){
            $this->pdf->Cell(40,  14, number_format($res->AO, 2), 'LB', 0, 'C');
        } else $this->pdf->Cell(40,  14, '-', 'LB', 0, 'C');

        if(strlen($res->AF)>0){
            $this->pdf->Cell(35,  14, number_format($res->AF, 2), 'LB', 0, 'C');
        } else $this->pdf->Cell(35,  14, '-', 'LB', 0, 'C');

        $this->pdf->Cell(45,  14, '', 'LB', 0, 'C');

        if(strlen($res->AS)>0){
            $this->pdf->Cell(35,  14, number_format($res->AS, 2), 'LB', 0, 'C');
        } else $this->pdf->Cell(35,  14, '-', 'LB', 0, 'C');
        
        if(strlen($res->EF)>0){
            $this->pdf->Cell(35,  14, number_format($res->EF, 2), 'LBR', 0, 'C');
        } else $this->pdf->Cell(35,  14, '-', 'LBR', 0, 'C');
       
    }

    $this->pdf->SetTextColor(0,0,0);
    }

    
    
    /**
     * Adiciona o rodapé ao final da lista de produtos
     * Este método completa o espaço da listagem
     */
    public function addRodapeNota($nota)
    {
        global $USER;
        $data = getdate();
      //  $this->pdf->Ln(20);
if ($this->pdf->GetY()>750 ){
    $this->pdf->AddPage();
    $this->pdf->Ln(30);
}
        $this->pdf->Ln(30);

        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  20, '', 'T', 0, 'C');

        $this->pdf->Ln(10);
        $this->pdf->SetX(60);
        $this->pdf->Cell(500,  10, 'OBS: AO - Média das Avaliações Online, AF - Avaliação Final, AS - Avaliação Substitutiva, EF - Exame Final', '', 0, 'C');
        
        $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, 'B - Bônus [ Fica facultado a(ao) professor(a) formador(a) pontuar em até 2,0 (dois pontos), no total, como bônus,', '', 0, 'C');
          $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, ' os fóruns conceituais e as webconferências e adicionar na nota da AF - Avaliação Final ]', '', 0, 'C');

         $this->pdf->Ln(20);
        $this->pdf->SetX(20);

        $this->pdf->Cell(550,  15, '', 'T', 0, 'C');
        $this->pdf->Ln(15);
        $this->pdf->SetX(20);

       
    }

    public function addRodapeNota2($nota)
    {
        global $USER, $CFG;
        $data = getdate();

        if ($this->pdf->GetY()>750 ){
    $this->pdf->AddPage();
    $this->pdf->Ln(15);
}
/*
       // $this->pdf->Ln(20);
        $this->pdf->SetX(20);
        $this->pdf->Cell(260,  15, '', 'LTR', 0, 'C');
        $this->pdf->SetX(310);
        $this->pdf->Cell(260,  15, '', 'LTR', 0, 'C');
        $this->pdf->Ln(12);

        $this->pdf->SetX(20);
        $this->pdf->Cell(260,  12, 'CARGA HORÁRIA EXIGIDA PELO CURSO', 'LR', 0, 'C');
        $this->pdf->SetX(310);

        $this->pdf->Cell(260,  12, 'CARGA HORÁRIA CURSADA', 'LR', 0, 'C');

        $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        $this->pdf->Cell(260,  12, '', 'LR', 0, 'C');
        $this->pdf->SetX(310);

        $this->pdf->Cell(260,  12, '', 'LR', 0, 'C');

        $this->pdf->Ln(12);
        $this->pdf->SetX(20);//Rect($x, $y, $w, $h, $style=”)
        //$this->pdf->Rect(20,550,20,20);
        $this->pdf->Cell(260,  12, '     C.H. MÍNIMA EXIGIDA: 3120 horas', 'LR', 0, 'L');
$this->pdf->SetX(310);
$this->pdf->Cell(260,  12, '     TOTAL DE C.H.: '.$nota.' horas', 'LR', 0, 'L');

  */  

    /*    
        $this->pdf->Ln(12);
        $this->pdf->SetX(20);//Rect($x, $y, $w, $h, $style=”)
        //$this->pdf->Rect(20,550,20,20);
        $this->pdf->Cell(240,  12, 'C.H. MÍNIMA DE OBRIGATÓRIAS: '.$nota.' horas', 'LR', 0, 'L');
$this->pdf->SetX(280);
$this->pdf->Cell(240,  12, 'TOTAL DE C.H. DAS OBRIGATÓRIAS: '.$nota.' horas', 'LR', 0, 'L');

$this->pdf->Ln(12);
        $this->pdf->SetX(20);//Rect($x, $y, $w, $h, $style=”)
        //$this->pdf->Rect(20,550,20,20);
        $this->pdf->Cell(240,  12, 'C.H. MÍNIMA DE OPTATIVAS: '.$nota.' horas', 'LR', 0, 'L');
$this->pdf->SetX(280);
$this->pdf->Cell(240,  12, 'TOTAL DE C.H. DAS OPTATIVAS: '.$nota.' horas', 'LR', 0, 'L');

$this->pdf->Ln(12);
        $this->pdf->SetX(20);//Rect($x, $y, $w, $h, $style=”)
        //$this->pdf->Rect(20,550,20,20);
        $this->pdf->Cell(240,  12, 'C.H. MÍNIMA DE ELETIVAS: '.$nota.' horas', 'LR', 0, 'L');
$this->pdf->SetX(280);
$this->pdf->Cell(240,  12, 'TOTAL DE C.H. DAS ELETIVAS: '.$nota.' horas', 'LR', 0, 'L');

$this->pdf->Ln(12);
        $this->pdf->SetX(20);//Rect($x, $y, $w, $h, $style=”)
        //$this->pdf->Rect(20,550,20,20);
        $this->pdf->Cell(240,  12, 'C.H. MÍNIMA DE COMUNS À UNIVERSIDADE: '.$nota.' horas', 'LR', 0, 'R');
$this->pdf->SetX(280);
$this->pdf->Cell(240,  12, 'TOTAL DE C.H. DAS COMUNS À UNIVERSIDADE: '.$nota.' horas', 'LR', 0, 'L');

$this->pdf->Ln(12);
        $this->pdf->SetX(20);//Rect($x, $y, $w, $h, $style=”)
        //$this->pdf->Rect(20,550,20,20);
        $this->pdf->Cell(240,  12, 'C.H. MÍNIMA DE COMUNS À ÁREA: '.$nota.' horas', 'LBR', 0, 'L');
$this->pdf->SetX(280);
$this->pdf->Cell(240,  12, 'TOTAL DE C.H. DAS COMUNS À ÁREA: '.$nota.' horas', 'LBR', 0, 'L');

         */

/*
$this->pdf->Ln(12);
        $this->pdf->SetX(20);//Rect($x, $y, $w, $h, $style=”)
        //$this->pdf->Rect(20,550,20,20);
        $this->pdf->Cell(260,  12, '', 'LBR', 0, 'L');
$this->pdf->SetX(310);
$this->pdf->Cell(260,  12, '', 'LBR', 0, 'L');

*/

       
    //}
    
    /**
     * Adiciona o rodapé da nota
     */
   // public function addRodapeAssinatura()
   // {
         $data = getdate();
        //$this->pdf->Ln(20);
      //   $this->pdf->Ln(12);
        
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->SetTextColor(0,0,0);


        $this->pdf->SetFont('Arial','B',10);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  10, 'Este documento é somente para Simples Conferência', 0, 0, 'C');
        
        $this->pdf->SetFont('Arial','',8);
        
        $this->pdf->Ln(25);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, 'Relatório para Conferência Moodle EaD UFGD, exportado por: '.utf8_decode($USER->firstname).' '.utf8_decode($USER->lastname).', '.$data[mday].'/'.$data[mon].'/'.$data[year].'  -  '.$data[hours].':'.$data[minutes].':'.$data[seconds], '', 0, 'C');
        
        $this->pdf->Ln(20);
        $this->pdf->SetX(20);
        $this->pdf->Cell(550,  12, ' '.$CFG->wwwroot, '', 0, 'C');
        
    }
    
    /**
     * Salva a nota fiscal em um arquivo
     * @param $arquivo localização do arquivo de saída
     */
    public function gerar($arquivo)
    {
        // salva o PDF
        ob_start ();
        $this->pdf->Output($arquivo,'I');//I
    }
}
?>
