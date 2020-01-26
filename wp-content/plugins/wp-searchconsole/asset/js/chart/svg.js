var SVG_Settings = {
    "Pen": 1,
    "Ink": "#000000",
    "Paper": "#ffffff",
    "Fill": "",
    "Font": "arial",
    "FontSize": "15",
    "FontAlign": "left",
    "FontRotate": 0,
    "Opacity": 1,
    "NextId": "",
    "NextCustomParm": "",
    "Width": 100,
    "Height": 100,
    "OffsetX": 0,
    "OffsetY": 0,
    "GraphScaleMin": 0,
    "GraphScaleMax": 100,
    "GraphVerticalUnit": "cm",
    "GraphHorizontalUnit": "cm",
    "LastRandomGroupName": 0,
    "GroupNames": []
}
var SVG_SettingStack = [];
////////////////////////////////////////
// save all SVG Settings
////////////////////////////////////////
function SVG_SaveSettings() {
        SVG_SettingStack.push(JSON.stringify(SVG_Settings));
    }
    ////////////////////////////////////////
    // restore the last saved settings
    ////////////////////////////////////////
function SVG_RestoreSettings() {
        SVG_Settings = JSON.parse(SVG_SettingStack.pop());
    }
    ////////////////////////////////////////
    // reset settings to default values
    // that is black thin lines with no fill
    // small arial font not rotated...
    ////////////////////////////////////////
function SVG_ResetSettings() {
	SVG_SetInk("#000000");
	SVG_SetFill("");
	SVG_SetOpacity(1);
	SVG_SetPen(1);
	SVG_SetPaper("#ffffff");
	SVG_SetFont("arial");
	SVG_SetFontSize(10);
	SVG_SetFontAlign("left");
	SVG_SetFontRotate(0);
	SVG_SetOffset(0, 0);
	SVG_SetGraphVerticalUnit('cm');
	SVG_SetGraphHorizontalUnit('cm');
	SVG_SetGraphVerticalLabel('');
	SVG_SetGraphHorizontalLabel('');
}
    ////////////////////////////////////////
    // next SVG command generated will contain a class parameter
    ////////////////////////////////////////
function SVG_Class(Class) {
	SVG_Settings["NextCustomParm"] = 'class="' + Class + '"';
}
    ////////////////////////////////////////
    // return code to create a svg group
    // name: name of the group to create
    // parms: could be something like "onclick=Toto()"
    ///////////////////////////////////////
function SVG_GroupStart(GroupName, Parms) {
	var Answer = "";
	if (GroupName == "") {
		SVG_Settings["LastRandomGroupName"] += 1;
		GroupName = "group" + SVG_Settings["LastRandomGroupName"];
	}
	Answer = "<g id=\"" + GroupName + "\"";
	if (Parms != "") {
		Answer += " " + Parms;
	}
	Answer += ">\n";
	SVG_Settings["GroupNames"].push(GroupName);
	return (Answer);
}
    ////////////////////////////////////////
    // return code to close a svg group
    // name: name of the group to close or "*all" or ""
    ///////////////////////////////////////
function SVG_GroupClose(GroupName) {
    var Answer = "";
    if (GroupName == "") {
        GroupName = SVG_Settings["GroupNames"].pop();
        if (GroupName != "") {
            Answer += "</g>\n";
        }
    } else if (GroupName == "*all") {
        var n;
        for (n = SVG_Settings["GroupNames"].pop(); n != ""; n = SVG_Settings["GroupNames"].pop()) {
            if (n != "") {
                Answer += "</g>\n";
            }
        }
    } else {
        var n;
        for (n = SVG_Settings["GroupNames"].pop(); n != ""; n = SVG_Settings["GroupNames"].pop()) {
            if (n == "") {
                Answer += "</g>\n";
            }
            if ((n == "") || (n == GroupName)) {
                break;
            }
        }
    }
    return (Answer);
}

////////////////////////////////////////
// return opening SVG code
////////////////////////////////////////
function SVG_Open(Width, Height) {
        var Html = "";
        Html = '<svg id="MitamboSVG" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="' + Width + '" height="' + Height + '" overflow="hidden">';
        Html += '<g id="Everything">';
        SVG_ResetSettings();
        SVG_Settings["Width"] = Width;
        SVG_Settings["Height"] = Height;
        return (Html);
    }
    ////////////////////////////////////////
    // return trailing SVG code
    ////////////////////////////////////////
function SVG_Close() {
    var Html = "";
    Html += '</g></svg>';
    return (Html);
}

