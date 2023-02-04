/**
 * 图形统计
 */
Vue.component('el-statistics-highcharts', {
    template:`<div class="el-statistics-highcharts" :id="id" :style="{'height': height}"></div>`,
    props: {
        height: {
            type: String,
            default: ''
        },
        // areaspline column pie
        type: {
            type: String,
            default: ''
        },
        data: {
            type: Array,
            default: []
        },
        tickInterval: {
            type: Number,
            default: 1
        },
        categories: {
            type: Array,
            default: []
        },
        config: {
            type: Object,
            default: {},
        },
    },
    data (){
        return {
            id: common.id(),
        }
    },
    mounted() {
        var self  = this;
        var shared  = self.type === 'column' ? false : true;
        var tooltip = self.type === 'pie' ? {pointFormat: '占比: {point.percentage:.1f}%'} : {shared: shared};
        var series  = self.type === 'pie' ? [{ type: self.type, data: self.data }] : self.data;
        new Highcharts.Chart({
            chart: {
                renderTo: self.id,
                type: self.type,
            },
            colors: ['#3292E0', '#198A4C', '#CCCCCC', '#D2691E', '#800000', '#FFA500', '#808000', '#008B8B', '#8A2BE2', '#FF1493'],
            plotOptions: {
                // column: {} 你也可以单独设置
                series: {
                    fillOpacity: 0.3,
                    marker: {
                        radius: 4,
                    },
                    lineWidth:1,
                    states: {
                        hover: {lineWidth: 1.5},
                        inactive: {
                            opacity: 1
                        }
                    },
                },
            },
            legend: {
                enabled: true
            },
            title: { 
                text: '' 
            },
            xAxis: {
                tickInterval: self.tickInterval,
                categories: self.categories
            },
            yAxis: {
                title:{ text:'' }, 
                gridLineWidth:0, 
                fontSize:'12px' 
            },
            series: series,
            tooltip: tooltip,
        });
    },
});

/**
 * 总览-图形统计
 */
Vue.component('el-statistics-overview-chart', {
    data() {
        return {
            chartUrl: "statistics/index/chart",
            data: [],
            loading: false,
            categories: [],
            tickInterval: 3,
        }
    },
    props: {
        chart: {
            type: String,
            default: '',
        },
        title: {
            type: String,
            default: '',
        },
        time: {
            type: String,
            default: '',
        },
        type: {
            type: String,
            default: '',
        },
        link: {
            type: String,
            default: '',
        }
    },
    template: `
        <el-card class="box-card el-statistics-overview-chart">
            <div slot="header" class="clearfix">
                <span class="title">{{title}}</span>
                <a @click="getPath(link)" href="javascript:;">
                    <el-tooltip class="item" effect="dark" content="查看更多" placement="top">
                        <el-button class="right-button" type="info"  icon="el-icon-arrow-right" size="mini" circle></el-button>
                    </el-tooltip>
                </a>
            </div>
            <div style="height: 377px;" v-loading="loading">
                <template v-if="chart === 'province'">
                    <el-statistics-mapchart
                        v-if="loading === false" 
                        height="377px" 
                        :type="chart" 
                        :data="data">
                    </el-statistics-mapchart>
                </template>
                <template v-else>
                    <el-statistics-highcharts
                        v-if="loading === false" 
                        height="377px" 
                        :type="chart" 
                        :data="data" 
                        :categories="categories" 
                        :tickInterval="tickInterval">
                    </el-statistics-highcharts>
                </template>
            </div>
        </el-card>
    `,
    created(){
        this.getData();
    },
    methods: {
        getData(){
            let self = this;
            self.loading = true;
            request.post(self.chartUrl, {time: self.time, type: self.type}, function (res) {
                self.categories = res.categories;
                self.data       = res.data;
                self.loading    = false;
                if (self.chart === 'column') self.tickInterval = 1;
            })
        },
        getPath(link) {
            parent.parentVm.path = link;
        }
    },
    watch: {
        time(v){
            this.$nextTick(() => {
                if (this.chart === 'areaspline') {
                    switch(v){
                        case ('today'):
                        case ('yesterday'):
                            this.tickInterval = 3;
                            break;
                        case ('week'):
                            this.tickInterval = 1;
                            break;
                        case ('month'):
                            this.tickInterval = 2;
                            break;
                    }
                }
                this.getData();
            })
        }
    }
});

/**
 * 总览-表格统计top10
 */
