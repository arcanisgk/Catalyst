$(document).ready(function(){function e(){$("#sparkline1").sparkline([34,43,43,35,44,32,44,52],{type:"line",width:"100%",height:"62",lineColor:"#1ab394",fillColor:"transparent"}),$("#sparkline2").sparkline([32,11,25,37,41,32,34,42],{type:"line",width:"100%",height:"62",lineColor:"#1ab394",fillColor:"transparent"}),$("#sparkline3").sparkline([34,22,24,41,10,18,16,8],{type:"line",width:"100%",height:"50",lineColor:"#1C84C6",fillColor:"transparent"})}var l;$(window).resize(function(i){clearTimeout(l),l=setTimeout(e,500)}),e();$("#flot-dashboard5-chart").length&&$.plot($("#flot-dashboard5-chart"),[[[0,4],[1,8],[2,5],[3,10],[4,4],[5,16],[6,5],[7,11],[8,6],[9,11],[10,20],[11,10],[12,13],[13,4],[14,7],[15,8],[16,12]],[[0,0],[1,2],[2,7],[3,4],[4,11],[5,4],[6,2],[7,5],[8,11],[9,5],[10,4],[11,1],[12,5],[13,2],[14,5],[15,2],[16,0]]],{series:{lines:{show:!1,fill:!0},splines:{show:!0,tension:.4,lineWidth:1,fill:.4},points:{radius:0,show:!0},shadowSize:2},grid:{hoverable:!0,clickable:!0,borderWidth:2,color:"transparent"},colors:["#1ab394","#1C84C6"],xaxis:{},yaxis:{},tooltip:!1})});