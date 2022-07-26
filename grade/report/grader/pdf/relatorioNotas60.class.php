<?php
/**
 * Classe Registro de notas
 * Encapsula o Relaat�rio de notas em PDF
 */
class RegistroNotas
{
    private $pdf;            // objeto PDF
    private $produtos;       // Vetor de Produtos
    private $total_produtos; // Valor total de produtos
    private $count_produtos; // Quantidade de produtos

    /**
     * M�todo construtor
     * Instancia o objeto FPDF
     * @param $numero numero da nota fiscal
     * @param $data data de emiss�o
     */
    public function __construct()
    {
        // Define o diret�rio das fontes
        define('FPDF_FONTPATH', getcwd() . '/app.util/pdf/font/');
        
        // Carrega a biblioteca FPDF
        include_once 'app.util/pdf/fpdf.php';
        
        // Cria um novo documento PDF
        $this->pdf = new FPDF('L', 'pt');//L||p
        $this->pdf->SetMargins(30,30,30); // define margens

        // Adiciona uma p�gina
        $this->pdf->AddPage();
        $this->pdf->Ln();
        
        // acrescenta a imagem de logo nestas coordenadas
        //$image  = 'header_logo.gif';
        $image  = 'logoead.jpg';
        $this->pdf->Image($image, 25, 20,null, 25);

        $this->pdf->SetAutoPageBreak(true,30); 
        // inicializa vari�veis
        $this->total_produtos = 0;
        $this->count_produtos = 0;

    }


    public function addCabecalhoDisciplina($disciplina)//cabe�alho turma
    {
        $this->pdf->Ln();

        $this->pdf->SetY(50);
        
        // exibe o t�tulo da se��o
        $this->pdf->SetFont('Arial','B',8);
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(800, 12, 'Relat�rio de Notas Moodle', 0, 0, 'C');
        $this->pdf->SetFont('Arial','',8);

        $this->pdf->Ln(16);

        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(800,  10, '', 'LTR', 0, 'C');
        $this->pdf->Ln(10);

        $this->pdf->SetX(20);
        $this->pdf->Cell(800, 12, " Faculdade: ".utf8_decode ($disciplina->faculdade).'                                 Curso: '.utf8_decode ($disciplina->curso).'                                   C.H.: 90hs', 'LR', 0, 'L');

        $this->pdf->SetFont('Arial','B',8);
        $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        $this->pdf->Cell(800, 12, ' Disciplina: '.utf8_decode ($disciplina->disciplina), 'LR', 0, 'L');
        $this->pdf->SetFont('Arial','',8);

        $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        if(strlen($disciplina->tutor)>0){
            $this->pdf->Cell(800, 12, ' Professor(a) Formador(a): '.utf8_decode ($disciplina->professor).'                      Tutor(a): '.utf8_decode ($disciplina->tutor), 'LR', 0, 'L');
        }else $this->pdf->Cell(800, 12, ' Professor(a) Formador(a): '.utf8_decode ($disciplina->professor), 'LR', 0, 'L');

        $this->pdf->Ln(10);
        $this->pdf->SetX(20);
        $this->pdf->Cell(800,  10, '', 'LBR', 0, 'C');
        $this->pdf->Ln(12);
    }



