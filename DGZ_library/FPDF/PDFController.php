<?php

class PDFController extends fpdfBaseController
{


	function __construct()
	{
		parent::FPDF();
	}



	/**
	 * This method creates the PDF page header
	 *
	 */
	function Header()
	{
		global $title;
		//Logo
		//$this->Image('logo_pb.png',10,8,33);
		//Arial bold 15
		$this->SetFont('Arial','B',15);
		//Move to the right
		$this->Cell(80);
		//Title
		$this->Cell(30,10,$title,0,0,'C');
		//Line break
		$this->Ln(20);
	}




	/**
	 * This method creates the PDF page footer
	 *
	 */
	function Footer()
	{
		//Position at 1.5 cm from bottom
		$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Arial','I',8);
		//Page number
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}






	/**
	 * This method sets up the table-like structure of the PDF report
	 *
	 */
	function Table($header, $data)
	{
		//colours, line width, font
		$this->SetFillColor(87,112,155);
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(0,0,0);
		$this->SetFont('','B','12');

		//header
		//these represent the width of the cells in px, change em depending on the width of the text you expect in em
		$w=array(30, 30, 50, 35, 30, 25, 55, 20);
		for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
		$this->Ln();

		//colour and font restoration
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
		$this->SetFont('','','12');

		//Data
		$fill=false;

		foreach($data as $row)
		{
			$this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
			$this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
			$this->Cell($w[2],6,$row[2],'LR',0,'L',$fill);
			$this->Cell($w[3],6,$row[3],'LR',0,'L',$fill);
			$this->Cell($w[4],6,$row[4],'LR',0,'L',$fill);
            $this->Cell($w[5],6,$row[5],'LR',0,'L',$fill);
            $this->Cell($w[6],6,$row[6],'LR',0,'L',$fill);
            $this->Cell($w[7],6,$row[7],'LR',0,'L',$fill);

			$this->Ln();
			$fill=!$fill;
		}
		$this->Cell(array_sum($w),0,'','T');

	}





	/**
	 * The value of the attended column could be one of several options (e.g. 'f', 't', 'ns'),
	 * so this method formats the output depending on the data in this column
	 *
	 */
	function FormatAttend($data)
	{
		if($data == "t") {
		//$this->Image('tick.png',5,5,5);
		//$data=$this;
			$data = "yes";
		}
		if($data == "f"){
			$data = "no";
		}
		if ($data == "ns"){
			$data = "No Show";
		}

		return $data;
	}



}