function SVG_Rectangle(x, y, w, h, r) {
	var Answer = "";
	var Style = "";

	if (h < 0) {
		y += h;
		h = -h;
	}
	if (w < 0) {
		x += w;
		w = -w;
	}
	Answer = "<rect x=\"" + (x + SVG_Settings["OffsetX"]) + "\" y=\"" + (SVG_Settings["Height"] - y - SVG_Settings["OffsetY"] - h) + "\" width=\"" + w + "\" height=\"" + h + "\" ";
	if (r > 0) {
		Answer += "rx=\"" + r + "\" ry=\"" + r + "\" ";
	}

	if (SVG_Settings["NextId"] != "") {
		Answer += "id=\"" + SVG_Settings["NextId"] + "\" ";
		SVG_Settings["NextId"] = "";
	}

	if (SVG_Settings["NextCustomParm"] != "") {
		Answer += SVG_Settings["NextCustomParm"] + " ";
		SVG_Settings["NextCustomParm"] = "";
	}

	if ((SVG_Settings["Ink"] == "") || (SVG_Settings["Pen"] == 0)) {
		Answer += "stroke=\"#000000\" stroke-width=\"0\" ";
	} else {
		Answer += "stroke=\"" + SVG_Settings["Ink"] + "\" stroke-width=\"" + SVG_Settings["Pen"] + "\" ";
		if (SVG_Settings["Opacity"] < 1) {
			Style += "stroke-opacity:" + SVG_Settings["Opacity"] + ";";
		}
	}

	if (SVG_Settings["Fill"] != "") {
		Answer += "fill=\"" + SVG_Settings["Fill"] + "\" ";
		if (SVG_Settings["Opacity"] < 1) {
			Style += "fill-opacity:" + SVG_Settings["Opacity"] + ";";
		}
	} else {
		Answer += "fill=\"none\" ";
	}

	if (Style != "") {
		Answer += "style=\"" + Style + "\" ";
	}

	Answer += "></rect>\n";
	return (Answer);
}
    ////////////////////////////////////////
    // return SVG code to display a circle
    ////////////////////////////////////////
function SVG_Circle(x, y, r) {
	var Answer = '<circle cx="' + (x + SVG_Settings["OffsetX"]) + '" cy="' + (SVG_Settings["Height"] - y - SVG_Settings["OffsetY"]) + '" r="' + r + '" ';
	var Style = "";

	if (SVG_Settings["NextId"] != "") {
		Answer += 'id="' + SVG_Settings["NextId"] + '" ';
		SVG_Settings["NextId"] = "";
	}

	if (SVG_Settings["NextCustomParm"] != "") {
		Answer += '' + SVG_Settings["NextCustomParm"] + ' ';
		SVG_Settings["NextCustomParm"] = "";
	}

	if ((SVG_Settings["Ink"] == "") || (SVG_Settings["Pen"] == 0)) {
		Answer += 'stroke="#000000" stroke-width="0" ';
	} else {
		Answer += 'stroke="' + SVG_Settings["Ink"] + '" stroke-width="' + SVG_Settings["Pen"] + '" ';
		if (SVG_Settings["Opacity"] < 1) {
			Style += 'stroke-opacity:' + SVG_Settings["Opacity"] + ';';
		}
	}

	if (SVG_Settings["Fill"] != "") {
		Answer += 'fill="' + SVG_Settings["Fill"] + '" ';
		if (SVG_Settings["Opacity"] < 1) {
			Style += 'fill-opacity:' + SVG_Settings["Opacity"] + ';';
		}
	} else {
		Answer += 'fill="none" ';
	}

	if (Style != "") {
		Answer += 'style="' + Style + '" ';
	}

	Answer += '></circle>\n';
	return (Answer);
}
    ////////////////////////////////////////
    // return SVG code to display polygon defined by
    // an array of numbers [x,y,xx,yy,xxx,yyy...]
    ////////////////////////////////////////
function SVG_Poly(CoordinateArray) {
	var Answer = "<polygon ";
	var i;
	var Coords = "";
	var Style = "";

	for (i = 0; i < CoordinateArray.length; i += 2) {
		if (Coords != "") {
			Coords += ",";
		}
		Coords += '' + (CoordinateArray[i] + SVG_Settings["OffsetX"]) + ',' + (SVG_Settings["Height"] - CoordinateArray[i + 1] - SVG_Settings["OffsetY"]);
	}
	Answer += 'points="' + Coords + '" ';

	if (SVG_Settings["NextId"] == "") {
		Answer += 'id="' + SVG_Settings["NextId"] + '" ';
		SVG_Settings["NextId"] = "";
	}

	if (SVG_Settings["NextCustomParm"] != "") {
		Answer += SVG_Settings["NextCustomParm"] + ' ';
		SVG_Settings["NextCustomParm"] = "";
	}

	if ((SVG_Settings["Ink"] == "") || (SVG_Settings["Pen"] == 0)) {
		Answer += 'stroke="#000000" stroke-width="0" ';
	} else {
		Answer += 'stroke="' + SVG_Settings["Ink"] + '" stroke-width="' + SVG_Settings["Pen"] + '" ';
		if (SVG_Settings["Opacity"] < 1) {
			Style += 'stroke-opacity:' + SVG_Settings["Opacity"] + ';';
		}
	}

	if (SVG_Settings["Fill"] != "") {
		Answer += 'fill="' + SVG_Settings["Fill"] + '" ';
		if (SVG_Settings["Opacity"] < 1) {
			Style += 'fill-opacity:' + SVG_Settings["Opacity"] + ';';
		}
	} else {
		Answer += 'fill="none" ';
	}

	if (Style != "") {
		Answer += 'style="' + Style + '" ';
	}

	Answer += '></polygon>\n';
	return (Answer);
}
    ////////////////////////////////////////
    // return SVG code to display a curve defined by
    // an array of numbers [x,y,xx,yy,xxx,yyy...]
    // this is the same than SVG_Poly, except this uses curves instead of lines
    ////////////////////////////////////////
