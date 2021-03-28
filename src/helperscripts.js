/* Set Calendar format; Using jQuery calendar because it works better across different browsers than default form calendar */
$( function() 
{
    $( "#datepicker" ).datepicker( { dateFormat: 'yy-mm-dd' });
} );

function cleanup() 
{
// Need to clean up this field in case they did a backpage in the browser
// 
    //var returnpath = document.getElementById("returnpath");
    //var location = returnpath.value.search("@");
	//if (location > 0) {returnpath.value = returnpath.value.substring(0, location)};
	var segmented = document.getElementById("segmented");
	segmented.value = "FALSE";
}


function prepsubmit() 
{
    var json = document.getElementById("json").innerHTML;
    var segmented = document.getElementById("segmented");
	if (json.value.length > 2) {segmented.value = "TRUE";}
 }
  
function countaddresses() 
{
    //var recipientCount = document.getElementById("recipientCount");
    var filteredcount = document.getElementById("filteredcount");
    var json = document.getElementById("json");
    var doublequote = (json.value.match(/"address"/g) || []).length;
    var singlequote = (json.value.match(/'address'/g) || []).length;
	//recipientCount.value = singlequote + doublequote;
	filteredcount.value = singlequote + doublequote;
 }
 
function recipientcount() 
{
    var recipientCount = document.getElementById("recipientCount");
    var recipientlist = document.getElementById("Recipients");
    var apikey = "'.$apikey.'";
    var apiroot = "'.$apiroot.'";

    $.ajax({
      url:'cgGetRecipientListCount.php',
      type: "POST",
      data: {"apikey" : apikey, "apiroot" : apiroot, "recipients" : recipientlist.value},
      complete: function (response) 
      {
          recipientCount.value=response.responseText;
      },
      error: function () {
          $('#output').html('0');
      }
    }); 
    return false;
}

function generatePreview()
{
	var selectList = document.getElementById("Template");
    var selectList2 = document.getElementById("Recipients");
    var globalsub = document.getElementById("globalsub").value;
    var apikey = "'.$apikey.'";
    var apiroot = "'.$apiroot.'";

    $.ajax({
      url:'cgBuildPreview.php',
      type: "POST",
      data: {"apikey" : apikey, "apiroot" : apiroot, "template" : selectList.value, "recipients" : selectList2.value, "globalsub" : globalsub},
      complete: function (response) 
      {
          $('#iframe1').contents().find('html').html(response.responseText);
          xbutton = document.getElementById("submit");
          var strCheck1 = "attempt to call non-existent macro";
          var strCheck2 = "crash";
          var location1 = response.responseText.search(strCheck1);
          var location2 = response.responseText.search(strCheck2);
          if (location1 > 0  && location2 > 0)
          {
              xbutton.disabled = true;
              xbutton.value = "Submit";
              xbutton.style.backgroundColor = "red";
              xbutton.style.color = "black";
              alert("Warning!! Your data protection check was triggered, bad Recipient List selected - Submit Turned off!");
          }
          else
          {  
              var strCheck = "Matching Problem";
              var location = response.responseText.search(strCheck);
              if (location > 0) 
              {
                  xbutton.disabled = true;
                  xbutton.value = "Submit";
                  xbutton.style.backgroundColor = "red";
                  xbutton.style.color = "black";
                  alert("Warning!! Template & Recipient error detected; please see preview box - Submit Turned off!");
              }
              else
              {   
                  xbutton.disabled = false;
                  xbutton.value = "Submit";
                  xbutton.style.color = "white";
                  xbutton.style.backgroundColor = "#72A4D2";
              }
          }
      },
      error: function () {
          $('#output').html('Bummer: there was an error!');
      }
    }); 
    return false;
}

function sendTestEmail()
{
	var templateList = document.getElementById("Template");
    var recipientList = document.getElementById("Recipients");
    var emailaddresses = document.getElementById("previewTestEmails").value;
    var campaign = document.getElementById("campaign").value;
    var open = document.getElementById("open").value;
    var click = document.getElementById("click").value;
    var meta1 = document.getElementById("meta1").value;
    var data1 = document.getElementById("data1").value;
    var meta2 = document.getElementById("meta2").value;
    var data2 = document.getElementById("data2").value;
    var meta3 = document.getElementById("meta3").value;
    var data3 = document.getElementById("data3").value;
    var meta4 = document.getElementById("meta4").value;
    var data4 = document.getElementById("data4").value;
    var meta5 = document.getElementById("meta5").value;
    var data5 = document.getElementById("data5").value;
    var returnpath = document.getElementById("returnpath").value;
    var domain = document.getElementById("domain").value;
    var apikey = "'.$apikey.'";
    var apiroot = "'.$apiroot.'";
    var globalsub = document.getElementById("globalsub").value;
    
    $.ajax({
      url:'segSendTestEmail.php',
      type: "POST",
      data: {"apikey" : apikey, "template" : templateList.value, "recipients" : recipientList.value, "emailaddresses" : emailaddresses, 
      		 "apiroot" : apiroot, "campaign" : campaign, "open" : open, "click" : click, "globalsub" : globalsub,
      		 "meta1" :  meta1, "data1" : data1,   "meta2" :  meta2, "data2" : data2, "meta3" :  meta3, "data3" : data3, "meta4" :  meta4, "data4" : data4, "meta5" :  meta5, "data5" : data5, 
      		 "returnpath" : returnpath, "domain" : domain },
      complete: function (response) 
      {
          // This is for error checking  in order to see echo'ed items...
          //$('#iframe1').contents().find('html').html(response.responseText);
      },
      error: function () 
      {
          $('#iframe1').contents().find('html').html(response.responseText);
      }
    });
    
    return false;
}

