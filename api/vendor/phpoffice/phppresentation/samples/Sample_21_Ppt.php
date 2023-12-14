<?php
// with your own install
//require_once 'src/PhpPresentation/Autoloader.php';
//\PhpOffice\PhpPresentation\Autoloader::register();
//require_once 'src/Common/Autoloader.php';
//\PhpOffice\Common\Autoloader::register();

// with Composer
include_once 'Sample_Header.php';

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Slide\Note;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\IOFactory;



$objPHPPowerPoint = new PhpPresentation();

// Create slide
$currentSlide = $objPHPPowerPoint->getActiveSlide();

$note = $currentSlide->getNote();
$text = $note->createRichTextShape()->setHeight(300)->setWidth(600);
$text->createTextRun("Any Note");


// Create a shape (drawing)
$shape = $currentSlide->createDrawingShape();
$shape->setName('PHPPresentation logo')
      ->setDescription('PHPPresentation logo')
     // ->setPath('../../dist/img/sqft.jpg')
      ->setHeight(708)
      ->setWidth(958);
      //->setOffsetX(10)
      //->setOffsetY(10);

$oSlide = $objPHPPowerPoint->createSlide();
$note = $oSlide->getNote();
$text = $note->createRichTextShape()->setHeight(300)->setWidth(600);
$text->createTextRun("Any Note");

$oSlide->setName('Title of the slide');
$shape = $oSlide->createDrawingShape();
$shape->getBorder()
->setLineStyle(Border::LINE_SINGLE)
->setLineWidth(4)
->getColor()->setARGB('FFCFFFFF');
$shape->setName('PHPPresentation logo')
      ->setDescription('PHPPresentation logo')
      //->setPath('uploads/property/location.jpg')
      ->setHeight(752)
      ->setWidth(505)
      ->setOffsetX(100)
      ->setOffsetY(100);

$tSlide = $objPHPPowerPoint->createSlide();
      
          // Create a shape (text)
          $note = $tSlide->getNote();
          $text = $note->createRichTextShape()->setHeight(300)->setWidth(600);
          $text->createTextRun("Slide Showing Commercial Terms");
      
          
          $shape = $tSlide->createRichTextShape();
          $shape->setHeight(100);
          $shape->setWidth(400);
          $shape->setOffsetX(100);
          $shape->setOffsetY(100);
          $shape->getActiveParagraph()->getBulletStyle()->setBulletType(Bullet::TYPE_BULLET)->setBulletColor(new Color(Color::COLOR_RED));
      
          $shape->createTextRun('Alpha');
          $shape->createParagraph()->createTextRun('Beta');
          $shape->createParagraph()->createTextRun('Delta');
          $shape->createParagraph()->createTextRun('Epsilon');

            $textRun = $shape->createTextRun('Thank you for using PHPPresentation!');
            $textRun->getFont()->setBold(true)
                  ->setSize(60)
                  ->setColor(new Color('FFE06B20'));

          
echo var_dump($shape);

$oWriterPPTX = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
$oWriterPPTX->save(__DIR__ . "/sample.pptx");

?>
