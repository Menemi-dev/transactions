/**
 * Toggles an element display by its id
 * @param {string} elementID
 */
function toggleDisplay(elementID){
    let element = document.getElementById(elementID);
    element.classList.toggle("d-block");
}

/**
 * Returns a random color with a transparency between 0.4-0.9
 * @returns string
 */
function random_light_color() {
    var o = Math.round, r = Math.random, s = 255;
    return 'rgba(' + o(r()*s) + ',' + o(r()*s) + ',' + o(r()*s) + ',' + (r()/2+0.4) + ')';
}

/**
 * Displays the data from items in the element block
 * @param {Array} items
 * @param {HTMLDivElement} block
 * @param {string} id
 */
function createReport(items, block, id){
    block.innerHTML = '';
    items.forEach(function (item) {
        var row = document.createElement("div");
        row.setAttribute('id', id+item.key);
        row.classList.add('row');
        var date = document.createElement("div");
        date.classList.add('col-3');
        date.innerHTML = item.date;
        var reference = document.createElement("div");
        reference.classList.add('col-7','overflow-auto');
        reference.innerHTML = item.reference;
        var amount = document.createElement("div");
        amount.classList.add('col-2', 'text-right');
        amount.innerHTML = item.amount;
        row.appendChild(date);
        row.appendChild(reference);
        row.appendChild(amount);
        block.appendChild(row);
    });
}

/**
 * Displays matching transactions by painting the rows on each report
 * @param {Array} matches
 */
function paintCloseMatches(matches){
    for(var key in matches){
        var color = random_light_color();
        var item1 = document.getElementById("f1"+key);
        var item2 = document.getElementById("f2"+matches[key]);
        item1.style.backgroundColor = color;
        item2.style.backgroundColor = color;
    }
}

/**
 * Requests file comparison and displays results
 */
function compareFiles(){
    let file1 = document.getElementById("file1");
    let file2 = document.getElementById("file2");
    if(file1.files.length === 0 || file2.files.length === 0){
        let alert = document.getElementById("inputAlert");
        alert.innerHTML = "Please upload two files for comparison.";
        alert.classList.add("d-block");
        return;
    }
    let formData = new FormData();
    formData.append("file1",file1.files[0]);
    formData.append("file2",file2.files[0]);
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax.php", true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            let response = JSON.parse(xhr.responseText);
            let name = document.getElementsByClassName("file-name");
            let total = document.getElementsByClassName("total-record");
            let matching = document.getElementsByClassName("matching-record");
            let unmatched = document.getElementsByClassName("unmatched-record");
            //Comparison results
            name[0].innerHTML = file1.files[0].name;
            name[1].innerHTML = file2.files[0].name;
            total[0].innerHTML = response.file1.total;
            total[1].innerHTML = response.file2.total;
            matching[0].innerHTML = response.file1.total-response.file1.unmatched;
            matching[1].innerHTML = response.file2.total-response.file2.unmatched;
            unmatched[0].innerHTML = response.file1.unmatched;
            unmatched[1].innerHTML = response.file2.unmatched;
            document.getElementById("inputAlert").classList.remove("d-block");
            document.getElementById("comparisonSection").classList.add("d-block");
            //Unmatched report
            name[2].innerHTML = file1.files[0].name;
            name[3].innerHTML = file2.files[0].name;
            let report = document.getElementsByClassName("file-report");
            createReport(response.unmatched1, report[0], 'f1');
            createReport(response.unmatched2, report[1], 'f2');
            paintCloseMatches(response.closematch);
        } else {
            let alert = document.getElementById("inputAlert");
            alert.innerHTML = "There was an error with your request.</br>" + xhr.responseText;
            alert.classList.add("d-block");
        }
    }
    xhr.send(formData);
}