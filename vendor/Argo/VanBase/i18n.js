if (window.FR == null) {
    window.FR = {};
}

$.extend(FR, {
    /**
     * 这里添加一些必须要使用中文的键值对，然后通过FR.plainText(key)进行调用
     */
    chinese : {

    },

    i18n:{
        "Chart-DataFunction_None":"无",
        "Chart-Unit_Hundred":'百',
        "Chart-Unit_Thousand":"千",
        "Chart-Unit_Ten_Thousand":"万",
        "Chart-Unit_Hundred_Thousand":"十万",
        "Chart-Unit_Million":"百万",
        "Chart-Unit_Ten_Million":"千万",
        "Chart-Unit_Hundred_Million":"亿",
        "Chart-Unit_Billion":"十亿",
        "Chart-Use_Unit":"单位",
        "Chart-Song_TypeFace":"宋体",
        "Chart-DataFunction_Percent":"占比",
        "Chart-Stock_Open":"开盘",
        "Chart-Stock_High":"盘高",
        "Chart-Stock_Low":"盘低",
        "Chart-Stock_Close":"收盘",
        "Chart-Stock_Volume":"成交量",
        "FR-Chart-Chart_Text":"测",
        "Chart-Chart_Name":"图表",
        "Chart-Chart_Title":"新建图表标题",
        "Chart-Trend_Line":"趋势线",
        "Chart-Black_Font":"黑体",
        "Chart-Use_MSBold":"微软雅黑",
        "FR-Chart-Gantt_PlanTime":'Plan Time',
        "FR-Chart-Gantt_RealStart":'Real Start Time',
        "FR-Chart-Gantt_Progress":'percentage',
        "FR-Chart-Gantt_RealEndTime":'Real End Time',
        "Chart-Series_Index":"系列序号",
        "Chart-Category_Index":"分类序号",
        "ChartF-Series_Name":"系列名称",
        "Chart-Category_Name":"分类名",
        "Chart-Series_Value":"系列值",
        "Chart-Project_ID":"项目名",
        "Chart-Step_Index":"步骤序号"
    },


    /**
     * 根据键获取国际化后的值
     * @param key 键
     * @returns {String} 国际化后的文本
     * @example
     *    FR.i18nText("Click");//输出结果为"点击"
     *    FR.i18nText("Sum({R1}, {R2}) = 3", 1,2);//输出结果为"Sum(1, 2) = 3"
     */
    i18nText: function (key) {
        var localeText = FR.i18n[key];
        if (!localeText) {
            localeText = key;
        }
        var len = arguments.length;
        if(len > 1){
            for(var i = 1;i<len;i++){
                var key = "{R"+i+"}";
                localeText = localeText.replaceAll(key, arguments[i]+"");
            }
        }
        return localeText;
    },

    /**
     * 获取不需要国际化的中文字符串
     * @param key 中文字符串对应的键
     * @returns {String} 中文字符串
     */
    plainText : function(key) {
        return this.chinese[key] || key;
    }
});