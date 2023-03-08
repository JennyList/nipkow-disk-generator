/*
Nipkow disk generator for mechanical TV
Basic idea ported to OpenSCAD from ancient 2000-ish PHP script

Jenny List 2023
CC-BY-SA 4.0

Rotation is clockwise

*/

//TV standard. Baird=30, nbtv.org=32
scanlines = 32;
//Aspect ratio, width:height $aspectratio:1, for example nbtv.org is 2:3 i.e. 0.667:1
aspectratio = 0.667;
//Disk diameter in mm. CD=120
diameter_mm = 300;
//Diameter of hole in centre in mm. CD=15
hole_diameter_mm = 15;
//Distance of sync hole from edge of disk in mm.
synchole_distance = 3;
//Distance of outside scan line from edge of disk in mm.
outsideline_distance = 6;
//Disk thickness in mm
disk_thickness = 1;

//calculate radius
radius = diameter_mm/2;
hole_radius = hole_diameter_mm/2;

/*
calculate dimensions of scanned area of disk.
Our scanned area is not quite rectangular, only the middle line is the
length it would be if it were. To get the width of the scanned area
we need to calculate the length of this middle line to get our scanned height.
*/
degreesperdot = 360/scanlines;
//approximating a "slice" of the disk to be roughly a triangle, calculate length of outside scan line.
outside_line = (radius - outsideline_distance)*sin(degreesperdot);  
scanned_height = outside_line/(1+(2*aspectratio)*tan(degreesperdot));
scanned_width = aspectratio * scanned_height;
dot_width = 1.5 * scanned_width/scanlines; // I added a fudge factor to make the holes a little bigger to let more light in at the expense of some resolution.
outside_dot_radius = radius - outsideline_distance; 
synchole_radius = radius - synchole_distance;

difference(){
    //Draw the disk
    cylinder(disk_thickness,radius,radius,$fn=degreesperdot*4,center=true);

    //Draw centre circle
    cylinder(disk_thickness,hole_radius,hole_radius,$fn=degreesperdot*4,center=true);


    //Draw sync holes
    for (alpha =[0:degreesperdot:360-degreesperdot-1]){ //end early for field sync
        translate([synchole_radius * cos(alpha), synchole_radius * sin(alpha)]) cylinder(disk_thickness,dot_width,dot_width,$fn=degreesperdot*2,center=true);
    }
    
    //Draw scan holes
    for (scanline =[0:1:scanlines-1]){
        alpha = scanline * degreesperdot;
       translate([(outside_dot_radius-(scanline*dot_width)) * cos(alpha), (outside_dot_radius-(scanline*dot_width)) * sin(alpha)]) cylinder(disk_thickness,dot_width,dot_width,$fn=degreesperdot*2,center=true);
    }

}