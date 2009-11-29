<?php

require_once('tcpdf/config/lang/eng.php');
require_once("tcpdf/tcpdf.php");

global $pdf;

class resume_pdf extends TCPDF {
	function Header() {
		$d = $this->getPageDimensions();
		
		//var_dump($d);
		
		$this->SetAutoPageBreak(false, 0);
		$this->RoundedRect($d['tm'], $d['lm'] , $d['wk'] - $d['lm'] - $d['rm'],  $d['hk'] - $d['tm'] - $d['bm'], 2.5);
		$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	}
	
	// Page footer
    public function Footer() {
    
    	if($this->getAliasNbPages() > 1 ) {
		    // Position at 1.5 cm from bottom
		    $this->SetY(-15);
		    // Set font
		    $this->SetFont('helvetica', 'I', 10);
		    // Page number
		    $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
        }
    }
    
    public function MultiRow($strong, $title, $desc) {
		        
        $page_start = $this->getPage();
        $y_start = $this->GetY();
        
        // write the left cell
        if($strong) {
			$this->SetFont('helvetica', 'B', 10);
		    $this->MultiCell(40, 0, $strong, 0, 'R', 0, 1, '', '', true, 0);
        }
        
        $this->SetFont('helvetica', '', 10);
        $this->MultiCell(40, 0, $title, 0, 'R', 0, 2, '', '', true, 0);

        
        $page_end_1 = $this->getPage();
        $y_end_1 = $this->GetY();
        
        $this->setPage($page_start);
        
        // write the right cell
        $this->MultiCell(0, 0, $desc, 0, 'L', 0, 1, $this->GetX() ,$y_start, true, 0);
        
        $page_end_2 = $this->getPage();
        $y_end_2 = $this->GetY();
        
        // set the new row position by case
        if (max($page_end_1,$page_end_2) == $page_start) {
            $ynew = max($y_end_1, $y_end_2);
        } elseif ($page_end_1 == $page_end_2) {
            $ynew = max($y_end_1, $y_end_2);
        } elseif ($page_end_1 > $page_end_2) {
            $ynew = $y_end_1;
        } else {
            $ynew = $y_end_2;
        }
        
        $this->setPage(max($page_end_1,$page_end_2));
        $this->SetXY($this->GetX(),$ynew);
    }

}

function resume_ext_pdf_fallback($title, $section) {
	global $pdf;
	
	$pdf->SetFont('helvetica', 'B', 11);
	$pdf->Cell(0, 10, $title, 0, 1, 'C');
	
	//var_dump($section);
	foreach( $section as $sect ) {
		$part1 = (isset($sect['strong'])?  ($sect['strong'] . " - "): "") . (isset($sect['title'])?$sect['title']: "");
		$part2 = (isset($sect['desc'])? $sect['desc']: "");
				
		$pdf->MultiRow(isset($sect['strong']) ? $sect['strong'] : "",
						 isset($sect['title'])? $sect['title']: "",
						 isset($sect['desc']) ? $sect['desc']: "");
		
		
		if( isset($sect['subsections']) ) {
			$pdf->SetTextColor(96);
			foreach($sect['subsections'] as $ss) {
				$pdf->MultiRow(isset($ss['strong']) ? $ss['strong'] : "",
							 isset($ss['title'])? $ss['title']: "",
							 isset($ss['desc']) ? $ss['desc']: "");
			}
		}
		// add some padding to the bottom
		$pdf->SetY($pdf->GetY() + 1.5);
		
		$pdf->SetFont('helvetica', '', 10);
		$pdf->SetTextColor(0);
	}

	// add some padding to the bottom
	$pdf->SetY($pdf->GetY() + 5);
	

}

function resume_ext_pdf_general($title, $section) {
	global $pdf;
	
	$pdf = new resume_pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, "LETTER", true, 'UTF-8', true); 
	
	$pdf->SetCreator("Resume Extended - Wordpress Plugin (http://wordpress.org/extend/plugins/resume-extended/) and " . PDF_CREATOR);
	$pdf->SetAuthor($section['resume_name']);
	$pdf->SetSubject($section['resume_objective']);

	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	
	$pdf->SetFont('helvetica', 'B', 25);
	
	$pdf->AddPage();
	
	//$pdf->RoundedRect(0,0, 175, 50, 2.5, "1111", "F");
	
	//$pdf->SetTextColor(255,255,255);
	
	$pdf->Cell(0, 10, $section['resume_name'], 0, 1, 'C');
	
	$pdf->SetFont('helvetica', 'B', 10);
	
	$pdf->Cell(0, 7, isset($section['resume_address'])?$section['resume_address']:"", 0, 1, 'C');
	// links
	$pdf->SetTextColor(0, 102, 204);
	$pdf->Cell(0, 7, isset($section['resume_email'])?$section['resume_email']: "", 0, 1, 'C', 0, "mailto:" . $section['resume_email']);
	$pdf->Cell(0, 7, isset($section['resume_website'])?$section['resume_website']: "", 0, 1, 'C', 0, $section['resume_website']);
	
	$pdf->SetTextColor(0);
	$pdf->Cell(0, 7, $section['resume_objective'], 0, 1, 'C');
	
	// set appropriate colors and styles before end
	$pdf->SetFont('helvetica', '', 10);
	$pdf->SetTextColor(0);
}

function resume_ext_pdf_finish($title, $section) {
	global $pdf;
	echo $pdf->Output('example_001.pdf', 'S'); // this needs some improvement 
}
?>
