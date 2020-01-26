// requires svg_base.js to work !
/////////////////////////////////////////////
// return code to draw axis for a bar graph
// x,y: origin (bottom left) of graph
// w,h: width and height of graph
// HorizontalLabels: a number (then h-axis is supposed 0..number)
//                   [a] (then axis is supposed 0..a)
//                   [a,b] (then axis is supposed a..b)
//                   [a,b,c...] values are displayed (if space allows)
/////////////////////////////////////////////
function SVG_BarGraphAxis(x, y, w, h, HorizontalLabels) {
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
	// Set horizontal label
	Html += SVG_Text(x + 200, y + h - 372, SVG_Settings["GraphHorizontalLabel"] );
	Html += SVG_Text(x + 10, y + h - 10, SVG_Settings["GraphVerticalUnit"] );
	// vertical axis scale
	SVG_SetFontRotate(0);
	Html += SVG_Text(x - 50, y + h - 145,	SVG_Settings["GraphVerticalLabel"] );
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
			var xx, xxx;
			var LastX = x - 100;
			for (i = 0; i < HorizontalLabels.length; i++) {
				xx = x + ((i + 0.5) / (HorizontalLabels.length)) * (w - 10);
				if ((xx > LastX + 10) && (xx < x + 2 + w - 15)) {
					LastX = xx;
					Html += SVG_Text(xx + 2, y - 5, HorizontalLabels[i]);
				}
				xxx = x + ((i + 1) / (HorizontalLabels.length)) * (w - 10);
				Html += SVG_Line(xxx, y - 5, xxx, y + 5);
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
function SVG_BarGraph(x, y, w, h, Values, Label, ColWidth, ColOffset, ColZeros) {
	var i;
	var ValQtt = Values.length;
	var Html = "";
	var ColW = (w - 10) / ValQtt;
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
		xx = x + i * ColW + ColOffset * ColW;
		yy = y;
		ww = ColW * ColWidth;
		hh = (Values[i] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * (h - 10);
		if (typeof ColZeros != 'undefined') {
			yy += (ColZeros[i] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * (h - 10);
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
function SVG_BarGraphGrads(x, y, w, h, Step, Values) {
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

var SVG_BarChartShowVal_Last = "";
var SVG_BarGraph_BarNames = 0;

function SVG_BarChartShowVal(Name) {
    if (SVG_BarChartShowVal_Last != "") {
        document.getElementById(SVG_BarChartShowVal_Last).style.display = "None";
    }
    if (Name != "") {
        document.getElementById(Name).style.display = "";
    }
    SVG_BarChartShowVal_Last = Name;
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
function SVG_BarGraphInteractive(x, y, w, h, Values, Label, ColWidth, ColOffset, ColZeros) {
    var i;
    var ValQtt = Values.length;
    var Html = "";
    var ColW = (w - 10) / ValQtt;
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
        xx = x + i * ColW + ColOffset * ColW;
        yy = y;
        ww = ColW * ColWidth;
        hh = (Values[i] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * (h - 10);
        if (typeof ColZeros != 'undefined') {
            yy += (ColZeros[i] - SVG_Settings["GraphScaleMin"]) / (SVG_Settings["GraphScaleMax"] - SVG_Settings["GraphScaleMin"]) * (h - 10);
        }
        SVG_BarGraph_BarNames += 1;
        Html += SVG_GroupStart("BarGraphBar" + SVG_BarGraph_BarNames, "onmouseover=\"SVG_BarChartShowVal('BarGraphBubble" + SVG_BarGraph_BarNames + "')\"");
        SVG_SetOpacity(0.0);
        Html += SVG_Rectangle(xx, yy, ww, hh);
        Html += SVG_GroupClose("");
        Html += SVG_GroupStart("BarGraphBubble" + SVG_BarGraph_BarNames, 'style="display:none;"');
        SVG_SetOpacity(0.7);
        Html += SVG_Circle(xx + ww / 2, yy + hh / 2, 2);
        Html += SVG_Line(xx + ww / 2 + 2, yy + hh / 2, xx + ww / 2 + 10, yy + hh / 2);
        Html += SVG_Rectangle(xx + ww / 2 + 10, yy + hh / 2 - 10, 100, 20);
        SVG_SetOpacity(1);
        Html += SVG_Text(xx + ww / 2 + 15, yy + hh / 2 - 5, Values[i] + " " + SVG_Settings["GraphVerticalUnit"]);
        Html += SVG_GroupClose("");
    }
    SVG_RestoreSettings();

    return (Html);
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
function DisplaySimpleBarGraph(DivId, JsonData) {

    var Element = document.getElementById(DivId);
    if (Element == null) {
        alert("There is no DIV named " + DivId + " sorry !");
    } else {
        var Box = GetLayerPosition(DivId);
        var Width = Box.right - Box.left;
        var Height = Box.bottom - Box.top;
        var Html = SVG_Open(Width, Height);
        var Labels = [];
        var ColWidth = 0.8;
        var ColOffset = 0.1;

        //var ColZeros= ;

        // 0,0 bottom left
        // x,y: origin (bottom left) of graph
        var DisplayX = Width * 0.1;
        var DisplayY = Height * 0.1;
        // w,h: width and height of graph
        var DisplayWidth = Width * 0.8;
        var DisplayHeight = Height * 0.8;

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
            // decide for unit on vertical axis
            if (JsonData.vertical_unit) {
                SVG_SetGraphVerticalUnit(JsonData.vertical_unit);
            }

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

            SVG_SetOpacity(1);
            // opacity of axis
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
            Html += SVG_BarGraphAxis(DisplayX, DisplayY, DisplayWidth, DisplayHeight, Labels);

            if (JsonData.ColWidth) {
                ColWidth = JsonData.ColWidth;
            }
            if (JsonData.ColOffset) {
                ColOffset = JsonData.ColOffset;
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

                if (JsonData.Data[i].Label) {
                    if (i == 0) {
                        Html += SVG_BarGraph(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, JsonData.Data[i].Label, ColWidth, ColOffset);
                    } else {
                        Html += SVG_BarGraph(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, JsonData.Data[i].Label, ColWidth, ColOffset, JsonData.Data[0].Values);
                    }
                } else {
                    if (i == 0) {
                        Html += SVG_BarGraph(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, "", ColWidth, ColOffset);
                    } else {
                        Html += SVG_BarGraph(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, "", ColWidth, ColOffset, JsonData.Data[0].Values);
                    }

                }
            }

            for (i = 0; i < JsonData.Data.length; i++) {
                if (JsonData.Data[i].Label) {
                    if (i == 0) {
                        Html += SVG_BarGraphInteractive(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, JsonData.Data[i].Label, ColWidth, ColOffset);
                    } else {
                        Html += SVG_BarGraphInteractive(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, JsonData.Data[i].Label, ColWidth, ColOffset, JsonData.Data[0].Values);
                    }
                } else {
                    if (i == 0) {
                        Html += SVG_BarGraphInteractive(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, "", ColWidth, ColOffset);
                    } else {
                        Html += SVG_BarGraphInteractive(DisplayX, DisplayY, DisplayWidth, DisplayHeight, JsonData.Data[i].Values, "", ColWidth, ColOffset, JsonData.Data[0].Values);
                    }
                }
            }


            Html += SVG_Close();
            document.getElementById(DivId).innerHTML = Html;
        } else {
            alert("Should have a JSON of the form { data:[ { values:[] },... ] }");
        }
    }
}

function CallDisplaySimpleBarGraph(JsonUrl, Token, TargetID) {
    jQuery.ajax({
        url: JsonUrl,
        dataType: "json",
        contentType: "application/json; charset=utf-8",
        headers: {
            'Authorization': 'Bearer ' + Token
        },
        success: function(data) {
            DisplaySimpleBarGraph(TargetID, data);
        }
    });
}