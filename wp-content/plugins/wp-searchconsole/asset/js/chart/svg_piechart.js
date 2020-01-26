// requires svg_base.js to work !

//////////////////////////////////////////////
// return the sum of elements of an array
// Vals: an array of numbers such as [1,2,3,4]
//        or a number such as 25
// return
//    the sum of values in the array (1+2+3+4)
//    or the number is a number is provided.
//////////////////////////////////////////////
function SVG_ArraySum(Vals)
   {
   var Answer=0;
   if (Vals.constructor===Array)
      {
      var i;
      for (i=0;i<Vals.length;i++)
         {
         Answer+=Vals[i];
         }
      }
   else
      {
      Answer=Vals;
      }
   return(Answer);
   }

/////////////////////////////////////////////
// return code to draw axis for pie chart (or donut chart)
// x,y:     center of the graph
// r_out:   radius of the graph
// r_in:    inner radius (0 for pie chart, n>0 for donut)
// max:     value corresponding to the full circle
//          (typical value for max is 100)
//          if you provide an array max will be the sum of the values
// from:    optional start value where axis should be drawn
//          (typical/default if not provided is 0)
// to:      optional stop value where axis should be drawn
//          (typical/default if not provided is same than "max" value)
//          use 0 for same as Max.
// Labels:  optional labels for values
/////////////////////////////////////////////
function SVG_PieChartAxis(x,y,Rout,Rin,Max,From,To,Labels)
   {
   var Angle1;
   var Angle2;
   var AngleStart;
   var AngleStop;
   var Html="";
   var i;
   var Margin=2;
   var r1;
   var r2;
   SVG_SaveSettings();
   Max=SVG_ArraySum(Max);
   if (Max <=0) { Max=100; }
   if ((typeof From=='undefined')||(From<=0)) { From=0; }
   if ((typeof To  =='undefined')||(To  <=0)) { To=Max; }
   AngleStart=From/Max*2*3.141592654;
   AngleStop =To  /Max*2*3.141592654;

   SVG_SetInk("#000000");
   SVG_SetPen(1);
   SVG_SetOpacity(1);
   SVG_SetFontSize(10);
   SVG_SetFontAlign("left");
   r1=Rout+Margin;
   r2=Rin-Margin; if (r2<0) { r2=0; }
   for (i=0;i<=100;i++)
      {
      Angle1=(AngleStart+(AngleStop-AngleStart)*(i  )/100);
      Angle2=(AngleStart+(AngleStop-AngleStart)*(i+1)/100);
      Html+=SVG_Line(x-r1*Math.cos(Angle1),y+r1*Math.sin(Angle1) , x-r1*Math.cos(Angle2),y+r1*Math.sin(Angle2));
      if (r2>0)
         {
         Html+=SVG_Line(x-r2*Math.cos(Angle1),y+r2*Math.sin(Angle1) , x-r2*Math.cos(Angle2),y+r2*Math.sin(Angle2));
         }
      }
   for (i=0;i<=10;i++)
      {
      Angle1=(AngleStart+(AngleStop-AngleStart)*i/10);
      if ((i%5)==0)
         {
         SVG_SetOpacity(0.5);
         Html+=SVG_Line(x-(r1+7)*Math.cos(Angle1),y+(r1+7)*Math.sin(Angle1) , x-r2*Math.cos(Angle1),y+r2*Math.sin(Angle1));
         SVG_SetFontAlign("right");
         SVG_SetFontRotate(Angle1/2/3.141592654*360);
         Html+=SVG_Text(x-(r1+10)*Math.cos(Angle1),y+(r1+10)*Math.sin(Angle1) , ""+(i*10)+"%");
         }
      else
         {
         SVG_SetOpacity(0.3);
         Html+=SVG_Line(x-(r1+5)*Math.cos(Angle1),y+(r1+5)*Math.sin(Angle1) , x-(r2+0)*Math.cos(Angle1),y+(r2+0)*Math.sin(Angle1));
         SVG_SetFontRotate(Angle1/2/3.141592654*360);
         SVG_SetFontAlign("right");
         Html+=SVG_Text(x-(r1+10)*Math.cos(Angle1),y+(r1+10)*Math.sin(Angle1) , ""+(i*10)+"%");
         }
      }

   SVG_RestoreSettings();
   return(Html);
   }