function SVG_Curve(CoordinateArray) {
	var Answer = "<path ";
	var i;
	var Coords = "";
	var Style = "";

	i = 0;
	Coords = "M" + (CoordinateArray[i] + SVG_Settings["OffsetX"]) + ',' + (SVG_Settings["Height"] - CoordinateArray[i + 1] - SVG_Settings["OffsetY"]);
	i = 0;
	var xxx = (CoordinateArray[i + 2] + SVG_Settings["OffsetX"]);
	var yyy = (SVG_Settings["Height"] - CoordinateArray[i + 3] - SVG_Settings["OffsetY"]);
	var xx = (CoordinateArray[i] + SVG_Settings["OffsetX"]);
	var yy = (SVG_Settings["Height"] - CoordinateArray[i + 1] - SVG_Settings["OffsetY"]);
	var x = (CoordinateArray[CoordinateArray.length - 2] + SVG_Settings["OffsetX"]);
	var y = (SVG_Settings["Height"] - CoordinateArray[CoordinateArray.length - 1] - SVG_Settings["OffsetY"]);
	var dd = Math.sqrt((xxx - xx) * (xxx - xx) + (yyy - yy) * (yyy - yy));
	var d = Math.sqrt((xx - x) * (xx - x) + (yy - y) * (yy - y));
	var cx = xx - (xx - x) / d * dd / 3;
	var cy = yy - (yy - y) / d * dd / 3;
	Coords += ' S' + cx + ',' + cy + ' ' + xx + ',' + yy;
	for (i = 2; i < CoordinateArray.length - 2; i += 2) {
		var xxx = (CoordinateArray[i + 2] + SVG_Settings["OffsetX"]);
		var yyy = (SVG_Settings["Height"] - CoordinateArray[i + 3] - SVG_Settings["OffsetY"]);
		var xx = (CoordinateArray[i] + SVG_Settings["OffsetX"]);
		var yy = (SVG_Settings["Height"] - CoordinateArray[i + 1] - SVG_Settings["OffsetY"]);
		var x = (CoordinateArray[i - 2] + SVG_Settings["OffsetX"]);
		var y = (SVG_Settings["Height"] - CoordinateArray[i - 1] - SVG_Settings["OffsetY"]);
		var dd = Math.sqrt((xxx - xx) * (xxx - xx) + (yyy - yy) * (yyy - yy));
		var d = Math.sqrt((xx - x) * (xx - x) + (yy - y) * (yy - y));
		var cx = xx - (xx - x) / d * dd / 3;
		var cy = yy - (yy - y) / d * dd / 3;
		Coords += ' S' + cx + ',' + cy + ' ' + xx + ',' + yy;
	}
	i = CoordinateArray.length - 2;
	var xxx = (CoordinateArray[0] + SVG_Settings["OffsetX"]);
	var yyy = (SVG_Settings["Height"] - CoordinateArray[1] - SVG_Settings["OffsetY"]);
	var xx = (CoordinateArray[i] + SVG_Settings["OffsetX"]);
	var yy = (SVG_Settings["Height"] - CoordinateArray[i + 1] - SVG_Settings["OffsetY"]);
	var x = (CoordinateArray[i - 2] + SVG_Settings["OffsetX"]);
	var y = (SVG_Settings["Height"] - CoordinateArray[i - 1] - SVG_Settings["OffsetY"]);
	var dd = Math.sqrt((xxx - xx) * (xxx - xx) + (yyy - yy) * (yyy - yy));
	var d = Math.sqrt((xx - x) * (xx - x) + (yy - y) * (yy - y));
	var cx = xx - (xx - x) / d * dd / 3;
	var cy = yy - (yy - y) / d * dd / 3;
	Coords += ' S' + cx + ',' + cy + ' ' + xx + ',' + yy;

	Answer += 'd="' + Coords + '" ';

	if (SVG_Settings["NextId"] == "") {
		Answer += 'id="' + SVG_Settings["NextId"] + '" ';
		SVG_Settings["NextId"] = "";
	}

	if (SVG_Settings["NextCustomParm"] != "") {
		Answer += SVG_Settings["NextCustomParm"] + ' ';
		SVG_Settings["NextCustomParm"] = "";
	}

	if ((SVG_Settings["Ink"] == "") || (SVG_Settings["Pen"] == 0)) {
		Answer += 'stroke="#000000" stroke-width="0" ';
	} else {
		Answer += 'stroke="' + SVG_Settings["Ink"] + '" stroke-width="' + SVG_Settings["Pen"] + '" ';
		if (SVG_Settings["Opacity"] < 1) {
			Style += 'stroke-opacity:' + SVG_Settings["Opacity"] + ';';
		}
	}

	if (SVG_Settings["Fill"] != "") {
		Answer += 'fill="' + SVG_Settings["Fill"] + '" ';
		if (SVG_Settings["Opacity"] < 1) {
			Style += 'fill-opacity:' + SVG_Settings["Opacity"] + ';';
		}
	} else {
		Answer += 'fill="none" ';
	}

	if (Style != "") {
		Answer += 'style="' + Style + '" ';
	}

	Answer += '></path>\n';
	return (Answer);
}
    ////////////////////////////////////////
    // return SVG code to display a ""patate" surrounding a set of coordinates
    // Parameter:
    //    Radius: added padding to the "patate"
    //    Coords; an array of numbers [x,y,xx,yy,xxx,yyy...]
    ////////////////////////////////////////
