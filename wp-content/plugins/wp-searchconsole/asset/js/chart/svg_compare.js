function Quality(i_Intention, i_Exposition) {
    var Html = "";
    var Distance = Math.sqrt((i_Intention - i_Exposition) * (i_Intention - i_Exposition));
    var Eloignement = Math.floor((i_Intention + i_Exposition) / 2) + 10;
    if (Distance > Eloignement) {
        Html += 'bad';
    } else if (Distance > Eloignement / 2) {
        Html += 'fair';
    } else if (Distance > Eloignement / 4) {
        Html += 'good';
    } else {
        Html += 'very good';
    }
    return (Html);
}

function isVertScrolledIntoView(elem) {
    var box         = el.getBoundingClientRect();
    var elemTop     = box.top;
    var elemBottom  = box.bottom;
    var elemLeft    = box.left;
    var elemRight   = box.right;
    var elemWidth   = box.width;
    var isVisible = (elemTop >= 0) && (elemBottom <= window.innerHeight);
    return isVisible;
}
function isHorizScrolledIntoView(elem) {
    var box         = el.getBoundingClientRect();
    var elemTop     = box.top;
    var elemBottom  = box.bottom;
    var elemLeft    = box.left;
    var elemRight   = box.right;
    var elemWidth   = box.width;
    var isVisible = (elemLeft >= 0) && (elemRight <= window.innerWidth);
    return isVisible;
}

function offset(elem) {
    var docElem, win, box = {
        top: 0,
        left: 0
    }

    doc = elem && elem.ownerDocument;
    if (!doc) {
        return;
    }

    docElem = doc.documentElement;

    // If we don't have gBCR, just use 0,0 rather than error
    // BlackBerry 5, iOS 3 (original iPhone)
    if (typeof elem.getBoundingClientRect !== strundefined) {
        box = elem.getBoundingClientRect();
    }
    win = (doc.nodeType === 9) ? doc.defaultView || doc.parentWindow : false
    return {
        top: box.top + (win.pageYOffset || docElem.scrollTop) - (docElem.clientTop || 0),
        left: box.left + (win.pageXOffset || docElem.scrollLeft) - (docElem.clientLeft || 0)
    };
}


function Highlight(i_Intention, i_Exposition, x, y) {
    var Html = "";
    var DivId = "Viewport";
    var Height = GetLayerHeight(DivId);
    X = x - 50;
    Y = Height - y - 100;
    if (i_Intention < 0) {
        document.getElementById("ViewportComment").style.display = "None";
    } else if ((i_Intention >= 0) && (i_Exposition >= 0)) {
        Html += '<a href="javascript:Highlight(-1,-1,0,0)"><div align=right>[X] <small>close</small></div></a>';
        Html += '<h3>You clicked on "<b>' + Intention[i_Intention].label + '</b>".</h3>';
        Html += '<ul>';
        Html += '<li>It is ranked #' + i_Intention + ' in your intention.';
        Html += '<li>It is ranked #' + i_Exposition + ' of your exposition.';
        Html += '</ul>';
        Distance = Math.sqrt((i_Intention - i_Exposition) * (i_Intention - i_Exposition));
        Html += '<p>' + Quality(i_Intention, i_Exposition) + '</p>';
        document.getElementById("ViewportComment").style.display = "";
    }
    document.getElementById("ViewportComment").innerHTML = Html;
    document.getElementById("ViewportComment").style.left = X + "px";
    document.getElementById("ViewportComment").style.top = Y + "px";
}
var UniqueId = 0;

function RenderWord(Width, Height, i_Intention, i_Exposition,Exposition,Intention,i_Intention2) {
    var Html = "";
    var Margin = 100;
    var x = ((i_Intention+1) / (Intention.length + 1) * Width) / (Width) * (Width - 2 * Margin) + Margin;
    var y = ((i_Exposition+1) / (Exposition.length + 1) * Height) / (Height) * (Height - 2 * Margin) + Margin;
    var q = Quality(i_Intention, i_Exposition);
    var cat = q ? q.replace(' ','-') : 'good';
	UniqueId += 1;
	Html += SVG_GroupStart("noname" + UniqueId, "data-quality=\"" + cat + "\" style=\"cursor:pointer;\" onmousedown=\"Highlight(" + i_Intention2 + "," + i_Exposition + "," + x + "," + y + ")\"");
    SVG_SetPen(1);
    SVG_SetInk("#ff0000");
    SVG_SetFill("#ffffff");
    SVG_SetOpacity(1);
    SVG_SetFont("arial");
    SVG_SetFontRotate(0);
    SVG_SetFontAlign("left");
    SVG_SetFontSize("10");
    Html += SVG_Circle(x, y, 2);
    SVG_SetInk("#000000");
    if (q == "bad") { SVG_SetFill("#ffcccc"); } else if (q == "fair") { SVG_SetFill("#ffffff"); } else if (q == "good") { SVG_SetFill("#eeff99"); } else if (q == "very good") { SVG_SetFill("#99ff99"); } else { SVG_SetFill("#ffffff"); }
    SVG_SetOpacity(0.6);
    SVG_SetFont("arial");
    SVG_SetFontRotate(0);
    SVG_SetFontAlign("left");
    SVG_SetFontSize("10");
    Html += SVG_Poly([x + 2, y, x + 2 + 7, y + 7, x + 100, y + 7, x + 100, y - 7, x + 7 + 2, y - 7]);
    Html += SVG_Text(x + 10, y - 3, Intention[i_Intention].label);
    Html += SVG_GroupClose("");
    return (Html);
}

