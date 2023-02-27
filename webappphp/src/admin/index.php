<?php
$dsn = 'mysql:dbname=webapp;host=db';
$user = 'root';
$password = 'root';

$dbh = new PDO($dsn, $user, $password);
$sql = 'SELECT*FROM webapp_hours ORDER BY id';


//! ***********************************************
//! 学習時間計算
//! ***********************************************

$hours = $dbh->query("SELECT * FROM webapp_hours")->fetchAll(PDO::FETCH_ASSOC);
// echo '<pre>';
// var_dump( $hours );
// echo '</pre>';
$contents = $dbh->query("SELECT * FROM contents")->fetchAll(PDO::FETCH_ASSOC);
// echo '<pre>';
// var_dump( $contents );
// echo '</pre>';
$languages = $dbh->query("SELECT * FROM languages")->fetchAll(PDO::FETCH_ASSOC);




// 今日の分の時間を取得
$today_date = date("Y-m-d");
$today_hours_sql = "SELECT hours FROM webapp_hours WHERE DATE(date) = '$today_date'";
$stmt = $dbh->query($today_hours_sql);
$this_hours = $stmt->fetchAll(PDO::FETCH_COLUMN);
$today_total = array_sum($this_hours);

// 今月分の時間を取得
$first_day_of_month = date("y-m-01");
$last_day_of_month = date("y-m-t");
$month_hours_sql = "SELECT hours FROM webapp_hours WHERE date BETWEEN '$first_day_of_month' AND '$last_day_of_month'";
$stmt = $dbh->query($month_hours_sql);
$this_hours = $stmt->fetchAll(PDO::FETCH_COLUMN);
$month_total = array_sum($this_hours);

// すべての日付分の時間を取得
$all_hours_sql = "SELECT hours FROM webapp_hours";
$stmt = $dbh->query($all_hours_sql);
$this_hours = $stmt->fetchAll(PDO::FETCH_COLUMN);
$all_total = array_sum($this_hours);


//! ***********************************************
//! 学習時間計算終わり このデータは下でも使う
//! ***********************************************





// ***********************************************
//  棒グラフ
// ***********************************************

//? *********************************************
//?*TODO    phpでのデータをjavascriptに渡す
//!※参照 ミニドリルweek31
//? *********************************************

class Study
{
    public $day;
    public $hours;
    public function get_day()
    {
        return $this->day;
    }
    public function get_hours()
    {
        return (int)$this->hours;
    }
}
$month = date('n');  //date('n')で今月の月を数値として取得
$sql = "SELECT DATE_FORMAT(webapp_hours.date, '%d') as day, sum(webapp_hours.hours) as hours FROM webapp_hours WHERE YEAR(webapp_hours.date) = YEAR(CURRENT_DATE()) AND MONTH(webapp_hours.date) = $month GROUP BY day";
$studies = $dbh->query($sql)->fetchAll(\PDO::FETCH_CLASS, Study::class);
$formatted_study_data = array_map(function ($study) {
    return [$study->get_day(), $study->get_hours()];
}, $studies);
$bar_data = json_encode($formatted_study_data);



// ***********************************************
//  棒グラフ終わり
// ***********************************************


//! ***********************************************
//!  学習言語グラフ
//! ***********************************************

//? *********************************************
//*TODO  webapp_languageテーブルとlanguagesテーブルを結合して、各言語の学習時間を計算
//? *********************************************



