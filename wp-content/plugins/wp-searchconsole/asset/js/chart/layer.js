function GetLayerX(id)
   {
   var x=0;
   var Brol=document.getElementById(id);
   if (Brol.offsetParent)
      {
      while(1)
         {
         x+=Brol.offsetLeft;
         if (!Brol.offsetParent) break;
         Brol=Brol.offsetParent;
         }
      }
   else if (Brol.x)
      {
      x+=Brol.x;
      }
   return x;
   }
function GetLayerY(id)
   {
   var y=0;
   var Brol=document.getElementById(id);
   if (Brol.offsetParent)
      {
      while(1)
         {
         y+=Brol.offsetTop;
         if (!Brol.offsetParent) break;
         Brol=Brol.offsetParent;
         }
      }
   else if (Brol.y)
      {
      y+=Brol.y;
      }
   return y;
   }
function GetWindowScrollX()
   {
   var WindowScrollLeft=window.pageXOffset||document.documentElement.scrollLeft;
   return(WindowScrollLeft);
   }
function GetWindowScrollY()
   {
   var WindowScrollTop =window.pageYOffset||document.documentElement.scrollTop;
   return(WindowScrollTop);
   }
function HideLayer(id)
   {
   document.getElementById(id).style.visibility="hidden";
   }
function ShowLayer(id)
   {
   document.getElementById(id).style.visibility="visible";
   }
function MoveLayer(id,x,y)
   {
   Brol=document.getElementById(id);
   if (Brol!=null)
      {
      if (x!="") { Brol.style.left=x+"px"; }
      if (y!="") { Brol.style.top =y+"px"; }
      }
   }
function GetLayerWidth(id)
   {
   return(document.getElementById(id).offsetWidth);
   }
function GetLayerHeight(id)
   {
   return(document.getElementById(id).offsetHeight);
   }
//////////////////////////////////////////////////////////////
// Get bounding box of a Div as absolute coordinates from
// document top left corner
// Parameters:
//    Id: name of the div/element
// Return
//    A complex hash value:
//    { "left"  :(top left corner x),
//      "top    :(top left corner y),
//      "right" :(bottom right corner x),
//      "bottom":(bottom right corner y)
//    }
// Example:
//    var Box=GetLayerPosition("MyDiv");
//    alert("Box is at ("+Box.left+","+Box.top+") width="+(Box.right-Box.left)+" height="+(Box.bottom-Box.top));
///////////////////////////////////////////////////////////////
function GetLayerPosition(Id)
   {
   var Elem=document.getElementById(Id);
   var x;
   var y;
   var xx=document.getElementById(Id).offsetWidth;
   var yy=document.getElementById(Id).offsetHeight;
   for (x=0,y=0;Elem!=null;x+=Elem.offsetLeft,y+=Elem.offsetTop,Elem=Elem.offsetParent) {}
   xx=xx+x;
   yy=yy+y;
   return {left:x ,top:y ,right:xx ,bottom:yy}
   }