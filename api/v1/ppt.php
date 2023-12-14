<?php

require_once '../vendor/autoload.php';

use PhpOffice\PhpPresentation\Autoloader;
use PhpOffice\PhpPresentation\Settings;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\AbstractShape;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Shape\Drawing;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\RichText\BreakElement;
use PhpOffice\PhpPresentation\Shape\RichText\TextElement;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Style\Font;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Slide\Note;

class ppt {

      public static function create_ppt($module_name,$id,$data,$filename) 
      {

            if($module_name == 'property')
            {
                  $sql  = "";
                  $db = new DbHandler();

                  $sql = "SELECT *,a.exp_price, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, CONCAT(h.salu,' ',h.fname,' ',h.lname) as assign_to, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%m') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%m') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.property_id in($data) GROUP BY a.property_id LIMIT 1 ";
                  
                  $propertydata = $db->getAllRecords($sql);


                  $objPHPPowerPoint = new PhpPresentation();
                              $objPHPPowerPoint->getLayout()->setDocumentLayout(['cx' => 978, 'cy' => 728], true)->setCX(978, DocumentLayout::UNIT_PIXEL)->setCY(728, DocumentLayout::UNIT_PIXEL);
                  $objPHPPowerPoint->removeSlideByIndex(0);


                  $sql_main = "SELECT * FROM report_template ORDER BY slide_no";
                  $stmt_main = $db->getRows($sql_main);
                  if($stmt_main->num_rows > 0)
                  {
                        while($row_main = $stmt_main->fetch_assoc())
                        { 
                              $slide_no = $row_main['slide_no'];
                              $property_id = $row_main['category_id'];
                              if ($slide_no==1)
                              {
                                    // FIRST SLIDE

                                    $currentSlide = createTemplatedSlide($objPHPPowerPoint,1); 

                                    $shape = $currentSlide->createDrawingShape();
                                    $shape->setName('RD BROTHERS')
                                    ->setDescription('RD BROTHERS')
                                    ->setPath('../../dist/img/ppt_main.jpg')
                                    ->setHeight(702)
                                    ->setWidth(944)
                                    ->setOffsetX(16)
                                    ->setOffsetY(16);
                              }

                              if ($slide_no==2)
                              {
                                    // SECOND SLIDE

                                    $currentSlide = createTemplatedSlide($objPHPPowerPoint,2);

                                    $shape = $currentSlide->createDrawingShape();
                                    $shape->setName('Property Image')
                                    ->setDescription('Property Image')

                                    ->setPath('uploads/property/'.$propertydata[0]['filenames'])
                                    ->setHeight(505)
                                    ->setWidth(752)
                                    ->setOffsetX(120)
                                    ->setWidthAndHeight(752, 505)
                                    ->setResizeProportional(true)
                                    ->setOffsetY(150);

                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(70);
                                    $shape->setWidth(880);
                                    $shape->setOffsetX(16);
                                    $shape->setOffsetY(15);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                                    $textRun = $shape->createTextRun($propertydata[0]['building_plot']);
                                    $textRun->getFont()->setBold(true)
                                    ->setItalic(false)
                                    ->setSize(30)
                                    ->setName('Calibri');
                                    $textRun->getFont()->setColor( new Color( '00000000' ) );
                                    $shape->createBreak();
                              }

                              if ($slide_no==3)
                              {

                                    // THIRD SLIDE

                                    $currentSlide = createTemplatedSlide($objPHPPowerPoint,3); 

                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(70);
                                    $shape->setWidth(880);
                                    $shape->setOffsetX(16);
                                    $shape->setOffsetY(15);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                                    $textRun = $shape->createTextRun('Commercial Terms');
                                    $textRun->getFont()->setBold(true);
                                    $textRun->getFont()->setSize(30)
                                    ->setName('Calibri');
                                    $textRun->getFont()->setColor( new Color( '00000000' ) );
                                    $shape->createBreak();

                                    $shape = $currentSlide->createTableShape(2);
                                    $shape->setHeight(600);
                                    $shape->setWidth(780);
                                    $shape->setOffsetX(100);
                                    $shape->setOffsetY(150);

                                    $row = $shape->createRow();
                                    //$row->getFill()->setFillType(Fill::FILL_SOLID)
                                    //->setStartColor(new Color('FF000000'))
                                    //->setEndColor(new Color('FF000000'));

                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Carpet Area (sqft)')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $tvar = 0;
                                    if ($propertydata[0]['carp_area'])
                                    {
                                          $tvar = $propertydata[0]['carp_area'];
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);


                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Saleable Area (sqft)')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $tvar = 0;
                                    if ($propertydata[0]['sale_area'])
                                    {
                                          $tvar = $propertydata[0]['sale_area'];
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);

                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Proposed Floors')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $tvar = 0;
                                    if ($propertydata[0]['floor'])
                                    {
                                          $tvar = $propertydata[0]['floor'];
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);
                                    
                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Quoted Rent on Saleable Area')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $tvar = 0;
                                    if ($propertydata[0]['exp_rent'])
                                    {
                                          $tvar = $propertydata[0]['exp_rent'];
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);


                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Security Deposit')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $tvar = 0;
                                    if ($propertydata[0]['security_depo'])
                                    {
                                          $tvar = $propertydata[0]['security_depo'];
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);


                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Lock in period')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $tvar = 0;
                                    if ($propertydata[0]['lock_per'])
                                    {
                                          $tvar = $propertydata[0]['lock_per'];
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);

                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Lease Tenure')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $tvar = 0;
                                    if ($propertydata[0]['tenure_year'])
                                    {
                                          $tvar = $propertydata[0]['tenure_year'];
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);

                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Rent Escalation')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();

                                    $tvar = 0;
                                    if ($propertydata[0]['rent_esc'])
                                    {
                                          $tvar = $propertydata[0]['rent_esc'];
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);


                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Location')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun($propertydata[0]['locality'].','.$propertydata[0]['city'])->getFont()->setSize(25);

                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Furniture Detail')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun($propertydata[0]['furniture'])->getFont()->setSize(25);
                              }
                              if ($slide_no==4)// && $slide_no<98) 
                              {

                                    // FOURTH SLIDE

                                    $currentSlide = createTemplatedSlide($objPHPPowerPoint,4); 

                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(70);
                                    $shape->setWidth(880);
                                    $shape->setOffsetX(16);
                                    $shape->setOffsetY(15);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                                    $textRun = $shape->createTextRun($row_main['description']);
                                    $textRun->getFont()->setBold(true);
                                    $textRun->getFont()->setSize(30);
                                    $textRun->getFont()->setColor( new Color( '00000000' ) );
                                    $shape->createBreak();

                                    
                                    $shape = $currentSlide->createDrawingShape();
                                    $shape->setName('Property Image1')
                                    ->setDescription('Property Image1')
                                    ->setPath('uploads/property/'.$row_main['image_1'])
                                    //->setHeight(228)
                                    //->setWidth(340)
                                    ->setWidthAndHeight(340, 228)
                                    ->setResizeProportional(true)
                                    ->setOffsetX(150)
                                    ->setOffsetY(120);
                        
                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(30);
                                    $shape->setWidth(340);
                                    $shape->setOffsetX(150);
                                    $shape->setOffsetY(340);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $textRun = $shape->createTextRun($row_main['description']);
                                    $textRun->getFont()->setSize(14);
                                    $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                    $shape->createBreak();

                                    if ($row_main['image_2'])
                                    {
                                          $shape = $currentSlide->createDrawingShape();
                                          $shape->setName('Property Image2')
                                          ->setDescription('Property Image2')
                                          ->setPath('uploads/property/'.$row_main['image_2'])
                                          //->setHeight(228)
                                          //->setWidth(340)
                                          ->setWidthAndHeight(340, 228)
                                          ->setResizeProportional(true)
                                          ->setOffsetX(150)
                                          ->setOffsetY(440);
                              
                                          $shape = $currentSlide->createRichTextShape();
                                          $shape->setHeight(30);
                                          $shape->setWidth(340);
                                          $shape->setOffsetX(150);
                                          $shape->setOffsetY(440);
                                          $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                          $textRun = $shape->createTextRun($row_main['description']);
                                          $textRun->getFont()->setSize(14);
                                          $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                          $shape->createBreak();
                                    }
                                    if ($row_main['image_3'])
                                    {
                                          $shape = $currentSlide->createDrawingShape();
                                          $shape->setName('Property Image3')
                                          ->setDescription('Property Image3')
                                          ->setPath('uploads/property/'.$row_main['image_3'])
                                          //->setHeight(228)
                                          //->setWidth(340)
                                          ->setWidthAndHeight(340, 228)
                                          ->setResizeProportional(true)
                                          ->setOffsetX(450)
                                          ->setOffsetY(180);
                              
                                          $shape = $currentSlide->createRichTextShape();
                                          $shape->setHeight(30);
                                          $shape->setWidth(340);
                                          $shape->setOffsetX(150);
                                          $shape->setOffsetY(440);
                                          $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                          $textRun = $shape->createTextRun($row_main['description']);
                                          $textRun->getFont()->setSize(14);
                                          $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                          $shape->createBreak();
                                    }
                                    if ($row_main['image_4'])
                                    {
                                          $shape = $currentSlide->createDrawingShape();
                                          $shape->setName('Property Image4')
                                          ->setDescription('Property Image4')
                                          ->setPath('uploads/property/'.$row_main['image_4'])
                                          //->setHeight(228)
                                          //->setWidth(340)
                                          ->setWidthAndHeight(340, 228)
                                          ->setResizeProportional(true)
                                          ->setOffsetX(550)
                                          ->setOffsetY(450);
                              
                                          $shape = $currentSlide->createRichTextShape();
                                          $shape->setHeight(30);
                                          $shape->setWidth(340);
                                          $shape->setOffsetX(150);
                                          $shape->setOffsetY(440);
                                          $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                          $textRun = $shape->createTextRun($row_main['description']);
                                          $textRun->getFont()->setSize(14);
                                          $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                          $shape->createBreak();
                                    }


                              }
                              if ($slide_no==98) 
                              {
                                    // FIFTH SLIDE

                                    $currentSlide = createTemplatedSlide($objPHPPowerPoint,5);

                                    $shape = $currentSlide->createDrawingShape();
                                    $shape->setName('Location Image')
                                    ->setDescription('Location Image')
                                    ->setPath('uploads/property/location.jpg')
                                    ->setHeight(505)
                                    ->setWidth(752)
                                    ->setOffsetX(120)
                                    ->setOffsetY(150);

                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(70);
                                    $shape->setWidth(880);
                                    $shape->setOffsetX(16);
                                    $shape->setOffsetY(15);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                                    $textRun = $shape->createTextRun('Location Map');
                                    $textRun->getFont()->setBold(true);
                                    $textRun->getFont()->setSize(30);
                                    $textRun->getFont()->setColor( new Color( 'FFFFFFFF' ) );
                                    $shape->createBreak();
                              }

                              if ($slide_no==99) 
                              {
                                    // SIX SLIDE

                                    $currentSlide = createTemplatedSlide($objPHPPowerPoint,6);

                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(100);
                                    $shape->setWidth(900);
                                    $shape->setOffsetX(20);
                                    $shape->setOffsetY(150);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $textRun = $shape->createTextRun('THANK YOU !!!');
                                    $textRun->getFont()->setSize(50);
                                    $textRun->getFont()->setColor( new Color( 'FFC00000' ) );
                                    $shape->createBreak();


                                    $shape = $currentSlide->createDrawingShape();
                                    $shape->setName('Address')
                                    ->setDescription('Address')
                                    ->setPath('../../dist/img/thanks.jpg')
                                    ->setHeight(271)
                                    ->setWidth(251)
                                    ->setOffsetX(350)
                                    ->setOffsetY(250);

                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(100);
                                    $shape->setWidth(900);
                                    $shape->setOffsetX(20);
                                    $shape->setOffsetY(550);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $textRun = $shape->createTextRun('402/403, Metro Avenue, Opp Guru Nanak Petrol Pump, Near Western Exp Highway, Andheri East, Mumbai-400 099.');
                                    $textRun->getFont()->setSize(14);
                                    $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                    $shape->createBreak();

                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(100);
                                    $shape->setWidth(900);
                                    $shape->setOffsetX(20);
                                    $shape->setOffsetY(580);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $textRun = $shape->createTextRun('Call : 75063 37725
                                    ');
                                    $textRun->getFont()->setSize(14);
                                    $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                    $shape->createBreak();

                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(100);
                                    $shape->setWidth(900);
                                    $shape->setOffsetX(20);
                                    $shape->setOffsetY(600);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $textRun = $shape->createTextRun('Website : www.sqft.co.in');
                                    $textRun->getFont()->setSize(14)
                                                      ->setColor(new Color( 'FFC00000' ));
                                    $textRun->getHyperlink()->setUrl('andhericommercial@rdbrothers.com
                                    ')
                                                            ->setTooltip('mail id');
                                    $shape->createBreak();
                              }
                        }
                  }
            }
            $oWriterPPTX = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
            //$oWriterPPTX->save(__DIR__ . "/sample.pptx");
            $ds        = DIRECTORY_SEPARATOR;
            $oWriterPPTX->save('uploads'.$ds.'reports'.$ds.$filename); 
            return 'done'; 
      }
}


/**
 * Creates a templated slide
 *
 * @param PHPPowerPoint $objPHPPowerPoint
 * @return PHPPowerPoint_Slide
 */

function createTemplatedSlide(PhpOffice\PhpPresentation\PhpPresentation $objPHPPowerPoint, $slide_count){
      // Create slide
      $slide = $objPHPPowerPoint->createSlide();
      // Add background image
      if ($slide_count>1)
      {
          $slide->createDrawingShape()
              ->setName('Background')
              ->setDescription('Background')
              ->setPath('../../dist/img/mini_logo.png')
              ->setWidth(30)
              ->setHeight(30)
              ->setOffsetX(890)
              ->setOffsetY(10);
      }
      $shape = $slide->createRichTextShape();
      $shape->setHeight(718)
          ->setWidth(960)
          ->setOffsetX(10)
          ->setOffsetY(10);
  
      $note = $slide->getNote();
      $text = $note->createRichTextShape()->setHeight(300)->setWidth(600);
      $text->createTextRun("Slides_".$slide_count);
      $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setLineWidth(10)->getColor()->setARGB('FFC00000');
      // Return slide
      return $slide;
  }

  


/*





class ppt {

      public static function create_ppt($module_name,$id,$data,$filename) 
      {

            if($module_name == 'property')
            {
            $sql  = "";
            $db = new DbHandler();

            $sql = "SELECT *,a.exp_price, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, CONCAT(h.salu,' ',h.fname,' ',h.lname) as assign_to, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%m') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%m') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.property_id in($data) GROUP BY a.property_id LIMIT 1 ";
            
            $propertydata = $db->getAllRecords($sql);
            $property_id = 0;
            if ($propertydata)
            {
                  $property_id = $propertydata[0]['property_id'];
                  $sql = "SELECT * from attachments WHERE category = 'property' and category_id = $property_id LIMIT 4";
                  $stmt = $db->getRows($sql);
                  $count = 1;
                  if($stmt->num_rows > 0)
                  {
                        while($row = $stmt->fetch_assoc())
                        {                          
                
                              $objPHPPowerPoint = new PhpPresentation();
                              $objPHPPowerPoint->getLayout()->setDocumentLayout(['cx' => 978, 'cy' => 728], true)->setCX(978, DocumentLayout::UNIT_PIXEL)->setCY(728, DocumentLayout::UNIT_PIXEL);


                              /*$objPHPPowerPoint->getProperties()->setCreator('Maarten Balliauw')
                                                            ->setLastModifiedBy('Maarten Balliauw')
                                                            ->setTitle('Office 2007 PPTX Test Document')
                                                            ->setSubject('Office 2007 PPTX Test Document')
                                                            ->setDescription('Test document for Office 2007 PPTX, generated using PHP classes.')
                                                            ->setKeywords('office 2007 openxml php')
                                                            ->setCategory('Test result file');*/

                          /*  $objPHPPowerPoint->removeSlideByIndex(0);


                              // FIRST SLIDE

                              $currentSlide = createTemplatedSlide($objPHPPowerPoint,1); 

                              $shape = $currentSlide->createDrawingShape();
                              $shape->setName('RD BROTHERS')
                              ->setDescription('RD BROTHERS')
                              ->setPath('../../dist/img/ppt_main.jpg')
                              ->setHeight(702)
                              ->setWidth(944)
                              ->setOffsetX(16)
                              ->setOffsetY(16);

                              // SECOND SLIDE

                              $currentSlide = createTemplatedSlide($objPHPPowerPoint,2);

                              $shape = $currentSlide->createDrawingShape();
                              $shape->setName('Property Image')
                              ->setDescription('Property Image')

                              ->setPath('uploads/property/'.$propertydata[0]['filenames'])
                              ->setHeight(505)
                              ->setWidth(752)
                              ->setOffsetX(120)
                              ->setWidthAndHeight(752, 505)
                              ->setResizeProportional(true)
                              ->setOffsetY(150);

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(70);
                              $shape->setWidth(880);
                              $shape->setOffsetX(16);
                              $shape->setOffsetY(15);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                              $textRun = $shape->createTextRun($propertydata[0]['building_plot']);
                              $textRun->getFont()->setBold(true);
                              $textRun->getFont()->setSize(30);
                              $textRun->getFont()->setColor( new Color( 'FFFFFFFF' ) );
                              $shape->createBreak();


                              // THIRD SLIDE

                              $currentSlide = createTemplatedSlide($objPHPPowerPoint,3); 

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(70);
                              $shape->setWidth(880);
                              $shape->setOffsetX(16);
                              $shape->setOffsetY(15);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                              $textRun = $shape->createTextRun('Commercial Terms');
                              $textRun->getFont()->setBold(true);
                              $textRun->getFont()->setSize(30);
                              $textRun->getFont()->setColor( new Color( 'FFFFFFFF' ) );
                              $shape->createBreak();

                              $shape = $currentSlide->createTableShape(2);
                              $shape->setHeight(600);
                              $shape->setWidth(780);
                              $shape->setOffsetX(100);
                              $shape->setOffsetY(150);

                              $row = $shape->createRow();
                              //$row->getFill()->setFillType(Fill::FILL_SOLID)
                              //->setStartColor(new Color('FF000000'))
                              //->setEndColor(new Color('FF000000'));

                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Carpet Area (sqft)')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $tvar = 0;
                              if ($propertydata[0]['carp_area'])
                              {
                                    $tvar = $propertydata[0]['carp_area'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);


                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Saleable Area (sqft)')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $tvar = 0;
                              if ($propertydata[0]['sale_area'])
                              {
                                    $tvar = $propertydata[0]['sale_area'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);

                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Proposed Floors')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $tvar = 0;
                              if ($propertydata[0]['floor'])
                              {
                                    $tvar = $propertydata[0]['floor'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);
                              
                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Quoted Rent on Saleable Area')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $tvar = 0;
                              if ($propertydata[0]['exp_rent'])
                              {
                                    $tvar = $propertydata[0]['exp_rent'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);


                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Security Deposit')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $tvar = 0;
                              if ($propertydata[0]['security_depo'])
                              {
                                    $tvar = $propertydata[0]['security_depo'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);


                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Lock in period')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $tvar = 0;
                              if ($propertydata[0]['lock_per'])
                              {
                                    $tvar = $propertydata[0]['lock_per'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);

                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Lease Tenure')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $tvar = 0;
                              if ($propertydata[0]['tenure_year'])
                              {
                                    $tvar = $propertydata[0]['tenure_year'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);

                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Rent Escalation')->getFont()->setSize(25);
                              $oCell = $row->nextCell();

                              $tvar = 0;
                              if ($propertydata[0]['rent_esc'])
                              {
                                    $tvar = $propertydata[0]['rent_esc'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);


                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Location')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun($propertydata[0]['locality'].','.$propertydata[0]['city'])->getFont()->setSize(25);

                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Furniture Detail')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun($propertydata[0]['furniture'])->getFont()->setSize(25);


                              // FOURTH SLIDE

                              $currentSlide = createTemplatedSlide($objPHPPowerPoint,4); 

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(70);
                              $shape->setWidth(880);
                              $shape->setOffsetX(16);
                              $shape->setOffsetY(15);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                              $textRun = $shape->createTextRun('Photos');
                              $textRun->getFont()->setBold(true);
                              $textRun->getFont()->setSize(30);
                              $textRun->getFont()->setColor( new Color( 'FFFFFFFF' ) );
                              $shape->createBreak();

                              $sql = "SELECT * from attachments WHERE category = 'property' and category_id = $property_id LIMIT 4";
                              $stmt = $db->getRows($sql);
                              $count = 1;

                              if($stmt->num_rows > 0)
                              {
                                    while($row = $stmt->fetch_assoc())
                                    {
                                          if ($count==1)
                                          {
                                                $shape = $currentSlide->createDrawingShape();
                                                $shape->setName('Property Image1')
                                                ->setDescription('Property Image1')
                                                ->setPath('uploads/property/'.$row['filenames'])
                                                //->setHeight(228)
                                                //->setWidth(340)
                                                ->setWidthAndHeight(340, 228)
                                                ->setResizeProportional(true)
                                                ->setOffsetX(150)

                                                ->setOffsetY(120);
                                    
                                                $shape = $currentSlide->createRichTextShape();
                                                $shape->setHeight(30);
                                                $shape->setWidth(340);
                                                $shape->setOffsetX(150);
                                                $shape->setOffsetY(340);
                                                $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                                $textRun = $shape->createTextRun($row['description']);
                                                $textRun->getFont()->setSize(14);
                                                $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                                $shape->createBreak();
                                          }     
                                          if ($count==2)
                                          {
                                                $shape = $currentSlide->createDrawingShape();
                                                $shape->setName('Property Image2')
                                                ->setDescription('Property Image2')
                                                ->setPath('uploads/property/'.$row['filenames'])
                                                ->setHeight(228)
                                                ->setWidth(340)
                                                ->setWidthAndHeight(340, 228)
                                                ->setResizeProportional(true)
                                                ->setOffsetX(510)
                                                ->setOffsetY(120);
                                    
                                                $shape = $currentSlide->createRichTextShape();
                                                $shape->setHeight(30);
                                                $shape->setWidth(340);
                                                $shape->setOffsetX(510);
                                                $shape->setOffsetY(340);
                                                $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                                $textRun = $shape->createTextRun($row['description']);
                                                $textRun->getFont()->setSize(14);
                                                $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                                $shape->createBreak();
                                          }
                                          if ($count==3)
                                          {
                                                $shape = $currentSlide->createDrawingShape();
                                                $shape->setName('Property Image3')
                                                ->setDescription('Property Image3')
                                                ->setPath('uploads/property/'.$row['filenames'])
                                                ->setHeight(228)
                                                ->setWidth(340)
                                                ->setWidthAndHeight(340, 228)
                                                ->setResizeProportional(true)
                                                ->setOffsetX(150)
                                                ->setOffsetY(410);
                                    
                                                $shape = $currentSlide->createRichTextShape();
                                                $shape->setHeight(30);
                                                $shape->setWidth(340);
                                                $shape->setOffsetX(150);
                                                $shape->setOffsetY(630);
                                                $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                                $textRun = $shape->createTextRun($row['description']);
                                                $textRun->getFont()->setSize(14);
                                                $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                                $shape->createBreak();
                                          }
                                          if ($count==4)
                                          {
                                                $shape = $currentSlide->createDrawingShape();
                                                $shape->setName('Property Image4')
                                                ->setDescription('Property Image4')
                                                ->setPath('uploads/property/'.$row['filenames'])
                                                ->setHeight(228)
                                                ->setWidth(340)
                                                ->setWidthAndHeight(340, 228)
                                                ->setResizeProportional(true)
                                                ->setOffsetX(510)
                                                ->setOffsetY(410);
                                    
                                                $shape = $currentSlide->createRichTextShape();
                                                $shape->setHeight(30);
                                                $shape->setWidth(340);
                                                $shape->setOffsetX(510);
                                                $shape->setOffsetY(630);
                                                $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                                $textRun = $shape->createTextRun($row['description']);
                                                $textRun->getFont()->setSize(14);
                                                $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                                $shape->createBreak();

                                          }
                                          $count++;

                                    }
                              }


                              // FIFTH SLIDE

                              $currentSlide = createTemplatedSlide($objPHPPowerPoint,5);

                              $shape = $currentSlide->createDrawingShape();
                              $shape->setName('Location Image')
                              ->setDescription('Location Image')
                              ->setPath('uploads/property/location.jpg')
                              ->setHeight(505)
                              ->setWidth(752)
                              ->setOffsetX(120)
                              ->setOffsetY(150);

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(70);
                              $shape->setWidth(880);
                              $shape->setOffsetX(16);
                              $shape->setOffsetY(15);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                              $textRun = $shape->createTextRun('Location Map');
                              $textRun->getFont()->setBold(true);
                              $textRun->getFont()->setSize(30);
                              $textRun->getFont()->setColor( new Color( 'FFFFFFFF' ) );
                              $shape->createBreak();


                              // SIX SLIDE

                              $currentSlide = createTemplatedSlide($objPHPPowerPoint,6);

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(100);
                              $shape->setWidth(900);
                              $shape->setOffsetX(20);
                              $shape->setOffsetY(150);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $textRun = $shape->createTextRun('THANK YOU !!!');
                              $textRun->getFont()->setSize(50);
                              $textRun->getFont()->setColor( new Color( 'FFC00000' ) );
                              $shape->createBreak();


                              $shape = $currentSlide->createDrawingShape();
                              $shape->setName('Address')
                              ->setDescription('Address')
                              ->setPath('../../dist/img/thanks.jpg')
                              ->setHeight(271)
                              ->setWidth(251)
                              ->setOffsetX(350)
                              ->setOffsetY(250);

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(100);
                              $shape->setWidth(900);
                              $shape->setOffsetX(20);
                              $shape->setOffsetY(550);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $textRun = $shape->createTextRun('402/403, Metro Avenue, Opp Guru Nanak Petrol Pump, Near Western Exp Highway, Andheri East, Mumbai-400 099.');
                              $textRun->getFont()->setSize(14);
                              $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                              $shape->createBreak();

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(100);
                              $shape->setWidth(900);
                              $shape->setOffsetX(20);
                              $shape->setOffsetY(580);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $textRun = $shape->createTextRun('Call : 75063 37725
                              ');
                              $textRun->getFont()->setSize(14);
                              $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                              $shape->createBreak();

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(100);
                              $shape->setWidth(900);
                              $shape->setOffsetX(20);
                              $shape->setOffsetY(600);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $textRun = $shape->createTextRun('Website : www.sqft.co.in');
                              $textRun->getFont()->setSize(14)
                                                 ->setColor(new Color( 'FFC00000' ));
                              $textRun->getHyperlink()->setUrl('andhericommercial@rdbrothers.com
                              ')
                                                      ->setTooltip('mail id');
                              $shape->createBreak();
                        }
                  }
            }
            }
            // Save files
            /*$basename = basename(__FILE__, '.php');
            $formats = array('PowerPoint2007' => 'pptx', 'ODPresentation' => 'odp');

            foreach ($formats as $format => $extension) {
            $objWriter = PHPPowerPoint_IOFactory::createWriter($objPHPPowerPoint, $format);
            $objWriter->save("results/{$basename}.{$extension}");
            }*/

            /*$oWriterPPTX = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
            //$oWriterPPTX->save(__DIR__ . "/sample.pptx");
            $ds        = DIRECTORY_SEPARATOR;
            $oWriterPPTX->save('uploads'.$ds.'reports'.$ds.$filename); 
            return 'done'; 
            
      }//if($module_name == 'property') code ends

      /*if($module_name == 'project')
      {
            $sql  = "";
            $db = new DbHandler();

            $sql = "SELECT *, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, CONCAT(h.salu,' ',h.fname,' ',h.lname) as assign_to, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%m') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%m') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date  from project as a  LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.project_id = f.category_id and f.category='project' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.developer_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.project_id in($data) GROUP BY a.project_id LIMIT 1 ";
            
            $projectdata = $db->getAllRecords($sql);
            $project_id = 0;
            if ($projectdata)
            {
                  $project_id = $projectdata[0]['project_id'];
                  $sql = "SELECT * from attachments WHERE category = 'project' and category_id = $project_id LIMIT 4";
                  $stmt = $db->getRows($sql);
                  $count = 1;
                  if($stmt->num_rows > 0)
                  {
                        while($row = $stmt->fetch_assoc())
                        {                          
                
                              $objPHPPowerPoint = new PhpPresentation();
                              $objPHPPowerPoint->getLayout()->setDocumentLayout(['cx' => 978, 'cy' => 728], true)->setCX(978, DocumentLayout::UNIT_PIXEL)->setCY(728, DocumentLayout::UNIT_PIXEL);


                              /*$objPHPPowerPoint->getProperties()->setCreator('Maarten Balliauw')
                                                            ->setLastModifiedBy('Maarten Balliauw')
                                                            ->setTitle('Office 2007 PPTX Test Document')
                                                            ->setSubject('Office 2007 PPTX Test Document')
                                                            ->setDescription('Test document for Office 2007 PPTX, generated using PHP classes.')
                                                            ->setKeywords('office 2007 openxml php')
                                                            ->setCategory('Test result file');*/

              /*                $objPHPPowerPoint->removeSlideByIndex(0);


                              // FIRST SLIDE

                              $currentSlide = createTemplatedSlide($objPHPPowerPoint,1); 

                              $shape = $currentSlide->createDrawingShape();
                              $shape->setName('SQFT')
                              ->setDescription('SQFT')
                              ->setPath('../../dist/img/sqft.jpg')
                              ->setHeight(702)
                              ->setWidth(944)
                              ->setOffsetX(16)
                              ->setOffsetY(16);

                              // SECOND SLIDE

                              $currentSlide = createTemplatedSlide($objPHPPowerPoint,2);

                              $shape = $currentSlide->createDrawingShape();
                              $shape->setName('Project Image')
                              ->setDescription('Project Image')

                              ->setPath('uploads/project/'.$projectdata[0]['filenames'])
                              ->setHeight(505)
                              ->setWidth(752)
                              ->setOffsetX(120)
                              ->setWidthAndHeight(752, 505)
                              ->setResizeProportional(true)
                              ->setOffsetY(150);

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(70);
                              $shape->setWidth(880);
                              $shape->setOffsetX(16);
                              $shape->setOffsetY(15);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                              $textRun = $shape->createTextRun($projectdata[0]['project_name'].':'.$projectdata[0]['project_type'].' for '.$projectdata[0]['project_for']);
                              $textRun->getFont()->setBold(true);
                              $textRun->getFont()->setSize(30);
                              $textRun->getFont()->setColor( new Color( 'FFFFFFFF' ) );
                              $shape->createBreak();


                              // THIRD SLIDE

                              $currentSlide = createTemplatedSlide($objPHPPowerPoint,3); 

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(70);
                              $shape->setWidth(880);
                              $shape->setOffsetX(16);
                              $shape->setOffsetY(15);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                              $textRun = $shape->createTextRun('Commercial Terms');
                              $textRun->getFont()->setBold(true);
                              $textRun->getFont()->setSize(30);
                              $textRun->getFont()->setColor( new Color( 'FFFFFFFF' ) );
                              $shape->createBreak();

                              $shape = $currentSlide->createTableShape(2);
                              $shape->setHeight(600);
                              $shape->setWidth(780);
                              $shape->setOffsetX(100);
                              $shape->setOffsetY(150);

                              $row = $shape->createRow();
                              //$row->getFill()->setFillType(Fill::FILL_SOLID)
                              //->setStartColor(new Color('FF000000'))
                              //->setEndColor(new Color('FF000000'));

                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Total Area(sqft)')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $tvar = 0;
                              if ($projectdata[0]['tot_area'])
                              {
                                    $tvar = $projectdata[0]['tot_area'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);


                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Total Unit (sqft)')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $tvar = 0;
                              if ($projectdata[0]['tot_unit'])
                              {
                                    $tvar = $projectdata[0]['tot_unit'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);

                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('No of Floors')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $tvar = 0;
                              if ($projectdata[0]['numof_floor'])
                              {
                                    $tvar = $projectdata[0]['numof_floor'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);
                              
                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Package Price')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $tvar = 0;
                              if ($projectdata[0]['pack_price'])
                              {
                                    $tvar = $projectdata[0]['pack_price'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);


                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Maintenance Charges')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $tvar = 0;
                              if ($projectdata[0]['maintenance_charges'])
                              {
                                    $tvar = $projectdata[0]['maintenance_charges'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);


                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Project Tax')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $tvar = 0;
                              if ($projectdata[0]['prop_tax'])
                              {
                                    $tvar = $projectdata[0]['prop_tax'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);

                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Transfer Charges')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $tvar = 0;
                              if ($projectdata[0]['transfer_charge'])
                              {
                                    $tvar = $projectdata[0]['transfer_charge'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);

                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Parking Charges')->getFont()->setSize(25);
                              $oCell = $row->nextCell();

                              $tvar = 0;
                              if ($projectdata[0]['park_charge'])
                              {
                                    $tvar = $projectdata[0]['park_charge'];
                              }
                              $oCell->createTextRun($tvar)->getFont()->setSize(25);


                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Location')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun($projectdata[0]['locality'].','.$projectdata[0]['city'])->getFont()->setSize(25);

                              $row = $shape->createRow();
                              $row->setHeight(20);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun('Project Status')->getFont()->setSize(25);
                              $oCell = $row->nextCell();
                              $oCell->createTextRun($projectdata[0]['proj_status'])->getFont()->setSize(25);


                              // FOURTH SLIDE

                              $currentSlide = createTemplatedSlide($objPHPPowerPoint,4); 

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(70);
                              $shape->setWidth(880);
                              $shape->setOffsetX(16);
                              $shape->setOffsetY(15);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                              $textRun = $shape->createTextRun('Photos');
                              $textRun->getFont()->setBold(true);
                              $textRun->getFont()->setSize(30);
                              $textRun->getFont()->setColor( new Color( 'FFFFFFFF' ) );
                              $shape->createBreak();

                              $sql = "SELECT * from attachments WHERE category = 'project' and category_id = $project_id LIMIT 4";
                              $stmt = $db->getRows($sql);
                              $count = 1;

                              if($stmt->num_rows > 0)
                              {
                                    while($row = $stmt->fetch_assoc())
                                    {
                                          if ($count==1)
                                          {
                                                $shape = $currentSlide->createDrawingShape();
                                                $shape->setName('Project Image1')
                                                ->setDescription('Project Image1')
                                                ->setPath('uploads/project/'.$row['filenames'])
                                                //->setHeight(228)
                                                //->setWidth(340)
                                                ->setWidthAndHeight(340, 228)
                                                ->setResizeProportional(true)
                                                ->setOffsetX(150)

                                                ->setOffsetY(120);
                                    
                                                $shape = $currentSlide->createRichTextShape();
                                                $shape->setHeight(30);
                                                $shape->setWidth(340);
                                                $shape->setOffsetX(150);
                                                $shape->setOffsetY(340);
                                                $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                                $textRun = $shape->createTextRun($row['description']);
                                                $textRun->getFont()->setSize(14);
                                                $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                                $shape->createBreak();
                                          }     
                                          if ($count==2)
                                          {
                                                $shape = $currentSlide->createDrawingShape();
                                                $shape->setName('Project Image2')
                                                ->setDescription('Project Image2')
                                                ->setPath('uploads/project/'.$row['filenames'])
                                                ->setHeight(228)
                                                ->setWidth(340)
                                                ->setWidthAndHeight(340, 228)
                                                ->setResizeProportional(true)
                                                ->setOffsetX(510)
                                                ->setOffsetY(120);
                                    
                                                $shape = $currentSlide->createRichTextShape();
                                                $shape->setHeight(30);
                                                $shape->setWidth(340);
                                                $shape->setOffsetX(510);
                                                $shape->setOffsetY(340);
                                                $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                                $textRun = $shape->createTextRun($row['description']);
                                                $textRun->getFont()->setSize(14);
                                                $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                                $shape->createBreak();
                                          }
                                          if ($count==3)
                                          {
                                                $shape = $currentSlide->createDrawingShape();
                                                $shape->setName('Project Image3')
                                                ->setDescription('Project Image3')
                                                ->setPath('uploads/project/'.$row['filenames'])
                                                ->setHeight(228)
                                                ->setWidth(340)
                                                ->setWidthAndHeight(340, 228)
                                                ->setResizeProportional(true)
                                                ->setOffsetX(150)
                                                ->setOffsetY(410);
                                    
                                                $shape = $currentSlide->createRichTextShape();
                                                $shape->setHeight(30);
                                                $shape->setWidth(340);
                                                $shape->setOffsetX(150);
                                                $shape->setOffsetY(630);
                                                $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                                $textRun = $shape->createTextRun($row['description']);
                                                $textRun->getFont()->setSize(14);
                                                $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                                $shape->createBreak();
                                          }
                                          if ($count==4)
                                          {
                                                $shape = $currentSlide->createDrawingShape();
                                                $shape->setName('Project Image4')
                                                ->setDescription('Project Image4')
                                                ->setPath('uploads/project/'.$row['filenames'])
                                                ->setHeight(228)
                                                ->setWidth(340)
                                                ->setWidthAndHeight(340, 228)
                                                ->setResizeProportional(true)
                                                ->setOffsetX(510)
                                                ->setOffsetY(410);
                                    
                                                $shape = $currentSlide->createRichTextShape();
                                                $shape->setHeight(30);
                                                $shape->setWidth(340);
                                                $shape->setOffsetX(510);
                                                $shape->setOffsetY(630);
                                                $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                                $textRun = $shape->createTextRun($row['description']);
                                                $textRun->getFont()->setSize(14);
                                                $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                                $shape->createBreak();

                                          }
                                          $count++;

                                    }
                              }


                              // FIFTH SLIDE

                              $currentSlide = createTemplatedSlide($objPHPPowerPoint,5);

                              $shape = $currentSlide->createDrawingShape();
                              $shape->setName('Location Image')
                              ->setDescription('Location Image')
                              ->setPath('uploads/project/location.jpg')
                              ->setHeight(505)
                              ->setWidth(752)
                              ->setOffsetX(120)
                              ->setOffsetY(150);

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(70);
                              $shape->setWidth(880);
                              $shape->setOffsetX(16);
                              $shape->setOffsetY(15);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                              $textRun = $shape->createTextRun('Location Map');
                              $textRun->getFont()->setBold(true);
                              $textRun->getFont()->setSize(30);
                              $textRun->getFont()->setColor( new Color( 'FFFFFFFF' ) );
                              $shape->createBreak();


                              // SIX SLIDE

                              $currentSlide = createTemplatedSlide($objPHPPowerPoint,6);

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(100);
                              $shape->setWidth(900);
                              $shape->setOffsetX(20);
                              $shape->setOffsetY(150);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $textRun = $shape->createTextRun('THANK YOU !!!');
                              $textRun->getFont()->setSize(50);
                              $textRun->getFont()->setColor( new Color( 'FFC00000' ) );
                              $shape->createBreak();


                              $shape = $currentSlide->createDrawingShape();
                              $shape->setName('Address')
                              ->setDescription('Address')
                              ->setPath('../../dist/img/sqftlogo.jpg')
                              ->setHeight(271)
                              ->setWidth(251)
                              ->setOffsetX(350)
                              ->setOffsetY(250);

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(100);
                              $shape->setWidth(900);
                              $shape->setOffsetX(20);
                              $shape->setOffsetY(550);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $textRun = $shape->createTextRun('402/403, Metro Avenue, Opp Guru Nanak Petrol Pump, Near Western Exp Highway, Andheri East, Mumbai-400 099.');
                              $textRun->getFont()->setSize(14);
                              $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                              $shape->createBreak();

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(100);
                              $shape->setWidth(900);
                              $shape->setOffsetX(20);
                              $shape->setOffsetY(580);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $textRun = $shape->createTextRun('Mob : 8879634179');
                              $textRun->getFont()->setSize(14);
                              $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                              $shape->createBreak();

                              $shape = $currentSlide->createRichTextShape();
                              $shape->setHeight(100);
                              $shape->setWidth(900);
                              $shape->setOffsetX(20);
                              $shape->setOffsetY(600);
                              $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                              $textRun = $shape->createTextRun('Website : www.sqft.co.in');
                              $textRun->getFont()->setSize(14)
                                                 ->setColor(new Color( 'FFC00000' ));
                              $textRun->getHyperlink()->setUrl('http://sqft.co.in')
                                                      ->setTooltip('Sqft Website');
                              $shape->createBreak();
                        }
                  }
            }
            // Save files
            /*$basename = basename(__FILE__, '.php');
            $formats = array('PowerPoint2007' => 'pptx', 'ODPresentation' => 'odp');

            foreach ($formats as $format => $extension) {
            $objWriter = PHPPowerPoint_IOFactory::createWriter($objPHPPowerPoint, $format);
            $objWriter->save("results/{$basename}.{$extension}");
            }*/

            /*$oWriterPPTX = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
            //$oWriterPPTX->save(__DIR__ . "/sample.pptx");
            $ds        = DIRECTORY_SEPARATOR;
            $oWriterPPTX->save('uploads'.$ds.'reports'.$ds.$filename); 
            return 'done'; 
      }//if($module_name== 'project') code ends*/
      /*}*/
/*}
/*
class ppt {
      public static function create_ppt($module_name,$id,$data,$filename) 
      {
            if($module_name == 'property')
            {
                  $sql  = "";
                  $db = new DbHandler();

                  $objPHPPowerPoint = new PhpPresentation();
                  $objPHPPowerPoint->getLayout()->setDocumentLayout(['cx' => 978, 'cy' => 728], true)->setCX(978, DocumentLayout::UNIT_PIXEL)->setCY(728, DocumentLayout::UNIT_PIXEL);
                  $objPHPPowerPoint->removeSlideByIndex(0);
                  $sql = "SELECT * FROM report_template ORDER BY slide_no";
                  $stmt = $db->getRows($sql);
                  if($stmt->num_rows > 0)
                  {
                        while($row = $stmt->fetch_assoc())
                        { 
                              $slide_no = $row['slide_no'];
                              if ($slide_no==1)
                              {
                                    $currentSlide = createTemplatedSlide($objPHPPowerPoint,$slide_no); 
                                    $shape = $currentSlide->createDrawingShape();
                                    $shape->setName('RD BROTHERS')
                                    ->setDescription('RD BROTHERS')
                                    ->setPath('uploads/property/'.$row['image_1'])
                                    ->setHeight(702)
                                    ->setWidth(944)
                                    ->setOffsetX(16)
                                    ->setOffsetY(16);
                              }
                              if ($slide_no==2)
                              {
                                    $currentSlide = createTemplatedSlide($objPHPPowerPoint,$slide_no); 
                                    $shape = $currentSlide->createDrawingShape();
                                    $shape->setName($row['description'])
                                    ->setDescription($row['description'])
                                    ->setPath('uploads/property/'.$row['image_1'])
                                    ->setHeight(702)
                                    ->setWidth(944)
                                    ->setOffsetX(16)
                                    ->setOffsetY(16);
                              }
                              if ($slide_no==3)
                              {
                                    $currentSlide = createTemplatedSlide($objPHPPowerPoint,$slide_no);
                                    $shape = $currentSlide->createDrawingShape();
                                    $shape->setName($row['description'])
                                    ->setDescription($row['description'])
                                    ->setPath('uploads/property/'.$row['image_1'])
                                    ->setHeight(505)
                                    ->setWidth(752)
                                    ->setOffsetX(120)
                                    ->setWidthAndHeight(752, 505)
                                    ->setResizeProportional(true)
                                    ->setOffsetY(150);

                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(70);
                                    $shape->setWidth(880);
                                    $shape->setOffsetX(16);
                                    $shape->setOffsetY(15);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                                    $textRun = $shape->createTextRun($building_plot);
                                    $textRun->getFont()->setBold(true);
                                    $textRun->getFont()->setSize(30);
                                    $textRun->getFont()->setColor( new Color( 'FFFFFFFF' ) );
                                    $shape->createBreak();

                                    /*$currentSlide = createTemplatedSlide($objPHPPowerPoint,3); 

                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(70);
                                    $shape->setWidth(880);
                                    $shape->setOffsetX(16);
                                    $shape->setOffsetY(15);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                                    $textRun = $shape->createTextRun('Commercial Terms');
                                    $textRun->getFont()->setBold(true);
                                    $textRun->getFont()->setSize(30);
                                    $textRun->getFont()->setColor( new Color( 'FFFFFFFF' ) );
                                    $shape->createBreak();

                                    $shape = $currentSlide->createTableShape(2);
                                    $shape->setHeight(600);
                                    $shape->setWidth(780);
                                    $shape->setOffsetX(100);
                                    $shape->setOffsetY(150);

                                    $row = $shape->createRow();
                                    //$row->getFill()->setFillType(Fill::FILL_SOLID)
                                    //->setStartColor(new Color('FF000000'))
                                    //->setEndColor(new Color('FF000000'));

                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Carpet Area (sqft)')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $tvar = 0;
                                    if ($carp_area)
                                    {
                                          $tvar = $carp_area;
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);


                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Saleable Area (sqft)')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $tvar = 0;
                                    if ($sale_area)
                                    {
                                          $tvar = $sale_area;
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);

                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Proposed Floors')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $tvar = 0;
                                    if ($propertydata[0]['floor'])
                                    {
                                          $tvar = $propertydata[0]['floor'];
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);
                                    
                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Quoted Rent on Saleable Area')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $tvar = 0;
                                    if ($propertydata[0]['exp_rent'])
                                    {
                                          $tvar = $propertydata[0]['exp_rent'];
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);


                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Security Deposit')->getFont()->setSize(25);
                                    $oCell = $row->nextCell(); 
                                    $tvar = 0;
                                    if ($propertydata[0]['security_depo'])
                                    {
                                          $tvar = $propertydata[0]['security_depo'];
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);


                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Lock in period')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $tvar = 0;
                                    if ($propertydata[0]['lock_per'])
                                    {
                                          $tvar = $propertydata[0]['lock_per'];
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);

                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Lease Tenure')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $tvar = 0;
                                    if ($propertydata[0]['tenure_year'])
                                    {
                                          $tvar = $propertydata[0]['tenure_year'];
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);

                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Rent Escalation')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();

                                    $tvar = 0;
                                    if ($propertydata[0]['rent_esc'])
                                    {
                                          $tvar = $propertydata[0]['rent_esc'];
                                    }
                                    $oCell->createTextRun($tvar)->getFont()->setSize(25);


                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Location')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun($propertydata[0]['locality'].','.$propertydata[0]['city'])->getFont()->setSize(25);

                                    $row = $shape->createRow();
                                    $row->setHeight(20);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun('Furniture Detail')->getFont()->setSize(25);
                                    $oCell = $row->nextCell();
                                    $oCell->createTextRun($propertydata[0]['furniture'])->getFont()->setSize(25);

                                    
                              }
                              if ($slide_no>4 && $slide_no<98)
                              {
                                    $currentSlide = createTemplatedSlide($objPHPPowerPoint,$slide_no); 

                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(70);
                                    $shape->setWidth(880);
                                    $shape->setOffsetX(16);
                                    $shape->setOffsetY(15);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                                    $textRun = $shape->createTextRun('Photos');
                                    $textRun->getFont()->setBold(true);
                                    $textRun->getFont()->setSize(30);
                                    $textRun->getFont()->setColor( new Color( 'FFFFFFFF' ) );
                                    $shape->createBreak();
                                    $shape = $currentSlide->createDrawingShape();
                                    $shape->setName('Property Image1')
                                    ->setDescription('Property Image1')
                                    ->setPath('uploads/property/'.$row['filenames'])
                                    //->setHeight(228)
                                    //->setWidth(340)
                                    ->setWidthAndHeight(340, 228)
                                    ->setResizeProportional(true)
                                    ->setOffsetX(150)
                                    ->setOffsetY(120);
                        
                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(30);
                                    $shape->setWidth(340);
                                    $shape->setOffsetX(150);
                                    $shape->setOffsetY(340);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $textRun = $shape->createTextRun($row['description']);
                                    $textRun->getFont()->setSize(14);
                                    $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                    $shape->createBreak();
                                                
                                    $shape = $currentSlide->createDrawingShape();
                                    $shape->setName('Property Image2')
                                    ->setDescription('Property Image2')
                                    ->setPath('uploads/property/'.$row['filenames'])
                                    //->setHeight(228)
                                    //->setWidth(340)
                                    ->setWidthAndHeight(340, 228)
                                    ->setResizeProportional(true)
                                    ->setOffsetX(510)
                                    ->setOffsetY(120);
                        
                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(30);
                                    $shape->setWidth(340);
                                    $shape->setOffsetX(510);
                                    $shape->setOffsetY(340);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $textRun = $shape->createTextRun($row['description']);
                                    $textRun->getFont()->setSize(14);
                                    $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                    $shape->createBreak();

                                    $shape = $currentSlide->createDrawingShape();
                                    $shape->setName('Property Image3')
                                    ->setDescription('Property Image3')
                                    ->setPath('uploads/property/'.$row['filenames'])
                                    //->setHeight(228)
                                    //->setWidth(340)
                                    ->setWidthAndHeight(340, 228)
                                    ->setResizeProportional(true)
                                    ->setOffsetX(150)
                                    ->setOffsetY(410);
                        
                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(30);
                                    $shape->setWidth(340);
                                    $shape->setOffsetX(150);
                                    $shape->setOffsetY(630);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $textRun = $shape->createTextRun($row['description']);
                                    $textRun->getFont()->setSize(14);
                                    $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                    $shape->createBreak();

                                    $shape = $currentSlide->createDrawingShape();
                                    $shape->setName('Property Image4')
                                    ->setDescription('Property Image4')
                                    ->setPath('uploads/property/'.$row['filenames'])
                                    //->setHeight(228)
                                    //->setWidth(340)
                                    ->setWidthAndHeight(340, 228)
                                    ->setResizeProportional(true)
                                    ->setOffsetX(510)
                                    ->setOffsetY(410);
                        
                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(30);
                                    $shape->setWidth(340);
                                    $shape->setOffsetX(510);
                                    $shape->setOffsetY(630);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $textRun = $shape->createTextRun($row['description']);
                                    $textRun->getFont()->setSize(14);
                                    $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                    $shape->createBreak();
                              }
                              if ($slide_no==98)
                              {
                                    $currentSlide = createTemplatedSlide($objPHPPowerPoint,$slide_no);

                                    $shape = $currentSlide->createDrawingShape();
                                    $shape->setName('Location Image')
                                    ->setDescription('Location Image')
                                    ->setPath('uploads/property/'.$row['image_1'])
                                    ->setHeight(505)
                                    ->setWidth(752)
                                    ->setOffsetX(120)
                                    ->setOffsetY(150);

                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(70);
                                    $shape->setWidth(880);
                                    $shape->setOffsetX(16);
                                    $shape->setOffsetY(15);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color( 'FF000000' ))->setEndColor(new Color( 'FF000000' ));
                                    $textRun = $shape->createTextRun('Location Map');
                                    $textRun->getFont()->setBold(true);
                                    $textRun->getFont()->setSize(30);
                                    $textRun->getFont()->setColor( new Color( 'FFFFFFFF' ) );
                                    $shape->createBreak();
                              }
                              if ($slide_no==99)
                              {
                                    $currentSlide = createTemplatedSlide($objPHPPowerPoint,$slide_no);

                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(100);
                                    $shape->setWidth(900);
                                    $shape->setOffsetX(20);
                                    $shape->setOffsetY(150);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $textRun = $shape->createTextRun('THANK YOU !!!');
                                    $textRun->getFont()->setSize(50);
                                    $textRun->getFont()->setColor( new Color( 'FFC00000' ) );
                                    $shape->createBreak();


                                    $shape = $currentSlide->createDrawingShape();
                                    $shape->setName('Address')
                                    ->setDescription('Address')
                                    ->setPath('uploads/property/'.$row['image_1'])
                                    ->setHeight(271)
                                    ->setWidth(251)
                                    ->setOffsetX(350)
                                    ->setOffsetY(250);

                                    /*$shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(100);
                                    $shape->setWidth(900);
                                    $shape->setOffsetX(20);
                                    $shape->setOffsetY(550);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $textRun = $shape->createTextRun('402/403, Metro Avenue, Opp Guru Nanak Petrol Pump, Near Western Exp Highway, Andheri East, Mumbai-400 099.');
                                    $textRun->getFont()->setSize(14);
                                    $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                    $shape->createBreak();

                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(100);
                                    $shape->setWidth(900);
                                    $shape->setOffsetX(20);
                                    $shape->setOffsetY(580);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $textRun = $shape->createTextRun('Call : 75063 37725
                                    ');
                                    $textRun->getFont()->setSize(14);
                                    $textRun->getFont()->setColor( new Color( 'FF000000' ) );
                                    $shape->createBreak();

                                    $shape = $currentSlide->createRichTextShape();
                                    $shape->setHeight(100);
                                    $shape->setWidth(900);
                                    $shape->setOffsetX(20);
                                    $shape->setOffsetY(600);
                                    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                                    $textRun = $shape->createTextRun('Website : www.sqft.co.in');
                                    $textRun->getFont()->setSize(14)
                                                            ->setColor(new Color( 'FFC00000' ));
                                    $textRun->getHyperlink()->setUrl('andhericommercial@rdbrothers.com
                                    ')->setTooltip('mail id');
                                    $shape->createBreak();
                              }
                        }
                  }
                  // Save files
                  /*$basename = basename(__FILE__, '.php');
                  $formats = array('PowerPoint2007' => 'pptx', 'ODPresentation' => 'odp');

                  foreach ($formats as $format => $extension) {
                  $objWriter = PHPPowerPoint_IOFactory::createWriter($objPHPPowerPoint, $format);
                  $objWriter->save("results/{$basename}.{$extension}");
                  }

                  $oWriterPPTX = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
                  //$oWriterPPTX->save(__DIR__ . "/sample.pptx");
                  $ds        = DIRECTORY_SEPARATOR;
                  $oWriterPPTX->save('uploads'.$ds.'reports'.$ds.$filename); 
                  return 'done'; 
            
            }
      }
}*/



?>