$stmt = $dbh->query("SELECT * FROM webapp_hours");
$webapp_hours = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $dbh->query("SELECT * FROM languages");
$languages = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $dbh->prepare("
    SELECT languages.language, SUM(webapp_hours.hours) AS total_hours, color
    FROM webapp_language
    INNER JOIN webapp_hours ON webapp_language.hours_id = webapp_hours.id
    INNER JOIN languages ON webapp_language.language_id = languages.id
    GROUP BY languages.id
");
// SELECT テーブル名.カラム名, ... FROM テーブル名1
//   INNER JOIN テーブル名2
//   ON テーブル名1.カラム名1 = テーブル名2.カラム名2;
$stmt->execute();
$languages_hours = $stmt->fetchAll(PDO::FETCH_ASSOC);




//! ***********************************************
//!  学習言語グラフ 終わり
//! ***********************************************




// ***********************************************
// *  学習コンテンツグラフ
// ***********************************************



$stmt = $dbh->query("SELECT * FROM contents");
$languages = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $dbh->prepare("
SELECT contents.content, SUM(webapp_content.hours_id/webapp_content_count.count) AS total_content_hours, contents.color
FROM webapp_content
INNER JOIN contents ON webapp_content.content_id = contents.id
INNER JOIN (
SELECT hours_id, COUNT(DISTINCT content_id) AS count
FROM webapp_content
GROUP BY hours_id
) AS webapp_content_count ON webapp_content.hours_id = webapp_content_count.hours_id
GROUP BY contents.id
");
$stmt->execute();
$contents_hours = $stmt->fetchAll(PDO::FETCH_ASSOC);

// [ 'content' => 'HTML', 'total_content_hours' => 1.5, 'color' => '#4E79A7' ],
$content_hour_sums = [];
// $content_hour_sumsの中身:
// [
// 'HTML' => 1.5,
// 'CSS' => 1.5,
// 'PHP' => 1,
// 'Laravel' => 0.5,
// 'SQL' => 0.5,
// ]
foreach ($contents_hours as $item) {
    $content_hour_sums[$item['content']] = $item['total_content_hours'];
}

$num_content_ids = $stmt->rowCount();  //$contents_hoursの行数を取得
$total_hours = array_sum($content_hour_sums); //配列ないの数値の全ての合計を取得
$average_hours = $total_hours / count($contents_hours);


//! ***********************************************
//! *  ここわからん
//! ***********************************************


$adjusted_contents_hours = array_map(function ($item) use ($content_hour_sums, $num_content_ids, $average_hours) {
    $content_hours = $content_hour_sums[$item['content']] / $num_content_ids;
    return [
        'content' => $item['content'],
        'total_content_hours' => $content_hours / $average_hours,
        'color' => $item['color'],
    ];
}, $contents_hours);


//! ***********************************************
//! *  ここわからん 終わり
//! ***********************************************

// array_map()関数を使用して、各コンテンツの時間を、平均時間で割ったものを計算し、$adjusted_contents_hours配列に格納しています。$content_hour_sums配列から各コンテンツの時間を取得するために、useキーワードを使用して、$content_hour_sums変数をクロージャー内で利用するように指定します。また、コンテンツの色情報も配列に含めています。最終的に、$adjusted_contents_hours配列には、各コンテンツの名前、時間、色情報が含まれています。

$series_content_data = json_encode(array_column($adjusted_contents_hours, 'total_content_hours'));
$labels = json_encode(array_column($adjusted_contents_hours, 'content'));
$colors = json_encode(array_column($adjusted_contents_hours, 'color'));



// ***********************************************
// *  学習コンテンツグラフ 終わり
// ***********************************************

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../top/style/normalize.css">
    <link rel="stylesheet" href="../top/style/style.css">
    <script src="https://unpkg.com/apexcharts/dist/apexcharts.min.js"></script>
    <script src="../top/js/webapp.js" defer></script>
    <script src="../top/js/calender.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr "></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js " integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin=" anonymous "></script>
    <title>ウェーブアップ</title>
</head>

<body id="bodybody">
    <header>
        <div class="header-menu">
            <div class="menu">
                <img src="../top/imgs/logo.png" class="header-logo">
                <p class="header-menu-week">4th week</p>
            </div>
            <div class="header-log" id="open">
                記録・投稿
            </div>
        </div>
        <div id="mask" class="hidden"></div>
        <div class="modal-box">
            <section id="modal" class="hidden">
                <div id="modal-content">
                    <div id="close">
                        ×
                    </div>
                    <div id="loading"></div>
                    <div id="modal-otherthan-close">
                        <div class="modal-log-list">
                            <div class="day-content-language">
                                <div class="modal-log-day">
                                    <p>学習日</p>
                                    <input class="day-textbox" id="date">
                                </div>
                                <div class="modal-log-content">
                                    <p class="log-title">学習コンテンツ（複数選択可）</p>
                                    <div class="modal-content-list">
                                        <script>
                                            const contents = ['N予備校', 'ドットインストール', 'POSSE課題'];
                                            contents.forEach((content) => {
                                                const id = `check-${content.replace(/[^a-zA-Z0-9]/g, '')}`;
                                                const label = content === 'N予備校' ? 'nyobi' :
                                                    content === 'ドットインストール' ? 'dot' :
                                                    content === 'POSSE課題' ? 'kadai' :
                                                    content.toLowerCase();
                                                document.write(`
                                                        <input type="checkbox" id="${id}" class="input-checkbox" name="content" value="${label}">
                                                        <label for="${id}" class="checkbox">
                                                        <div class="checkmark-true"></div>${content}
                                                        </label>
                                                    `);
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="modal-log-language">
                                    <p class="log-title">学習言語（複数選択可）</p>
                                    <div class="modal-language-list">
                                        <script>
                                            const languages = ['HTML', 'CSS', 'JavaScript', 'PHP', 'Laravel', 'SQL', 'SHELL', '情報システム基礎知識（その他）'];
                                            languages.forEach((language) => {
                                                const id = `check-${language.replace(/[^a-zA-Z0-9]/g, '')}`;
                                                const label = language === '情報システム基礎知識（その他）' ? 'other' : language.toLowerCase();
                                                // language.toLowerCase入力されたものを小文字にする
                                                document.write(`
                                                        <input type="checkbox" id="${id}" class="input-checkbox" name="language" value="${label}">
                                                        <label for="${id}" class="checkbox">
                                                        <div class="checkmark-true"></div>${language}
                                                        </label>
                                                    `);
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                            <div class="time-twitter">
                                <p>学習時間</p>
                                <input class="time-textbox" type="text" placeholder="when" />
                                <p class="log-title">Twitter用コメント</p>
                                <div class="tweet">
                                    <textarea class="twitter-textbox" cols="40" rows="12" onkeyup="viewStrLen();" id="twitterarea"></textarea>
                                    <div class="share">
                                        <input type="checkbox" id="check" class="input-checkbox js-twitter" name="twitter" value="twitter"><label for="check" class="log-title-twitter checkbox">
                                            <div class="checkmark-true"></div>Twitterにシェアする
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="header-log" id="opened-log-and-submit" onclick="showLoad()">
                            記録・投稿
                        </div>
                    </div>
                    <article class="finish-sign" id="finish-finish">
                        <span class="record-title">AWESOME!</span>
                        <div class="check-mark-containor">
                            <div class="check-mark">&not;</div>
                        </div>
                        <figure class="icon-check-done">
                        </figure>
                        <div class="record-text">記録・投稿<br>完了しました
                        </div>
                    </article>
                    <div class="finish-sign" id="finish-finish2">
                        <span class="arrowmark" id="back">←</span>
                        <table id="calendar">
                            <thead>
                                <tr>
                                    <th id="prev">&lt;</th>
                                    <th id="title" colspan="5"> </th>
                                    <th id="next">&gt;</th>
                                </tr>
                                <tr class="">
                                    <th>Sun</th>
                                    <th>Mon</th>
                                    <th>Tue</th>
                                    <th>Wed</th>
                                    <th>Thu</th>
                                    <th>Fri</th>
                                    <th>Sat</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="header-log" id="decide">
                            決定
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </header>
    <main>
        <div class="log">
            <section class="graphs">
                <article class="time">
                    <ul class="parameter">
                        <li>
                            <ul class="todays">
                                <li class="range"></li>
                                <li class="parameter-time"><?= $today_total;  ?></li>
                                <li class="unit">hour</li>
                            </ul>
                        </li>
                        <li>
                            <ul class="todays">
                                <li class="range"></li>
                                <li class="parameter-time"><?= $month_total  ?></li>
                                <li class="unit">hour</li>
                            </ul>
                        </li>
                        <li>
                            <ul class="todays">
                                <li class="range"></li>
                                <li class="parameter-time"><?= $all_total;  ?></li>
                                <li class="unit">hour</li>
                            </ul>
                        </li>
                    </ul>
                    <div class="graph" id="bar-graph">
                    </div>
                </article>
                <article class="circle-graphs">
                    <div class="language">
                        <p class="graph-title">学習言語</p>
                        <php?>
                            <div class="circle-graph" id="circle-charts1"></div>
                            </php>
                    </div>
                    <div class="content">
                        <p class="graph-title">学習コンテンツ</p>
                        <div class="circle-graph" id="circle-charts2"></div>
                    </div>
                </article>
            </section>
            <div>
                <div class="date">
                    &nbsp; 2023年 2月&nbsp;
                </div>
                <div class="header-log" id="open2">
                    記録・投稿
                </div>
            </div>
        </div>
    </main>
    <script>
        //! **********************************************
        //! *  棒グラフjs
        //! **********************************************



        const transpose = a => a[0].map((_, c) => a.map(r => r[c]));
        let original_chart = JSON.parse('<?php echo $bar_data; ?>');
        let new_chart = transpose(original_chart);
        console.log(new_chart)
        let options = {
            series: [{
                name: '時間', // グラフにホバーした時に出る名前
                data: new_chart[1]
                // 各グラフのデータ
            }],
            legend: // legend:出てくる文字のやつ
            {
                display: false
            },
            chart: {
                height: 350,
                type: 'bar',
                toolbar: {
                    show: false // 自動で作られるハンバーガーメニューの生成を防ぐ
                }
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%', // グラフの太さ
                    borderRadius: 5, // グラフの先っちょ丸くする
                }
            },
            dataLabels: {
                enabled: false // グラフ一本一本には数値書き込まなくていいよ
            },
            xaxis: {
                axisTicks: {
                    show: false, // x軸の区切りいらない
                },
                labels: {
                    formatter: function(value) {
                        if (value != undefined) {
                            let day = value.split(" ")
                            return day;
                            // % 2 == 1 ? "" : value;
                        }
                    },
                    style: {
                        colors: '#B5CDDE',
                    }
                },
            },


            grid: {
                yaxis: {
                    lines: {
                        show: false // 横線いらない
                    },
                },
            },

            yaxis: {
                labels: {
                    formatter: function(value) {
                        return value + "h";
                    },
                    style: {
                        colors: '#B5CDDE',
                    }
                },
            },


            labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30'],


            fill: {
                colors: ["#0F71BB"],
                type: 'gradient',
                gradient: {
                    type: 'vertical',
                    gradientToColors: ['#74DAFF'],
                }
            },
            // 色指定

            responsive: [{
                breakpoint: 768,
                options: {
                    chart: {
                        height: 200,
                    }
                },
            }]
        };
        let chart = new ApexCharts(document.querySelector("#bar-graph"), options);
        chart.render();

        //! ***********************************************
        //! *  棒グラフjs終わり
        //! **********************************************





        //***********************************************
        //*  学習言語グラフjs
        // **********************************************



        let seriesData = <?php echo json_encode(array_column($languages_hours, 'total_hours')); ?>;

        let convertedSeriesData = seriesData.map(function(item) {
            return parseFloat(item);
        });
        console.log(convertedSeriesData)
        let options2 = {
            series: convertedSeriesData,
            chart: {
                height: 400,
                type: 'donut',
            },

            plotOptions: {
                pie: {
                    donut: {
                        size: '55px',
                        labels: {
                            show: true,
                            total: {
                                showAlways: false,
                                show: false
                            }
                        }
                    }
                }
            },

            stroke: {
                width: 0 //グラフ間の隙間の大きさ ０なら開けない
            },
            labels: <?php echo json_encode(array_column($languages_hours, 'language')); ?>,
            colors: <?php echo json_encode(array_column($languages_hours, 'color')); ?>,

            dataLabels: {
                style: {
                    fontSize: '0.75rem',
                }
            },
            // ％を指定して描画

            legend: {
                position: 'bottom',
                horizontalAlign: 'left',
                fontSize: '15px',
                // したの色々説明とか apexC
            },

            responsive: [{
                breakpoint: 768,
                options: {
                    chart: {
                        width: 200
                    },
                }
            }],
        };

        let chart2 = new ApexCharts(document.getElementById("circle-charts1"), options2);
        chart2.render();

        //***********************************************
        //*  学習言語グラフjs 終わり
        // **********************************************






        //! ***********************************************
        //!    学習コンテンツグラフjs
        //! **********************************************




        let series_content_Data = <?php echo json_encode(array_column($contents_hours, 'total_content_hours')); ?>;

        let convertedSeriesData2 = series_content_Data.map(function(item) {
            return parseFloat(item);
        });
        console.log(convertedSeriesData2)

        let options3 = {
            series: convertedSeriesData2,
            chart: {
                height: 400,
                type: 'donut',
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '55px',
                        labels: {
                            show: true,
                            total: {
                                showAlways: false,
                                show: false
                            }
                        }
                    }
                }
            },
            labels: <?php echo $labels; ?>,
            colors: <?php echo $colors; ?>,
            dataLabels: {
                style: {
                    fontSize: '0.75rem',
                }
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'bottom',
                fontSize: '15px',
            },
            stroke: {
                width: 0,
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: {
                        width: 200
                    },
                }
            }],
            tooltips: {
                enabled: false
            },
        };
        let chart3 = new ApexCharts(document.getElementById("circle-charts2"), options3);
        chart3.render();


        //! **********************************************
        //! *  学習コンテンツグラフjs 終わり
        //! **********************************************
    </script>
</body>

</html>