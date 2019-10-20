let updatePage;
(function() {
    //variables
    //this is for the data we want to post
    let default_json = {
        "interval": {
            "enable": false,
            "times": []
        },
        "auto_generate": false,
        "date_start": "",
        "date_finish": "",
        "days": {
            "FRI": {
                "data": [],
                "enable": false
            },
            "MON": {
                "data": [],
                "enable": false
            },
            "SAT": {
                "data": [],
                "enable": false
            },
            "SUN": {
                "data": [],
                "enable": false
            },
            "THU": {
                "data": [],
                "enable": false
            },
            "TUS": {
                "data": [],
                "enable": false
            },
            "WED": {
                "data": [],
                "enable": false
            }
        }
    };
    let dataToSubmit = [];
    let timers = 0;
    let disableAll;
    let disableInterval;
    let days_array = ['SAT', 'SUN', 'MON', 'TUS', 'WED', 'THU', 'FRI'];
    let timeregexp = /([01]?[0-9]|2[0-3]):[0-5][0-9]/;
    let intervals = [];
    //removeSeconds
    function removeSeconds(time) {
        return time.split(':')[0] + ":" + time.split(':')[1];
    }
    //this is when you want to fill the data
    function fillPage(json) {
        json = JSON.parse(json);
        if (json.date_start)
            $('#reservation').data('daterangepicker').setStartDate(json.date_start);
        if (json.date_finish)
            $('#reservation').data('daterangepicker').setEndDate(json.date_finish);
        if (json.auto_generate === undefined)
            json.auto_generate = default_json.auto_generate;

        disableAll.checked = json.auto_generate;

        if (json.interval === undefined)
            json.interval = {times: default_json.interval.times, enable: default_json.interval.enable};

        if (json.interval.enable === undefined)
            json.interval.enable = false
        disableInterval.checked = json.interval.enable;

        checkDisable();

        if (json.interval.times === undefined)
            json.interval.times = [];
        for (let i = 0; i < json.interval.times.length; i ++) {
            appendIntervalByVal(json.interval.times[i]);
        }


        if (json.days === undefined)
            json.days = default_json.days;

        for (let j in days_array) {
            if (!json.days.hasOwnProperty(days_array[j]))
                json.days[days_array[j]] = default_json.days[days_array[j]];
        }

        for (let day in json.days) {
            if (days_array.indexOf(day) === -1)
                continue;
            if (json.days[day].data === undefined)
                json.days[day].data = default_json.days[day].data;
            for (let i in json.days[day].data) {
                if (json.days[day].data.start === undefined || !json.days[day].data.start.match(timeregexp))
                    json.days[day].data.start = default_json.days[day].data.start;
                if (json.days[day].data.finish === undefined || !json.days[day].data.finish.match(timeregexp))
                    json.days[day].data.finish = default_json.days[day].data.finish;
                if (json.days[day].data.duration === undefined || !json.days[day].data.duration.match(timeregexp))
                    json.days[day].data.duration = default_json.days[day].data.duration;
                var reserv = submitReservation({
                    start: removeSeconds(json.days[day].data[i].start),
                    end: removeSeconds(json.days[day].data[i].finish),
                    duration: removeSeconds(json.days[day].data[i].duration),
                    day: day
                })
                document.getElementById(day + '-reservations').appendChild(reserv);
            }
            //console.log(json.days[day].enable);
            if (json.days[day].enable === undefined)
                json.days[day].enable = default_json.days[day].enable;
            document.querySelector('#checkbox-' + day).checked = json.days[day].enable;
        }
    }
    //this is for time picker creation
    function NewTimePicker() {
        const div = document.createElement('div');
        div.className = "input-group bootstrap-timepicker timepicker";
        const input = document.createElement('input');
        input.setAttribute('id', `timepicker${timers}`);
        input.setAttribute('type', 'text');
        input.className = "form-control";
        input.setAttribute('size', '1');
        //const span = document.createElement('span');
        //span.className = "input-group-addon";
        /*const i = document.createElement('i');
         i.className = "fa fa-clock-o fa-1";*/

        //span.appendChild(i);
        div.appendChild(input);
        //div.appendChild(span);

        let script = document.createElement('script');
        script.setAttribute('type', 'text/javascript');
        script.innerHTML = `$('#timepicker${timers}').timepicker({showInputs: false, minuteStep: 5,showMeridian: false});`;
        div.appendChild(script);

        return {
            main: div,
            input,
            id: timers++
        }

    }

    //button instantiation okbtn/nobtn
    const BTN_TYPE = {OK: 0, NO: 1};
    function Btn(type, size, type2) {
        if(size === undefined)
            size = 'sm';
        var btn = document.createElement('button');
        var span = document.createElement('span');
        if (type === BTN_TYPE.OK) {
            span.className = 'glyphicon glyphicon-ok';
            btn.className = 'btn btn-success btn-sm';
            btn.type = 'submit';
        } else {
            span.className = 'glyphicon glyphicon-remove';
            btn.type = 'reset';
            btn.className = 'btn btn-danger btn-' + size;
        }
        if (type2 !== undefined)
            btn.type=type2;
        btn.appendChild(span);
        return btn;
    }

    //this generates times to add through interval
    function IntervalInput() {
        let input1 = NewTimePicker();
        let input2 = NewTimePicker();

        let interval_input = document.createElement('input');
        interval_input.setAttribute('type', 'number');
        interval_input.setAttribute('step', '5');
        interval_input.setAttribute('min', '5');
        interval_input.setAttribute('value', '5');
        interval_input.className = 'form-control';

        let span = document.createElement('b');
        span.innerHTML = "00:00";


        let form = document.createElement('form');
        form.className = "form-inline";
        form.appendChild(input1.main);
        form.appendChild(document.createTextNode(' - '));
        form.appendChild(input2.main);
        form.appendChild(document.createTextNode(' ['));
        form.appendChild(interval_input);
        form.appendChild(document.createTextNode(']  '));

        const okbtn = Btn(BTN_TYPE.OK);
        //const nobtn = Btn(BTN_TYPE.NO);

        let div = document.createElement('div');
        div.className = 'btn-group';

        div.appendChild(okbtn);
        //div.appendChild(nobtn);
        form.appendChild(div);

        form.addEventListener('submit', function(e) { e.preventDefault(); });

        return {
            form,
            input1,
            input2,
            interval_input
        }
    }

    //This creates the availability Input
    function ReservationInput() {

        let input1 = NewTimePicker();
        let input2 = NewTimePicker();

        let span = document.createElement('b');
        span.innerHTML = "00:00";


        let form = document.createElement('form');
        form.className = "form-inline";
        form.appendChild(input1.main);
        form.appendChild(document.createTextNode(' - '));
        form.appendChild(input2.main);
        form.appendChild(document.createTextNode(' ['));
        form.appendChild(span);
        form.appendChild(document.createTextNode(']  '));

        const okbtn = Btn(BTN_TYPE.OK);
        const nobtn = Btn(BTN_TYPE.NO);

        let div = document.createElement('div');
        div.className = 'btn-group';

        div.appendChild(okbtn);
        div.appendChild(nobtn);
        form.appendChild(div);

        form.addEventListener('submit', function(e) { e.preventDefault(); });

        return {
            form,
            input1,
            input2,
            nobtn,
            span,
            appended() {
                let that = this;
                //we made this function here to use the scope
                function GeneratePeriod(e) {
                    setTimeout(() => {
                        let starttime = {
                            hh: $(`#timepicker${input1.id}`).data("timepicker").hour,
                            mm: $(`#timepicker${input1.id}`).data("timepicker").minute,
                        }
                        let endtime = {
                            hh: $(`#timepicker${input2.id}`).data("timepicker").hour,
                            mm: $(`#timepicker${input2.id}`).data("timepicker").minute,
                        }
                        if (endtime.mm < starttime.mm) {
                            endtime.hh--;
                            endtime.mm += 60;
                        }
                        let diff = {
                            hh: endtime.hh - starttime.hh,
                            mm: endtime.mm - starttime.mm,
                            time() {
                                let str = "";
                                if (this.hh < 10) str += "0" + this.hh;
                                else str += this.hh;

                                str += ":"

                                if (this.mm < 10) str += "0" + this.mm;
                                else str += this.mm;

                                return str;
                            }
                        }

                        if (diff.hh < 0) {
                            diff.hh += 24;
                        }
                        that.span.innerHTML = diff.time();
                    }, 2);
                }

                $(`#timepicker${input1.id}`).on('changeTime.timepicker', GeneratePeriod);
                $(`#timepicker${input2.id}`).on('changeTime.timepicker', GeneratePeriod);
            }
        }
    }
    //this is for creating alert divs
    function alert2(str) {
        var div = document.createElement('div');
        div.className = 'alert alert-info alert-dismissible';
        div.setAttribute('role', 'alert');

        var btn = document.createElement('button');
        btn.className = 'close';
        btn.type = 'button';
        btn.setAttribute('data-dismiss', 'alert');
        btn.setAttribute('aria-label', 'Close');
        var span = document.createElement('span');
        span.setAttribute('aria-hidden', true);
        span.className = 'glyphicon glyphicon-remove';
        //span.appendChild(document.createTextNode('&times;'));
        btn.appendChild(span);

        div.appendChild(btn);
        div.appendChild(document.createTextNode('Warning! ' + str));

        const alert_area = document.querySelector('#alert-area');

        //this is for removing
        while (alert_area.lastChild)
            alert_area.removeChild(alert_area.lastChild);

        alert_area.appendChild(div);

    }
    //this is when you add a new availability
    function submitReservation(data) {
        dataToSubmit.push(data);
        let container = document.createElement('div');
        container.appendChild(document.createTextNode(`${data.start} - ${data.end} [${data.duration}]  `));

        var removebtn = document.createElement('button');
        var span = document.createElement('span');
        span.className = 'glyphicon glyphicon-remove';
        removebtn.appendChild(span);
        removebtn.className = 'btn btn-danger btn-xs';

        removebtn.addEventListener('click', function(e) {
            container.remove();
            console.log(dataToSubmit[dataToSubmit.indexOf(data)]);
            dataToSubmit.splice(dataToSubmit.indexOf(data), 1);
        }, false);


        container.appendChild(removebtn);

        return container;

    }
    //HELPER FUNCTIONs
    function convert2Time(time) {
        return {
            hh: parseInt(time.split(":")[0].trim()),
            mm: parseInt(time.split(":")[1].trim()),
        }
    }

    function time2String(time) {
        if (time.mm < 10)
            time.mm = "0" + time.mm;
        if (time.hh < 10)
            time.hh = "0" + time.hh;
        return time.hh + ":" + time.mm;
    }
    //for comparing times
    function compareTime(time1, time2, det) {
        const t1 = convert2Time(time1);
        const t2 = convert2Time(time2);

        if (t1.hh === t2.hh) {
            if (t1.mm === t2.mm)
                return 0;
            else if (t1.mm < t2.mm)
                return -1
            else if (t1.mm > t2.mm)
                return 1
        } else if (t1.hh < t2.hh)
            return -1
        else if (t1.hh > t2.hh)
            return 1


    }

    function _compare(time1, time2) {
        if (compareTime(time1.start, time2.start) === 0) {
            return true;
        }
        if (compareTime(time1.end, time2.end) === 0) {
            return true;
        }
        if (compareTime(time2.start, time1.start) === 1 &&
            compareTime(time2.start, time1.end) === -1) {
            return true;
        }
        if (compareTime(time2.end, time1.start) === 1 &&
            compareTime(time2.end, time1.end) === -1) {
            return true;
        }
        if (compareTime(time1.start, time2.start) === 1 &&
            compareTime(time1.start, time2.end) === -1) {
            return true;
        }
        if (compareTime(time1.end, time2.start) === 1 &&
            compareTime(time1.end, time2.end) === -1) {
            return true;
        }
        return false;
    }

    function checkOverNight(time) {
        if (compareTime(time.start, time.end) === 1) {
            const t1end = convert2Time(time.end);
            t1end.hh += 24;
            time.end = time2String(t1end);
        }
    }

    function timeDiffMinutes(time) {
        const start = convert2Time(time.start);
        const end = convert2Time(time.end);

        let diff = (end.hh - start.hh) * 60;
        diff += (end.mm - start.mm);
        return diff;
    }

    function addMinutes(time, minutes) {
        let hh = parseInt(minutes/60);
        let mm = minutes%60;
        time = convert2Time(time);
        time.hh += hh;
        time.mm += mm;
        while (time.mm >= 60) {
            time.mm -= 60;
            time.hh += 1;
        }
        return time2String(time);
    }

    function min2Time(minutes) {
        const hh = parseInt(minutes/60);
        const mm = minutes%60;
        return time2String({hh, mm});
    }

    function compare(time1, time2) {
        let newtime1 = {
            start: time1.start,
            end: time1.end
        };

        let newtime2 = {
            start: time2.start,
            end: time2.end
        };
        checkOverNight(newtime1);
        checkOverNight(newtime2);

        if (_compare(newtime1, newtime2))
            return true;
    }

    function checkInterval() {
        if (!disableInterval.checked) {
            document.querySelector('#interval').disabled = true;
            document.querySelector('#add-interval').disabled = true;
        } else {
            document.querySelector('#interval').disabled = false;
            document.querySelector('#add-interval').disabled = false;

        }
    }
    function appendIntervalByVal(val) {
        val = parseInt(val);
        if (val % 5 !== 0) {
            alert2("you can't add an interval that's not of the multiplications of 5")
            return;
        }
        if (val < 5) {
            alert2("you can't add an interval that's less than 5")
            return;
        }
        if (intervals.indexOf(val) !== -1) {
            alert2("you can't add an interval that already exists")
            return;
        }

        intervals.push(val);
        const div = document.createElement('div');
        div.className="col-sm-2 col-xs-3";
        div.appendChild(document.createTextNode(val + ' M '));
        const nobtn = Btn(BTN_TYPE.NO, 'xs', 'button')
        nobtn.addEventListener('click', () => {
            intervals.splice(intervals.indexOf(val), 1);
            div.remove();
        })
        div.appendChild(nobtn);
        document.querySelector('#interval-container').appendChild(div);
    }

    function appendInterval() {
        const val = document.querySelector('#interval').value;
        appendIntervalByVal(val);
    }

    function checkDisable() {
        if (!disableAll.checked) {
            document.querySelectorAll('input').forEach(el => {
                el.disabled = true;
            });
            document.querySelectorAll('button').forEach(el => {
                el.disabled = true;
            });
            disableAll.disabled = false;
            document.querySelector('#submit-data').disabled = false;
            document.querySelector('#data-to-send').disabled = false;
            document.querySelector('#state').innerHTML = "Disabled";
        } else {
            document.querySelectorAll('input').forEach(el => {
                el.disabled = false;
            })
            document.querySelectorAll('button').forEach(el => {
                el.disabled = false;
            });

            document.querySelector('#state').innerHTML = "Enabled";
        }
        checkInterval();


        document.getElementsByName('_token').forEach(el => el.disabled = false);
    }
    //this runs after all elements in the page are loaded
    window.addEventListener('load', function() {
        disableInterval = document.querySelector('#disable-interval');
        disableInterval.addEventListener('change', checkInterval);
        disableAll = document.querySelector('#disable-all-form');
        disableAll.addEventListener('change', checkDisable);

        let checkAll = document.querySelector('#check-all');
        checkAll.addEventListener('change', () => {
            let temp = disableAll.checked;
            let tmp2 = disableInterval.checked;
            if (checkAll.checked) {
                document.querySelectorAll('input[type=checkbox]').forEach(el => {
                    el.checked = true;
                });
            } else {
                document.querySelectorAll('input[type=checkbox]').forEach(el => {
                    el.checked = false;
                })
            }
            disableAll.checked = temp;
            disableInterval.checked = tmp2;
        });


        //main form
        let mform = document.querySelector('#mform');
        //main submit
        mform.addEventListener('submit', function(e) {
            let availability = {};
            for (let key in dataToSubmit) {
                if (availability[dataToSubmit[key].day] === undefined)
                    availability[dataToSubmit[key].day] = {
                        enable: document.querySelector('#checkbox-' + dataToSubmit[key].day).checked,
                        data: []
                    };
            }
            for (let key in dataToSubmit) {
                availability[dataToSubmit[key].day].data.push({
                    start: dataToSubmit[key].start + ":00",
                    finish: dataToSubmit[key].end + ":00",
                    duration: dataToSubmit[key].duration + ":00",
                });
            }

            var data = {
                date_start: $('#reservation').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                date_finish: $('#reservation').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                days: availability,
                auto_generate: disableAll.checked,
                interval: {
                    enable: disableInterval.checked,
                    times: intervals
                }
            };

            document.querySelector('#data-to-send').value = JSON.stringify(data);

        });

        mform.addEventListener('reset', (e) => e.preventDefault());


        //this input is for all days
        let inputall = ReservationInput();
        document.querySelector('#cont').appendChild(inputall.form);
        inputall.appended();
        inputall.nobtn.remove();
        inputall.form.addEventListener('submit', function() {

            if ($(`#timepicker${inputall.input1.id}`).val().indexOf(":") == -1 ||
                $(`#timepicker${inputall.input2.id}`).val().indexOf(":") == -1) {
                alert2("You must fill the start and finish time!");
                return;
            }

            loop1: for (var i = 0; i < dayTables.length; i++) {

                var day = dayTables[i].getAttribute('day');

                if (!document.querySelector('#checkbox-' + day).checked)
                    continue loop1;

                let reservData = {
                    start: $(`#timepicker${inputall.input1.id}`).val(),
                    end: $(`#timepicker${inputall.input2.id}`).val(),
                    duration: inputall.span.innerHTML,
                    day: day
                }

                if (inputall.span.innerHTML === "00:00") {
                    alert2("can't reserve no range");
                    return;
                }

                for (var j = 0; j < dataToSubmit.length; j++) {
                    if (dataToSubmit[j].day === day) {
                        if (compare(reservData, dataToSubmit[j])) {
                            alert2("Can't reserve an already reserved time");
                            continue loop1;
                        }
                    }
                }


                var reserv = submitReservation(reservData);

                document.getElementById(day + '-reservations').appendChild(reserv);
            }
        });

        document.querySelector('#clear-btn').addEventListener('click', () => {
            for (let i = 0; i < dayTables.length; i++) {
                let day = dayTables[i].getAttribute('day');
                if (!document.querySelector('#checkbox-' + day).checked)
                    continue;

                let container = document.querySelector(`#${day}-reservations`);
                while (container.firstChild) {
                    container.removeChild(container.firstChild);
                }

                for (let i = 0; i < dataToSubmit.length; i++) {
                    if (dataToSubmit[i].day === day) {
                        dataToSubmit.splice(i, 1);
                        i--;
                    }
                }
            }
        });

        //this is for intervals
        let inputInterval = IntervalInput();
        document.querySelector('#cont2').appendChild(inputInterval.form);
        inputInterval.form.addEventListener('submit', (e) => {
            let start = $(`#timepicker${inputInterval.input1.id}`).val();
            let end = $(`#timepicker${inputInterval.input2.id}`).val();
            let _interval = parseInt(inputInterval.interval_input.value);

            if (_interval < 5) return alert2('interval has to be greater than 5');
            if (_interval%5 !== 0) return alert2('interval has to be from the multiplications of 5');

            let time = {
                start: start,
                end: end
            }

            checkOverNight(time);


            let diff = timeDiffMinutes(time);

            if(diff === 0) {
                return alert2('have to set a range');
            }

            let obj = {start: 0, end: 0};
            let arr = [];
            let ind = -1;

            for (let t = 0; t <= diff; t += _interval) {
                ind ++;
                const nt = addMinutes(time.start, t);
                if (ind == 0) obj.start = nt;
                if (ind == 1) {
                    obj.end = nt;
                    arr.push({
                        start: obj.start,
                        end: obj.end
                    });
                    obj.start = nt;
                    ind = 0;
                }
            }
            for (let i = 0; i < arr.length; i ++) {
                const start = convert2Time(arr[i].start);
                const end = convert2Time(arr[i].end);
                if (start.hh >= 24)
                    start.hh -= 24;
                if (end.hh >= 24)
                    end.hh -= 24;
                arr[i].start = time2String(start);
                arr[i].end = time2String(end);

                days: for (let j = 0; j < dayTables.length; j ++) {
                    const day = dayTables[j].getAttribute('day');
                    if (!document.querySelector('#checkbox-' + day).checked)
                        continue days;

                    const reservData = {
                        start: arr[i].start,
                        end: arr[i].end,
                        duration: min2Time(_interval),
                        day: day
                    }

                    for (var k = 0; k < dataToSubmit.length; k++) {
                        if (dataToSubmit[k].day === day) {
                            if (compare(reservData, dataToSubmit[k])) {
                                alert2("Can't reserve an already reserved time");
                                if (day === 'SAT')console.log(reservData, dataToSubmit[k]);
                                continue days;
                            }
                        }
                    }

                    const reserv = submitReservation(reservData);

                    document.getElementById(day + '-reservations').appendChild(reserv);
                }

            }

        });

        //fill day tables
        var dayTables = document.querySelectorAll('day-table');
        for (var i = 0; i < dayTables.length; i++) {
            dayTables[i].className = 'container';
            var div = document.createElement('span');
            var day = dayTables[i].getAttribute('day');
            div.appendChild(document.createTextNode(" " + day + " "));

            var btn = document.createElement('button');
            var glf = document.createElement('span');
            glf.className = 'glyphicon glyphicon-plus';
            btn.appendChild(glf);
            btn.className = 'btn btn-primary bnt-sm';

            let input = document.createElement('input');
            input.setAttribute('type', 'checkbox');
            input.checked = true;
            input.setAttribute('id', 'checkbox-' + day);


            var reservations = document.createElement('div');
            reservations.setAttribute('is', 'fillable');
            reservations.setAttribute('id', day + '-reservations');

            dayTables[i].appendChild(input);
            dayTables[i].appendChild(div);
            dayTables[i].appendChild(btn);
            dayTables[i].appendChild(reservations);

            btn.addEventListener('click',
                middleware({ reservations: reservations, btn: btn, day: day }, function(data) {
                    data.btn.disabled = true;
                    let res = ReservationInput();

                    res.form.addEventListener('submit',
                        middleware(undefined, function(d) {
                            let reservData = {
                                start: $(`#timepicker${res.input1.id}`).val(),
                                end: $(`#timepicker${res.input2.id}`).val(),
                                duration: res.span.innerHTML,
                                day: data.day
                            };
                            if (res.span.innerHTML === '00:00') {
                                alert2("can't reserve no range on " + data.day);
                                return;
                            }
                            for (var i = 0; i < dataToSubmit.length; i++) {
                                if (dataToSubmit[i].day === data.day) {
                                    if (compare(reservData, dataToSubmit[i])) {
                                        alert2("can't reserve an already reserved time on " + data.day);
                                        return;
                                    }

                                }
                            }

                            var reserv = submitReservation(reservData)
                            res.form.remove();

                            data.reservations.appendChild(reserv);
                            data.btn.disabled = false;
                        }));
                    res.form.addEventListener('reset', middleware(undefined, function() {
                        data.btn.disabled = false;
                        res.form.remove();
                    }));

                    data.reservations.appendChild(res.form);
                    res.appended();

                })
            );


        }
        checkDisable();

        document.querySelector('#add-interval').addEventListener('click', () => appendInterval());
    });
    //this function is used to send data from different scopes to a callback
    //function
    function middleware(data, cb) {
        return function(e) {
            cb(data, e);
        }
    }
    updatePage = fillPage;

})();