var SVG_PieChartShowVal_Last="";
function SVG_PieChartShowVal(Name)
   {
   if (SVG_PieChartShowVal_Last!="")
      {
      document.getElementById(SVG_PieChartShowVal_Last).style.display="None";
      }
   if (Name!="")
      {
      document.getElementById(Name).style.display="";
      }
   SVG_PieChartShowVal_Last=Name;
   }

/////////////////////////////////////////////
// return code to draw axis for pie chart (or donut chart)
// x,y:     center of the graph
// r_out:   radius of the graph
// r_in:    inner radius (0 for pie chart, n>0 for donut)
// Values:  the values to represent
// max:     value corresponding to the full circle
//          (typical value for max is 100)
//          if you provide an array max will be the sum of the values
// from:    optional start value where axis should be drawn
//          (typical/default if not provided is 0)
// to:      optional stop value where axis should be drawn
//          (typical/default if not provided is same than "max" value)
/////////////////////////////////////////////
function SVG_PieChart(x,y,Rout,Rin,Values,Max,From,To,Labels)
   {
   var Angle1;
   var Angle2;
   var AngleStart;
   var AngleStop;
   var Html="";
   var i;
   var j;
   var r1;
   var r2;
   var TotalSoFar=0;
   var P;
   var a;
   var Label;
   var w;
   var h;
   var X,Y;
   SVG_SaveSettings();
   SVG_GraphInteractiveId+=1;
   Max=SVG_ArraySum(Max);
   if (Max <=0) { Max=100; }
   if ((typeof From=='undefined')||(From<=0)) { From=0; }
   if ((typeof To  =='undefined')||(To  <=0)) { To=Max; }

   for (i=0;i<Values.length;i++)
      {
      if (Values[i]>0)
         {
         AngleStart=(From+(TotalSoFar          )/Max*(To-From))/Max*2*3.141592654;
         AngleStop =(From+(TotalSoFar+Values[i])/Max*(To-From))/Max*2*3.141592654;
         r1=Rout;
         r2=Rin; if (r2<0) { r2=0; }
         P=[];
         for (j=0;j<=50;j++)
            {
            a=AngleStart+j*(AngleStop-AngleStart)/50;
            P.push(x-r1*Math.cos(a))
            P.push(y+r1*Math.sin(a));
            }
         if (r2<=0)
            {
            P.push(x);
            P.push(y);
            }
         else
            {
            for (j=50;j>=0;j--)
               {
               a=AngleStart+j*(AngleStop-AngleStart)/50;
               P.push(x-r2*Math.cos(a))
               P.push(y+r2*Math.sin(a));
               }
            }
         SVG_SetOpacity(0.8);
         SVG_SetPen(2);
         SVG_SetInk("#ffffff");
         SVG_SetFill(SVG_NewColor(i));
         Html+=SVG_GroupStart("Pie"+SVG_GraphInteractiveId+"_"+i,"onmouseover=\"SVG_PieChartShowVal('PieVal"+SVG_GraphInteractiveId+"_"+i+"')\"");
         Html+=SVG_Poly(P);
         Html+=SVG_GroupClose("");
         }
      TotalSoFar+=Values[i];
      }

   TotalSoFar=0;
   for (i=0;i<Values.length;i++)
      {
      if (Values[i]>0)
         {
         AngleStart=(From+(TotalSoFar          )/Max*(To-From))/Max*2*3.141592654;
         AngleStop =(From+(TotalSoFar+Values[i])/Max*(To-From))/Max*2*3.141592654;
         r1=(Rout+2*Rin)/3;
         r2=(2*Rout+Rin)/3;
         a=(AngleStop+AngleStart)/2;

         Html+=SVG_GroupStart("PieVal"+SVG_GraphInteractiveId+"_"+i,"style=\"display:none;\"");

         SVG_SetOpacity(0.8);
         SVG_SetPen(0.5);
         SVG_SetInk("#000000");
         SVG_SetFill("#ffffff");
         Html+=SVG_Line(x-r1*Math.cos(a),y+r1*Math.sin(a),x-r2*Math.cos(a),y+r2*Math.sin(a));
         Html+=SVG_Circle(x-r1*Math.cos(a),y+r1*Math.sin(a),2);

         if (typeof Labels=='undefined') { Label="Value:"; }
         else if (Labels.constructor===Array) { Label=Labels[i]; }
         else { Label=Labels; }
         w=Label.length*10;
         if ((""+Values[i]).length>Label.length)
            {
            w=(""+Values[i]).length*10;
            }
         h=30;
         if (Math.cos(a)>0)
            {
            if (Math.sin(a)<0)
               {
               X=x-r2*Math.cos(a)-w
               Y=y+r2*Math.sin(a)-h;
               }
            else
               {
               X=x-r2*Math.cos(a)-w;
               Y=y+r2*Math.sin(a);
               }
            }
         else
            {
            if (Math.sin(a)<0)
               {
               X=x-r2*Math.cos(a);
               Y=y+r2*Math.sin(a)-h;
               }
            else
               {
               X=x-r2*Math.cos(a);
               Y=y+r2*Math.sin(a);
               }
            }
         Html+=SVG_Rectangle(X,Y,w,h);
         SVG_SetFontSize(10);
         SVG_SetFontAlign("left");
         Html+=SVG_Text(X+5,Y+20,Label);
         SVG_SetFontSize(13);
         Html+=SVG_Text(X+5,Y+5,Values[i]);

         Html+=SVG_GroupClose("");

         }
      TotalSoFar+=Values[i];
      }

   SVG_RestoreSettings();
   return(Html);
   }

