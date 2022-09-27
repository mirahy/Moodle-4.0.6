<?php

class RegistroNotas
{
    private $pdf;            // objeto PDF

    public function __construct()
    {
        // Define o diretório das fontes
        define('FPDF_FONTPATH', getcwd() . '/app.util/pdf/font/');
        
        // Carrega a biblioteca FPDF
        include_once 'app.util/pdf/fpdf.php';
        
        // Cria um novo documento PDF
        $this->pdf = new FPDF('L', 'pt');
        $this->pdf->SetMargins(30,30,30); // define margens
        
        // Adiciona uma página
        $this->pdf->AddPage();
        $this->pdf->Ln();
        $this->pdf->SetAutoPageBreak(true,20); 
        
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
        

    }

    public function addDisciplina($disciplina)//cabeçalho turma
    {
        $this->pdf->SetY(70);
        
        // exibe o título da seção
        $this->pdf->SetFont('Arial','B',8);
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(790, 12, ' Relatório de Acesso - Professores Formadores e Tutores a Distância', 0, 0, 'C');
        $this->pdf->SetFont('Arial','',8);

        $this->pdf->SetX(20);
        $this->pdf->Ln(6);

    }

    public function addCabecalhoProduto()
    {
       // $this->pdf->SetY(140);
        
        // exibe o título da seção
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(300, 12, ' ', 0, 0, 'L');
        
        // exibe os títulos das colunas
        $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        $this->pdf->SetFillColor(230,230,230);
        $this->pdf->Cell(270, 15, 'DISCIPLINA',  1, 0, 'C', 1);
        $this->pdf->Cell(310, 15, 'NOME',  1, 0, 'C', 1);
        $this->pdf->Cell(90,  15, 'PAPEL',    1, 0, 'C', 1);
        $this->pdf->Cell(130,  15, 'ÚLTIMO ACESSO', 1, 0, 'C', 1);

    }

    public function addNota($nota, $faltas)
    {
        $datetime = time();

        $segundos_diferenca =  $datetime - $nota->timecreated; 
        $horas_diferenca = (int)floor( $segundos_diferenca / ( 60));
        $horas = (int) $horas_diferenca /60;

        if($horas > 24 && $horas < 48){
            $this->pdf->SetTextColor(210,105,30);
        } else if( $horas > 48){ $this->pdf->SetTextColor(255,0,0);} else
                $this->pdf->SetTextColor(0,0,0);

        if( $horas > 96){  
            $this->pdf->SetFont('','B','9');
        }
        $this->pdf->Ln(15);
        $this->pdf->SetX(20);

        $curso = explode(' ', $nota->curso);
        
        $this->pdf->Cell(270,  15, substr($nota->disciplina, 0, 49), '1', 0, 'C');

        $this->pdf->Cell(310, 15, substr($nota->firstname.' '.$nota->lastname, 0, 38), '1', 0, 'L');

        if ($nota->roleid == 4)
            $funcao = 'Tutor(a) a distância';
        else $funcao = 'Prof(a) Formador(a)';

        $this->pdf->Cell(90, 15, $funcao, '1', 0, 'C');

        if ($nota->timecreated ==0){
            $this->pdf->Cell(130,  15, 'Nunca', '1', 0, 'C');
        }else{
            $this->pdf->Cell(90,  15, ''.date("d/m/Y H:i:s",$nota->timecreated ), '1', 0, 'C');
            $this->pdf->Cell(40,  15, ''.(int)$horas.' h', '1', 0, 'C');
        }
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetFont('','', '');

    }

    
    
    public function addRodapeNota()
    {
        global $USER;
        $data = getdate();
        
     /*     if ($this->pdf->GetY()>100 ){
            $this->pdf->AddPage();
            $this->pdf->Ln(20);
        }*/
        $this->pdf->Ln(20);

        $this->pdf->SetX(20);
        
        $this->pdf->Cell(800,  12, 'Relatório emitido por: '.utf8_decode ($USER->firstname).' '.utf8_decode ($USER->lastname).' EM: '.str_pad($data[mday],2,"0", STR_PAD_LEFT).'/'.str_pad($data[mon],2,"0", STR_PAD_LEFT).'/'.str_pad($data[year],2,"0", STR_PAD_LEFT).'  '.str_pad($data[hours],2,"0", STR_PAD_LEFT).':'.str_pad($data[minutes],2,"0", STR_PAD_LEFT).':'.str_pad($data[seconds],2,"0", STR_PAD_LEFT), '', 0, 'L');
    
    }
    

    public function gerar($arquivo)
    {
        // salva o PDF
        ob_start ();
        $this->pdf->Output($arquivo,'I');
    }
}
?>
