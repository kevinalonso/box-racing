///////////////////////////////////////////////////////////////////////////////////////
// this is a quick and dirty code. All function and variables should be reviewed to
// have a separated namespace
///////////////////////////////////////////////////////////////////////////////////////

var Semtree_TreeDivId = "Viewport";
var Semtree_CommentDivId = "ViewportComment";
var Semtree_AdjustButtons = "AdjustWords";
var SemTree_SemTreeData;


var UniqueId = 0;

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Render a "main" node of a semtree
// those looking like a rectangle with a downpointing arrow
// Parameters
//    SemTree: the semantic screen data structure
//    i      ; number of the node in the SemTree
//    x,y    : position where to render
//    Scale  ; scale
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function RenderMainNode(SemTree, i, x, y, Scale) {
    var Html = "";
    var X = (SemTree[i].x - XOffset) * Zoom * Scale + x;
    var Y = (SemTree[i].y - YOffset) * Zoom * Scale + y;
    UniqueId += 1;
    if (HighlightNode == i) {
        SVG_SetPen(2);
    } else {
        SVG_SetPen(1);
    }
    if ((HighlightNode == i) || (HighlightNode == -1)) {
        SVG_SetOpacity(1);
    } else {
        SVG_SetOpacity(0.2);
    }
    SVG_SetInk("#ff0000");
    SVG_SetFill("#ff0000");
    Html += SVG_Circle(X, Y, 4);
    SVG_SetInk("#000000");
    if (HighlightNode == i) {
        SVG_SetFill("#ffdddd");
    } else {
        SVG_SetFill("#ffffff");
    }
    Html += SVG_GroupStart("noname" + UniqueId, "style=\"cursor:pointer;\" onmousedown=\"Highlight(" + i + ",-1)\"");
    if (HighlightNode == i) {
        Html += SVG_Poly([X, Y, X, Y + 35, X + 100, Y + 35, X + 100, Y + 10, X + 10, Y + 10, X, Y]);
        SVG_SetFontSize("18");
    }
    else {
        Html += SVG_Poly([X, Y, X, Y + 30, X + 100, Y + 30, X + 100, Y + 10, X + 10, Y + 10, X, Y]);
        SVG_SetFontSize("15");
    }
    SVG_SetFont("arial");
    SVG_SetFontRotate(0);
    SVG_SetFontAlign("left");
    Html += SVG_Text(X + 5, Y + 15, SemTree[i].label);
    Html += SVG_GroupClose("");
    return (Html);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Render a "sub" node of a semtree
// those looking like a label with a leftpointing arrow
// Parameters
//    SemTree: the semantic screen data structure
//    i      ; number of the main node node in the SemTree
//    j      : number of the subnode to render in that main node
//    x,y    : position where to render
//    Scale  ; scale
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function RenderSubNode(SemTree, i, j, x, y, Scale) {
    var Html = "";
    var X = (SemTree[i].x - XOffset) * Zoom * Scale + x;
    var Y = (SemTree[i].y - YOffset) * Zoom * Scale + y;
    var XX = (SemTree[i].subnodes[j].x - XOffset) * Zoom * Scale + x;
    var YY = (SemTree[i].subnodes[j].y - YOffset) * Zoom * Scale + y;
    UniqueId += 1;
    SVG_SetInk("#ff0000");
    if ((HighlightNode == i) || (HighlightNode == -1)) {
        SVG_SetOpacity(0.5);
    } else {
        SVG_SetOpacity(0.1);
    }
    Html += SVG_Line(X, Y, XX, YY);
    Html += SVG_GroupStart("noname" + UniqueId, "style=\"cursor:pointer;\" onmousedown=\"Highlight(" + i + "," + j + ")\"");
    if ((HighlightNode == i) || (HighlightNode == -1)) {
        SVG_SetOpacity(0.7);
    } else {
        SVG_SetOpacity(0.2);
    }
    Html += SVG_Line(XX - 3, YY - 3, XX + 3, YY + 3);
    Html += SVG_Line(XX - 3, YY + 3, XX + 3, YY - 3);
    SVG_SetInk("#000000");
    if ((HighlightNode == i) && (HighlightSubnode == j)) {
        SVG_SetFill("#ff9999");
    }
    else if (MultiNodes[SemTree[i].subnodes[j].label] == "y") {
        SVG_SetFill("#dddddd");
    }
    else {
        SVG_SetFill("#ffffff");
    }
    SVG_SetFont("arial");
    SVG_SetFontRotate(0);
    SVG_SetFontAlign("left");
    if (HighlightNode == i) {
        Html += SVG_Poly([XX + 1, YY, XX + 10, YY + 10, XX + 100, YY + 10, XX + 100, YY - 10, XX + 10, YY - 10, XX + 1, YY]);
        SVG_SetFontSize("14");
        Html += SVG_Text(XX + 10, YY - 5, SemTree[i].subnodes[j].label);
    }
    else {
        Html += SVG_Poly([XX + 1, YY, XX + 6, YY + 6, XX + 100, YY + 6, XX + 100, YY - 6, XX + 6, YY - 6, XX + 1, YY]);
        SVG_SetFontSize("10");
        Html += SVG_Text(XX + 5, YY - 3, SemTree[i].subnodes[j].label);
    }
    Html += SVG_GroupClose("");
    return (Html);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// those numbers are computed to make sure the graph fits in the div...
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
var XOffset = 0;
var YOffset = 0;
var ZOffset = 0;
var Zoom = 1;
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ... and here it is how
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ComputeOffsetAndZoom(SemTree) {
    var i, j;
    var minx = SemTree[0].x;
    var miny = SemTree[0].y;
    var minz = SemTree[0].z;
    var maxx = SemTree[0].x;
    var maxy = SemTree[0].y;
    var maxz = SemTree[0].z;
    for (i = 0; i < SemtreeLength(SemTree); i++) {
        if (SemTree[i].x < minx) {
            minx = SemTree[i].x;
        }
        if (SemTree[i].y < miny) {
            miny = SemTree[i].y;
        }
        if (SemTree[i].z < minz) {
            minz = SemTree[i].z;
        }
        if (SemTree[i].x > maxx) {
            maxx = SemTree[i].x;
        }
        if (SemTree[i].y > maxy) {
            maxy = SemTree[i].y;
        }
        if (SemTree[i].z > maxz) {
            maxz = SemTree[i].z;
        }
        for (j = 0; j < (SemTree[i].subnodes).length - 1; j++) {
            if (SemTree[i].subnodes[j].x < minx) {
                minx = SemTree[i].subnodes[j].x;
            }
            if (SemTree[i].subnodes[j].y < miny) {
                miny = SemTree[i].subnodes[j].y;
            }
            if (SemTree[i].subnodes[j].z < minz) {
                minz = SemTree[i].subnodes[j].z;
            }
            if (SemTree[i].subnodes[j].x > maxx) {
                maxx = SemTree[i].subnodes[j].x;
            }
            if (SemTree[i].subnodes[j].y > maxy) {
                maxy = SemTree[i].subnodes[j].y;
            }
            if (SemTree[i].subnodes[j].z > maxz) {
                maxz = SemTree[i].subnodes[j].z;
            }
        }
    }
    XOffset = (maxx + minx) / 2;
    YOffset = (maxy + miny) / 2;
    ZOffset = (maxz + minz) / 2;
    Zoom = 1 / (maxx - minx) * 1.5;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Create SVG code for displaying the semtree
// parameters:
//     Semtree: the semtree to render
//     x,y    : position
//     Scale  : scale
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function RenderSemTree(SemTree, x, y, Scale) {
    var Html = "";
    var minx = SemTree[0].x;
    var miny = SemTree[0].y;
    var minz = SemTree[0].z;
    var maxx = SemTree[0].x;
    var maxy = SemTree[0].y;
    var maxz = SemTree[0].z;
    var i, j;
    ComputeOffsetAndZoom(SemTree);

    for (i = 0; i < SemtreeLength(SemTree); i++) {
        var PatateCoords = [];
        for (j = 0; j < SemsubtreeLength(SemTree, i); j++) {
            PatateCoords.push((SemTree[i].subnodes[j].x - XOffset) * Zoom * Scale + x);
            PatateCoords.push((SemTree[i].subnodes[j].y - YOffset) * Zoom * Scale + y);
        }
        PatateCoords.push((SemTree[i].x - XOffset) * Zoom * Scale + x);
        PatateCoords.push((SemTree[i].y - YOffset) * Zoom * Scale + y);
        if (HighlightNode == -1) {
            SVG_SetOpacity(0.1);
        }
        else if (HighlightNode == i) {
            SVG_SetOpacity(0.3);
        }
        else {
            SVG_SetOpacity(0.1);
        }
        SVG_SetFill(SVG_NewColor(i));
        Html += SVG_Patate(20, PatateCoords);
    }
    for (i = 0; i < SemtreeLength(SemTree); i++) {
        for (j = 0; j < SemsubtreeLength(SemTree, i); j++) {
            Html += RenderSubNode(SemTree, i, j, x, y, Scale);
        }
    }
    for (i = 0; i < SemtreeLength(SemTree); i++) {
        Html += RenderMainNode(SemTree, i, x, y, Scale);
    }
    return (Html);
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////
// id of the node and subnode which are highlighted.
// or -1 if no
//////////////////////////////////////////////////////////////////////////////////////////////////////////
var HighlightNode = -1;
var HighlightSubnode = -1;


//////////////////////////////////////////////////////////////////////////////////////////////////////////
// Hisghlight a node (and possibly a subnode)
// This also generate comment in a div about the selected node
// parametes
//    i: node id
//    j: subnode id for that node
//////////////////////////////////////////////////////////////////////////////////////////////////////////
function Highlight(i, j) {
    if ((i == HighlightNode) && (j == HighlightSubnode)) {
        HighlightNode = -1;
        HighlightSubnode = -1;
    }
    else {
        HighlightNode = i;
        HighlightSubnode = j;
    }
    GenerateComment(HighlightNode, HighlightSubnode);
    Step = 0;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////
// Display the semantic tree in a div
//////////////////////////////////////////////////////////////////////////////////////////////////////////
function DisplaySemTree(SemTree, DivId) {
    var DivId = Semtree_TreeDivId;
    var Width = GetLayerWidth(DivId);
    var Height = GetLayerHeight(DivId);
    var x = Width / 2;
    var y = Height / 2;
    var Scale = (Width + Height) / 4;
    var Html = "";
    Html = SVG_Open(Width, Height);
    Html += RenderSemTree(SemTree, x, y, Scale);
    Html += SVG_Close();
    document.getElementById(DivId).innerHTML = Html;
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////
// search for similar subnodes (ie when you click on a given subnode,
// you want to know if that same subnode also exists in other nodes.
// You usually want to know this fast, so you have to search for them
// once and cache the result in an array
//////////////////////////////////////////////////////////////////////////////////////////////////////////
var SimilarSubnodes = [];
var MultiNodes = {};

function SearchSimilarSubnodes(SemTree) {
    var i, j, ii, jj;
    for (i = 0; i < SemtreeLength(SemTree); i++) {
        for (j = i + 1; j < SemtreeLength(SemTree); j++) {
            for (ii = 0; ii < SemsubtreeLength(SemTree, i); ii++) {
                for (jj = 0; jj < SemsubtreeLength(SemTree, j); jj++) {
                    if (SemTree[j].subnodes[jj].label == SemTree[i].subnodes[ii].label) {
                        SimilarSubnodes.push({"node1": i, "subnode1": ii, "node2": j, "subnode2": jj});
                        MultiNodes[SemTree[j].subnodes[jj].label] = "y";
                    }
                }
            }
        }
    }

}

var Step = 0;
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// those function return default distances between node and subnodes, nodes and other
// nodes,
function DefaultNodeNodeDist(i, j) {
    var Answer = 7;
    if (HighlightNode == i) {
        Answer = 8;
    }
    return (Answer);
}

function DefaultSubnodeSubnodeDist(i, j, k) {
    var Answer = 1.5;
    if (HighlightNode == i) {
        Answer = 3;
    }
    return (Answer);
}

function DefaultNodeSubnodeDist(i, j) {
    var Answer = 0.5;
    if (HighlightNode == i) {
        Answer = 1;
    }
    return (Answer);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// compute the next step in physic simulation for the semtree
///////////////////////////////////////////////////////////////////////////////////////////////////////////
function UpdateSemTree(SemTree) {
    var i, j, k;
    var NodeNodeIters = 50;
    var NodeSubnodeIters = 50;
    var GetTogetherIters = 50;
//   var DefaultNodeNodeDist      =7;
//   var DefaultNodeSubnodeDist   =0.5;
//   var DefaultSubnodeSubnodeDist=1.5;
    // spread main nodes
    for (; NodeNodeIters > 0; NodeNodeIters--) {
        i = Math.floor(Math.random() * (SemtreeLength(SemTree)));
        j = Math.floor(Math.random() * (SemtreeLength(SemTree)));
        Step += 1;
        if (i != j) {
            dx = SemTree[i].x - SemTree[j].x;
            dy = SemTree[i].y - SemTree[j].y;
            Dist = Math.sqrt(dx * dx + dy * dy);
            if (Dist == 0) {
                Dist = 1;
            }
            F = (DefaultNodeNodeDist(i, j) - Dist) / (1000 + Step);
            if (F == 0) {
                F = 1;
            }
            SemTree[i].ax += dx / Dist * F;
            SemTree[i].ay += dy / Dist * F;
            SemTree[j].ax += -dx / Dist * F;
            SemTree[j].ay += -dy / Dist * F;
        }
    }
    // keep subnodes close to nodes and spread subnodes
    for (; NodeSubnodeIters > 0; NodeSubnodeIters--) {
        i = Math.floor(Math.random() * (SemtreeLength(SemTree)));
        j = Math.floor(Math.random() * (SemsubtreeLength(SemTree, i)));
        k = Math.floor(Math.random() * (SemsubtreeLength(SemTree, i)));
        if (SemTree[i].subnodes[j].label != "") {
            // subnodes are close to their node
            if (j == j) {
                dx = SemTree[i].x - SemTree[i].subnodes[j].x;
                dy = SemTree[i].y - SemTree[i].subnodes[j].y;
                Dist = Math.sqrt(dx * dx + dy * dy);
                if (Dist == 0) {
                    Dist = 1;
                }
                F = (DefaultNodeSubnodeDist(i, j) - Dist) / (1000 + Step);
                if (F == 0) {
                    F = 1;
                }
                SemTree[i].ax += dx / Dist * F;
                SemTree[i].ay += dy / Dist * F;
                SemTree[i].subnodes[j].ax += -dx / Dist * F;
                SemTree[i].subnodes[j].ay += -dy / Dist * F;
            }
            // subnodes tends to avoid each other (spread)
            if (j != k) {
                dx = SemTree[i].subnodes[j].x - SemTree[i].subnodes[k].x;
                dy = SemTree[i].subnodes[j].y - SemTree[i].subnodes[k].y;
                Dist = Math.sqrt(dx * dx + dy * dy);
                if (Dist == 0) {
                    Dist = 1;
                }
                F = (DefaultSubnodeSubnodeDist(i, j, k) - Dist) / (1000 + Step / 3);
                if (F == 0) {
                    F = 1;
                }
                SemTree[i].subnodes[j].ax += dx / Dist * F;
                SemTree[i].subnodes[j].ay += dy / Dist * F;
                SemTree[i].subnodes[k].ax += -dx / Dist * F;
                SemTree[i].subnodes[k].ay += -dy / Dist * F;
            }
        }
    }
    // similar subnodes attract each other
    if (SimilarSubnodes.length > 0) {
        for (; GetTogetherIters > 0; GetTogetherIters--) {
            i = Math.floor(Math.random() * (SimilarSubnodes.length - 1));
            dx = (SemTree[SimilarSubnodes[i].node1].subnodes[SimilarSubnodes[i].subnode1].x + SemTree[SimilarSubnodes[i].node2].subnodes[SimilarSubnodes[i].subnode2].x) / 2;
            dy = (SemTree[SimilarSubnodes[i].node1].subnodes[SimilarSubnodes[i].subnode1].y + SemTree[SimilarSubnodes[i].node2].subnodes[SimilarSubnodes[i].subnode2].y) / 2;
            SemTree[SimilarSubnodes[i].node1].subnodes[SimilarSubnodes[i].subnode1].x = dx;
            SemTree[SimilarSubnodes[i].node1].subnodes[SimilarSubnodes[i].subnode1].y = dy;
            SemTree[SimilarSubnodes[i].node2].subnodes[SimilarSubnodes[i].subnode2].x = dx;
            SemTree[SimilarSubnodes[i].node2].subnodes[SimilarSubnodes[i].subnode2].y = dy;
        }
    }

    // update positions
    for (i = 0; i < SemtreeLength(SemTree); i++) {
        SemTree[i].vx += SemTree[i].ax;
        SemTree[i].vy += SemTree[i].ay;
        SemTree[i].x += SemTree[i].vx;
        SemTree[i].y += SemTree[i].vy;
        for (j = 0; j < SemsubtreeLength(SemTree, i); j++) {
            SemTree[i].subnodes[j].x += SemTree[i].vx;
            SemTree[i].subnodes[j].y += SemTree[i].vy;
        }
        SemTree[i].ax = SemTree[i].ax * 0.95;
        SemTree[i].ay = SemTree[i].ay * 0.95;
        SemTree[i].vx = SemTree[i].ax * 0.9;
        SemTree[i].vy = SemTree[i].ay * 0.9;
        for (j = 0; j < SemsubtreeLength(SemTree, i); j++) {
            SemTree[i].subnodes[j].vx += SemTree[i].subnodes[j].ax;
            SemTree[i].subnodes[j].vy += SemTree[i].subnodes[j].ay;
            SemTree[i].subnodes[j].x += SemTree[i].subnodes[j].vx;
            SemTree[i].subnodes[j].y += SemTree[i].subnodes[j].vy;
            SemTree[i].subnodes[j].ax = SemTree[i].subnodes[j].ax * 0.95;
            SemTree[i].subnodes[j].ay = SemTree[i].subnodes[j].ay * 0.95;
            SemTree[i].subnodes[j].vx = SemTree[i].subnodes[j].ax * 0.9;
            SemTree[i].subnodes[j].vy = SemTree[i].subnodes[j].ay * 0.9;
        }
    }

}

/////////////////////////////////////////////////////////////////////////////////////////
// that function displays a comment about a node (subnode) in a given div
// parameters
//    i: the node id
//    j: the subnodeid
/////////////////////////////////////////////////////////////////////////////////////////
function GenerateComment(i, j) {
    var Width = GetLayerWidth(Semtree_TreeDivId);
    var Height = GetLayerHeight(Semtree_TreeDivId);
    var x = Width / 2;
    var y = Height / 2;
    var Scale = (Width + Height) / 4;
    var X;
    var Y;
    var Html = "";
    var SemTree = SemTree_SemTreeData;
    Html += '<a href="javascript:GenerateComment(-1,-1)"><div align=right class="SemTree_CommentWindow_Close">[X]&nbsp;Close</div></a>';
    if ((i < 0) && (j < 0)) {
        Html += "Click on any word in the graph."
        document.getElementById(Semtree_CommentDivId).style.display = "None";
    }
    else if ((i >= 0) && (j < 0)) {
        X = (SemTree[i].x - XOffset) * Zoom * Scale + x;
        Y = Height - ((SemTree[i].y - YOffset) * Zoom * Scale + y);
        var k;
        var List = "";
        Html += '<h2 class="SemTree_CommentWindow_Title">You clicked on the word "<b>' + SemTree[i].label + '</b>".</h2>';
        Html += '<ul>';
        Html += '<li>This is one of the keyword that defines the content of your website<br>(other examples of those important words are: ';
        for (k = 0; k < SemtreeLength(SemTree); k++) {
            if (k != i) {
                if (List != '') {
                    List += ', ';
                }
                List += SemTree[k].label;
            }
        }
        Html += '"' + List + '").<br>';
        Html += 'This word is likely one that you\'d like to be SEO optimised.<br>';
        Html += 'If this is the case, here is a link explaining <a href="javascript:alert(\'Not yet !\')">how to optimise "' + SemTree[i].label + '" on your website</a>.';
        Html += '<li>Sentences containing "' + SemTree[i].label + '" also often contain the following words:<br>';
        Html += '<small><ul>';
        for (k = 0; k < SemsubtreeLength(SemTree, i); k++) {
            Html += '<li>' + SemTree[i].subnodes[k].label;
        }
        Html += '</ul></small>';
        Html += 'Combinations such as "' + SemTree[i].label + ' ' + SemTree[i].subnodes[0].label + '", ';
        Html += '"' + SemTree[i].label + ' ' + SemTree[i].subnodes[1].label + '", ';
        Html += '"' + SemTree[i].label + ' ' + SemTree[i].subnodes[2].label + '",... ';
        Html += 'are also good candidates for SEO.';
        Html += '<li>Here are the pages where "' + SemTree[i].label + '" appears most:';
        Html += '<small><ul>';
        for (k = 0; k < SemTree[i].urls.length; k++) {
            Html += '<li>Appears ' + SemTree[i].urls[k].count + ' times on <a href="' + SemTree[i].urls[k].url + '" target=_blank>' + SemTree[i].urls[k].url + '</a>';
        }
        Html += '</ul></small>';
        Html += '<li>If "' + SemTree[i].label + '" seems odd as representative of your content, you may have ';
        Html += 'an issue with the way your content is written and how it appears to others.<br>';
        Html += 'This link will <a href="javascript:alert(\'Not yet !\')">give you advices on how to write content on your site</a>.';
        document.getElementById(Semtree_CommentDivId).style.display = "";
    }
    else {
        X = (SemTree[i].subnodes[j].x - XOffset) * Zoom * Scale + x;
        Y = Height - ((SemTree[i].subnodes[j].y - YOffset) * Zoom * Scale + y);
        Html += '<h2 class="SemTree_CommentWindow_Title">You clicked on the word "<b>' + SemTree[i].subnodes[j].label + '</b>".</h2>';
        Html += '<ul>';
        Html += '<li>This word is often found in the neighborhood of "' + SemTree[i].label + '".';
        List = "";
        for (k = 0; k < SemtreeLength(SemTree); k++) {
            if (k != i) {
                var kk;
                for (kk = 0; kk < SemsubtreeLength(SemTree, k); kk++) {
                    if (SemTree[i].subnodes[j].label == SemTree[k].subnodes[kk].label) {
                        if (List != "") {
                            List += ", ";
                        }
                        List += '"' + SemTree[k].label + '"';
                    }
                }
            }
        }
        if (List != "") {
            Html += "<li>It is also found in the neighborhood of " + List + ".";
        }
        Html += '<ul>';
        document.getElementById(Semtree_CommentDivId).style.display = "";
    }
    document.getElementById(Semtree_CommentDivId).innerHTML = Html;
    document.getElementById(Semtree_CommentDivId).style.left = X + "px";
    document.getElementById(Semtree_CommentDivId).style.top = Y + "px";
}

var WordsInGraph = 10;
var SubWordsInGraph = 10;
/////////////////////////////////////////////////////////////////////////
// generate buttons to adjust the quantity of nodes and subnode displayed...
/////////////////////////////////////////////////////////////////////////
function UpdateAdjust() {
    var Html = "";
    Html = "";
    Html += 'Main words: <b>' + WordsInGraph + ' words</b> (';
    Html += '[<a href="javascript:MoreWordsInGraph()">+</a>]';
    Html += '/';
    Html += '[<a href="javascript:LessWordsInGraph()">-</a>]';
    Html += ')';

    Html += " &nbsp; | &nbsp; ";
    Html += 'Neighboring words: <b>' + SubWordsInGraph + ' words</b> (';
    Html += '[<a href="javascript:MoreSubWordsInGraph()">+</a>]';
    Html += '/';
    Html += '[<a href="javascript:LessSubWordsInGraph()">-</a>]';
    Html += ')';
    document.getElementById(Semtree_AdjustButtons).innerHTML = Html;
}

//////////////////////////////////////////////////////////////////////////
// ...functions to handle what happens when you click on those buttons
function MoreWordsInGraph() {
    WordsInGraph = Math.floor(WordsInGraph * 1.5 + 0.5);
    UpdateAdjust();
    Step = 0;
}

function LessWordsInGraph() {
    WordsInGraph = Math.floor(WordsInGraph / 1.5 + 0.5);
    if (WordsInGraph < 5) {
        WordsInGraph = 5;
    }
    UpdateAdjust();
    Step = 0;
}

function MoreSubWordsInGraph() {
    SubWordsInGraph = Math.floor(SubWordsInGraph * 1.5 + 0.5);
    UpdateAdjust();
    Step = 0;
}

function LessSubWordsInGraph() {
    SubWordsInGraph = Math.floor(SubWordsInGraph / 1.5 + 0.5);
    if (SubWordsInGraph < 2) {
        SubWordsInGraph = 2;
    }
    UpdateAdjust();
    Step = 0;
}

//
////////////////////////////////////////////////////////////////////////

function SemtreeLength(SemTree) {
    var Answer = SemTree.length - 1;
    if (Answer > WordsInGraph) {
        Answer = WordsInGraph;
    }
    return (Answer);
}

function SemsubtreeLength(SemTree, i) {
    var Answer = SemTree[i].subnodes.length - 1;
    if (Answer > SubWordsInGraph) {
        Answer = SubWordsInGraph;
    }
    return (Answer);
}


function Animate() {
    UpdateSemTree(SemTree_SemTreeData);
    DisplaySemTree(SemTree_SemTreeData, Semtree_TreeDivId);
    if (Step < 50000) {
        setTimeout("Animate()", 10 + Step / 150);
    }
    else {
        setTimeout("Animate()", 1000);
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////
// those function displays the semtree as a text
///////////////////////////////////////////////////////////////////
function DisplayWordDetail(i) {
    var SemTree = SemTree_SemTreeData;
    var Html = document.getElementById("worddet" + i).innerHTML;
    if (Html.length < 200) {
        Html = "";
        Html += '<div class="SemTree_TextViewport_Detail">';
        Html += '<a href="javascript:DisplayWordDetail(' + i + ')"><div class="SemTree_CommentWindow_Close" align=right>[x] close</div></a>';
        Html += 'Other words found in the neighborhood of "<b>' + SemTree[i].label + '</b>"<br>(the most importants comes first):<br><br>';
        Html += '<ul>';
        for (j = 0; j < SemTree[i].subnodes.length - 1; j++) {
            Html += '' + SemTree[i].subnodes[j].label + '<span style="opacity:0.7;">(' + SemTree[i].subnodes[j].qtt + ')</span>, ';
        }
        Html += '...</ul><br>';
        Html += 'Pages on which "<b>' + SemTree[i].label + '</b>" appears most:<br><br>';
        Html += '<ul>';
        for (j = 0; j < SemTree[i].urls.length; j++) {
            Html += '<li><span style="opacity:0.7;">' + SemTree[i].urls[j].count + ' times on </span><a href="' + SemTree[i].urls[j].url + '" target=_blank>' + SemTree[i].urls[j].url + '</a>';
        }
        Html += '</ul><br>';
        Html += '</div>';
    }
    else {
        Html = '[<a href="javascript:DisplayWordDetail(' + i + ')">Show detail</a>]';
    }
    document.getElementById("worddet" + i).innerHTML = Html;
}

function RenderWordListItem(i) {
    var SemTree = SemTree_SemTreeData;
    var Html = "";
    Html += '<tr>';
    Html += '<td valign=top>#' + i + '</td>';
    Html += '<td valign=top><b><a href="javascript:DisplayWordDetail(' + i + ')">' + SemTree[i].label + '</a></b></td>';
    Html += '<td valign=top>appears</td>';
    Html += '<td valign=top align=right>' + SemTree[i].qtt + '</td>';
    Html += '<td valign=top>times&nbsp;on</td>';
    Html += '<td valign=top align=right>' + SemTree[i].pages + '</td>';
    Html += '<td valign=top>pages.</td>';
    Html += '<td valign=top>Weight:' + SemTree[i].weight + '</td>';
    Html += '</tr><tr>';
    Html += '<td colspan=8 valign=top id="worddet' + i + '">[<a href="javascript:DisplayWordDetail(' + i + ')">Show detail</a>]</td>';
    Html += '</tr>';
    return (Html);
}

function RenderWordList(SemTree) {
    var SemTree = SemTree_SemTreeData;
    var Html = "";
    var i;
    Html += '<table>';
    for (i = 0; i < SemTree.length - 1; i++) {
        Html += RenderWordListItem(i);
    }
    Html += '</table>';
    return (Html);
}

function DisplayWordList(SemTree, DivId) {
    SemTree_SemTreeData = SemTree;
    document.getElementById(DivId).innerHTML = RenderWordList(SemTree);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////
// display and animate the semantic tree
// parameters
//    SemTree: the data for the semtree, presumably obtained from a JSON call
//    DivForSemTree    : where to display the semantic tree
//                       can be "" if you don't want
//    DivForTextVersion: where to display the text version of the semantic tree
//                       can be "" if you don't want
//    DivForButtons    : Div where to display adjust buttons
//    DivForComments   : div where to display comments
/////////////////////////////////////////////////////////////////////////////////
function DisplaySimpleSemTree(SemTree, DivForSemTree, DivForTextVersion, DivForButtons, DivForComments) {
    SemTree_SemTreeData = SemTree;
    if (DivForSemTree != "") {
        if (document.getElementById(DivForSemTree)) {
            Semtree_TreeDivId = DivForSemTree;
            Semtree_CommentDivId = DivForComments;
            Semtree_AdjustButtons = DivForButtons;

            SearchSimilarSubnodes(SemTree);
            UpdateAdjust();
            Animate();
            GenerateComment(-1, -1);
        }
        else {
            alert("div " + DivForSemTree + " where to display sem tree animation doesn't exist");
        }
    }

    if (DivForTextVersion != "") {
        if (document.getElementById(DivForTextVersion)) {
            SemTree_SemTreeData = SemTree;
            DisplayWordList(SemTree_SemTreeData, DivForTextVersion);
        }
        else {
            alert("div " + DivForTextVersion + " where to display sem tree text doesn't exist");
        }
    }

}

function CallDisplaySimpleSemTree(JsonUrl, Token, TargetID, TargetText, TargetButtons, TargetComment) {

    jQuery.ajax({
        url: JsonUrl,
        type: 'GET',
        dataType: "json",
        contentType: "application/json;charset=utf-8",
        headers: {
            'Authorization': 'Bearer ' + Token
        },
        success: function (data) {
            DisplaySimpleSemTree(data, TargetID, TargetText, TargetButtons, TargetComment)
        }
    });

};
