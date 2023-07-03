//search then sum
var counter = 0;
function searchFunction() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");

  for (var i = 1; i < tr.length - 1; i++) {
    var tds = tr[i].getElementsByTagName("td");

    var flag = false;

    for (var j = 0; j < tds.length; j++) {
      var td = tds[j];

      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        flag = true;
      }
    }
    if (flag) {
      tr[i].className = "show";
    } else {
      tr[i].className = "hide";
    }
  }
  countLastRow();
}

searchFunction();

function countLastRow() {
  trtdLast = document.querySelectorAll("#myTable tr.lastRow td");
  tr = document.querySelectorAll("#myTable tr.show");

  sumCount = [];

  for (var x = 0; x < tr.length; x++) {
    var tds = tr[x].getElementsByTagName("td");

    for (var j = 0; j < tds.length; j++) {
      var td = tds[j];

      if (x == 0) {
        sumCount[j] = parseInt(td.innerText.replace(/,/g, ""));
      } else {
        sumCount[j] = sumCount[j] + parseInt(td.innerText.replace(/,/g, ""));
      }

      if (!isNaN(sumCount[j]) && sumCount[j] > 500000) {
        trtdLast[j].innerText = sumCount[j].toLocaleString();
      }
    }
  }

  trtdLast[0].innerText = tr.length + " rows";
}

function searchHiglightFunction() {
  tr = document.querySelectorAll("#myTable tr");
  var toggleHighlight = false;

  if (document.querySelectorAll("#myTable tr.table-warning").length == 0) {
    alert("No Highlight Row, please highlight some rows!");
    return;
  }

  for (var i = 1; i < tr.length - 1; i++) {
    if (tr[i].classList.contains("table-warning")) {
      tr[i].className = "show";
      tr[i].classList.add("table-warning");
    } else {
      tr[i].className = "hide";
    }
  }

  countLastRow();
}

function thisMonth() {
  var today = new Date();
  var todayYearMonth = today.getFullYear() + "-" + (today.getMonth() + 1);
  var myInput = document.querySelector("#myInput");

  if (myInput.value == todayYearMonth) {
    myInput.value = "";
  } else {
    myInput.value = todayYearMonth;
  }

  searchFunction();
}

function redirectSelect() {
  var x = document.getElementById("selectReport").value;
  window.location.replace(
    "https://crm.jaybranding.com/export-html.php?report=" + x
  );
}
const urlParams = new URLSearchParams(window.location.search);
const reportParam = urlParams.get("report");

var optionSelect = document.querySelectorAll("#selectReport option");

for (var i = 0; i < optionSelect.length; i++) {
  if (optionSelect[i].value == reportParam) {
    optionSelect[i].setAttribute("selected", true);
  }
}

var btnPlus = document.querySelectorAll(".expand");

for (var i = 0; i < btnPlus.length; i++) {
  if (btnPlus[i].innerHTML == '<span class="note"></span>') {
    btnPlus[i].style.display = "none";
  }
}

function exportTableToExcel(
  tableID,
  filename = '<?php echo "JAY-". $reportCode ."-". date(Ymdhis); ?>'
) {
  var downloadLink;
  var dataType = "application/vnd.ms-excel";
  var tableSelect = document.getElementById(tableID);
  var tableHTML = tableSelect.outerHTML.replace(/ /g, "%20");

  // Specify file name
  filename = filename ? filename + ".xls" : "excel_data.xls";

  // Create download link element
  downloadLink = document.createElement("a");

  document.body.appendChild(downloadLink);

  if (navigator.msSaveOrOpenBlob) {
    var blob = new Blob(["\ufeff", tableHTML], {
      type: dataType,
    });
    navigator.msSaveOrOpenBlob(blob, filename);
  } else {
    // Create a link to the file
    downloadLink.href = "data:" + dataType + ", " + tableHTML;

    // Setting the file name
    downloadLink.download = filename;

    //triggering the function
    downloadLink.click();
  }
}

document.querySelector("#searchBoxbtn").onclick = function () {
  var x = document.getElementById("searchBoxContent");

  if (x.style.display == "" || x.style.display == "none") {
    x.style.display = "block";
    this.innerText = "+";
  } else {
    x.style.display = "none";
    this.innerText = "-";
  }
};

document.querySelector("#viewAllNote").onclick = function () {
  var x = document.querySelectorAll(".note");

  for (var i = 0; i < x.length; i++) {
    x[i].classList.toggle("expandall");
  }
};
var trvar = document.querySelectorAll("#myTable tr");
for (var i = 0; i < trvar.length; i++) {
  trvar[i].onclick = function () {
    this.classList.toggle("table-warning");
  };
}
