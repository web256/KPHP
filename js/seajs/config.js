seajs.config({
    base: '../js',
    alias: {
      'jquery': 'public/jquery-1.7.1.min', 
      'jcarousellite':'public/jcarousellite.min', //首页轮播插件缩写
      'tabso':'public/jquery.tabso_yeso',
      'carouFredSel':'jquery.carouFredSel-6.2.1-packed'
    },
    preload: 'jquery',
    charset: 'utf-8',
    //timeout: 20000,
    debug: false
});