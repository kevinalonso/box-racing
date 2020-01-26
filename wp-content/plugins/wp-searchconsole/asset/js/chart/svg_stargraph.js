// requires svg_base.js to work !

/////////////////////////////////////////////
// return code to draw axis for a star graph
// x,y: center of the graph
// redius: radius of the graph
// Values: a number of values
//         or an array
/////////////////////////////////////////////
function SVG_GraphStarAxis(x,y,Radius,Values)
   {
   var ValQtt;
   var Angle;
   var Html="";
   var xx,yy;
   SVG_SaveSettings();

   SVG_SetFontSize("15");
   SVG_SetFontAlign("left");
   SVG_SetFontRotate(0);

   if (Values.constructor===Array)
      {
      ValQtt=Values.length;
      }
   else
      {
      ValQtt=Values;
      }
   for (Angle=0;Angle<360;Angle+=360/ValQtt)
      {
      xx=x+Radius*Math.sin(Angle/180*3.141592654);
      yy=y+Radius*Math.cos(Angle/180*3.141592654);
      Html+=SVG_Line(x,y,xx,yy);
      Html+=SVG_Line(xx,yy,x+(Radius-10)*Math.sin((Angle-2)/180*3.141592654),y+(Radius-10)*Math.cos((Angle-2)/180*3.141592654));
      Html+=SVG_Line(xx,yy,x+(Radius-10)*Math.sin((Angle+2)/180*3.141592654),y+(Radius-10)*Math.cos((Angle+2)/180*3.141592654));
      SVG_SetFontSize(10);
      SVG_SetFontAlign("left");
      SVG_SetFontRotate(Angle+2+90);
      xx=x+Radius*Math.sin((Angle+2)/180*3.141592654);
      yy=y+Radius*Math.cos((Angle+2)/180*3.141592654);
      Html+=SVG_Text(xx,yy," "+SVG_Settings["GraphScaleMax"]+" "+SVG_Settings["GraphVerticalUnit"]);
      }
   SVG_RestoreSettings();
   return(Html);
   }
/////////////////////////////////////////////
// return code to draw values for a star graph
// x,y: center of the graph
// radius: radius of the graph
// Values: an array of values
/////////////////////////////////////////////
function SVG_GraphStar(x,y,Radius,Values)
   {
   var ValNr;
   var ValQtt;
   var Angle;
   var Html="";
   var Coords=[];
   ValQtt=Values.length;
   Angle=0;
   for (ValNr=0;ValNr<ValQtt;ValNr++)
      {
      Angle+=360/ValQtt;
      xx=x+Radius*((Values[ValNr]-SVG_Settings["GraphScaleMin"])/(SVG_Settings["GraphScaleMin"]-SVG_Settings["GraphScaleMax"]))*Math.sin(Angle/180*3.141592654);
      yy=y-Radius*((Values[ValNr]-SVG_Settings["GraphScaleMin"])/(SVG_Settings["GraphScaleMin"]-SVG_Settings["GraphScaleMax"]))*Math.cos(Angle/180*3.141592654);
      Coords.push(xx);
      Coords.push(yy);
      }
   Html+=SVG_Poly(Coords);
   return(Html);
   }

/////////////////////////////////////////////
// return code to draw values for a star graph
// x,y: center of the graph
// radius: radius of the graph
// Values: an array of values
/////////////////////////////////////////////
function SVG_GraphStarInteractive(x,y,Radius,Values)
   {
   var ValNr;
   var ValQtt;
   var Angle;
   var Html="";
   var Coords=[];
   var DotRadius=5;
   ValQtt=Values.length;
   Angle=0;
   for (ValNr=0;ValNr<ValQtt;ValNr++)
      {
      Angle+=360/ValQtt;
      xx=x+Radius*((Values[ValNr]-SVG_Settings["GraphScaleMin"])/(SVG_Settings["GraphScaleMin"]-SVG_Settings["GraphScaleMax"]))*Math.sin(Angle/180*3.141592654);
      yy=y-Radius*((Values[ValNr]-SVG_Settings["GraphScaleMin"])/(SVG_Settings["GraphScaleMin"]-SVG_Settings["GraphScaleMax"]))*Math.cos(Angle/180*3.141592654);
      Html+=SVG_HoverDot(xx,yy,DotRadius,Values[ValNr]+" "+SVG_Settings["GraphVerticalUnit"]);
      }
   return(Html);
   }
