// requires svg_base.js to work !
/////////////////////////////////////////////
// return code to draw axis for a bar graph
// x,y: origin (bottom left) of graph
// w,h: width and height of graph
// VerticalLabels: a number (then h-axis is supposed 0..number)
//                   [a] (then axis is supposed 0..a)
//                   [a,b] (then axis is supposed a..b)
//                   [a,b,c...] values are displayed (if space allows)
/////////////////////////////////////////////
function SVG_HBarGraphAxis(x, y, w, h, VerticalLabels) {
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
	Html += SVG_Text(x + w - 10, y + 5, SVG_Settings["GraphHorizontalUnit"] );
	SVG_SetFontRotate(0);
	Html += SVG_Text(x + 200, y + h - 372, SVG_Settings["GraphVerticalLabel"] );
	Html += SVG_Text(x + 10, y + h - 5, SVG_Settings["GraphVerticalUnit"] );
	// vertical axis scale
	SVG_SetFontRotate(0);
	Html += SVG_Text(x - 50, y + h - 145,	SVG_Settings["GraphHorizontalLabel"] );
	SVG_SetFontAlign("left");
	Html += SVG_Text(x + w - 15, y - 12, SVG_Settings["GraphScaleMax"]);
	Html += SVG_Text(x - 5, y - 12, SVG_Settings["GraphScaleMin"]);
	// horizontal axis scale
	SVG_SetFontRotate(0);
	SVG_SetFontAlign("right");
	if (VerticalLabels.constructor === Array) {
		if (VerticalLabels.length == 1) {
			Html += SVG_Text(x + 2, y - 5, "0");
			Html += SVG_Text(x + 2, y - 5 + h - 10, VerticalLabels[0]);
		} else if (VerticalLabels.length == 2) {
			Html += SVG_Text(x + 2, y - 5, VerticalLabels[0]);
			Html += SVG_Text(x + 2, y - 5 + h - 10, VerticalLabels[1]);
		} else if (VerticalLabels.length > 2) {
			var i;
			var yy, yyy;
			var LastY = y - 100;
			for (i = 0; i < VerticalLabels.length; i++) {
				yy = y + ((i + 0.5) / (VerticalLabels.length)) * (h - 10);
				if ((yy > LastY + 10) && (yy < y + h - 15)) {
					LastY = yy;
					Html += SVG_Text(x - 5, yy, VerticalLabels[i]);
				}
				yyy = y + ((i + 1) / (VerticalLabels.length)) * (h - 10);
				Html += SVG_Line(x - 5, yyy, x + 5, yyy);
			}
		}
	} else {
		Html += SVG_Text(x + 2, y - 5, "0");
		Html += SVG_Text(x + 2, y - 5 + h - 10, VerticalLabels);
	}

	SVG_RestoreSettings();
	return (Html);
}
    /////////////////////////////////////////////
    // return code to draw values for a bar graph
    // x,y: origin (bottom left) of graph
    // w,h: width and height of graph
    // Values: an array of values
    // Label: an optional label for the bar set
    // ColWidth: optional collumn width (1=full, 0.5=half, etc...)
    // ColOffset: optional collumn horizontal offset (0.5=move column 0.5 col width to the right)
    // ColZeros: optional array defining a vertical offset for each collumns
    //          (this is used for "stacked" collumns)
    /////////////////////////////////////////////