//////////////////////////////////////////////////////////////
// Display a SimplePieChart inside a DIV
// document top left corner
// Parameters:
//    Id: name of the div/element
//    JSONData of the following format
// obviously this normally comes from a Ajax call...
// var JsonData=
//    {
//    data:
//       [
//          {
//          values:[1,3,2,5,4,6,7,9,8],
//          label :"Curve1",
//          color :"#ff0000"
//          },
//          {
//          values:[4,5,6,7,7,2,3,1,1],
//          label :"Curve2",
//          color :"#00ff00"
//          }
//       ],
//    labels         :["a","b","c","d","e","f","g","h","i"],
//    vertical_unit  :"foo",
//    horizontal_unit:"bar",
//    vertical_max   :10,
//    vertical_min   :0,
//    radius: 100,
//    max: 100
//    }
// Most lines in the above JSON are optional: here is a minimalist version
// var JsonData=
//    {
//    data:
//       [
//          {
//          values:[1,3,2,5,4,6,7,9,8]
//          },
//          {
//          values:[4,5,6,7,7,2,3,1,1]
//          }
//       ],
//    }
// Return
// Insert the generated SVG inside the named DIV
// Example:
//    <div id="toto" style="width:50%;height:300px;border:solid 1px #ff0000"></div>
//    DisplaySimplePieChart("toto",JsonData);
///////////////////////////////////////////////////////////////
function DisplaySimplePieChart(DivId,JsonData)
   {
   var Element=document.getElementById(DivId);
   if (Element==null)
      {
      alert("There is no DIV named "+DivId+" sorry !");
      }
   else
      {

      var Box   = GetLayerPosition(DivId);
      var Width = Box.right-Box.left;
      var Height= Box.bottom-Box.top;
      var RadiusIn= 0;//0.45 * Height;
      var RadiusOut= 0.45 * Height;
      if (Width<100)
         {
         alert("DIV "+DivId+" is very narrow ("+Width+"px) consider at least 100px !");
         }
      else if (Height<50)
         {
         alert("DIV "+DivId+" isn't tall enough ("+Height+"px) consider at least 50px !");
         }
      else if ((JsonData.data)&&(JsonData.data.length>0))
         {
         var i;
         var Max=0;
         var Min=0;
         var Labels=[];
         var Values=JsonData.data[0].values;
         if (JsonData.max){Max= JsonData.max};
         if (JsonData.labels)
            {Labels= JsonData.labels}
         else
            {Labels= Values};

         var Html  =SVG_Open(Width,Height);

         //SVG_PieChartAxis(x,y,Rout,Rin,Max,From,To,Labels)
         Html+=SVG_PieChartAxis(Width*0.5,Height*0.5,RadiusOut,RadiusIn,Values,0,0,Labels);
         //SVG_PieChart(x,y,Rout,Rin,Values,Max,From,To,Labels)
         Html+=SVG_PieChart(Width*0.5,Height*0.5,RadiusOut,RadiusIn,Values,Values);

         Html+=SVG_Close();
         document.getElementById(DivId).innerHTML=Html;
         }
      else
         {
         alert("Should have a JSON of the form { data:[ { values:[] },... ] }");
         }
      }
   }

function CallDisplaySimplePieChart(JsonUrl,TargetID){
     jQuery.ajax({
         url:        JsonUrl,
         dataType:   "json",
         success:    function(data){
             DisplaySimplePieChart(TargetID,data);
         }
     });
 };