// 現在の日付を取得


// カレンダー内の全ての日付要素を取得
const getdates = document.querySelectorAll(td);

// 既にtodayクラスが付与されているかどうかのフラグ
let isTodaySet = false;

// 各日付要素に対して処理を行う
dates.forEach(date => {
  // 日付を取得
  const dateValue = new Date(date.dataset.date);

  // 日付が今日の日付と一致するかどうかを判定
  if (dateValue.toDateString() === today.toDateString()) {
    // 既にtodayクラスが付与されている場合は何もしない
    if (isTodaySet) {
      return;
    }

    // 今日の日付にtodayクラスを付与
    date.classList.add("aaaaa");
    isTodaySet = true;
  }
});