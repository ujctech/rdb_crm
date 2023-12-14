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

                $sql = "SELECT *,a.exp_price,a.rera_num, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%m') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%m') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.property_id in($data) GROUP BY a.property_id ORDER BY CAST(a.property_id as UNSIGNED) DESC LIMIT 1";
                
                $stmt = $db->getRows($sql);
                
                if($stmt->num_rows > 0)
                {
                    while($row = $stmt->fetch_assoc())
                    { 
                                            
                    
                        $objPHPPowerPoint = new PhpPresentation();
                        $objPHPPowerPoint->getLayout()->setDocumentLayout(['cx' => 978, 'cy' => 728], true)->setCX(978, DocumentLayout::UNIT_PIXEL)->setCY(728, DocumentLayout::UNIT_PIXEL);
                                
                        $objPHPPowerPoint->removeSlideByIndex(0);


                        $currentSlide = createTemplatedSlide($objPHPPowerPoint,2);

                        $shape = $currentSlide->createDrawingShape();
                        $shape->setName('Property Image')
                        ->setDescription('Property Image')

                        ->setPath('uploads/property/'.$row['filenames'])
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
                        $textRun = $shape->createTextRun($row['project_name'].':'.$row['propsubtype'].' for '.$row['property_for']);
                        $textRun->getFont()->setBold(true);
                        $textRun->getFont()->setSize(30);
                        $textRun->getFont()->setColor( new Color( 'FFFFFFFF' ) );
                        $shape->createBreak();
                    }
                }
            
                $oWriterPPTX = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
                $ds        = DIRECTORY_SEPARATOR;
                $oWriterPPTX->save('uploads'.$ds.'reports'.$ds.$filename); 
                return 'done'; 
            }
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
            ->setPath('../../dist/img/sqftlogo1.jpg')
            ->setWidth(79)
            ->setHeight(82)
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

?>



