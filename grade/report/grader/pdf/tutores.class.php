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
    public function __construct($numero)
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
        $this->pdf->Cell(100, 6, 'EAD - Faculdade de Educação a Distância', 0, 0, 'L');
        

    }

    /**
     * Método addCliente
     * Adiciona um cliente na nota
     * @param $cliente Objeto contendo os atributos do cliente
     */
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

    /**
     * Adiciona a linha de cabeçalho para os produtos
     */
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
            $funcao = 'Tutor(a) a distância';
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
     * Adiciona o rodapé ao final da lista de produtos
     * Este método completa o espaço da listagem
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
        
        $this->pdf->Cell(800,  12, 'Relatório emitido por: '.utf8_decode ($USER->firstname).' '.utf8_decode ($USER->lastname).' EM: '.str_pad($data[mday],2,"0", STR_PAD_LEFT).'/'.str_pad($data[mon],2,"0", STR_PAD_LEFT).'/'.str_pad($data[year],2,"0", STR_PAD_LEFT).'  '.str_pad($data[hours],2,"0", STR_PAD_LEFT).':'.str_pad($data[minutes],2,"0", STR_PAD_LEFT).':'.str_pad($data[seconds],2,"0", STR_PAD_LEFT), '', 0, 'L');
    
    }
    
    /**
     * Adiciona o rodapé da nota
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
     * @param $arquivo localização do arquivo de saída
     */
    public function gerar($arquivo)
    {
        // salva o PDF
        ob_start ();
        $this->pdf->Output($arquivo,'I');
    }
}
?>