/////////////////////////////////////////////
// return code to draw graduations
// x,y: center of the graph
// radius: radius of the graph
// step: step between graduations
// Values: an array of values
/////////////////////////////////////////////
function SVG_GraphStarGrads(x,y,Radius,Step,Values)
   {
   var ValQtt;
   var Angle;
   var Html="";
   var xx,yy,xxx,yyy;
   var Val;
   SVG_SaveSettings();

   if (Values.constructor===Array)
      {
      ValQtt=Values.length;
      }
   else
      {
      ValQtt=Values;
      }
   for (Val=Math.floor(SVG_Settings["GraphScaleMin"]/Step)*Step;Val<SVG_Settings["GraphScaleMax"];Val+=Step)
      {
      if (Val>SVG_Settings["GraphScaleMin"])
         {
         for (Angle=0;Angle<360;Angle+=360/ValQtt)
            {
            xx =x+(Val-SVG_Settings["GraphScaleMin"])/(SVG_Settings["GraphScaleMax"]-SVG_Settings["GraphScaleMin"])*Radius*Math.sin(Angle/180*3.141592654);
            yy =y+(Val-SVG_Settings["GraphScaleMin"])/(SVG_Settings["GraphScaleMax"]-SVG_Settings["GraphScaleMin"])*Radius*Math.cos(Angle/180*3.141592654);
            xxx=x+(Val-SVG_Settings["GraphScaleMin"])/(SVG_Settings["GraphScaleMax"]-SVG_Settings["GraphScaleMin"])*Radius*Math.sin((Angle+360/ValQtt)/180*3.141592654);
            yyy=y+(Val-SVG_Settings["GraphScaleMin"])/(SVG_Settings["GraphScaleMax"]-SVG_Settings["GraphScaleMin"])*Radius*Math.cos((Angle+360/ValQtt)/180*3.141592654);
            Html+=SVG_Line(xx,yy,xxx,yyy);
            }
         }
      }
   SVG_RestoreSettings();

   return(Html);
   }
//////////////////////////////////////////////////////////////
// Display a SimpleStarGraph inside a DIV
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
//    radius: 100
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
//    DisplaySimpleStarGraph("toto",JsonData);
///////////////////////////////////////////////////////////////
function DisplaySimpleStarGraph(DivId,JsonData)
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
      var Radius= 0.5 * Height;

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
         // Html+=SVG_GraphStarAxis(SVGx,SVGy,150,data["values"]);
         if (JsonData.labels)
            {
            Html+=SVG_GraphStarAxis(Width*0.5,Height*0.5,Radius,JsonData.data[0].values.length,JsonData.labels);
            }
         else
            {
            Html+=SVG_GraphStarAxis(Width*0.5,Height*0.5,Radius,JsonData.data[0].values.length);
            }

         // and now let's draw curves
         for (i=0;i<JsonData.data.length;i++)
            {
            SVG_SetPen(5);
            SVG_SetFill("");
            SVG_SetOpacity(0.5);
            if (JsonData.data[i].color) { SVG_SetInk(JsonData.data[i].color); } else { SVG_SetInk(SVG_NewColor(i)); }
            //Html+=SVG_GraphStar(SVGx,SVGy,150,data["values"]);
            if (JsonData.data[i].label)
               {
               Html+=SVG_GraphStar(Width*0.5,Height*0.5,Radius,JsonData.data[i].values,JsonData.data[i].label);
               }
            else
               {
               Html+=SVG_GraphStar(Width*0.5,Height*0.5,Radius,JsonData.data[i].values);
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

function CallDisplaySimpleStarGraph(JsonUrl,TargetID){
    jQuery.ajax({
        url:        JsonUrl,
        dataType:   "json",
        success:    function(data){
            DisplaySimpleStarGraph(TargetID,data);
        }
    });
}