'use strict';
// 基本はドットインストール
const load = document.getElementById('opened-log-and-submit');
const loading = document.getElementById('loading');
const finish__open = document.getElementById("finish-finish");
const body = document.getElementById("bodybody");
const finish__open2 = document.getElementById("finish-finish2");
const hide = document.getElementById("modal-otherthan-close")
const close = document.getElementById('close');
const modal = document.getElementById('modal');
const mask = document.getElementById('mask');
const calender__open = document.getElementById("date")
const calender__display = document.getElementById("calendar")
const back = document.getElementById("back")
const decide = document.getElementById("decide");

back.addEventListener('click', () => {
    close.click();
    // finish__open.style.display = "none";
    finish__open.classList.remove("open")
        // バツ印以外押しても消えるよ！
    modal.classList.remove('hidden');
    mask.classList.remove('hidden');
    finish__open.classList.remove("open")
    finish__open2.classList.remove("open2")
});
mask.addEventListener('click', () => {
    close.click();
    finish__open2.classList.remove("open2")
        // バツ印以外押しても消えるよ！
});
decide.addEventListener('click', () => {
    close.click();
    // finish__open.style.display = "none";
    finish__open.classList.remove("open")
        // バツ印以外押しても消えるよ！
    modal.classList.remove('hidden');
    mask.classList.remove('hidden');
    finish__open.classList.remove("open")
    finish__open2.classList.remove("open2")
    console.log("aa")
});
calender__open.addEventListener('click', () => {
    hide.classList.add("hide");
    // this.style.display = "none";
    // バツ印以外をけす
    modal.classList.add("one-screen2");
    // body.classList.add('overflow-hidden')
    // 提出押したら画面が動かないようにする
    //バツ印以外を消す
    loading.classList.add("loader");
    // ぐるぐる君を表示
    let timerId
    timerId = setTimeout(() => {
        loading.classList.remove("loader")
        clearTimeout(timerId);
    }, 0.01);
    // 三秒で消える
    // finish__open.classList.add("open")
    setTimeout(function() {
        finish__open2.classList.add("open2")
    }, 0.01);
    // 三秒たったら出す
    calender__display.classList.add("display_block");
    // calender__display.classList.add("aaaaa");
});

document.getElementById('opened-log-and-submit').onclick = function() {
    // イベントと関数を紐付け
    document.addEventListener('touchmove', disableScroll, { passive: false });
    document.body.classList.add('overflow-hidden');
}
 {
    const today = new Date();
    let year = today.getFullYear();
    let month = today.getMonth();
    function getCalendarHead() {
        const dates = [];
        const d = new Date(year, month, 0).getDate();
        const n = new Date(year, month, 1).getDay();
        for (let i = 0; i < n; i++) {
            dates.unshift({
                date: d - i,
                isToday: false,
                isDisabled: true,
            });
        }
        return dates;
    }
    function getCalendarBody() {
        const dates = []; // date: 日付, day: 曜日
        const lastDate = new Date(year, month + 1, 0).getDate();
        for (let i = 1; i <= lastDate; i++) {
            dates.push({
                date: i,
                isToday: false,
                isDisabled: false,
                isPast: true,
            });
        }
        if (year === today.getFullYear() && month === today.getMonth()) {
            for (let i = 0; i < dates.length; i++) {
                if (dates[i].date === today.getDate()) {
                    dates[i].isToday = true;
                    break;
                }
            }
        }

        for (let i = 0; i < dates.length; i++) {
            const today = new Date();
            if (
                year === today.getFullYear() && month === today.getMonth() &&
                ((dates[i].date > today.getDate() - 1) || month > today.getMonth())
            ) {
                dates[i].isPast = false;
            } else if (year > today.getFullYear()) {
                dates[i].isPast = false;
            } else if (year < today.getFullYear()) {
                dates[i].isPast = true;
            }
        }
        return dates;
    }
    function getCalendarTail() {
        const dates = [];
        const lastDay = new Date(year, month + 1, 0).getDay();
        for (let i = 1; i < 7 - lastDay; i++) {
            dates.push({
                date: i,
                isToday: false,
                isDisabled: true,
            });
        }
        return dates;
    }
    function clearCalendar() {
        const tbody = document.querySelector('tbody');
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
    }
    function renderTitle() {
        const title = `${year}年${String(month + 1).padStart(2, '0')}月`;
        document.getElementById('title').textContent = title;
    }
    function renderWeeks() {
        const dates = [
            ...getCalendarHead(),
            ...getCalendarBody(),
            ...getCalendarTail(),
        ];
        const weeks = [];
        const weeksCount = dates.length / 7;
        for (let i = 0; i < weeksCount; i++) {
            weeks.push(dates.splice(0, 7));
        }
        weeks.forEach(week => {
            const tr = document.createElement('tr');
            week.forEach(date => {
                const td = document.createElement('td');
                td.textContent = date.date;
                if (date.isToday) {
                    td.classList.add('aaaaa');
                }
                if (date.isDisabled) {
                    td.classList.add('disabled');
                }
                if (date.isPast) {
                    td.classList.add('past');
                }
                tr.appendChild(td);
                td.addEventListener("click", () => {
                    const gettitle = document.getElementById("title");
                    const getday = gettitle.innerHTML + td.innerHTML + "日";
                    const day_display = document.getElementById("date")
                    day_display.value = getday
                    td.classList.toggle('aaaaa');
                        // テキストボックスの場合はinnerhtmlじゃなくて.value
                })
            });
            document.querySelector('tbody').appendChild(tr);
        });
    }
    function createCalendar() {
        clearCalendar();
        renderTitle();
        renderWeeks();
    }

    document.getElementById('prev').addEventListener('click', () => {
        month--;
        if (month < 0) {
            year--;
            month = 11;
        }
        createCalendar();
    });
    document.getElementById('next').addEventListener('click', () => {
        month++;
        if (month > 11) {
            year++;
            month = 0;
        }
        createCalendar();
    });
    createCalendar();
}