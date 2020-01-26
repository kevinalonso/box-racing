// requires svg_base.js to work !


/////////////////////////////////////////////
// return code to draw axis for a star graph
// x,y: origin (bottom left) of graph
// w,h: width and height of graph
// HorizontalLabels: a number (then h-axis is supposed 0..number)
//                   [a] (then axis is supposed 0..a)
//                   [a,b] (then axis is supposed a..b)
//                   [a,b,c...] values are displayed (if space allows)
/////////////////////////////////////////////
function SVG_GraphLineAxis(x,y,w,h,HorizontalLabels)
   {
   var ValQtt;
   var Html="";
   SVG_SaveSettings();

   // axis, with arrows
   Html+=SVG_Line(x,y,x+w,y);
   Html+=SVG_Line(x+w,y,x+w-10,y-3);
   Html+=SVG_Line(x+w,y,x+w-10,y+3);
   Html+=SVG_Line(x,y,x,y+h);
   Html+=SVG_Line(x,y+h,x-3,y+h-10);
   Html+=SVG_Line(x,y+h,x+3,y+h-10);
   // units
   SVG_SetFontSize(10);
   SVG_SetFontRotate(0);
   SVG_SetFontAlign("left");
   Html+=SVG_Text(x+w-10,y+5,SVG_Settings["GraphHorizontalUnit"]);
   SVG_SetFontRotate(-90);
   Html+=SVG_Text(x+10,y+h-10,SVG_Settings["GraphVerticalUnit"]);
   // vertical axis scale
   SVG_SetFontRotate(0);
   SVG_SetFontAlign("right");
   Html+=SVG_Text(x-5,y+h-15,SVG_Settings["GraphScaleMax"]);
   Html+=SVG_Text(x-5,y     ,SVG_Settings["GraphScaleMin"]);
   // horizontal axis scale
   SVG_SetFontRotate(-90);
   SVG_SetFontAlign("right");
   if (HorizontalLabels.constructor===Array)
      {
      if (HorizontalLabels.length==1)
         {
         Html+=SVG_Text(x+2,y-5,"0");
         Html+=SVG_Text(x+2+w-10,y-5,HorizontalLabels[0]);
         }
      else if (HorizontalLabels.length==2)
         {
         Html+=SVG_Text(x+2,y-5,HorizontalLabels[0]);
         Html+=SVG_Text(x+2+w-10,y-5,HorizontalLabels[1]);
         }
      else if (HorizontalLabels.length>2)
         {
         var i;
         var xx;
         var LastX=x-100;
         Html+=SVG_Text(x+2+w-10,y-5,HorizontalLabels[HorizontalLabels.length-1]);
         for (i=0;i<HorizontalLabels.length;i++)
            {
            xx=x+(i/(HorizontalLabels.length-1))*(w-10);
            if ((xx>LastX+10)&&(xx<x+2+w-15))
               {
               LastX=xx;
               Html+=SVG_Text(xx+2,y-5,HorizontalLabels[i]);
               }
            }
         }
      }
   else
      {
      Html+=SVG_Text(x+2,y-5,"0");
      Html+=SVG_Text(x+2+w-10,y-5,HorizontalLabels);
      }


   SVG_RestoreSettings();
   return(Html);
   }