function RenderOverlay(Width, Height,translation) {
    var Html = "";
    var Margin = 100;
    var Narrowing = 0.8;
    for (Narrowing = 0.8; Narrowing > 0.1; Narrowing = 0.7 * Narrowing) {
        SVG_SetPen(0);
        SVG_SetInk("#ffffff");
        SVG_SetFill("#000000");
        SVG_SetOpacity(0.05);
        Html += SVG_Poly([Margin, Margin + 10 + Height * (1 - Narrowing) / 3, Width * Narrowing, Height - Margin, Margin, Height - Margin]);

        SVG_SetPen(1);
        SVG_SetInk("#ffffff");
        SVG_SetFill("");
        SVG_SetOpacity(0.5);
        SVG_SetFont("arial");
        SVG_SetFontRotate(0);
        SVG_SetFontAlign("right");
        Html += SVG_Poly([Margin, Margin + 10 + Height * (1 - Narrowing) / 3, Width * Narrowing, Height - Margin, Margin, Height - Margin]);

        SVG_SetPen(0);
        SVG_SetInk("#ffffff");
        SVG_SetFill("#000000");
        SVG_SetOpacity(0.05);
        Html += SVG_Poly([Margin + 10 + Width * (1 - Narrowing) / 3, Margin, Width - Margin, Height * Narrowing, Width - Margin, Margin]);

        SVG_SetPen(1);
        SVG_SetInk("#ffffff");
        SVG_SetFill("");
        SVG_SetOpacity(0.5);
        SVG_SetFont("arial");
        SVG_SetFontRotate(0);
        SVG_SetFontAlign("left");
        Html += SVG_Poly([Margin + 10 + Width * (1 - Narrowing) / 3, Margin, Width - Margin, Height * Narrowing, Width - Margin, Margin]);
    }

    Narrowing = 0.8;
    SVG_SetInk("#ffffff");
    SVG_SetOpacity(1);
    SVG_SetFont("arial");
    SVG_SetFontRotate(0);
    SVG_SetFontAlign("right");
    SVG_SetFontSize("30");
    Html += SVG_Text(Width * 0.2, Height * 0.95 - 20, translation['t1']+":");
    SVG_SetFontSize("15");
    Html += SVG_Text(Width * 0.2, Height * 0.95 - 35, translation['t2']);
    Html += SVG_Text(Width * 0.2, Height * 0.95 - 50, translation['t3']);

    SVG_SetFontAlign("left");
    SVG_SetFontSize("30");
    Html += SVG_Text(Width * 0.78, Height * 0.2 - 20, translation['t4']+":");
    SVG_SetFontSize("15");
    Html += SVG_Text(Width * 0.78, Height * 0.2 - 35, translation['t5']);
    Html += SVG_Text(Width * 0.78, Height * 0.2 - 50, translation['t6']);

    return (Html);
}

function DisplayGraph(DivId,Exposition,Intention,translation) {
    var Width = GetLayerWidth(DivId);
    var Height = GetLayerHeight(DivId);
    var i;
    var Assoc = {};
    var Int2 = new Array();
    var Assoc2 = {};
    Html = SVG_Open(Width, Height);
    Html += RenderOverlay(Width, Height,translation);
    for (i = 0; i < Exposition.length - 1; i++) {
        Assoc[Exposition[i].label] = i;

    }
    // filter intention to match Exposition
    for (i=0;i<Intention.length-1;i++){
        Assoc2[Intention[i].label]=i;
        if (Assoc[Intention[i].label]) {
            Int2.push(Intention[i]);
        }
    }
    Intention =Int2;
   for (i=0;i<Intention.length-1;i++)
        {Html+=RenderWord(Width - 100,Height,i,Assoc[Intention[i].label],Exposition,Intention,Assoc2[Intention[i].label]);
      }
    Html += SVG_Close();
    document.getElementById(DivId).innerHTML = Html;
    document.getElementById("ViewportComment").style.display = "None";
}

function GlobalQuality(DivId, Qtt) {
    var i;
    var Assoc = {};
    var q;
    var Total = 0;
    var Html = "";
    for (i = 0; i < Exposition.length - 1; i++) {
        Assoc[Exposition[i].label] = i;
    }
    for (i = 0;
        (i < Intention.length - 1) && (i < Qtt); i++) {
        q = Quality(i, Assoc[Intention[i].label]);
        if (q == "bad") { Total -= 1; } else if (q == "fair") {} else if (q == "good") { Total += 0.5; } else if (q == "very good") { Total += 1; } else {}
    }
    if (Total > 0) {
        Html += '' + Total + ' (good)';
    } else {
        Html += '' + Total + ' (bad)';
    }
    document.getElementById(DivId).innerHTML = Html;
}
