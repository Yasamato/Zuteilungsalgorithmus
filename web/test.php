<?php
  require "../data/config.php";
  require 'php/db.php';
  require 'php/TCPDF-6.2.26/tcpdf.php';

  class projektPDF extends TCPDF {

    function printProjekt($projekt) {
      $this->AddPage();
      $this->setCellHeightRatio(1.1);
      $this->ln(13);

      // Zeile 1
      $this->SetFont('freeserif', 'B', 14);
      $this->Cell(180, 0, 'Projekttitel:', "LTR");
      $this->Cell(97, 0, 'Betreuer:', "TR");
      $this->SetFont('freeserif', '', 10);
      $this->ln();
      $this->Cell(180, 0, $projekt["name"], "LBR");
      $this->Cell(97, 0, $projekt["betreuer"], "BR");
      $this->ln(6);

      // Zeile 2
      $this->SetFont('freeserif', 'B', 14);
      $this->Cell(22, 0, 'Stufen:', "LTR");
      $this->Cell(52, 0, 'Teilnehmeranzahl:', "TR");
      $this->Cell(203, 0, 'Kosten/Sonstiges:', "TR");
      $this->SetFont('freeserif', '', 10);
      $this->ln();
      $this->Cell(22, 0, $projekt["minKlasse"] . " - " . $projekt["maxKlasse"], "LBR");
      $this->Cell(52, 0, $projekt["minPlatz"] . " - " . $projekt["maxPlatz"], "BR");
      $this->Cell(203, 0, $projekt["sonstiges"], "BR");
      $this->ln(6);

      // Zeile 3
      $this->SetFont('freeserif', 'B', 14);
      $this->Cell(277, 0, 'Vorraussetzungen:', "LTR");
      $this->SetFont('freeserif', '', 10);
      $this->ln();
      $this->Cell(277, 0, $projekt["vorraussetzungen"], "LBR");
      $this->ln(6);

      // Zeile 4
      $this->SetFont('freeserif', 'B', 14);
      $this->Cell(0, 0, 'Beschreibung:', "LTR");
      $this->SetFont('freeserif', '', 10);
      $this->ln();
      $y = $this->getY();
      //$this->Write(5, newlineBack($projekt["beschreibung"]), '', 0, 'L', false, 0, false, false, 0);
      //$this->MultiCell(0, 5, newlineBack($projekt["beschreibung"]), "LBR", "L");
      $this->writeHTMLCell(0, 0, 10, $y, $projekt["beschreibung"]);
      $this->Line(10, $y, 10, $y + 80);
      $this->Line(10, $y + 80, 287, $y + 80);
      $this->Line(287, $y, 287, $y + 80);
      $this->ln(82);

      // Zeile 5
      $this->SetFont('freeserif', 'B', 14);
      $this->Cell(277, 0, 'Vorraussichtlicher Ablauf', 0, 0, "C");
      $this->ln(6);

      // Tabelle
      // Zeile 6
      $this->SetFont('freeserif', 'B', 14);
      $this->Cell(27, 0, 'Tag', "LTRB");
      $this->Cell(50, 0, 'Montag', "TRB");
      $this->Cell(50, 0, 'Dienstag', "TRB");
      $this->Cell(50, 0, 'Mittwoch', "TRB");
      $this->Cell(50, 0, 'Donnerstag', "TRB");
      $this->Cell(50, 0, 'Freitag', "TRB");
      $this->ln();

      //Zeile 7 Vormittag
      $y = $this->getY();
      $h = 20;
      $this->SetFont('freeserif', 'B', 10);
      $this->Cell(27, 0, "Vormittag");
      $this->Line(10, $y, 10, $y + $h);
      $this->Line(10, $y + $h, 37, $y + $h);
      $this->Line(37, $y, 37, $y + $h);
      $this->SetFont('freeserif', '', 10);
      $this->writeHTMLCell(50, 0, 37, $y, $projekt["moVor"]);
      $this->Line(37, $y, 37, $y + $h);
      $this->Line(37, $y + $h, 87, $y + $h);
      $this->Line(87, $y, 87, $y + $h);
      $this->writeHTMLCell(50, 0, 87, $y, $projekt["diVor"]);
      $this->Line(87, $y, 87, $y + $h);
      $this->Line(87, $y + $h, 137, $y + $h);
      $this->Line(137, $y, 137, $y + $h);
      $this->writeHTMLCell(50, 0, 137, $y, $projekt["miVor"]);
      $this->Line(137, $y, 137, $y + $h);
      $this->Line(137, $y + $h, 187, $y + $h);
      $this->Line(187, $y, 187, $y + $h);
      $this->writeHTMLCell(50, 0, 187, $y, $projekt["doVor"]);
      $this->Line(187, $y, 187, $y + $h);
      $this->Line(187, $y + $h, 237, $y + $h);
      $this->Line(237, $y, 237, $y + $h);
      $this->writeHTMLCell(50, 0, 237, $y, $projekt["frVor"]);
      $this->Line(237, $y, 237, $y + $h);
      $this->Line(237, $y + $h, 287, $y + $h);
      $this->Line(287, $y, 287, $y + $h);
      $this->ln($h);
      //Zeile 7 Mensa
      $y = $this->getY();
      $h = 5;
      $this->SetFont('freeserif', 'B', 10);
      $this->Cell(27, 0, "Mensa");
      $this->Line(10, $y, 10, $y + $h);
      $this->Line(10, $y + $h, 37, $y + $h);
      $this->Line(37, $y, 37, $y + $h);
      $this->SetFont('freeserif', '', 10);
      $this->writeHTMLCell(50, 0, 37, $y, $projekt["moMensa"] == "true" ? "Ja" : "Nein");
      $this->Line(37, $y, 37, $y + $h);
      $this->Line(37, $y + $h, 87, $y + $h);
      $this->Line(87, $y, 87, $y + $h);
      $this->writeHTMLCell(50, 0, 87, $y, $projekt["diMensa"] == "true" ? "Ja" : "Nein");
      $this->Line(87, $y, 87, $y + $h);
      $this->Line(87, $y + $h, 137, $y + $h);
      $this->Line(137, $y, 137, $y + $h);
      $this->writeHTMLCell(50, 0, 137, $y, $projekt["miMensa"] == "true" ? "Ja" : "Nein");
      $this->Line(137, $y, 137, $y + $h);
      $this->Line(137, $y + $h, 187, $y + $h);
      $this->Line(187, $y, 187, $y + $h);
      $this->writeHTMLCell(50, 0, 187, $y, $projekt["doMensa"] == "true" ? "Ja" : "Nein");
      $this->Line(187, $y, 187, $y + $h);
      $this->Line(187, $y + $h, 237, $y + $h);
      $this->Line(237, $y, 237, $y + $h);
      $this->writeHTMLCell(50, 0, 237, $y, $projekt["frMensa"] == "true" ? "Ja" : "Nein");
      $this->Line(237, $y, 237, $y + $h);
      $this->Line(237, $y + $h, 287, $y + $h);
      $this->Line(287, $y, 287, $y + $h);
      $this->ln($h);
      //Zeile 7 Nachmittag
      $y = $this->getY();
      $h = 20;
      $this->SetFont('freeserif', 'B', 10);
      $this->Cell(27, 0, "Nachmittag");
      $this->Line(10, $y, 10, $y + $h);
      $this->Line(10, $y + $h, 37, $y + $h);
      $this->Line(37, $y, 37, $y + $h);
      $this->SetFont('freeserif', '', 10);
      $this->writeHTMLCell(50, 0, 37, $y, $projekt["moNach"]);
      $this->Line(37, $y, 37, $y + $h);
      $this->Line(37, $y + $h, 87, $y + $h);
      $this->Line(87, $y, 87, $y + $h);
      $this->writeHTMLCell(50, 0, 87, $y, $projekt["diNach"]);
      $this->Line(87, $y, 87, $y + $h);
      $this->Line(87, $y + $h, 137, $y + $h);
      $this->Line(137, $y, 137, $y + $h);
      $this->writeHTMLCell(50, 0, 137, $y, $projekt["miNach"]);
      $this->Line(137, $y, 137, $y + $h);
      $this->Line(137, $y + $h, 187, $y + $h);
      $this->Line(187, $y, 187, $y + $h);
      $this->writeHTMLCell(50, 0, 187, $y, $projekt["doNach"]);
      $this->Line(187, $y, 187, $y + $h);
      $this->Line(187, $y + $h, 237, $y + $h);
      $this->Line(237, $y, 237, $y + $h);
      $this->writeHTMLCell(50, 0, 237, $y, $projekt["frNach"]);
      $this->Line(237, $y, 237, $y + $h);
      $this->Line(237, $y + $h, 287, $y + $h);
      $this->Line(287, $y, 287, $y + $h);
      $this->ln($h);
    }

    function Header() {
      $this->Image("Logo_Farbe.jpg", 10, 6, 30); // pfad ,x ,y , size
      $this->SetFont('freeserif', 'B', 15);
      $this->Ln(11);
      $this->Cell(30);
      $this->Cell(66, 10, 'Projektwoche ' . date("Y"));
      $this->ln(13);
    }
  }

  $pdf = new projektPDF('L', 'mm', 'A4');
  //$pdf->setProjekt(dbRead("../data/projekte.csv"));
  foreach (dbRead("../data/projekte.csv") as $key => $projekt) {
    $pdf->printProjekt($projekt);
    //var_dump($pdf->GetPageWidth()); // 297mm -2cm margin => 277mm zum Arbeiten
  }
  $pdf->Output();
?>