/////////////////////////////////////////////
// return code to draw values for a line graph
// x,y: origin (bottom left) of graph
// w,h: width and height of graph
// Values: an array of values
// Label: an optional label for the curve
/////////////////////////////////////////////
function SVG_GraphLine(x,y,w,h,Values,Label)
   {
   var i;
   var ValQtt;
   var Html="";
   ValQtt=Values.length;
   if (SVG_Settings["Fill"]=="")
      {
      var xx,yy,xxx,yyy;
      for (i=1;i<ValQtt;i++)
         {
         xx =x+(i-1)*(w-10)/(ValQtt-1);
         xxx=x+i    *(w-10)/(ValQtt-1);
         yy =y+(Values[i-1]-SVG_Settings["GraphScaleMin"])/(SVG_Settings["GraphScaleMax"]-SVG_Settings["GraphScaleMin"])*(h-10);
         yyy=y+(Values[i  ]-SVG_Settings["GraphScaleMin"])/(SVG_Settings["GraphScaleMax"]-SVG_Settings["GraphScaleMin"])*(h-10);
         Html+=SVG_Line(xx,yy,xxx,yyy);
         }
      }
   else
      {
      var Coords=[];
      var xx,yy;
      Coords.push(x);
      Coords.push(y);
      for (i=0;i<ValQtt;i++)
         {
         xx=x+i*(w-10)/(ValQtt-1);
         yy=y+(Values[i]-SVG_Settings["GraphScaleMin"])/(SVG_Settings["GraphScaleMax"]-SVG_Settings["GraphScaleMin"])*(h-10);
         Coords.push(xx);
         Coords.push(yy);
         }
      Coords.push(x+w-10);
      Coords.push(y);
      Html+=SVG_Poly(Coords);
      }
   if ((Label)&&(Label!=""))
      {
      var Len=Label.length;
      SVG_SaveSettings();
      SVG_SetFontSize(10);
      SVG_SetPen(1);
      SVG_SetOpacity(0.5);
      SVG_SetFill("#ffffff");
      SVG_SetInk("#000000");
      SVG_SetFont("arial");
      SVG_SetFontRotate(0);
      SVG_SetFontAlign("right");
      var Bubble=[];
      yy=y+(Values[ValQtt-1]-SVG_Settings["GraphScaleMin"])/(SVG_Settings["GraphScaleMax"]-SVG_Settings["GraphScaleMin"])*(h-10);
      Bubble.push(x+w-10         ); Bubble.push(yy);
      Bubble.push(x+w-10+ 3      ); Bubble.push(yy+10);
      Bubble.push(x+w-10+15      ); Bubble.push(yy+10);
      Bubble.push(x+w-10+15      ); Bubble.push(yy+28);
      Bubble.push(x+w-10+15-5.5*Len); Bubble.push(yy+28);
      Bubble.push(x+w-10+15-5.5*Len); Bubble.push(yy+10);
      Bubble.push(x+w-10- 3      ); Bubble.push(yy+10);
      Html+=SVG_Poly(Bubble);
      Html+=SVG_Text(x+w,yy+15,Label)
      SVG_RestoreSettings();
      }
   return(Html);
   }
/////////////////////////////////////////////
// return code to draw values for a line graph
// x,y: origin (bottom left) of graph
// w,h: width and height of graph
// Step: for vertical axis
// Values: an array of values
/////////////////////////////////////////////
function SVG_GraphLineGrads(x,y,w,h,Step,Values)
   {
   var Html="";
   var yy;
   if (Step>0)
      {
      for (Val=Math.floor(SVG_Settings["GraphScaleMin"]/Step)*Step;Val<=SVG_Settings["GraphScaleMax"];Val+=Step)
         {
         yy=y+(Val-SVG_Settings["GraphScaleMin"])/(SVG_Settings["GraphScaleMax"]-SVG_Settings["GraphScaleMin"])*(h-10);
         Html+=SVG_Line(x,yy,x+w-10,yy);
         }
      }
   if (Values)
      {
      if (Values.constructor===Array)
         {
         var xx,yy;
         for (i=0;i<Values.length;i++)
            {
            if (i!=0)
               {
               xx=x+i/(Values.length-1)*(w-10);
               Html+=SVG_Line(xx,y,xx,y+h-10);
               }
            }
         }
      else
         {
         var xx,yy;
         for (i=0;i<=Values;i++)
            {
            if (i!=0)
               {
               xx=x+i/Values*(w-10);
               Html+=SVG_Line(xx,y,xx,y+h-10);
               }
            }
         }
      }
   return(Html);
   }