function buildsegdrop()
{
    var recipientList = document.getElementById("Recipients");
    var segList1 = document.getElementById("segList1");
    var segList2 = document.getElementById("segList2");
    var segList3 = document.getElementById("segList3");
    var segList4 = document.getElementById("segList4");
    var segList5 = document.getElementById("segList5");
    var apikey = "'.$apikey.'";
    var apiroot = "'.$apiroot.'";
    
    $.ajax({
      url:'segFieldDropdown.php',
      type: "POST",
      data: {"apikey" : apikey, "recipients" : recipientList.value, "apiroot" : apiroot },
      complete: function (response) 
      {
          // This is for error checking  in order to see echo'ed items...
          segList1.innerHTML=response.responseText;
          segList2.innerHTML=response.responseText;
          segList3.innerHTML=response.responseText;
          segList4.innerHTML=response.responseText;
          segList5.innerHTML=response.responseText;
      },
      error: function (response) 
      {
          $('#iframe1').contents().find('html').html(response.responseText);
      }
    });
    
    return false;
}


function resetpreview()
{
	$('#iframe1').contents().find('html').html("<p>Please select your Template and Recipient List</p>");
	xbutton = document.getElementById("submit");
	xbutton.disabled = false;
    xbutton.value = "Submit";
    xbutton.style.color = "white";
    xbutton.style.backgroundColor = "#72A4D2";
}

function resetsummary()
{
	$('#template').contents().find('html').html("<p>Please select your Template and Recipient List</p>");
	$('#substitution').contents().find('html').html("<p>Please select your Template and Recipient List</p>");
}

function getSegment()
{

    var apikey = "'.$apikey.'";
    var apiroot = "'.$apiroot.'";
    var recipientList = document.getElementById("Recipients");
	var filterArray = {
		'filter': [],
		'logic' : 'and'
	};
	var segList1 = document.getElementById("segList1");
	var segValue1 = document.getElementById("segValue1");
	var segOperand1 = document.getElementById("operand1");
	var segList2 = document.getElementById("segList2");
	var segValue2 = document.getElementById("segValue2");
	var segOperand2 = document.getElementById("operand2");
	var segList3 = document.getElementById("segList3");
	var segValue3 = document.getElementById("segValue3");
	var segOperand3 = document.getElementById("operand3");
	var segList4 = document.getElementById("segList4");
	var segValue4 = document.getElementById("segValue4");
	var segOperand4 = document.getElementById("operand4");
	var segList5 = document.getElementById("segList5");
	var segValue5 = document.getElementById("segValue5");
	var segOperand5 = document.getElementById("operand5");
	var jsonEntry = document.getElementById("json");
	if (segList1.value != "Filter Not Entered" ) {filterArray.filter.push({ 'metadata-fieldname': segList1.value, 'logical-comparison': operand1.value, 'comparison-value': segValue1.value })};
	if (segList2.value != "Filter Not Entered" ) {filterArray.filter.push({ 'metadata-fieldname': segList2.value, 'logical-comparison': operand2.value, 'comparison-value': segValue2.value })};
	if (segList3.value != "Filter Not Entered" ) {filterArray.filter.push({ 'metadata-fieldname': segList3.value, 'logical-comparison': operand3.value, 'comparison-value': segValue3.value })};
	if (segList4.value != "Filter Not Entered" ) {filterArray.filter.push({ 'metadata-fieldname': segList4.value, 'logical-comparison': operand4.value, 'comparison-value': segValue4.value })};
	if (segList5.value != "Filter Not Entered" ) {filterArray.filter.push({ 'metadata-fieldname': segList5.value, 'logical-comparison': operand5.value, 'comparison-value': segValue5.value })};


	$.ajax({
      url:'segStub.php',
      type: "POST",
      data: {"apikey" : apikey, "recipients" : recipientList.value, "apiroot" : apiroot, "filterArray" : filterArray },
      complete: function (response) 
      {
          json.innerHTML = response.responseText;
    	  var segmented = document.getElementById("segmented");
	  if (json.innerHTML.length > 2) {segmented.value = "TRUE";}
 	  //var recipientCount = document.getElementById("recipientCount");
    	  var filteredcount = document.getElementById("filteredcount");
    	  //var json = document.getElementById("json");
    	  var doublequote = (json.value.match(/"address"/g) || []).length;
    	  var singlequote = (json.value.match(/'address'/g) || []).length;
          //recipientCount.value = singlequote + doublequote;
          filteredcount.value = singlequote + doublequote;
      },
      error: function () 
      {
          $('#iframe1').contents().find('html').html(response.responseText);
      }
    });
    
    return false;
}