function SVG_Patate(Radius, CoordinateArray) {
	var Answer = "";
	var CenterX = 0;
	var CenterY = 0;
	var i;
	var n = 0;
	for (i = 0; i < CoordinateArray.length; i += 2) {
		n += 1;
		CenterX += CoordinateArray[i];
		CenterY += CoordinateArray[i + 1];
	}
	if (n == 0) {
		// no coordinates, no patate
	} else if (n == 1) {
		Answer = SVG_Circle(CenterX, CenterY, Radius);
	} else {
		var CoordArray = [];
		var PatateCoordArray = [];
		CenterX = CenterX / n;
		CenterY = CenterY / n;

		for (i = 0; i < CoordinateArray.length; i += 2) {
			var dx = -CenterX + CoordinateArray[i];
			var dy = -CenterY + CoordinateArray[i + 1];
			var d = Math.sqrt(dx * dx + dy * dy);
			if (d > 0) {
				var a = Math.asin(dy / d);
				if ((dx < 0) && (dy < 0)) {
					a = 3.141592654 - a;
				}
				if ((dx <= 0) && (dy >= 0)) {
					a = 3.141592654 - a;
				}
				CoordArray.push({
					"x": CoordinateArray[i],
					"y": CoordinateArray[i + 1],
					"a": a,
					"d": d
				});
			}
		}
		CoordArray.sort(function(a, b) {
			return (a.a - b.a)
		});
		for (i = 0; i < CoordArray.length; i++) {
			var d;
			d = CoordArray[i].d;
			if ((i > 0) && (i < CoordArray.length - 1)) {
				if ((CoordArray[i - 1].d + CoordArray[i + 1].d) / 2 > d) {
					d = (CoordArray[i - 1].d + CoordArray[i + 1].d) / 2;
				}
			} else if (i == 0) {
				if ((CoordArray[CoordArray.length - 1].d + CoordArray[i + 1].d) / 2 > d) {
					d = (CoordArray[CoordArray.length - 1].d + CoordArray[i + 1].d) / 2;
				}
			} else if (i == CoordArray.length - 1) {
				if ((CoordArray[i - 1].d + CoordArray[0].d) / 2 > d) {
					d = (CoordArray[i - 1].d + CoordArray[0].d) / 2;
				}
			}
			var x = CenterX + Math.cos(CoordArray[i].a) * (d + Radius);
			var y = CenterY + Math.sin(CoordArray[i].a) * (d + Radius);
			PatateCoordArray.push(x);
			PatateCoordArray.push(y);
			//         Answer+=SVG_Circle(x,y,Radius);
			//         Answer+=SVG_Line(x,y,CoordArray[i].x,CoordArray[i].y);
		}
		if (CoordArray.length > 1) {
			Answer = SVG_Curve(PatateCoordArray);
		} else {
			Answer = SVG_Circle(CenterX, CenterY, Radius);
		}
	}
	return (Answer);
}
////////////////////////////////////////
// return SVG code to display a linbe from x,y to xx,yy
////////////////////////////////////////
function SVG_Line(x, y, xx, yy) {
	var Answer = '<line x1="' + (x + SVG_Settings["OffsetX"]) + '" y1="' + (SVG_Settings["Height"] - y - SVG_Settings["OffsetY"]) + '" x2="' + (xx + SVG_Settings["OffsetX"]) + '" y2="' + (SVG_Settings["Height"] - yy - SVG_Settings["OffsetY"]) + '" ';
	var Style = "";

	if (SVG_Settings["NextId"] != "") {
		Answer += 'id="' + SVG_Settings["NextId"] + '" ';
		SVG_Settings["NextId"] = "";
	}

	if (SVG_Settings["NextCustomParm"] != "") {
		Answer += ' ' + SVG_Settings["NextCustomParm"] + ' ';
		SVG_Settings["NextCustomParm"] = "";
	}

	if ((SVG_Settings["Ink"] == "") || (SVG_Settings["Pen"] == 0)) {
		Answer += 'stroke="#000000" stroke-width="0" ';
	} else {
		Answer += 'stroke="' + SVG_Settings["Ink"] + '" stroke-width="' + SVG_Settings["Pen"] + '" ';
		if (SVG_Settings["Opacity"] < 1) {
			Style += 'stroke-opacity:' + SVG_Settings["Opacity"] + ';';
		}
	}

	if (Style != "") {
		Answer += 'style="' + Style + '" ';
	}

	Answer += '></line>\n';
	return (Answer);
}
    ////////////////////////////////////////
    // return SVG code to display the text "t" at position x,y
    ////////////////////////////////////////