/////////////////////////////////////////////
// return code to draw values for a line graph
// x,y: origin (bottom left) of graph
// w,h: width and height of graph
// Values: an array of values
/////////////////////////////////////////////
function SVG_GraphLineInteractive(x,y,w,h,Values)
   {
   var i;
   var ValQtt;
   var Html="";
   ValQtt=Values.length;
   if (SVG_Settings["Fill"]=="")
      {
      var xx,yy,Lastx,Lasty;
      Lastx=0; Lasty=0;

      for (i=0;i<ValQtt;i++)
         {
         xx=x+i    *(w-10)/(ValQtt-1);
         yy=y+(Values[i  ]-SVG_Settings["GraphScaleMin"])/(SVG_Settings["GraphScaleMax"]-SVG_Settings["GraphScaleMin"])*(h-10);
         if (((Lastx-xx)*(Lastx-xx)+(Lasty-yy)*(Lasty-yy))>100)
            {
            Html+=SVG_HoverDot(xx,yy,5,Values[i]+" "+SVG_Settings["GraphVerticalUnit"]);
            Lastx=xx;
            Lasty=yy;
            }
         }
      }
   return(Html);
   }

//////////////////////////////////////////////////////////////
// Display a SimpleLineGraph inside a DIV
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
//    vertical_min   :0
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
//    <div id="toto" style="width:50%;height:200px;border:solid 1px #ff0000"></div>
//    DisplaySimpleLineGraph("toto",JsonData);
///////////////////////////////////////////////////////////////
function DisplaySimpleLineGraph(DivId,JsonData)
   {
   var Element=document.getElementById(DivId);
   if (Element==null)
      {
      alert("There is no DIV named "+DivId+" sorry !");
      }
   else
      {
      var Box   =GetLayerPosition(DivId);
      var Width =Box.right-Box.left;
      var Height=Box.bottom-Box.top;
      var Html  =SVG_Open(Width,Height);

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
         var Max=1;
         var Min=0;

         // Lets start with the axis: determine max value
         if (JsonData.vertical_max)
            {
            SVG_SetGraphScaleMax(JsonData.vertical_max);
            }
         else
            {
            SVG_SetGraphScaleMax(0);
            for (i=0;i<JsonData.data.length;i++)
               {
               SVG_SetGraphScaleMax(JsonData.data[i].values,1);
               }
            }

         // determine min value
         if (JsonData.vertical_min)
            {
            SVG_SetGraphScaleMin(JsonData.vertical_min);
            }
         else
            {
            SVG_SetGraphScaleMin(0);
            for (i=0;i<JsonData.data.length;i++)
               {
               SVG_SetGraphScaleMin(JsonData.data[i].values,1);
               }
            }

         // units
         if (JsonData.vertical_unit)
            {
            SVG_SetGraphVerticalUnit(JsonData.vertical_unit);
            }
         else
            {
            SVG_SetGraphVerticalUnit("");
            }

         if (JsonData.horizontal_unit)
            {
            SVG_SetGraphHorizontalUnit(JsonData.horizontal_unit);
            }
         else
            {
            SVG_SetGraphHorizontalUnit("");
            }

         // and now ready to draw axis
         if (JsonData.labels)
            {
            Html+=SVG_GraphLineAxis(Width*0.1,Height*0.1,Width*0.8,Height*0.8,JsonData.labels);
            }
         else
            {
            Html+=SVG_GraphLineAxis(Width*0.1,Height*0.1,Width*0.8,Height*0.8,JsonData.data[0].values.length);
            }

         // and now let's draw curves
         for (i=0;i<JsonData.data.length;i++)
            {
            SVG_SetPen(2);
            SVG_SetFill("");
            SVG_SetOpacity(1);
            if (JsonData.data[i].color) { SVG_SetInk(JsonData.data[i].color); } else { SVG_SetInk(SVG_NewColor(i)); }
            if (JsonData.data[i].label)
               {
               Html+=SVG_GraphLine(Width*0.1,Height*0.1,Width*0.8,Height*0.8,JsonData.data[i].values,JsonData.data[i].label);
               }
            else
               {
               Html+=SVG_GraphLine(Width*0.1,Height*0.1,Width*0.8,Height*0.8,JsonData.data[i].values);
               }
            }

         Html+=SVG_Close();
         document.getElementById(DivId).innerHTML=Html;
         }
      else
         {
         alert("Should have a JSON of the form { data:[ { values:[] },... ] }");
         }
      }
   }

function CallDisplaySimpleLineGraph(JsonUrl,TargetID){
    jQuery.ajax({
        url:        JsonUrl,
        dataType:   "json",
        success:    function(data){
            DisplaySimpleLineGraph(TargetID,data);
        }
    });
};