    /**
     * Adiciona a linha de cabe�alho para os produtos
     */
    public function addCabecalhoNotas()
    {
      //  $this->pdf->SetY(185);
        
        // exibe o t�tulo da se��o
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(300, 12, ' ', 0, 0, 'L');
        
        // exibe os t�tulos das colunas
        $this->pdf->Ln(10);
        $this->pdf->SetX(20);
        //$this->pdf->SetFillColor(230,230,230);
        $this->pdf->Cell(20,  14, '',     1, 0, 'C', 0);
        $this->pdf->Cell(440, 14, 'NOME',  1, 0, 'C', 0);
        $this->pdf->Cell(35, 14, 'A1',  1, 0, 'C', 0);
        $this->pdf->Cell(35, 14, 'A2',  1, 0, 'C', 0);
        $this->pdf->Cell(35, 14, 'A3',  1, 0, 'C', 0);
        $this->pdf->Cell(35, 14, 'A4',  1, 0, 'C', 0);
        
     //   $this->pdf->Cell(35, 14, 'A5',  1, 0, 'C', 0);
       // $this->pdf->Cell(35, 14, 'A6',  1, 0, 'C', 0);

        $this->pdf->Cell(40,  14, 'AO', 1, 0, 'C', 0);
        $this->pdf->Cell(40,  14, 'AF', 1, 0, 'C', 0);
        $this->pdf->Cell(50,  14, 'B', 1, 0, 'C', 0);
        $this->pdf->Cell(35,  14, 'AS', 1, 0, 'C', 0);
        $this->pdf->Cell(35,  14, 'EF', 1, 0, 'C', 0);

        $this->pdf->SetFont('Arial','',9);
       
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


        $this->pdf->Cell(20,  14, $res->count, 'LB', 0, 'C');
        

        if(strlen($nomedisciplina[1])>0){
            $this->pdf->Cell(440, 14, $res->nome.' '.$estudantedp , 'LB', 0, 'R');
         }else{ $this->pdf->Cell(440, 14, ' '.$res->nome.' '.$estudantedp , 'LB', 0, 'R'); }

     

        if(strlen($res->A1)>0){
            $this->pdf->Cell(35,  14, number_format($res->A1, 1), 'LB', 0, 'C');
        } else $this->pdf->Cell(35,  14, '-', 'LB', 0, 'C');

        if(strlen($res->A2)>0){
            $this->pdf->Cell(35,  14, number_format($res->A2, 1), 'LB', 0, 'C');
        } else $this->pdf->Cell(35,  14, '-', 'LB', 0, 'C');

        if(strlen($res->A3)>0){
            $this->pdf->Cell(35,  14, number_format($res->A3, 1), 'LB', 0, 'C');
        } else $this->pdf->Cell(35,  14, '-', 'LB', 0, 'C');

        if(strlen($res->A4)>0){
            $this->pdf->Cell(35,  14, number_format($res->A4, 1), 'LB', 0, 'C');
        } else $this->pdf->Cell(35,  14, '-', 'LB', 0, 'C');

   /*     if(strlen($res->A5)>0){
            $this->pdf->Cell(35,  14, number_format($res->A5, 1), 'LB', 0, 'C');
        } else $this->pdf->Cell(35,  14, '-', 'LB', 0, 'C');

        if(strlen($res->A6)>0){
            $this->pdf->Cell(35,  14, number_format($res->A6, 1), 'LB', 0, 'C');
        } else $this->pdf->Cell(35,  14, '-', 'LB', 0, 'C');
     */  

         if(strlen($res->AO)>0){
            $this->pdf->Cell(40,  14, number_format($res->AO, 1), 'LB', 0, 'C');
        } else $this->pdf->Cell(40,  14, '-', 'LB', 0, 'C');

        if(strlen($res->AF)>0){
            $this->pdf->Cell(40,  14, number_format($res->AF, 1), 'LB', 0, 'C');
        } else $this->pdf->Cell(40,  14, '-', 'LB', 0, 'C');

        $this->pdf->Cell(50,  14, '', 'LB', 0, 'C');

        if(strlen($res->AS)>0){
            $this->pdf->Cell(35,  14, number_format($res->AS, 1), 'LB', 0, 'C');
        } else $this->pdf->Cell(35,  14, '-', 'LB', 0, 'C');
        
        if(strlen($res->EF)>0){
            $this->pdf->Cell(35,  14, number_format($res->EF, 1), 'LBR', 0, 'C');
        } else $this->pdf->Cell(35,  14, '-', 'LBR', 0, 'C');
       
    }

    $this->pdf->SetTextColor(0,0,0);
    }

    
    
    /**
     * Adiciona o rodap� ao final da lista de produtos
     * Este m�todo completa o espa�o da listagem
     */
    public function addRodapeNota()
    {
        global $USER, $CFG;
        $data = getdate();

        if ($this->pdf->GetY()>750 ){
            $this->pdf->AddPage();
            $this->pdf->Ln(15);
        }

        $this->pdf->Ln(20);
        $this->pdf->SetX(20);
        $this->pdf->Cell(800,  10, 'OBS: AO - M�dia das Avalia��es Online, AF - Avalia��o Final, AS - Avalia��o Substitutiva, EF - Exame Final, B - B�nus [ Fica facultado a(ao) professor(a) formador(a) pontuar em at� 2,0 (dois pontos),', '', 0, 'L');
        
        $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        $this->pdf->Cell(800,  10, ' no total, como b�nus, os f�runs conceituais e as webconfer�ncias e adicionar na nota da AF - Avalia��o Final ]', '', 0, 'L');
        
        $this->pdf->Ln(15);
        $this->pdf->SetX(20);
        $this->pdf->Cell(800,  15, '', 'T', 0, 'C');

        $this->pdf->Ln(5);
        $this->pdf->SetX(20);
        $this->pdf->Cell(800,  12, 'Relat�rio para Confer�ncia de notas Moodle EaD UFGD, exportado por: '.utf8_decode($USER->firstname).' '.utf8_decode($USER->lastname).', '.$data[mday].'/'.$data[mon].'/'.$data[year].'  -  '.$data[hours].':'.$data[minutes].':'.$data[seconds].'  -  '.$CFG->wwwroot, '', 0, 'C');
    }

    
    /**
     * Salva a nota fiscal em um arquivo
     * @param $arquivo localiza��o do arquivo de sa�da
     */
    public function gerar($arquivo)
    {
        // salva o PDF
        ob_start ();
        $this->pdf->Output($arquivo,'I');//I
    }
}
?>