function SVG_HBarGraph(x, y, w, h, Values, Label, ColWidth, ColOffset, ColZeros) {
	var i;
	var ValQtt = Values.length;
	var Html = "";
	var ColW = (h - 10) / ValQtt;
	var xx, yy, ww, hh;

	if ((typeof ColOffset == 'undefined') || (ColOffset != 0)) {
		ColOffset = 1 * ColOffset;
	} else {
		ColOffset = 0;
	}
	if ((typeof ColWidth == 'undefined') || (ColWidth != 0)) {
		ColWidth = 1 * ColWidth;
	} else {
		ColWidth = 0;
	}

	for (i = 0; i < ValQtt; i++) {
		xx = x;
		yy = y + i * ColW + ColOffset * ColW;
		hh = ColW * ColWidth;
		ww = (Values[i] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * (w - 10);
		if (typeof ColZeros != 'undefined') {
			xx += (ColZeros[i] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * (w - 10);
		}
		Html += SVG_Rectangle(xx, yy, ww, hh);
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
		//Html += SVG_Text(x + w, yy + 15, Label)
		SVG_RestoreSettings();
	}
	return (Html);
}
    /////////////////////////////////////////////
    // return code to draw vertical lines for bargraph
    // x,y: origin (bottom left) of graph
    // w,h: width and height of graph
    // Step: for vertical axis
    // Values: an array of values
    /////////////////////////////////////////////
function SVG_HBarGraphGrads(x, y, w, h, Step, Values) {
    var Html = "";
    var xx, yy;
    if (Step > 0) {
        for (Val = Math.floor(SVG_Settings["GraphScaleMin"] / Step) * Step; Val <= SVG_Settings["GraphScaleMax"]; Val += Step) {
            xx = x + (Val - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * (w - 10);
            Html += SVG_Line(xx, y, xx, y + h - 10);
        }
    }
    if (Values) {
        if (Values.constructor === Array) {
            for (i = 0; i < Values.length; i++) {
                if (i != 0) {
                    yy = y + i / (Values.length - 1) * (h - 10);
                    Html += SVG_Line(x, yy, x + w - 10, yy);
                }
            }
        } else {
            for (i = 0; i <= Values; i++) {
                if (i != 0) {
                    yy = y + i / Values * (h - 10);
                    Html += SVG_Line(x, yy, x + w - 10, yy);
                }
            }
        }
    }
    return (Html);
}

var SVG_HBarChartShowVal_Last = "";
var SVG_HBarGraph_BarNames = 0;

function SVG_HBarChartShowVal(Name) {
    if (SVG_HBarChartShowVal_Last != "") {
        document.getElementById(SVG_HBarChartShowVal_Last).style.display = "None";
    }
    if (Name != "") {
        document.getElementById(Name).style.display = "";
    }
    SVG_HBarChartShowVal_Last = Name;
}

/////////////////////////////////////////////
// return code to make graph interactive
// this is essentially the same parms than to draw the graph.
// x,y: origin (bottom left) of graph
// w,h: width and height of graph
// Values: an array of values
// Label: an optional label for the bar set
// ColWidth: optional collumn width (1=full, 0.5=half, etc...)
// ColOffset: optional collumn horizontal offset (0.5=move column 0.5 col width to the right)
// ColZeros: optional array defining a vertical offset for each collumns
//          (this is used for "stacked" collumns)
/////////////////////////////////////////////
function SVG_HBarGraphInteractive(x, y, w, h, Values, Label, ColWidth, ColOffset, ColZeros) {
    var i;
    var ValQtt = Values.length;
    var Html = "";
    var ColW = (h - 10) / ValQtt;
    var xx, yy, ww, hh;

    if ((typeof ColOffset == 'undefined') || (ColOffset != 0)) {
        ColOffset = 1 * ColOffset;
    } else {
        ColOffset = 0;
    }
    if ((typeof ColWidth == 'undefined') || (ColWidth != 0)) {
        ColWidth = 1 * ColWidth;
    } else {
        ColWidth = 0;
    }

    SVG_SaveSettings();
    SVG_SetInk("#000000");
    SVG_SetFill("#ffffff");
    SVG_SetPen(0.5);
    SVG_SetFontSize(10);
    SVG_SetFont("arial");
    SVG_SetFontRotate(0);
    SVG_SetFontAlign("left");
    for (i = 0; i < ValQtt; i++) {
        xx = x;
        yy = y + i * ColW + ColOffset * ColW;
        hh = ColW * ColWidth;
        ww = (Values[i] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * (w - 10);
        if (typeof ColZeros != 'undefined') {
            xx += (ColZeros[i] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * (w - 10);
        }
        SVG_BarGraph_BarNames += 1;
        Html += SVG_GroupStart("HBarGraphBar" + SVG_BarGraph_BarNames, "onmouseover=\"SVG_HBarChartShowVal('HBarGraphBubble" + SVG_BarGraph_BarNames + "')\"");
        SVG_SetOpacity(0.0);
        Html += SVG_Rectangle(xx, yy, ww, hh);
        Html += SVG_GroupClose("");
        Html += SVG_GroupStart("HBarGraphBubble" + SVG_BarGraph_BarNames, 'style="display:none;"');
        SVG_SetOpacity(0.7);
        Html += SVG_Circle(xx + ww / 2, yy + hh / 2, 2);
        Html += SVG_Line(xx + ww / 2 + 2, yy + hh / 2, xx + ww / 2 + 10, yy + hh / 2);
        Html += SVG_Rectangle(xx + ww / 2 + 10, yy + hh / 2 - 10, 100, 20);
        SVG_SetOpacity(1);
        Html += SVG_Text(xx + ww / 2 + 15, yy + hh / 2 - 5, Values[i] + " " + SVG_Settings["GraphHorizontalUnit"]);
        Html += SVG_GroupClose("");
    }
    SVG_RestoreSettings();

    return (Html);
}
//////////////////////////////////////////////////////////////
// Display a legend on the graph

function SVG_Legend(JsonData,x,y){
    SVG_SetInk("#000000");
    SVG_SetPen(0.5);
    SVG_SetFontSize(16);
    SVG_SetFont("arial");
    SVG_SetFontRotate(0);
    SVG_SetFontAlign("left");

    for (i = 0; i < JsonData.Data.length; i++) {
        SVG_SetFill(JsonData.Data[i].Color);
        SVG_Rectangle(x+i*10,y,20,10,0);
        SVG_Text(x+i*10+5,y+30,JsonData.Data[i].Label);
    }

}

//////////////////////////////////////////////////////////////
// Display a SimpleBarGraph inside a DIV
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
//    DisplaySimpleBarGraph("toto",JsonData);
///////////////////////////////////////////////////////////////
function DisplaySimpleHBarGraph(DivId, JsonData) {

    var Element = document.getElementById(DivId);
    if (Element == null) {
        alert("There is no DIV named " + DivId + " sorry !");
    } else {
        var Box = GetLayerPosition(DivId);
        var Width = Box.right - Box.left;
        var Height = Box.bottom - Box.top;
        // 0,0 bottom left
        // x,y: origin (bottom left) of graph
        var DisplayX = Width * 0.1;
        var DisplayY = Height * 0.1;
        // w,h: width and height of graph
        var DisplayWidth = Width * 0.8;
        var DisplayHeight = Height * 0.8;
        var Labels = [];
        var ColWidth = 0.6;
        var ColOffset = 0.2;
        //var ColZeros= ;

        var Html = SVG_Open(Width, Height);


        if (Width < 100) {
            alert("DIV " + DivId + " is very narrow (" + Width + "px) consider at least 100px !");
        } else if (Height < 50) {
            alert("DIV " + DivId + " isn't tall enough (" + Height + "px) consider at least 50px !");
        } else if ((JsonData.Data) && (JsonData.Data.length > 0)) {
            var i;
            var Max = 1;
            var Min = 0;

            // Lets start with the axis: determine max value
            if (JsonData.vertical_max) {
                SVG_SetGraphScaleMax(JsonData.vertical_max);
            } else {
                SVG_SetGraphScaleMax(0);
                for (i = 0; i < JsonData.Data.length; i++) {
                    SVG_SetGraphScaleMax(JsonData.Data[i].Values, 1);
                }
            }

            SVG_SetGraphScaleMin(0);
            // decide for unit on vertical axis
            if (JsonData.vertical_unit) {
                SVG_SetGraphVerticalUnit(JsonData.vertical_unit);
            }
            /*else {
				SVG_SetGraphVerticalUnit('');
			}*/
            // decide for unit on horizontal axis
            if (JsonData.horizontal_unit) {
                SVG_SetGraphHorizontalUnit(JsonData.horizontal_unit);
            }
            // set the vertical Label
            if (JsonData.Vertical_label) {
                SVG_SetGraphVerticalLabel(JsonData.Vertical_label);
            }
            // set the horizontal Label
            if (JsonData.Horizontal_label) {
                SVG_SetGraphHorizontalLabel(JsonData.Horizontal_label);
            }

            SVG_SetOpacity(1); // opacity of axis

            // Lets start with the axis: determine max value
            if (JsonData.axis_color) {
                SVG_SetInk(JsonData.axis_color);
            } else {
                SVG_SetInk("#000000"); // Default to black axis color
            }

            if (JsonData.Labels) {
                Labels = JsonData.Labels;
            } else {
                for (i = 0; i < JsonData.Data.length; i++) {
                    Labels.push(JsonData.Data[i][0]);
                }
            }

            // draw axis
            Html += SVG_HBarGraphAxis(DisplayX, DisplayY, DisplayWidth, DisplayHeight, Labels);
            //Html += SVG_HBarGraphGrads(DisplayX, DisplayY, DisplayWidth, DisplayHeight, 2, 9); // draw vertical lines

            if (JsonData.col_width) {
                ColWidth = JsonData.col_width;
            }
            if (JsonData.col_offset) {
                ColOffset = JsonData.col_offset;
            }

            // and now let's draw curves
            for (i = 0; i < JsonData.Data.length; i++) {
                SVG_SetInk("#000000");
                SVG_SetPen(2);
                SVG_SetOpacity(0.5);
                if (JsonData.Data[i].Color) {
                    SVG_SetFill(JsonData.Data[i].Color);
                } else {
                    SVG_SetFill(JsonData.Data[i].Color=SVG_NewColor(i));
                }

                if (i == 0) {
                    if (JsonData.Data[i].Label) {
                        Html += SVG_HBarGraph(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, JsonData.Data[i].Label, ColWidth, ColOffset);
                    } else {
                        Html += SVG_HBarGraph(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, "", ColWidth, ColOffset);
                    }
                } else {
                    if (JsonData.Data[i].Label) {
                        Html += SVG_HBarGraph(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, JsonData.Data[i].Label, ColWidth, ColOffset, JsonData.Data[0].Values);
                    } else {
                        Html += SVG_HBarGraph(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, "", ColWidth, ColOffset, JsonData.Data[0].Values);
                    }
                }

            }

            for (i = 0; i < JsonData.Data.length; i++) {
                if (i == 0) {
                    if (JsonData.Data[i].Label) {
                        Html += SVG_HBarGraphInteractive(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, JsonData.Data[i].Label, ColWidth, ColOffset);
                    } else {
                        Html += SVG_HBarGraphInteractive(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, "", ColWidth, ColOffset);
                    }
                } else {
                    if (JsonData.Data[i].Label) {
                        Html += SVG_HBarGraphInteractive(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, JsonData.Data[i].Label, ColWidth, ColOffset, JsonData.Data[0].Values);
                    } else {
                        Html += SVG_HBarGraphInteractive(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, "", ColWidth, ColOffset, JsonData.Data[0].Values);
                    }
                }

            }
            html += SVG_Legend(JsonData,Width * 0.6,Height * 0.3);
            Html += SVG_Close();
            document.getElementById(DivId).innerHTML = Html;
            //SVG_RestoreSettings();
        } else {
            alert("Should have a JSON of the form { data:[ { values:[] },... ] }");
        }
    }
    SVG_SettingStack = [];
}

// Jquery ajax call to get the json and display the graph
function CallDisplaySimpleHBarGraph(JsonUrl, Token, TargetID) {
    jQuery.ajax({
        url: JsonUrl,
        dataType: "json",
        contentType: "application/json; charset=utf-8",
        headers: {
            'Authorization': 'Bearer ' + Token
        },
        success: function(data) {
            DisplaySimpleHBarGraph(TargetID, data);
        }
    });
};