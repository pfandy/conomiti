
/**
*responsiveのhover処理
  * spでhoverさせない。
  * spのmedia queryの際に、bodyの.hoverを取る。
*/
$(window).on('load resize orientationchange', function(){
  "use strict"; 
  if($(window).width() <= 749){
    $('body').removeClass('hover');
  } else {
    $('body').addClass('hover');
  }
});
/**
*responsiveのhover処理
*/

$(function(){
  "use strict"; 
  
/**
*career table
*/
  $('#intro-difference-table').each(function(){

    var $trsTable = $(this).find('tr'),
        $tdsTable = $trsTable.find('td');
//        tdsHeight = [];

    $(window).on('resize', function(){

      var windowWidth = $(window).width();
      if(windowWidth <= 749){
        var tr0Height = $tdsTable.eq(0).outerHeight(),
            tr1Height = $tdsTable.eq(2).outerHeight(),
            tr2Height = $tdsTable.eq(5).outerHeight(),
            tr3Height = $tdsTable.eq(7).outerHeight();
        $tdsTable.eq(1).css('height', tr0Height);
        $tdsTable.eq(3).css('height', tr1Height);
        $tdsTable.eq(4).css('height', tr2Height);
        $tdsTable.eq(6).css('height', tr3Height);
      }
    });
    $(window).trigger('resize');
  });

/**
*career table
*/
  
/**
*smooth scroll
*/
  var $logoTabAnchors = $('#logo-tab a');//#logo-tabのaは除外
  
  $('a[href^="#"]').not($logoTabAnchors).click(function() {
    
    var headerHeight = $('header').height(),
        adjust = headerHeight + 20,
        time = 500,
        href= $(this).attr("href"),
        target = $(href === "#" || href === "" ? 'html' : href),
        position = target.offset().top - adjust;
    
    $('body,html').animate({scrollTop:position}, time, 'swing');
    return false;
  });
/**
*smooth scroll
*/

/**
*current section
    各セクションの場所を取得
    現在のスクロール上の場所を取得
    それぞれの数値を比較し、自分がどのセクションにいるかを判別
    それに基づき.activeを付与する
*/
  $('body#page-index').each(function(){
    
    var $window = $(window),
        windowHeight = $window.height(),
        headerHeight = $('header').height(),
        adjust = headerHeight + windowHeight / 4,
        $service = $('#service'),
        $vision = $('#vision'),
        $overview = $('#overview'),
        $logo = $('#logo'),
        $contact = $('#contact'),
        serviceOffsetTop = $service.offset().top,
        visionOffsetTop = $vision.offset().top,
        overviewOffsetTop = $overview.offset().top,
        logoOffsetTop = $logo.offset().top,
        contactOffsetTop = $contact.offset().top;

    $('#nav-container').each(function(){
      var $nav = $(this).find('li'),
          currentIndex;

      $window.on('scroll', function(){
        var currentPosition = $window.scrollTop(),
            adjustedCurrentPosition = currentPosition + adjust;

        if(adjustedCurrentPosition >= serviceOffsetTop && adjustedCurrentPosition < visionOffsetTop){
          currentIndex = 0;
        }
        if(adjustedCurrentPosition >= visionOffsetTop && adjustedCurrentPosition < overviewOffsetTop){
          currentIndex = 1;
        }
        if(adjustedCurrentPosition >= overviewOffsetTop && adjustedCurrentPosition < logoOffsetTop){
          currentIndex = 2;
        }
        if(adjustedCurrentPosition >= logoOffsetTop && adjustedCurrentPosition < contactOffsetTop){
          currentIndex = 3;
        }
        if(adjustedCurrentPosition >= contactOffsetTop || adjustedCurrentPosition < serviceOffsetTop){
          currentIndex = 4;
          $nav.removeClass('navActive');
        }

        $nav.removeClass('navActive');
        $nav.eq(currentIndex).addClass('navActive');

      });
    });
  });
/**
*current section
*/

/**
*header border
*/
  
  $('#top').each(function(){
    
    var $window = $(window),
        $header = $('header'),
        $top = $(this),
        topOffsetTop = $top.offset().top;
    
    $window.on('scroll', function(){
      if($window.scrollTop() > topOffsetTop){
        $header.addClass('header-shadow');
      } else {
        $header.removeClass('header-shadow');
      }
    });
    
    $window.trigger('scroll');
    
  });
/**
*header border
*/

/**
*logo tab
*/
  $('#logo').each(function(){

    var $tabUl = $(this).find('#logo-tab'),
        $tabLists = $tabUl.find('li'),
        $tabPanels = $(this).find('.tab-panel');

    $tabUl.on('click', 'li', function(event){
      
      event.preventDefault();

      var $this = $(this),
          $targetAnchor = $this.find('a');
      
      if($this.hasClass('tabActive')){
        return;
      }
      
      $tabLists.removeClass('tabActive');
      $this.addClass('tabActive');
      
//      $tabPanels.hide();
//      $($targetAnchor.attr('href')).show();
      $tabPanels.removeClass('tabActive');
      $($targetAnchor.attr('href')).addClass('tabActive');
      
    });
    
    $tabLists.eq(0).trigger('click');
  });
/**
*log tab
*/

/**
*scroll fadeIn
*/
  $(window).scroll(function(){
    $('.scrollFadeIn').each(function(){
      var targetElement = $(this).offset().top,
          scroll = $(window).scrollTop(),
          windowHeight = $(window).height();
      if(scroll > targetElement - windowHeight * 0.75){
        $(this).css('opacity', '1');
        $(this).not('#vision-theme-column, #logo-parts').css('transform', 'translate(0, 0)');
        $('#vision-theme-column').css('transform', 'translate(0, 0) rotate(270deg)');
        $('#logo-parts').css('transform', 'none');
      }
    });
  });
  $(window).trigger('scroll');
/**
*scroll fadeIn
*/

/**
*logo accordion
*/
  $('#logo-parts').each(function(){

    var $logoLists = $(this).find('li'),
        $logoExplain = $logoLists.find('.logo-explain');

    //breakpointを超えたらlogo accordionの開閉をリセットする処理（responsive処理）
    //range pcに入る時のみリセットを実施（range sp中に開閉が起こり得る為）
    $(window).on('resize', function(){

      var windowWidth = $(window).width(),
          $body = $('body');

      if(windowWidth <= 749){
        if(!$body.hasClass('range-sp')){
          $body.addClass('range-sp').removeClass('range-pc');
        }
      } else {
        if(!$body.hasClass('range-pc')){
          $logoLists.removeClass('explainActive');
          $logoExplain.removeClass('explainActive');
          $body.addClass('range-pc').removeClass('range-sp');
        }
      }
    });
    
    //clickでlogo accordionを開閉させる処理
    $logoLists.on('click', function(){

      var $this = $(this),
          $targetExplain = $this.find('.logo-explain');

      if($targetExplain.hasClass('explainActive')){
        $this.removeClass('explainActive');
        $targetExplain.removeClass('explainActive');
      } else {
        $this.addClass('explainActive');
        $targetExplain.addClass('explainActive');
      }
    });
  });
/**
*logo accordion
*/
  

});