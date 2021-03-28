/* Set Calendar format; Using jQuery calendar because it works better across different browsers than default form calendar */
$( function()
{
    $( "#datepicker" ).datepicker( { dateFormat: \'yy-mm-dd\' });
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



function d2h(d) 
{
	return d.toString(16);
}

function stringToHex() 
{
    var japikey = document.getElementById("key").value;
    var str = '',
        i = 0,
        tmp_len = japikey.length,
        c;
 
    for (; i < tmp_len; i += 1) 
    {
        c = japikey.charCodeAt(i);
        str += d2h(c);
    }
    document.keyform.key.value = str;
    return true;
}

function generatorNav() 
{
    var x = document.getElementById("generatorTopNav");
    if (x.className === "topnav") {
        x.className += " responsive";
    } 
    else 
    {
        x.className = "topnav";
    }
}

function stripbigentry() 
{
    var e = document.getElementById("dataentry");
    e.value='' ;
 }

function showhide() {
    var e = document.getElementById("substitutionTable");
    var f = document.getElementById("templateTable");
    if (e.style.display == 'none') {e.style.display = "block"} else {e.style.display = 'none'};
    if (f.style.display == 'none') {f.style.display = "block"} else {f.style.display = 'none'};
 }

function updateCall()
{
	var selectList = document.getElementById("Template");
    var selectList2 = document.getElementById("Recipients");
    var manuallyentered = document.getElementById("dataentry").value;
    var apikey = "<?php echo $apikey; ?>";

    $.ajax({
      url:'testpreview.php',
      type: "POST",
      data: {"apikey" : apikey, "template" : selectList.value, "recipients" : selectList2.value, "entered" : manuallyentered},
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

function match()
{
	var selectList = document.getElementById("Template");
    var selectList2 = document.getElementById("Recipients");
    var manuallyentered = document.getElementById("dataentry").value;
    var apikey = "<?php echo $apikey; ?>";
    
    $.ajax({
      url:'testmatch.php',
      type: "POST",
      data: {"apikey" : apikey, "template" : selectList.value, "recipients" : selectList2.value, "type" : "substitution", "entered" : manuallyentered},
      complete: function (response) 
      {
          $('#substitution').contents().find('html').html(response.responseText);
          $.ajax({
      		url:'testmatch.php',
      		type: "POST",
      		data: {"apikey" : apikey, "template" : selectList.value, "recipients" : selectList2.value, "type" : "template", "entered" : manuallyentered},
      		complete: function (response) 
      		{
          		$('#template').contents().find('html').html(response.responseText);
      		},
      		error: function () {
          		$('#output').html('Bummer: there was an error!');
      		}
    		});
      },
      error: function () {
          $('#output').html('Bummer: there was an error!');
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
