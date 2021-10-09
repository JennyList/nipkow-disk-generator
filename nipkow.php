<?php
#Nipkow disk generator for mechanical TV
#adapted by Jenny List from Uwe Steinmann's PDF clock example in PHP docs
#so parts © Copyright the PHP Documentation Group, Uwe Steinmann, Jenny List

#Page width and height in point. A4=595.28x841.89, US Letter=612.00x792.00
#thanks to http://www.ros.co.nz/pdf for the dimensions
#$pagewidth = 595.28;
#$pageheight = 841.89;
#end up using page size smaller than paper because of pdf reader shrinking to printer margins.
$pagewidth = 550;
$pageheight = 700;

#disk rotation: clockwise or anticlockwise
$rotation = "anticlockwise";

#TV standard. Baird=30, nbtv.org=32
$scanlines = 32;

#Aspect ratio, width:height $aspectratio:1, for example nbtv.org is 2:3 i.e. 0.667:1
$aspectratio = 0.667;

#Disk diameter in mm. CD=120
$diameter_mm = 120;

#Diameter of hole in centre in mm. CD=15
$hole_diameter_mm = 15;

#Distance of sync hole from edge of disk in mm.
$synchole_distance = 3;

#Distance of outside scan line from edge of disk in mm.
$outsideline_distance = 6;

#take one point to be 0.3528mm, calculate radius in point
$one_point = 0.3528;
$radius = ($diameter_mm/$one_point)/2;
$hole_radius = ($hole_diameter_mm/$one_point)/2;
$centre_dot_radius = (1/$one_point)/2; #1mm dot at centre

#calculate dimensions of scanned area of disk.
#Our scanned area is not quite rectangular, only the middle line is the
# length it would be if it were. To get the width of the scanned area
# we need to calculate the length of this middle line to get our scanned height.
$degreesperdot = 360/$scanlines;
#approximating a "slice" of the disk to be roughly a triangle, calculate length of outside scan line.
$outside_line = (($diameter_mm/2) -$outsideline_distance)*sin(deg2rad($degreesperdot));  
$scanned_height = $outside_line/(1+(2*$aspectratio)*tan(deg2rad($degreesperdot)));
$scanned_width = $aspectratio * $scanned_height;
$dot_width = ($scanned_width/$scanlines)/$one_point; #in point
$outside_dot_radius = (($diameter_mm/2) - $outsideline_distance)/$one_point; #in point
$synchole_radius = (($diameter_mm/2) - $synchole_distance)/$one_point;


$pdf = pdf_new();

if (!pdf_open_file($pdf, "")) {
    print error;
    exit;
};

pdf_set_parameter($pdf, "warning", "true");

pdf_set_info($pdf, "Creator", "nipkow.php");
pdf_set_info($pdf, "Author", "Jenny List");
pdf_set_info($pdf, "Title", "Nipkow disk generator");

    pdf_begin_page($pdf,$pagewidth,$pageheight);

    #put the origin in the middle of the page
    pdf_translate($pdf, $pagewidth/2, $pageheight/2);
    pdf_save($pdf);

    #Draw full size circle
    pdf_setrgbcolor($pdf, 0.0, 0.0, 0.0);
    pdf_circle($pdf, 0, 0, $radius);
    pdf_fill($pdf);
    pdf_restore($pdf);
    pdf_save($pdf);

    #Draw centre circle
    pdf_setrgbcolor($pdf, 1.0, 1.0, 1.0);
    pdf_circle($pdf, 0, 0, $hole_radius);
    pdf_fill($pdf);
    pdf_restore($pdf);
    pdf_save($pdf);

    #Draw little dot in centre
    pdf_setrgbcolor($pdf, 0.0, 0.0, 0.0);
    pdf_circle($pdf, 0, 0, $centre_dot_radius);
    pdf_fill($pdf);
    pdf_restore($pdf);
    pdf_save($pdf);

    #Draw sync holes
    for ($alpha = 0; $alpha < (360-$degreesperdot); $alpha += $degreesperdot) { #end at 360- degreesperdot for field sync

       pdf_setrgbcolor($pdf, 1.0, 1.0, 1.0);
       pdf_circle($pdf, $synchole_radius * cos(deg2rad($alpha)),  $synchole_radius * sin(deg2rad($alpha)), $dot_width);
       pdf_fill($pdf);

    }

    pdf_restore($pdf);
    pdf_save($pdf);

    if($rotation=="clockwise"){
        $current_radius = $outside_dot_radius; 
    }
    else{
        $current_radius = $outside_dot_radius-($scanned_width/$one_point);   
    }
    #Draw scan holes
    for ($alpha = 0; $alpha < 360; $alpha += $degreesperdot) {

       pdf_setrgbcolor($pdf, 1.0, 1.0, 1.0);
       pdf_circle($pdf, $current_radius * cos(deg2rad($alpha)),  $current_radius * sin(deg2rad($alpha)), $dot_width);
       pdf_fill($pdf);
       if($rotation=="clockwise"){
          $current_radius -= $dot_width;
       }
       else{
          $current_radius += $dot_width;
       }
    }

    pdf_restore($pdf);
    pdf_save($pdf);

    #Put up some explanatory text
    $font = pdf_findfont($pdf, "Arial", "winansi", 1);
    pdf_setfont($pdf, $font, 10);
    pdf_set_value($pdf, "textrendering", 1);
    pdf_show_xy($pdf, "Nipkow disk generator 1.0", -$radius, $radius+70);
    pdf_show_xy($pdf, "Disk diameter: " . $diameter_mm . "mm  Scan lines: " . $scanlines . " Aspect ratio: " . $aspectratio . ":1", -$radius, $radius+60);
    pdf_show_xy($pdf, "Rotation: " . $rotation . " Drill diameter: " . round($scanned_width/$scanlines,3) . "mm", -$radius, $radius+50);



    pdf_restore($pdf);
    pdf_save($pdf);

    pdf_restore($pdf);
    pdf_end_page($pdf);


pdf_close($pdf);

$buf = pdf_get_buffer($pdf);
$len = strlen($buf);

header("Content-type: application/pdf");
header("Content-Length: $len");
header("Content-Disposition: inline; filename=nipkow.pdf");
print $buf;

pdf_delete($pdf);
?>

