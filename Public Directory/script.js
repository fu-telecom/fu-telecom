var mainDirectory = [];
var currentDirectory = [];
var $mySelect = $('#mySelect');
var serverAddress = '172.16.1.2:80';

$(document).ready(function () {
    loadMainDirectory();
});

function loadMainDirectory() {
    $.ajax({
        type: "GET",
        url: "../directory.xml",
        cache: false,
        dataType: "xml",
        success: function (xml) {
            $(xml).find('MenuItem').each(function () {
                var eName = $(this).find("Name").text();
                var eUrl = $(this).find("URL").text();
        	
		if (eUrl.includes("campdirectory.xml")) {
			mainDirectory.push({name: eName, url: eUrl, selected: 1});
		} else {
			mainDirectory.push({ name: eName, url: eUrl, selected: 0});
		}
            })
            populateOption();
	    loadCurrentDirectory($("#mySelect").children("option:selected").val());
        }
    })
}

function populateOption() {
    $.each(mainDirectory, function (key, value, selected) {
	if (value.selected == 1) {
		$('#mySelect')
                    .append($('<option>', { value: value.url, selected: 'selected' })
                        .text(value.name));
	} else {
	        $('#mySelect')
	            .append($('<option>', { value: value.url })
        	        .text(value.name));

	}
    });
}

function loadCurrentDirectory(directoryLocation) {
    var directoryURL = 'http://' + serverAddress + '/directory/' + directoryLocation.split('/')[4];
    $.ajax({
        type: "GET",
        url: directoryURL,
        cache: false,
        dataType: "xml",
        success: function (xml) {
            $(xml).find('DirectoryEntry').each(function () {
                var eName = $(this).find("Name").text();
                var ePhone = $(this).find("Telephone").text();
                currentDirectory.push({ name: eName, phone: ePhone });
            })
            populateTable();
        }
    })
}

function populateTable() {
    var text = "<table><tbody><tr><th>Camp</th><th>Number</th></tr>";
    for (i = 0; i < currentDirectory.length; i++) {
        text += "<tr>";
        text += "<td>" + currentDirectory[i].name + "</td>";
        text += "<td><a href='tel:+" + currentDirectory[i].phone+ "'>" + currentDirectory[i].phone + "</a></td>";
        text += "</tr>";
    }
    text += "</tbody></table>";
    document.getElementById("futdirectory").innerHTML = text;
}

function loadDirectory(selectObject) {
    currentDirectory = [];
    loadCurrentDirectory(selectObject.value);
}