function SVG_Text(x, y, t) {
	var Answer = '<text x="' + (x + SVG_Settings["OffsetX"]) + '" y="' + (SVG_Settings["Height"] - y - SVG_Settings["OffsetY"]) + '" ';
	var Style = "";

	if (SVG_Settings["NextId"] != "") {
		Answer += 'id="' + SVG_Settings["NextId"] + '" ';
		SVG_Settings["NextId"] = "";
	}

	if (SVG_Settings["NextCustomParm"] != "") {
		Answer += '' + SVG_Settings["NextCustomParm"] + ' ';
		SVG_Settings["NextCustomParm"] = "";
	}

	if (SVG_Settings["Ink"] != "") {
		Answer += 'fill="' + SVG_Settings["Ink"] + '" ';
		if (SVG_Settings["Opacity"] < 1) {
			Style += 'fill-opacity:' + SVG_Settings["Opacity"] + ';';
		}
	} else {
		Answer += 'fill="none" ';
	}

	if (SVG_Settings["FontRotate"] != 0) {
		Answer += 'transform="rotate(' + SVG_Settings["FontRotate"] + ' ' + x + ',' + (SVG_Settings["Height"] - y) + ')" ';
	}

	if (SVG_Settings["FontAlign"].indexOf("right") >= 0) {
		Style += 'text-anchor:end;';
	}
	if (SVG_Settings["FontAlign"].indexOf("top") >= 0) {
		Style += 'dominant-baseline:top;';
	}
	if (SVG_Settings["FontSize"] != "") {
		Style += 'font-size:' + SVG_Settings["FontSize"] + 'px;';
	}
	if (SVG_Settings["Font"] != "") {
		Style += 'font-family:' + SVG_Settings["Font"] + ';';
	}
	if (Style != "") {
		Answer += 'style="' + Style + '" ';
	}

	Answer += '>' + t + '</text>\n';
	return (Answer);
}
    ////////////////////////////////////////////////////
    // Set or compute a grph scale
    // Min      : minimum of scale, a number or an array of values where minimum will be found
    // Increment: 0 reset to values Min Max
    //            1 use Min only if smaller than current Min
    ////////////////////////////////////////////////////
function SVG_SetGraphScaleMin(Min, Increment) {
    if (Min.constructor === Array) {
        var i;
        if (Increment != 1) {
            SVG_Settings["GraphScaleMin"] = Min[0];
        }
        for (i == 0; i < Min.length; i++) {
            if (Min[i] < SVG_Settings["GraphScaleMin"]) {
                SVG_Settings["GraphScaleMin"] = Min[i];
            }
        }
    } else {
        if ((Increment != 1) || (Min < SVG_Settings["GraphScaleMin"])) {
            SVG_Settings["GraphScaleMin"] = Min;
        }
    }
}

function SVG_SetGraphScaleMax(Max, Increment) {
    if (Max.constructor === Array) {
        var i;
        if (Increment != 1) {
            SVG_Settings["GraphScaleMax"] = Max[0];
        }
        for (i = 0; i < Max.length; i++) {
            if (Max[i] > SVG_Settings["GraphScaleMax"]) {
                SVG_Settings["GraphScaleMax"] = Max[i];
            }
        }
    } else {
        if ((Increment != 1) || (Max > SVG_Settings["GraphScaleMax"])) {
            SVG_Settings["GraphScaleMax"] = Max;
        }
    }
}
function SVG_SetGraphVerticalLabel(n) {
    SVG_Settings["GraphVerticalLabel"] = n;
}
function SVG_SetGraphHorizontalLabel(n) {
    SVG_Settings["GraphHorizontalLabel"] = n;
}
function SVG_SetGraphVerticalUnit(n) {
    SVG_Settings["GraphVerticalUnit"] = n;
}
function SVG_SetGraphHorizontalUnit(n) {
    SVG_Settings["GraphHorizontalUnit"] = n;
}
// change used line width
function SVG_SetPen(n) {
	SVG_Settings["Pen"] = n;
}
// change color for lines ("#rrggbb" or "" for none)
function SVG_SetInk(n) {
	SVG_Settings["Ink"] = n;
} 

function SVG_SetPaper(n) {
	SVG_Settings["Paper"] = n;
} 
 // change fill color ("#rrggbb" or "" for none)
function SVG_SetFill(n) {
	SVG_Settings["Fill"] = n;
}
// set font name
function SVG_SetFont(n) {
	SVG_Settings["Font"] = n;
} 
// set font size
function SVG_SetFontSize(n) {
	SVG_Settings["FontSize"] = n;
} 
// "right", "left"
function SVG_SetFontAlign(n) {
	SVG_Settings["FontAlign"] = n;
} 
// angle 0..360
function SVG_SetFontRotate(n) {
	SVG_Settings["FontRotate"] = n;
} 
// for 0 (transparent) to 1 (opaque)
function SVG_SetOpacity(n) {
	SVG_Settings["Opacity"] = n;
} 
// all subsequent draw coords will add this offset
function SVG_SetOffset(x, y) {
	SVG_Settings["OffsetX"] = x;
	SVG_Settings["OffsetY"] = y;
} 


////////////////////////////////////////////////////////
// create a new color
// parameter:
//    i: a positive interger number [0..[
// return
//    a color such as #rrggbb depending of i
////////////////////////////////////////////////////////
function SVG_NewColor(i) {
    var Preset = ["#ff0000", "#ff9900", "#99ff00", "#00ff00", "#00ff99", "#0099ff", "#0000ff", "#9900ff", "#ff0099", "#999999"];
    var Answer = "";
    Answer = Preset[i % Preset.length];
    return (Answer);
}