Vue.component('el-statistics-overview-table', {
    template: `
        <el-card class="box-card el-statistics-overview-table">
            <div slot="header" class="clearfix">
                <span class="title">TOP10{{title}}</span>
                <a @click="getPath(link)" href="javascript:;">
                    <el-tooltip class="item" effect="dark" content="查看更多" placement="top">
                        <el-button class="right-button" type="info"  icon="el-icon-arrow-right" size="mini" circle></el-button>
                    </el-tooltip>
                </a>
            </div>
            <el-table :data="data" v-loading="loading">
                <el-table-column prop="name" :label="title"></el-table-column>
                <el-table-column prop="pv"label="浏览量(PV)" width="100"></el-table-column>
                <el-table-column prop="percentage" label="占比" width="100"></el-table-column>
            </el-table>
        </el-card>
    `,
    props: {
        title: {
            type: String,
            default: '',
        },
        time: {
            type: String,
            default: '',
        },
        type: {
            type: String,
            default: '',
        },
        link: {
            type: String,
            default: '',
        }
    },
    data() {
        return {
            tableUrl: "statistics/index/table",
            data: [],
            loading: false,
        }
    },
    created(){
        this.getData();
    },
    methods: {
        getData(){
            let self = this;
            self.loading = true;
            request.post(self.tableUrl, {time: self.time, type: self.type}, function (res) {
                self.data       = res.data;
                self.loading    = false;
            })
        },
        getPath(link) {
            parent.parentVm.path = link;
        }
    },
    watch: {
        time(){
            this.$nextTick(() => {
                this.getData();
            })
        }
    }
});

/**
 * 头部统计
 */
Vue.component('el-statistics-trend-header', {
    template: `
    <div class="el-statistics-trend-header">
        <div class="el-trend-item">
            <div>
                <span>浏览量(PV)</span>
                <el-tooltip 
                    class="item" 
                    effect="light" 
                    placement="bottom">
                    <div slot="content">即通常说的Page View(PV)，用户每打开一个网站页面就被记录1次。<br/>用户多次打开同一页面，浏览量值累计。</div>
                    <i class="el-icon-question"></i>
                </el-tooltip>
            </div>
            <div>{{count['pv']}}</div>
        </div>
        <div class="el-trend-item">
            <div>
                <span>访客数(UV)</span>
                <el-tooltip 
                    class="item" 
                    effect="light" 
                    placement="bottom">
                    <div slot="content">一天之内您网站的独立访客数(以Cookie为依据)，<br/>一天内同一访客多次访问您网站只计算1个访客。</div>
                    <i class="el-icon-question"></i>
                </el-tooltip>
            </div>
            <div>{{count['uv']}}</div>
        </div>
        <div class="el-trend-item">
            <div>
                <span>IP数</span>
                <el-tooltip 
                    class="item" 
                    effect="light" 
                    content="一天之内您网站的独立访问ip数。" 
                    placement="bottom">
                    <i class="el-icon-question"></i>
                </el-tooltip>
            </div>
            <div>{{count['ip']}}</div>
        </div>
        <div class="el-trend-item">
            <div>
                <span>跳出率</span>
                <el-tooltip 
                    class="item" 
                    effect="light" 
                    content="只浏览了一个页面便离开了网站的访问次数占总的访问次数的百分比。" 
                    placement="bottom">
                    <i class="el-icon-question"></i>
                </el-tooltip>
            </div>
            <div>{{count['quit']}}</div>
        </div>
        <div class="el-trend-item">
            <div>
                <span>平均访问时长</span>
                <el-tooltip 
                    class="item" 
                    effect="light" 
                    placement="bottom">
                    <div slot="content">访客在一次访问中，平均打开网站的时长。<br/>即每次访问中，打开第一个页面到关闭最后一个页面的平均值，<br/>打开一个页面时计算打开关闭的时间差。</div>
                    <i class="el-icon-question"></i>
                </el-tooltip>
            </div>
            <div>{{count['duration_avg']}}</div>
        </div>
    </div>
    `,
    props: {
        count: {
            type: Array,
            default: '',
        },
    },
});

/**
 * 地图统计
 */
Vue.component('el-statistics-mapchart', {
    template:`<div class="el-statistics-mapchart" :id="id" :style="{'height': height}"></div>`,
    props: {
        height: {
            type: String,
            default: ''
        },
        // province country
        type: {
            type: String,
            default: ''
        },
        data: {
            type: Array,
            default: []
        },
    },
    data () {
        return {
            id: common.id(),
        }
    },
    mounted() {
        var self    = this;
        var mapData = self.type === 'province' ? chinaMapData : Highcharts.maps['custom/world'];
        Highcharts.mapChart(self.id, {
            title : {
                text : ''
            },
            colors: ['rgba(19,64,117,0.05)', 'rgba(19,64,117,0.2)', 'rgba(19,64,117,0.4)', 'rgba(19,64,117,0.5)', 'rgba(19,64,117,0.6)', 'rgba(19,64,117,0.8)', 'rgba(19,64,117,1)'],
            mapNavigation: {
                enabled: false,
                buttonOptions: {
                    verticalAlign: 'bottom'
                }
            },
            tooltip: {
                useHTML: true,
                headerFormat: '<div>',
                pointFormat: '<div>{point.name}</div><div>浏览量(PV)：{point.value}</div>',
                footerFormat: '</div>'
            },
            colorAxis: {
                min: 1,
                max: 1000,
                type: 'logarithmic'
            },
            series : [{
                data : self.data,
                mapData: mapData,
                joinBy: 'name',
                name: '地图',
                states: {
                    hover: {
                        color: '#a4edba'
                    }
                },
            }]
        });
    },
});
