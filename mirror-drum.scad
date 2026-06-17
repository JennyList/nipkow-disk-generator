/*
Mirror drum generator for mechanical TV
Jenny List 2026
CC-BY-SA 4.0

Rotation is clockwise

*/

//TV standard. Baird=30, nbtv.org=32
scanlines = 32;
//diameter of drum
diameter = 200;
//Drum height
height = 50;
// mirror tilt parameters
tiltstart = -10;
tiltrange = 20;

drumradius = diameter/2;
degreesperdot = 360/scanlines;
tilt = tiltstart;
tiltstep = tiltrange/360;
mirrorheight = height;
mirrorwidth =(PI*diameter)/scanlines;
    
//Draw mirror mounts
for (alpha =[0:degreesperdot:360-degreesperdot]){ //end early for field sync
    translate([drumradius * cos(alpha), drumradius * sin(alpha)]) rotate([0,(tiltstep*alpha)+tiltstart,alpha]) cube([3,mirrorwidth,mirrorheight],true);
}

//Draw central spindle
difference(){
    union(){
        cylinder(10,drumradius,drumradius,true);   
        cylinder(height,20,20,true);
    }
    cylinder(height,10,10,true);
}
