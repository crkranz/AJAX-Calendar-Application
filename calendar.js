(function () { Date.prototype.deltaDays = function (c) { return new Date(this.getFullYear(), this.getMonth(), this.getDate() + c) }; Date.prototype.getSunday = function () { return this.deltaDays(-1 * this.getDay()) } })();
function Week(c) { this.sunday = c.getSunday(); this.nextWeek = function () { return new Week(this.sunday.deltaDays(7)) }; this.prevWeek = function () { return new Week(this.sunday.deltaDays(-7)) }; this.contains = function (b) { return this.sunday.valueOf() === b.getSunday().valueOf() }; this.getDates = function () { for (var b = [], a = 0; 7 > a; a++)b.push(this.sunday.deltaDays(a)); return b } }
function Month(c, b) { this.year = c; this.month = b; this.nextMonth = function () { return new Month(c + Math.floor((b + 1) / 12), (b + 1) % 12) }; this.prevMonth = function () { return new Month(c + Math.floor((b - 1) / 12), (b + 11) % 12) }; this.getDateObject = function (a) { return new Date(this.year, this.month, a) }; this.getWeeks = function () { var a = this.getDateObject(1), b = this.nextMonth().getDateObject(0), c = [], a = new Week(a); for (c.push(a); !a.contains(b);)a = a.nextWeek(), c.push(a); return c } };


let isLoggedIn = false;
let token = false;


// build the calendar on the page using a month object
function buildCal(month) {
    var numWeeks = month.getWeeks().length; // Get the number of weeks in the month
    var numDays = monthLength[month.month]; // Get the number of days in the month

    // Check for leap year adjustment if the month is February
    if (month.month == 1 && month.year % 4 === 0 && month.year % 100 !== 0) {
        numDays = 29;
    }

    var startDayWeek = month.getDateObject(1).getDay(); // Get the day of the week for the first of the month
    document.getElementById("dispMon").innerHTML = monthNames[month.month] + " " + month.year;
    var table = document.getElementById("calendar");

    var dayNum = 1;
    var started = false;

    // Loop through each week to create rows in the calendar table
    for (var i = 0; i < numWeeks; i++) {
        var r = i + 1;
        var row = table.insertRow(r);

        // Loop through each day of the week to create cells
        for (var j = 0; j < 7; j++) {
            var cell = row.insertCell(j);
            // Insert day numbers into cells when the month has started but not ended
            if (dayNum <= numDays && (j === Number(startDayWeek) || started === true)) {
                cell.innerHTML = dayNum;
                var cellID = dayNum;
                cell.id = cellID;
                cell.className = "day";
                started = true;
                dayNum++; // Increment the day number
            }
        }
    }
}

// build a calendar for the previous month
function decreaseMonth() {
    var wksRemove = currentMonth.getWeeks().length;
    var table = document.getElementById("calendar");
    for (var i = 0; i < wksRemove; i++) {
        table.deleteRow(1); // Remove rows except the header
    }

    currentMonth = currentMonth.prevMonth(); // Move to the previous month
    buildCal(currentMonth); // Rebuild the calendar
}

// build a calendar for the next month
function increaseMonth() {
    var wksRemove = currentMonth.getWeeks().length;
    var table = document.getElementById("calendar");
    for (var i = 0; i < wksRemove; i++) {
        table.deleteRow(1); // Remove rows except the header
    }

    currentMonth = currentMonth.nextMonth(); // Move to the next month
    buildCal(currentMonth); // Rebuild the calendar
}

