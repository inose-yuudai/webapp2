'use strict';

{
    let options = {
        series: [30, 20, 10, 5, 5, 20, 20, 10],
        chart: {
            height: 400,
            type: 'donut',
        },

        plotOptions: {
            pie: {
                donut: {
                    size: '55px',
                }
            }
        },

        stroke: {
            width: 0 //グラフ間の隙間の大きさ　０なら開けない
        },
        labels: ["HTML", "CSS", "JavaScript", "PHP", "Laravel", "SQL", "SHELL", "情報システム基礎知識(その他)"],
        colors: ['#0445EC', '#0F70BC', '#20BDDE', '#3CCEFE', '#B29EF3', '#6C46EB', '#4A17EF', '#3005C0'],

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

    let ctx = document.getElementById('circle-charts1')
    let chart = new ApexCharts(ctx, options);

    chart.render();
}