/////////////////////////////////////////////
// return code to draw axis for a star graph
// x,y: center of the graph
// redius: radius of the graph
// Values: a number of values
//         or an array
/////////////////////////////////////////////
function SVG_GraphStarAxis(x, y, Radius, Values) {
	var ValQtt;
	var Angle;
	var Html = "";
	var xx, yy;
	SVG_SaveSettings();

	SVG_SetFontSize("15");
	SVG_SetFontAlign("left");
	SVG_SetFontRotate(0);

	if (Values.constructor === Array) {
		ValQtt = Values.length;
	} else {
		ValQtt = Values;
	}
	for (Angle = 0; Angle < 360; Angle += 360 / ValQtt) {
		xx = x + Radius * Math.sin(Angle / 180 * 3.141592654);
		yy = y + Radius * Math.cos(Angle / 180 * 3.141592654);
		Html += SVG_Line(x, y, xx, yy);
		Html += SVG_Line(xx, yy, x + (Radius - 10) * Math.sin((Angle - 2) / 180 * 3.141592654), y + (Radius - 10) * Math.cos((Angle - 2) / 180 * 3.141592654));
		Html += SVG_Line(xx, yy, x + (Radius - 10) * Math.sin((Angle + 2) / 180 * 3.141592654), y + (Radius - 10) * Math.cos((Angle + 2) / 180 * 3.141592654));
		SVG_SetFontSize(10);
		SVG_SetFontAlign("left");
		SVG_SetFontRotate(Angle + 2 + 90);
		xx = x + Radius * Math.sin((Angle + 2) / 180 * 3.141592654);
		yy = y + Radius * Math.cos((Angle + 2) / 180 * 3.141592654);
		Html += SVG_Text(xx, yy, " " + SVG_Settings["GraphScaleMax"] + " " + SVG_Settings["GraphVerticalUnit"]);
	}
	SVG_RestoreSettings();
	return (Html);
}
    /////////////////////////////////////////////
    // return code to draw values for a star graph
    // x,y: center of the graph
    // radius: radius of the graph
    // Values: an array of values
    /////////////////////////////////////////////
