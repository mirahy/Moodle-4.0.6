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
    public function __construct($numero)
    {
        // Define o diret�rio das fontes
        define('FPDF_FONTPATH', getcwd() . '/app.util/pdf/font/');
        
        // Carrega a biblioteca FPDF
        include_once 'app.util/pdf/fpdf.php';
        
        // Cria um novo documento PDF
        $this->pdf = new FPDF('L', 'pt');
        $this->pdf->SetMargins(30,30,30); // define margens
        
        // Adiciona uma p�gina
        $this->pdf->AddPage();
        $this->pdf->Ln();
        $this->pdf->SetAutoPageBreak(true,20); 
        
        // acrescenta a imagem de logo nestas coordenadas
        $image  = 'header_logo.gif';
        $this->pdf->Image($image, 25, 20, 50);
       

     //   $this->pdf->SetLineWidth(1);
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetFont('Arial','',6);
        $this->pdf->SetXY(80,30);
        $this->pdf->Cell(70, 6, 'Minist�rio da Educa��o -', 0, 0, 'L');
        $this->pdf->SetFont('Arial','B',6);
        $this->pdf->Cell(50, 6, 'Universidade Federal da Grande Dourados', 0, 0, 'L');
        $this->pdf->SetXY(80,40);
        $this->pdf->SetFont('Arial','',6);
        $this->pdf->Cell(100, 6, 'PROGRAD - Pr�-Reitoria de Ensino de Gradua��o', 0, 0, 'L');
        $this->pdf->SetXY(80,50);
        $this->pdf->Cell(100, 6, 'EAD - Faculdade de Educa��o a Dist�ncia', 0, 0, 'L');
        

    }

    /**
     * M�todo addCliente
     * Adiciona um cliente na nota
     * @param $cliente Objeto contendo os atributos do cliente
     */
    public function addDisciplina($disciplina)//cabe�alho turma
    {
        $this->pdf->SetY(70);
        
        // exibe o t�tulo da se��o
        $this->pdf->SetFont('Arial','B',8);
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(790, 12, ' Relat�rio de Acesso - Professores Formadores e Tutores a Dist�ncia', 0, 0, 'C');
        $this->pdf->SetFont('Arial','',8);

        $this->pdf->SetX(20);
        $this->pdf->Ln(6);

    }

    /**
     * Adiciona a linha de cabe�alho para os produtos
     */
    public function addCabecalhoProduto()
    {
       // $this->pdf->SetY(140);
        
        // exibe o t�tulo da se��o
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(300, 12, ' ', 0, 0, 'L');
        
        // exibe os t�tulos das colunas
        $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        $this->pdf->SetFillColor(230,230,230);
        $this->pdf->Cell(270, 15, 'DISCIPLINA',  1, 0, 'C', 1);
        $this->pdf->Cell(310, 15, 'NOME',  1, 0, 'C', 1);
        $this->pdf->Cell(90,  15, 'PAPEL',    1, 0, 'C', 1);
        $this->pdf->Cell(130,  15, '�LTIMO ACESSO', 1, 0, 'C', 1);

    }
    
    /**
     * Adiciona um produto na nota
     * @param $produto Objeto com os atributos do produto
     */
    public function addNota($nota, $faltas)
    {
        $datetime2 = time();

        $segundos_diferenca =  $datetime2 - $nota->timecreated; //- 1418809913; - 24hverd
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

        if ($nota->roleid == 9)
            $funcao = 'Tutor(a) a dist�ncia';
        else $funcao = 'Prof(a) Formador(a)';

        $this->pdf->Cell(90, 15, $funcao, '1', 0, 'C');

        if ($nota->timecreated ==0){
            $this->pdf->Cell(130,  15, 'Nunca', '1', 0, 'C');
        }else{
            $this->pdf->Cell(90,  15, ''.date("d/m/Y H:i:s",$nota->timecreated ), '1', 0, 'C');//pegar data
            $this->pdf->Cell(40,  15, ''.(int)$horas.' h', '1', 0, 'C');//pegar data
        }
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetFont('','', '');

    }

    
    
    /**
     * Adiciona o rodap� ao final da lista de produtos
     * Este m�todo completa o espa�o da listagem
     */
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
        
        $this->pdf->Cell(800,  12, 'Relat�rio emitido por: '.utf8_decode ($USER->firstname).' '.utf8_decode ($USER->lastname).' EM: '.str_pad($data[mday],2,"0", STR_PAD_LEFT).'/'.str_pad($data[mon],2,"0", STR_PAD_LEFT).'/'.str_pad($data[year],2,"0", STR_PAD_LEFT).'  '.str_pad($data[hours],2,"0", STR_PAD_LEFT).':'.str_pad($data[minutes],2,"0", STR_PAD_LEFT).':'.str_pad($data[seconds],2,"0", STR_PAD_LEFT), '', 0, 'L');
    
    }
    
    /**
     * Adiciona o rodap� da nota
     */
    public function addRodapeAssinatura()
    {
        
    //    if ($this->pdf->GetY()>420 ){
      //      $this->pdf->AddPage();
        //    $this->pdf->Ln(20);
        //}
        $this->pdf->Ln(30);
        
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->SetTextColor(0,0,0);
/*
        $this->pdf->SetX(100);
        $this->pdf->Cell(190,  12, '', 0, 0, 'C');
        $this->pdf->Cell(230, 12, '', 'B', 0, 'L');
        $this->pdf->Cell(120,  12, '', 0, 0, 'C');
        //$this->pdf->Cell(130, 12, '', 'B', 0, 'L');
        $this->pdf->Cell(20,  12, '', 0, 0, 'C');
        //$this->pdf->Cell(130,  12, '', 'B', 0, 'C');

        $this->pdf->Cell(20,  12, '', 0, 0, 'R');
        $this->pdf->Ln(12);

        $this->pdf->SetX(100);
        $this->pdf->Cell(190,  12, '', 0, 0, 'C');

        $this->pdf->Cell(230,  12, 'Coordenador(a) de Tutoria', 0, 0, 'C');
        $this->pdf->Cell(120,  12, '', 0, 0, 'C');
      // $this->pdf->Cell(130,  12, 'Tutor Presencial', 0, 0, 'C');
        $this->pdf->Cell(20,  12, '', 0, 0, 'C');
        //$this->pdf->Cell(130,  12, 'Coordenador do Curso', 0, 0, 'C');
        //$this->pdf->Cell(20,  12, '', 0, 0, 'R');
*/
        $this->pdf->Ln(12);
        
    }
    
    /**
     * Salva a nota fiscal em um arquivo
     * @param $arquivo localiza��o do arquivo de sa�da
     */
    public function gerar($arquivo)
    {
        // salva o PDF
        ob_start ();
        $this->pdf->Output($arquivo,'I');
    }
}
?>