function SVG_GraphStar(x, y, Radius, Values) {
    var ValNr;
    var ValQtt;
    var Angle;
    var Html = "";
    var Coords = [];
    ValQtt = Values.length;
    Angle = 0;
    for (ValNr = 0; ValNr < ValQtt; ValNr++) {
        Angle += 360 / ValQtt;
        xx = x + Radius * ((Values[ValNr] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMin"] - SVG_Settings["GraphScaleMax"])) * Math.sin(Angle / 180 * 3.141592654);
        yy = y - Radius * ((Values[ValNr] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMin"] - SVG_Settings["GraphScaleMax"])) * Math.cos(Angle / 180 * 3.141592654);
        Coords.push(xx);
        Coords.push(yy);
    }
   // Html += SVG_Poly(Coords);
    return (Html);
}

function SVG_Hide(Id) {
    document.getElementById(Id).style.display = "None";
}

function SVG_Show(Id) {
    document.getElementById(Id).style.display = "";
}
var SVG_GraphInteractiveId = 0;

function SVG_HoverDot(x, y, DotRadius, Text) {
	var Html = "";
	var Coords = [];
	var w = 11 * Text.length;
	SVG_SaveSettings();
	SVG_SetPen(1);
	SVG_SetFont("arial");
	SVG_SetFontSize(15);
	SVG_SetFontAlign("left");
	SVG_SetFontRotate(0);
	SVG_SetInk("#000000");
	SVG_SetFill("#ffffff");
	Html += SVG_GroupStart("sg" + SVG_GraphInteractiveId + "dot", "onmouseover=\"SVG_Show('sg" + SVG_GraphInteractiveId + "box')\" onmouseout=\"SVG_Hide('sg" + SVG_GraphInteractiveId + "box')\"");
	SVG_SetOpacity(0.2);
	Html += SVG_Circle(x, y, DotRadius);
	Html += SVG_GroupStart("sg" + SVG_GraphInteractiveId + "box", "style=\"display:none;\"");
	SVG_SetOpacity(0.7);
	Coords.push(x + DotRadius + 10);
	Coords.push(y - 10);
	Coords.push(x + DotRadius + 10 + w);
	Coords.push(y - 10);
	Coords.push(x + DotRadius + 10 + w);
	Coords.push(y + 10);
	Coords.push(x + DotRadius + 10);
	Coords.push(y + 10);
	Coords.push(x + DotRadius + 10);
	Coords.push(y + 2);
	Coords.push(x);
	Coords.push(y);
	Coords.push(x + DotRadius + 10);
	Coords.push(y - 2);
	//Html += SVG_Poly(Coords);
	SVG_SetOpacity(1);
	Html += SVG_Text(x + DotRadius + 15, y - 5, Text);
	Html += SVG_GroupClose("");
	Html += SVG_GroupClose("");
	SVG_GraphInteractiveId += 1;
	SVG_RestoreSettings();
	return (Html);
}
/////////////////////////////////////////////
// return code to draw values for a star graph
// x,y: center of the graph
// radius: radius of the graph
// Values: an array of values
/////////////////////////////////////////////
function SVG_GraphStarInteractive(x, y, Radius, Values) {
	var ValNr;
	var ValQtt;
	var Angle;
	var Html = "";
	var Coords = [];
	var DotRadius = 5;
	ValQtt = Values.length;
	Angle = 0;
	for (ValNr = 0; ValNr < ValQtt; ValNr++) {
		Angle += 360 / ValQtt;
		xx = x + Radius * ((Values[ValNr] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMin"] - SVG_Settings["GraphScaleMax"])) * Math.sin(Angle / 180 * 3.141592654);
		yy = y - Radius * ((Values[ValNr] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMin"] - SVG_Settings["GraphScaleMax"])) * Math.cos(Angle / 180 * 3.141592654);
		Html += SVG_HoverDot(xx, yy, DotRadius, Values[ValNr] + " " + SVG_Settings["GraphVerticalUnit"]);
	}
	return (Html);
}
    /////////////////////////////////////////////
    // return code to draw graduations
    // x,y: center of the graph
    // radius: radius of the graph
    // step: step between graduations
    // Values: an array of values
    /////////////////////////////////////////////
function SVG_GraphStarGrads(x, y, Radius, Step, Values) {
    var ValQtt;
    var Angle;
    var Html = "";
    var xx, yy, xxx, yyy;
    var Val;
    SVG_SaveSettings();

    if (Values.constructor === Array) {
        ValQtt = Values.length;
    } else {
        ValQtt = Values;
    }
    for (Val = Math.floor(SVG_Settings["GraphScaleMin"] / Step) * Step; Val < SVG_Settings["GraphScaleMax"]; Val += Step) {
        if (Val > SVG_Settings["GraphScaleMin"]) {
            for (Angle = 0; Angle < 360; Angle += 360 / ValQtt) {
                xx = x + (Val - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * Radius * Math.sin(Angle / 180 * 3.141592654);
                yy = y + (Val - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * Radius * Math.cos(Angle / 180 * 3.141592654);
                xxx = x + (Val - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * Radius * Math.sin((Angle + 360 / ValQtt) / 180 * 3.141592654);
                yyy = y + (Val - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * Radius * Math.cos((Angle + 360 / ValQtt) / 180 * 3.141592654);
                Html += SVG_Line(xx, yy, xxx, yyy);
            }
        }
    }
    SVG_RestoreSettings();

    return (Html);
}

/////////////////////////////////////////////
// return code to draw axis for a star graph
// x,y: origin (bottom left) of graph
// w,h: width and height of graph
// HorizontalLabels: a number (then h-axis is supposed 0..number)
//                   [a] (then axis is supposed 0..a)
//                   [a,b] (then axis is supposed a..b)
//                   [a,b,c...] values are displayed (if space allows)
/////////////////////////////////////////////
function SVG_GraphLineAxis(x, y, w, h, HorizontalLabels) {
	var ValQtt;
	var Html = "";
	SVG_SaveSettings();

	// axis, with arrows
	Html += SVG_Line(x, y, x + w, y);
	Html += SVG_Line(x + w, y, x + w - 10, y - 3);
	Html += SVG_Line(x + w, y, x + w - 10, y + 3);
	Html += SVG_Line(x, y, x, y + h);
	Html += SVG_Line(x, y + h, x - 3, y + h - 10);
	Html += SVG_Line(x, y + h, x + 3, y + h - 10);
	// units
	SVG_SetFontSize(10);
	SVG_SetFontRotate(0);
	SVG_SetFontAlign("left");
	Html += SVG_Text(x + w - 10, y + 5, SVG_Settings["GraphHorizontalLabel"] + "(" + SVG_Settings["GraphHorizontalUnit"] + ")");
	SVG_SetFontRotate(0);
	Html += SVG_Text(x + 10, y + h - 10, SVG_Settings["GraphVerticalLabel"] + "(" + SVG_Settings["GraphVerticalUnit"] + ")");
	// vertical axis scale
	SVG_SetFontRotate(0);
	SVG_SetFontAlign("right");
	Html += SVG_Text(x - 5, y + h - 15, SVG_Settings["GraphScaleMax"]);
	Html += SVG_Text(x - 5, y, SVG_Settings["GraphScaleMin"]);
	// horizontal axis scale
	SVG_SetFontRotate(-90);
	SVG_SetFontAlign("right");
	if (HorizontalLabels.constructor === Array) {
		if (HorizontalLabels.length == 1) {
			Html += SVG_Text(x + 2, y - 5, "0");
			Html += SVG_Text(x + 2 + w - 10, y - 5, HorizontalLabels[0]);
		} else if (HorizontalLabels.length == 2) {
			Html += SVG_Text(x + 2, y - 5, HorizontalLabels[0]);
			Html += SVG_Text(x + 2 + w - 10, y - 5, HorizontalLabels[1]);
		} else if (HorizontalLabels.length > 2) {
			var i;
			var xx;
			var LastX = x - 100;
			Html += SVG_Text(x + 2 + w - 10, y - 5, HorizontalLabels[HorizontalLabels.length - 1]);
			for (i = 0; i < HorizontalLabels.length; i++) {
				xx = x + (i / (HorizontalLabels.length - 1)) * (w - 10);
				if ((xx > LastX + 10) && (xx < x + 2 + w - 15)) {
					LastX = xx;
					Html += SVG_Text(xx + 2, y - 5, HorizontalLabels[i]);
				}
			}
		}
	} else {
		Html += SVG_Text(x + 2, y - 5, "0");
		Html += SVG_Text(x + 2 + w - 10, y - 5, HorizontalLabels);
	}


	SVG_RestoreSettings();
	return (Html);
}
/////////////////////////////////////////////
// return code to draw values for a line graph
// x,y: origin (bottom left) of graph
// w,h: width and height of graph
// Values: an array of values
// Label: an optional label for the curve
/////////////////////////////////////////////
function SVG_GraphLine(x, y, w, h, Values, Label) {
	var i;
	var ValQtt;
	var Html = "";
	ValQtt = Values.length;
	if (SVG_Settings["Fill"] == "") {
		var xx, yy, xxx, yyy;
		for (i = 1; i < ValQtt; i++) {
			xx = x + (i - 1) * (w - 10) / (ValQtt - 1);
			xxx = x + i * (w - 10) / (ValQtt - 1);
			yy = y + (Values[i - 1] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * (h - 10);
			yyy = y + (Values[i] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * (h - 10);
			Html += SVG_Line(xx, yy, xxx, yyy);
		}
	} else {
		var Coords = [];
		var xx, yy;
		Coords.push(x);
		Coords.push(y);
		for (i = 0; i < ValQtt; i++) {
			xx = x + i * (w - 10) / (ValQtt - 1);
			yy = y + (Values[i] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * (h - 10);
			Coords.push(xx);
			Coords.push(yy);
		}
		Coords.push(x + w - 10);
		Coords.push(y);
		//Html += SVG_Poly(Coords);
	}
	if ((Label) && (Label != "")) {
		var Len = Label.length;
		SVG_SaveSettings();
		SVG_SetFontSize(10);
		SVG_SetPen(1);
		SVG_SetOpacity(0.5);
		SVG_SetFill("#ffffff");
		SVG_SetInk("#000000");
		SVG_SetFont("arial");
		SVG_SetFontRotate(0);
		SVG_SetFontAlign("right");
		var Bubble = [];
		yy = y + (Values[ValQtt - 1] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * (h - 10);
		Bubble.push(x + w - 10);
		Bubble.push(yy);
		Bubble.push(x + w - 10 + 3);
		Bubble.push(yy + 10);
		Bubble.push(x + w - 10 + 15);
		Bubble.push(yy + 10);
		Bubble.push(x + w - 10 + 15);
		Bubble.push(yy + 28);
		Bubble.push(x + w - 10 + 15 - 5.5 * Len);
		Bubble.push(yy + 28);
		Bubble.push(x + w - 10 + 15 - 5.5 * Len);
		Bubble.push(yy + 10);
		Bubble.push(x + w - 10 - 3);
		Bubble.push(yy + 10);
		//Html += SVG_Poly(Bubble);
		Html += SVG_Text(x + w, yy + 15, Label)
		SVG_RestoreSettings();
	}
	return (Html);
}
/////////////////////////////////////////////
// return code to draw values for a line graph
// x,y: origin (bottom left) of graph
// w,h: width and height of graph
// Step: for vertical axis
// Values: an array of values
/////////////////////////////////////////////
function SVG_GraphLineGrads(x, y, w, h, Step, Values) {
	var Html = "";
	var yy;
	if (Step > 0) {
		for (Val = Math.floor(SVG_Settings["GraphScaleMin"] / Step) * Step; Val <= SVG_Settings["GraphScaleMax"]; Val += Step) {
			yy = y + (Val - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * (h - 10);
			Html += SVG_Line(x, yy, x + w - 10, yy);
		}
	}
	if (Values) {
		if (Values.constructor === Array) {
			var xx, yy;
			for (i = 0; i < Values.length; i++) {
				if (i != 0) {
					xx = x + i / (Values.length - 1) * (w - 10);
					Html += SVG_Line(xx, y, xx, y + h - 10);
				}
			}
		} else {
			var xx, yy;
			for (i = 0; i <= Values; i++) {
				if (i != 0) {
					xx = x + i / Values * (w - 10);
					Html += SVG_Line(xx, y, xx, y + h - 10);
				}
			}
		}
	}
	return (Html);
}
    /////////////////////////////////////////////
    // return code to draw values for a line graph
    // x,y: origin (bottom left) of graph
    // w,h: width and height of graph
    // Values: an array of values
    /////////////////////////////////////////////
function SVG_GraphLineInteractive(x, y, w, h, Values) {
    var i;
    var ValQtt;
    var Html = "";
    ValQtt = Values.length;
    if (SVG_Settings["Fill"] == "") {
        var xx, yy, Lastx, Lasty;
        Lastx = 0;
        Lasty = 0;

        for (i = 0; i < ValQtt; i++) {
            xx = x + i * (w - 10) / (ValQtt - 1);
            yy = y + (Values[i] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * (h - 10);
            if (((Lastx - xx) * (Lastx - xx) + (Lasty - yy) * (Lasty - yy)) > 100) {
                Html += SVG_HoverDot(xx, yy, 5, Values[i] + " " + SVG_Settings["GraphVerticalUnit"]);
                Lastx = xx;
                Lasty = yy;
            }
        }
    }
    return (Html);
}