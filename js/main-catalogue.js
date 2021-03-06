// main-catalogue.js

var ul, $image, sliders;

requirejs(['jquery', 'angular', 'bxslider', 'colpick', 'jquery-ui', 'shapeshift', 'mCustomScrollbar-concat', 'pramukhime', 'pramukhindic', 'pramukhime-common', 'bootstrap', 'cropper', 'main-crop', 'intlTelInput', 'html2canvas'], function ($, angular) {
  
  $image = $('.img-container > img');

  $(document).ready(function () {

    $(document).on('click', 'a[href="#"]', function (e) {
      e.preventDefault();
    });
    
    $(document).on('click', 'a.read_more', function (e) {
      e.preventDefault();
      var realHeight = $(this).prev()[0].scrollHeight;
      if ($(this).prev().height() == 18) {
        $(this).prev().animate({
            height: realHeight
        });
        $(this).text("Close");
      } else {
        $(this).prev().animate({
            height: 18
        });
        $(this).text("Read More...");
      }
    });
    
    $(document).on("click", ".theme_head a.nav_open", navOpenFunction);

    $("#fakeLanguage").on("change", function () {
      $("#drpLanguage").val($(this).find(":selected").val()).trigger("change");
    });
    
    /*$('#od_domainname').on('click', function () {

      if ($('#orderdomain').attr('readonly') == 'readonly') {
        $('#orderdomain').removeAttr('readonly');
      } else {
        $('#orderdomain').attr('readonly', true);
      }
    });*/

    var theme_bg = $(".theme_head").eq(1).css("background-color");

    var rightHeight = $(window).height() - 95;
    $(".middle").css("height", rightHeight + "px");


    $('.editPicker21').colpick({
      colorScheme: 'dark',
      layout: 'rgbhex',
      color: app_bg_color || theme_bg,
      submit: 0,
      onChange: function (hsb, hex, rgb, el, bySetColor) {
        $("#bg").val("#" + hex);
        $(el).css('background-color', '#' + hex);
        $("#present p.long_text_content").css("color", "#" + hex);
        $(".theme_head").css("background-color", "#" + hex);
      }
    });

    if (upreview == "preview") {
      $(".preview").trigger('click');
    }

    $("#mobile_country").intlTelInput({
      utilsScript: "js/utils.js"
    });

  });

  pramukhIME.addLanguage(PramukhIndic);

  pramukhIME.enable();
  pramukhIME.onLanguageChange(scriptChangeCallback);
  var lang = (getCookie('pramukhime_language', ':english')).split(':');
  pramukhIME.setLanguage(lang[1], lang[0]);
  ul = document.getElementById('pi_tips');

  var elem, len = ul.childNodes.length,
    i;
  for (i = 0; i < len; i++) {
    elem = ul.childNodes[i];
    if (elem.tagName && elem.tagName.toLowerCase() == 'li') {
      tips.push(elem.innerHTML);
    }
  }
  for (i = len - 1; i > 1; i--) {
    ul.removeChild(ul.childNodes[i]);
  }
  ul.childNodes[i].className = 'tip'; // replace small tip text with large

  showNextTip(); // call for first time
  setTimeout('turnOffTip()', 90000); // show tips for 1.5 minutes
  document.getElementById('typingarea').focus();

  // set width and height of dialog
  var w = window,
    d = document,
    e = d.documentElement,
    g = d.getElementsByTagName('body')[0],
    x = w.innerWidth || e.clientWidth || g.clientWidth,
    y = w.innerHeight || e.clientHeight || g.clientHeight;
  var elem = document.getElementById('dialog');
  elem.style.top = ((y - 550) / 2) + 'px';
  elem.style.left = ((x - 700) / 2) + 'px';

  var referBaseUrl;
  
  if (BASEURL.indexOf('panel/') > -1) {
    referBaseUrl = BASEURL.split('panel/')[0];
  }
  else {
    referBaseUrl = BASEURL;
  }

  // angular code starts
  var app = angular.module('catalogueApp', []);

  app.constant('ApiMap', {
    getComponents: {
      url: BASEURL + 'API/retailcomponent.php/getcomponents',
      serial: false
    },
    getAppData: {
      url: BASEURL + 'API/retailsave.php/catalogLaunch',
      serial: true
    },
    currentTheme: {
      url: BASEURL + 'API/retailsave.php/gettemplate',
      serial: true
    },
    getProducts: {
      url: referBaseUrl + 'catalogue/ecommerce_catalog_api/webproducts.php/prodList',
      serial: true
    },
    getCumulativeCats: {
      url: referBaseUrl + 'catalogue/ecommerce_catalog_api/webcategories.php/webcategorylist',
      serial: true
    },
    postAppData: {
      url: BASEURL + 'API/retailsave.php/setretail',
      serial: true
    },
    saveScreenshot: {
      url: BASEURL + 'modules/checkapp/screenshot.php',
      serial: true
    },
    checkAppName: {
      url: BASEURL + 'API/checkAppName.php/index',
      serial: true
    }
  });

  app.directive('imgOnLoad', function () {
    return {
      restrict: 'A',
      link: function (scope, element, attrs) {
        element.on('load', function () {
          scope.$apply(attrs.imgOnLoad);
          // usage: <img ng-src="src" img-on-load="imgLoadCallback()" />
        });
      }
    };
  });
  
  app.directive('strToNum', function () {
    return {
      require: 'ngModel',
      link: function (scope, element, attrs, ngModel) {
        ngModel.$parsers.push(function (value) {
          return '' + value;
        });
        ngModel.$formatters.push(function (value) {
          return parseFloat(value, 10);
        });
      }
    }
  });
  
  app.directive('uploadImage', function () {
    return {
      restrict: 'A',
      replace: true,
      scope: {
        current: '=',
        comp: '=',
        index: '='
      },
      template: '<div class="change_image"><img ng-src="{{ current.imageurl || comp.dummy_dtls.img }}" class="appbannereditbrowse banner_img"><span ng-if="comp.elements.element_array.length > 1"><a href="" ng-click="removeElement()">Remove</a></span></div>',
      controller: ['$scope', '$timeout', function ($scope, $timeout) {
        $scope.removeElement = function () {
          $scope.comp.elements.element_array.splice($scope.index, 1);
          if ($scope.comp.comp_type === '111') {
            $timeout (function () {
              sliders.reloadSlider();
            });
          }
        };
      }],
      link: function (scope, elem, attrs) {
        elem.find('img').on('click', function () {
          
          var scopeNew = angular.element($('#cropper-example-2-modal')).scope();
          scopeNew.$apply(function(){
            scopeNew.showCropBtn = false;
          });

          $("a#openModalW").trigger("click");
          var $image = $('.img-container > img');

          $('#cropper-example-2-modal').one('shown.bs.modal', function () {
            $image.cropper('destroy').cropper({
              aspectRatio: scope.comp.dummy_dtls.width / scope.comp.dummy_dtls.height,
              data: {
                width: scope.comp.dummy_dtls.width,
                height: scope.comp.dummy_dtls.height
              },
              highlight: false,
              viewMode: 1
            });
          });
        });
      }
    };
  });

  app.factory('catalogueFactory', ['$http', '$q', '$httpParamSerializer', 'ApiMap', function ($http, $q, $httpParamSerializer, ApiMap) {

    var factoryAPI = {
      clientRequest: function (type, dtls) {
        var deferred = $q.defer(),
          config = {
            method: ApiMap[type].method || 'post',
            url: ApiMap[type].url,
            data: ApiMap[type].serial ? $httpParamSerializer(dtls) : dtls,
          };
        
        if (ApiMap[type].serial) {
          config.headers = {
            'Content-Type': 'application/x-www-form-urlencoded'
          };
        }

        $http(config).then(function (data) {
          deferred.resolve(data.data);
        }, function (error) {
          deferred.reject(error);
        });

        return deferred.promise;
      }
    };

    return factoryAPI;

  }]);

  app.controller('catalogueController', ['$scope', '$timeout', '$sce', '$compile', '$filter', 'catalogueFactory', function ($scope, $timeout, $sce, $compile, $filter, catalogueFactory) {
    
    $('#cropper-example-2-modal').on('hidden.bs.modal', function () {
      $(".modal-backdrop.in").css({
        'display': 'none',
        'opacity': '0'
      });
      $(".modal").css('display', 'none');
      $image.cropper('destroy');
    });
    
    $('.editPickerTitle').colpick({
      colorScheme: 'dark',
      layout: 'rgbhex',
      color: app_bg_color || 'theme_bg',
      submit: 0,
      onChange: function (hsb, hex, rgb, el, bySetColor) {
        $(el).css('background-color', '#' + hex);
        $(".theme_head").css("background-color", '#' + hex);
        $scope.appDtls.screen_properties.background_color = ('#' + hex);
		app_bg_color = ('#' + hex);
        $scope.$apply();
      }
    });
    
    $('.editPickerText').colpick({
      colorScheme: 'dark',
      layout: 'rgbhex',
      color: app_bg_color || 'theme_bg',
      submit: 0,
      onChange: function (hsb, hex, rgb, el, bySetColor) {
        $(el).css('background-color', '#' + hex);
        $scope.appDtls.screen_properties.font_color = ('#' + hex);
		text_color = ('#' + hex);
        $scope.$apply();
      }
    });
    
    $('.editPickerDiscount').colpick({
      colorScheme: 'dark',
      layout: 'rgbhex',
      color: app_bg_color || 'theme_bg',
      submit: 0,
      onChange: function (hsb, hex, rgb, el, bySetColor) {
        $(el).css('background-color', '#' + hex);
        $scope.appDtls.screen_properties.discount_color = ('#' + hex);
		discount_color = ('#' + hex);
        $scope.$apply();
      }
    });
	
	$('.editPickerLabelBg').colpick({
		colorScheme: 'dark',
		layout: 'rgbhex',
		color: app_bg_color || 'theme_bg',
		submit: 0,
		onChange: function (hsb, hex, rgb, el, bySetColor) {
			$scope.currentComp.label_bg = ('#' + hex);
			$scope.$apply();
		}
	});
    
    $scope.specialTags = [
      {
        id: '0',
        name: 'Select Tag'
      },
      {
        id: '1',
        name: 'Special Offers'
      },
      {
        id: '2',
        name: 'New in Store'
      },
      {
        id: '3',
        name: 'Pick of the Day'
      },
      {
        id: '4',
        name: 'Pick of the Week'
      },
      {
        id: '5',
        name: 'Trending Now'
      },
      {
        id: '6',
        name: 'Discounted Product'
      }
    ];
    
    var compMap = {
      '101': {
        'markup': '<section data-cid="101" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>Categories</p><ul class="black-card slider1"><li class="slide"> <img src="images-new/theme.png" alt=""><div><span>Category Name</span></div></li><li class="slide"> <img src="images-new/theme.png" alt=""><div><span>Category Name</span></div></li><li class="slide"> <img src="images-new/theme.png" alt=""><div><span>Category Name</span></div></li><li class="slide"> <img src="images-new/theme.png" alt=""><div><span>Category Name</span></div></li><li class="slide"> <img src="images-new/theme.png" alt=""><div><span>Category Name</span></div></li><li class="slide"> <img src="images-new/theme.png" alt=""><div><span>Category Name</span></div></li></ul><div class="common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="101" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>{{ comp.title.name }}</p><ul class="black-card slider1"><li class="slide" ng-repeat="elem in comp.elements.element_array"><img ng-src="{{ elem.imageurl || comp.dummy_dtls.img }}" alt=""><div ng-style="{ \'background-color\' : comp.label_bg }"><span>{{ elem.itemheading }}</span></div></li></ul><div class="common_input_overlay"></div></section>',
        'heading': 'Category Name',
        'subheading': 'Sub-heading',
        'dummyImg': 'images-new/theme.png',
        'width': 540,
        'height': 540,
        'elem_count': 6,
        'background-color': '#444',
        'title': {
          'name': 'Categories'
        }
      },
      '102': {
        'markup': '<section data-cid="102" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>Categories</p><ul class="white-card slider1"><li class="slide"> <img src="images-new/theme.png" alt=""><div><span>Category Name</span></div></li><li class="slide"> <img src="images-new/theme.png" alt=""><div><span>Category Name</span></div></li><li class="slide"> <img src="images-new/theme.png" alt=""><div><span>Category Name</span></div></li><li class="slide"> <img src="images-new/theme.png" alt=""><div><span>Category Name</span></div></li><li class="slide"> <img src="images-new/theme.png" alt=""><div><span>Category Name</span></div></li><li class="slide"> <img src="images-new/theme.png" alt=""><div><span>Category Name</span></div></li></ul><div class="common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="102" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>{{ comp.title.name }}</p><ul class="white-card slider1"><li class="slide" ng-repeat="elem in comp.elements.element_array"><img ng-src="{{ elem.imageurl || comp.dummy_dtls.img }}" alt=""><div ng-style="{ \'background-color\' : comp.label_bg }"><span>{{ elem.itemheading }}</span></div></li></ul><div class="common_input_overlay"></div></section>',
        'heading': 'Category Name',
        'subheading': 'Sub-heading',
        'dummyImg': 'images-new/theme.png',
        'width': 540,
        'height': 540,
        'elem_count': 4,
        'background-color': '#fff',
        'title': {
          'name': 'Categories'
        }
      },
      '103': {
        'markup': '<section data-cid="103" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>Categories</p><ul class="half-black-larg"><li > <img src="images-new/half-large.jpg" alt=""><div><span>Category Name</span></div></li><li > <img src="images-new/half-large.jpg" alt=""><div><span>Category Name</span></div></li><li > <img src="images-new/half-large.jpg" alt=""><div><span>Category Name</span></div></li><li > <img src="images-new/half-large.jpg" alt=""><div><span>Category Name</span></div></li></ul><div class="view-more"><a href="#">View more &raquo;</a></div><div class="common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="103" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>{{ comp.title.name }}</p><ul class="half-black-larg"><li ng-repeat="elem in comp.elements.element_array"><img ng-src="{{ elem.imageurl || comp.dummy_dtls.img }}" alt=""><div ng-style="{ \'background-color\' : comp.label_bg }"><span>{{ elem.itemheading }}</span></div></li></ul><div class="view-more"><a href="#">View more &raquo;</a></div><div class="common_input_overlay"></div></section>',
        'heading': 'Category Name',
        'subheading': 'Sub-heading',
        'dummyImg': 'images-new/half-large.jpg',
        'width': 540,
        'height': 540,
        'elem_count': 4,
        'background-color': '#444',
        'title': {
          'name': 'Categories'
        }
      },
      '104': {
        'markup': '<section data-cid="104" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>Special Tags</p><ul class="half-white-tag slider1"><li class="slide"> <span>Product</span> <img src="images-new/half-white-tag.jpg" alt=""><span>Price</span></li><li class="slide"><span>Product</span><img src="images-new/half-white-tag.jpg "alt=""><span>Price</span></li><li class="slide"><span>Product</span><img src="images-new/half-white-tag.jpg" alt=""><span>Price</span></li><li class="slide"><span>Product</span><img src="images-new/half-white-tag.jpg" alt=""><span>Price</span></li></ul><div class="common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="104" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>{{ comp.title.name }}</p><ul class="half-white-tag slider1"><li class="slide" ng-repeat="elem in comp.elements.element_array" ng-style="{ \'background-color\' : comp.label_bg }"><span>Product</span><img ng-src="{{ elem.imageurl || comp.dummy_dtls.img }}" alt=""><span>Price</span></li></ul><div class="common_input_overlay"></div></section>',
        'heading': 'Heading',
        'subheading': 'Sub-heading',
        'dummyImg': 'images-new/half-white-tag.jpg',
        'width': 540,
        'height': 540,
        'elem_count': 6,
        'title': {
          'name': 'Select Tag',
          'id': '0'
        }
      },
      '105': {
        'markup': '<section data-cid="105" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p> Tags</p><ul class="half-white-tag-nor"><li> <span>Product name</span> <img src="images-new/half-white-tag.jpg" alt=""><span>Price</span> </li><li><span>Product name</span><img src="images-new/half-white-tag.jpg" alt=""><span>Price</span></li><li><span>Product name</span><img src="images-new/half-white-tag.jpg" alt=""><span>Price</span></li><li><span>Product name</span> <img src="images-new/half-white-tag.jpg" alt=""><span>Price</span> </li><li> <span>Product name</span> <img src="images-new/half-white-tag.jpg" alt=""><span>Price</span> </li><li><span>Product name</span><img src="images-new/half-white-tag.jpg" alt=""><span>Price</span> </li></ul><div class="view-more"><a href="#">View more &raquo;</a></div><div class="common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="105" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>{{ comp.title.name }}</p><ul class="half-white-tag-nor"><li ng-repeat="elem in comp.elements.element_array" ng-style="{ \'background-color\' : comp.label_bg }"><span>{{ elem.itemheading }}</span><img ng-src="{{ elem.imageurl || comp.dummy_dtls.img }}" alt=""><span>{{ elem.actualprice ? (elem.symbol_left ? (elem.symbol_left + elem.special_price || elem.symbol_left + elem.actualprice) : (elem.special_price + elem.symbol_right || elem.actualprice + elem.symbol_right)) : "Price" }}</span></li></ul><div class="view-more"><a href="#">View more &raquo;</a></div><div class="common_input_overlay"></div></section>',
        'heading': 'Product name',
        'subheading': 'Sub-heading',
        'dummyImg': 'images-new/half-white-tag.jpg',
        'width': 540,
        'height': 540,
        'elem_count': 6,
        'background-color': '#fff',
        'title': {'background-color': '#fff',
          'name': 'Select Tag',
          'id': '0'
        }
      },
      '106': {
        'markup': '<section data-cid="106" class="cat-border2" data-ss-colspan="2"><p class="widgetClose">x</p><ul class="full-banner"><li><img src="images-new/full-banner.jpg" alt=""></li></ul><div class="common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="106" ng-click="fnEditComponent($event)" class="cat-border2" data-ss-colspan="2"><p class="widgetClose">x</p><ul class="full-banner"><li><img ng-src="{{ comp.elements.element_array[0].imageurl || comp.dummy_dtls.img }}" alt=""></li></ul><div class="common_input_overlay"></div></section>',
        'heading': '',
        'subheading': '',
        'dummyImg': 'images-new/full-banner.jpg',
        'width': 1080,
        'height': 360,
        'elem_count': 1,
        'background-color': ' ',
        'title': {
          'name': ''
        }
      },
      '107': {
        'markup': '<section data-cid="107" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><ul class="half-white-four clearfix"><li><img src="images-new/half-white-four.jpg" alt=""><div class="heading-area"><strong>Heading</strong><span> Sub-heading </span></div></li><li><img src="images-new/half-white-four.jpg" alt=""><div class="heading-area"><strong>Heading</strong><span> Sub-heading </span></div></li></ul><div class="common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="107" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><ul class="half-white-four clearfix"><li ng-repeat="elem in comp.elements.element_array"><img ng-src="{{ elem.imageurl || comp.dummy_dtls.img }}" alt=""><div class="heading-area" ng-style="{ \'background-color\' : comp.label_bg }"><strong>{{ elem.itemheading }}</strong><span>{{ elem.itemdesc }}</span></div></li></ul><div class="common_input_overlay"></div></section>',
        'heading': 'Heading',
        'subheading': 'Sub-heading',
        'dummyImg': 'images-new/half-white-four.jpg',
        'width': 540,
        'height': 540,
        'elem_count': 2,
        'background-color': '#fff',
        'title': {
          'name': ''
        }
      },
      '108': {
        'markup': '<section data-cid="108" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><ul class="half-white-four clearfix"><li><img src="images-new/half-white-four.jpg" alt=""><div class="heading-area"><strong>Heading</strong><span> Sub-heading </span></div></li><li><img src="images-new/half-white-four.jpg" alt=""><div class="heading-area"><strong>Heading</strong><span> Sub-heading </span></div></li><li><img src="images-new/half-white-four.jpg" alt=""><div class="heading-area"><strong>Heading</strong><span> Sub-heading </span></div></li><li><img src="images-new/half-white-four.jpg" alt=""><div class="heading-area"><strong>Heading</strong><span> Sub-heading </span></div></li></ul><div class="common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="108" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><ul class="half-white-four clearfix"><li ng-repeat="elem in comp.elements.element_array"><img ng-src="{{ elem.imageurl || comp.dummy_dtls.img }}" alt=""><div class="heading-area" ng-style="{ \'background-color\' : comp.label_bg }"><strong>{{ elem.itemheading }}</strong><span>{{ elem.itemdesc }}</span></div></li></ul><div class="common_input_overlay"></div></section>',
        'heading': 'Heading',
        'subheading': 'Sub-heading',
        'dummyImg': 'images-new/half-white-four.jpg',
        'width': 540,
        'height': 540,
        'elem_count': 4,
        'background-color': '#fff',
        'title': {
          'name': ''
        }
      },
      '109': {
        'markup': '<section data-cid="109" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>Products</p><ul class="half-white-products-slide slider1 clearfix"><li class="slide"><img src="images-new/half-white-products.jpg" alt=""><div><span>Text</span><strong>Price</strong></div></li><li class="slide"><img src="images-new/half-white-products.jpg" alt=""><div><span>Text</span><strong>Price</strong> </div></li><li class="slide"><img src="images-new/half-white-products.jpg" alt=""><div><span>Text</span><strong>Price</strong></div></li><li class="slide"><img src="images-new/half-white-products.jpg" alt=""><div><span>Text</span><strong>Price</strong></div></li><li class="slide"><img src="images-new/half-white-products.jpg" alt=""><div><span>Text</span><strong>Price</strong></div></li><li class="slide"><img src="images-new/half-white-products.jpg" alt=""><div><span>Text</span>Price <strong>Price</strong></div></li></ul><div class="common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="109" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>{{ comp.title.name }}</p><ul class="half-white-products-slide slider1 clearfix"><li class="slide" ng-repeat="elem in comp.elements.element_array" ng-style="{ \'background-color\' : comp.label_bg }"><img ng-src="{{ elem.imageurl || comp.dummy_dtls.img }}" alt=""><div><span>{{ elem.itemheading || "Text" }}</span><strong>{{ elem.actualprice ? (elem.symbol_left ? (elem.symbol_left + elem.special_price || elem.symbol_left + elem.actualprice) : (elem.special_price + elem.symbol_right || elem.actualprice + elem.symbol_right)) : "Price" }}</strong></div></li></ul><div class="common_input_overlay"></div></section>',
        'heading': 'Text',
        'subheading': 'Sub-heading',
        'dummyImg': 'images-new/half-white-products.jpg',
        'width': 540,
        'height': 540,
        'elem_count': 6,
        'background-color': '#fff',
        'title': {
          'name': 'Products'
        }
      },
      '110': {
        'markup': '<section data-cid="110" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>Products</p><ul class="half-white-products clearfix"><li> <img src="images-new/half-white-products.jpg" alt=""><div><span>Text</span><strong>Price</strong></div></li><li> <img src="images-new/half-white-products.jpg" alt=""><div><span>Text</span><strong>Price</strong></div></li><li> <img src="images-new/half-white-products.jpg" alt=""><div><span>Text</span><strong>Price</strong></div></li><li> <img src="images-new/half-white-products.jpg" alt=""><div><span>Text</span><strong>Price</strong></div></li><li> <img src="images-new/half-white-products.jpg" alt=""><div><span>Text</span><strong>Price</strong></div></li><li> <img src="images-new/half-white-products.jpg" alt=""><div><span>Text</span><strong>Price</strong></div></li></ul><div class="view-more"><a href="#">View more &raquo;</a></div><div class="common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="110" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>{{ comp.title.name }}</p><ul class="half-white-products clearfix"><li ng-repeat="elem in comp.elements.element_array" ng-style="{ \'background-color\' : comp.label_bg }"><img ng-src="{{ elem.imageurl || comp.dummy_dtls.img }}" alt=""><div><span>{{ elem.itemheading || "Text" }}</span><strong>{{ elem.actualprice ? (elem.symbol_left ? (elem.symbol_left + elem.special_price || elem.symbol_left + elem.actualprice) : (elem.special_price + elem.symbol_right || elem.actualprice + elem.symbol_right)) : "Price" }}</strong></div></li></ul><div class="view-more"><a href="#">View more &raquo;</a></div><div class="common_input_overlay"></div></section>',
        'heading': 'Text',
        'subheading': 'Sub-heading',
        'dummyImg': 'images-new/half-white-products.jpg',
        'width': 540,
        'height': 540,
        'elem_count': 6,
        'background-color': '#fff',
        'title': {
          'name': 'Products'
        }
      },
      '111': {
        'markup': '<section data-cid="111" class="cat-border2" data-ss-colspan="2"><p class="widgetClose">x</p><ul class="full-large-banner bxslider"><li><img alt="" src="images-new/full-large-slider.jpg" class=""></li><li><img alt="" src="images-new/full-large-slider.jpg" class=""></li><li><img alt="" src="images-new/full-large-slider.jpg" class=""></li></ul><div class="common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="111" ng-click="fnEditComponent($event)" class="cat-border2" data-ss-colspan="2"><p class="widgetClose">x</p><ul class="full-large-banner bxslider"><li ng-repeat="elem in comp.elements.element_array"><img alt="" ng-src="{{ elem.imageurl || comp.dummy_dtls.img }}"></li></ul><div class="common_input_overlay"></div></section>',
        'heading': '',
        'subheading': '',
        'dummyImg': 'images-new/full-large-slider.jpg',
        'width': 1080,
        'height': 720,
        'elem_count': 3,
        'background-color': ' ',
        'title': {
          'name': ''
        }
      },
      '112': {
        'markup': '<section data-cid="112" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>Tags</p><ul class="half-white-tag slider1"><li class="slide"> <span>Discount</span> <img src="images-new/half-white-tag.jpg" alt=""><span>Price</span></li><li class="slide"> <span>Discount</span><img src="images-new/half-white-tag.jpg" alt=""><span>Price</span></li><li class="slide"> <span>Discount</span><img src="images-new/half-white-tag.jpg" alt=""><span>Price</span></li><li class="slide"> <span>Discount</span><img src="images-new/half-white-tag.jpg" alt=""><span>Price</span></li></ul><div class="common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="112" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>{{ comp.title.name }}</p><ul class="half-white-tag slider1"><li class="slide" ng-repeat="elem in comp.elements.element_array" ng-style="{ \'background-color\' : comp.label_bg }"><span>{{ elem.discount || "Discount" }}</span><img ng-src="{{ elem.imageurl || comp.dummy_dtls.img }}" alt=""><span>{{ elem.actualprice ? (elem.symbol_left ? (elem.symbol_left + elem.special_price || elem.symbol_left + elem.actualprice) : (elem.special_price + elem.symbol_right || elem.actualprice + elem.symbol_right)) : "Price" }}</span></li></ul><div class="common_input_overlay"></div></section>',
        'heading': 'Heading',
        'subheading': 'Sub-heading',
        'dummyImg': 'images-new/half-white-tag.jpg',
        'width': 540,
        'height': 540,
        'elem_count': 6,
        'background-color': '#fff',
        'title': {
          'name': 'Select Tag',
          'id': '0'
        }
      },
      '113': {
        'markup': '<section data-cid="113" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>Tags</p><ul class="half-white-tag-nor"><li> <span>Discount</span> <img src="images-new/half-white-tag.jpg" alt=""><span>Price</span></li><li><span>Discount</span><img src="images-new/half-white-tag.jpg" alt=""><span>Price</span></li><li> <span>Discount</span><img src="images-new/half-white-tag.jpg" alt=""><span>Price</span></li><li><span>Discount</span><img src="images-new/half-white-tag.jpg" alt=""><span>Price</span></li><li> <span>Discount</span><img src="images-new/half-white-tag.jpg" alt=""><span>Price</span></li><li><span>Discount</span><img src="images-new/half-white-tag.jpg" alt=""><span>Price</span></li></ul><div class="view-more"><a href="#">View more &raquo;</a></div><div class="common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="113" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>{{ comp.title.name }}</p><ul class="half-white-tag-nor"><li ng-repeat="elem in comp.elements.element_array" ng-style="{ \'background-color\' : comp.label_bg }"><span>{{ elem.discount || "Discount" }}</span><img ng-src="{{ elem.imageurl || comp.dummy_dtls.img }}" alt=""><span>{{ elem.actualprice ? (elem.symbol_left ? (elem.symbol_left + elem.special_price || elem.symbol_left + elem.actualprice) : (elem.special_price + elem.symbol_right || elem.actualprice + elem.symbol_right)) : "Price" }}</span></li></ul><div class="view-more"><a href="#">View more &raquo;</a></div><div class="common_input_overlay"></div></section>',
        'heading': 'Heading',
        'subheading': 'Sub-heading',
        'dummyImg': 'images-new/half-white-tag.jpg',
        'width': 540,
        'height': 540,
        'elem_count': 6,
        'background-color': '#fff',
        'title': {
          'name': 'Select Tag',
          'id': '0'
        }
      },
       '114': {
        'markup': '<section data-cid="114" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>Tags</p><ul class="half-white-larg-four"><li><span>Discount</span><img src="images-new/half-large.jpg" alt=""><span>Price</span></li><li><span>Discount</span><img src="images-new/half-large.jpg" alt=""><span>Price</span></li><li><span>Discount</span><img src="images-new/half-large.jpg" alt=""><span>Price</span></li><li><span>Discount</span><img src="images-new/half-large.jpg" alt=""><span>Price</span></li></ul><div class="view-more"><a href="#">View more »</a></div><div class="common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="114" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>{{ comp.title.name }}</p><ul class="half-white-larg-four"><li ng-repeat="elem in comp.elements.element_array" ng-style="{ \'background-color\' : comp.label_bg }"><span>{{ elem.discount || "Discount" }}</span><img ng-src="{{ elem.imageurl || comp.dummy_dtls.img }}" alt=""><span>{{ elem.actualprice ? (elem.symbol_left ? (elem.symbol_left + elem.special_price || elem.symbol_left + elem.actualprice) : (elem.special_price + elem.symbol_right || elem.actualprice + elem.symbol_right)) : "Price" }}</span></li></ul><div class="view-more"><a href="#">View more »</a></div><div class="common_input_overlay"></div></section>',
        'heading': 'Heading',
        'subheading': 'Sub-heading',
        'dummyImg': 'images-new/half-large.jpg',
        'width': 540,
        'height': 540,
        'elem_count': 4,
         'background-color': '#fff',
        'title': {
          'name': 'Select Tag',
          'id': '0'
        }
      },
       '115': {
        'markup': '<section data-cid="115" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>Tags</p><ul class="half-white-larg-four slider2"><li class="slide"><span>Discount</span><img src="images-new/half-large.jpg" alt="" /><span>Price</span></li><li class="slide"><span>Discount</span><img src="images-new/half-large.jpg" alt="" /><span>Price</span></li><li class="slide"><span>Discount</span><img src="images-new/half-large.jpg" alt="" /><span>Price</span></li><li class="slide"><span>Discount</span><img src="images-new/half-large.jpg" alt="" /><span>Price</span></li></ul><div class="common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="115" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>{{ comp.title.name }}</p><ul class="half-white-larg-four slider2"><li class="slide" ng-repeat="elem in comp.elements.element_array" ng-style="{ \'background-color\' : comp.label_bg }"><span>{{ elem.discount || "Discount" }}</span><img ng-src="{{ elem.imageurl || comp.dummy_dtls.img }}" alt="" /><span>{{ elem.actualprice ? (elem.symbol_left ? (elem.symbol_left + elem.special_price || elem.symbol_left + elem.actualprice) : (elem.special_price + elem.symbol_right || elem.actualprice + elem.symbol_right)) : "Price" }}</span></li></ul><div class="common_input_overlay"></div></section>',
        'heading': 'Heading',
        'subheading': 'Sub-heading',
        'dummyImg': 'images-new/half-large.jpg',
        'width': 540,
        'height': 540,
        'elem_count': 4,
        'background-color': '#fff',
        'title': {
          'name': 'Select Tag',
          'id': '0'
        }
      },
      '116': {
        'markup': '<section data-cid="116" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>Products</p><ul class="full-white-product-cart clearfix"><li><img src="images-new/product-img.jpg" alt=""><div class="icon-text-container"><div class="text-container"><span>Text</span><strong>Price</strong></div><div class="icon-container"><img src="images-new/cart-icon.png" ></div></div></li></ul><div class=common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="116" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><ul class="full-white-product-cart clearfix"><li ng-style="{ \'background-color\' : comp.label_bg }" ng-repeat="elem in comp.elements.element_array"><div class="heart_icon" ng-if="appDtls.app_type == 3"><i class="fa fa-heart"></i></div><img ng-src="{{ elem.itemprod.imageurl || comp.dummy_dtls.img }}" alt="" /><div class="icon-text-container"><div class="text-container"><span>{{ elem.itemprod.itemheading || "Text" }}</span><strong>{{ elem.itemprod.actualprice ? (elem.itemprod.symbol_left ? (elem.itemprod.symbol_left + elem.itemprod.price || elem.itemprod.symbol_left + elem.itemprod.actualprice) : (elem.itemprod.price + elem.itemprod.symbol_right || elem.itemprod.actualprice + elem.itemprod.symbol_right)) : "Price" }}</strong></div><div class="icon-container"><img ng-if="appDtls.app_type == 2" ng-src="images-new/cart-icon.png" alt="" /></div></div></li></ul><div class="common_input_overlay"></div><section> ',
          'heading': ' ',
          'subheading': ' ',
          'dummyImg': 'images-new/product-img.jpg',
          'width': 1080,
          'height': 360,
          'elem_count': 1,
          'background-color': '#fff',
          'title': {
            'name': '',
          'id': '0'
        }
      },
      '117': {
        'markup': '<section data-cid="117" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><p>Categories</p><ul class="full-white-product-cart clearfix"><li><img src="images-new/product-img.jpg" alt=""><div class="icon-text-container"><div class="text-container2"><strong>Category</strong></div></div></li></ul><div class=common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="117" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><ul class="full-white-product-cart clearfix"><li ng-style="{ \'background-color\' : comp.label_bg }" ng-repeat="elem in comp.elements.element_array"><img ng-src="{{ elem.imageurl || comp.dummy_dtls.img }}" alt="" /><div class="icon-text-container"><div class="text-container2"><strong>{{ elem.itemheading || \"Category\" }}</strong></div></div></li></ul><div class="common_input_overlay"></div><section> ',
          'heading': 'Category',
          'subheading': ' ',
          'dummyImg': 'images-new/product-img.jpg',
          'width': 1080,
          'height': 360,
          'elem_count': 1,
          'background-color': '#fff',
          'title': {
            'name': '',
            'id': '0'
          }
        },
       '118': {
        'markup': '<section data-cid="118" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><div class="all-snap-category"> <span>Category</span><button>View All</button> </div><div class="snap-list-box"> <ul class="snap-list"> <li><div class="snap-img"><img src="http://www.instappy.com/images/category-img_118.png" alt=""></div><div class="snap-area"> <h5 class="snap-heading">Category Name</h5><p class="snap-detail">Short Description</p></div><div class="snap-arrow"><i class="fa fa-angle-right fa-lg" aria-hidden="true"></i></div></li><li><div class="snap-img"><img src="http://www.instappy.com/images/category-img_118.png" alt=""></div><div class="snap-area"> <h5 class="snap-heading">Category Name</h5><p class="snap-detail">Short Description</p></div><div class="snap-arrow"><i class="fa fa-angle-right fa-lg" aria-hidden="true"></i></div></li><li><div class="snap-img"><img src="http://www.instappy.com/images/category-img_118.png" alt=""></div><div class="snap-area"> <h5 class="snap-heading">Category Name</h5><p class="snap-detail">Short Description</p></div><div class="snap-arrow"><i class="fa fa-angle-right fa-lg" aria-hidden="true"></i></div></li><li><div class="snap-img"><img src="http://www.instappy.com/images/category-img_118.png" alt=""></div><div class="snap-area"> <h5 class="snap-heading">Category Name</h5><p class="snap-detail">Short Description</p></div><div class="snap-arrow"><i class="fa fa-angle-right fa-lg" aria-hidden="true"></i></div></li></ul></div><div class=common_input_overlay"></div></section>',
        'ng-markup': '<section data-cid="118" ng-click="fnEditComponent($event)" class="cat-border" data-ss-colspan="2"><p class="widgetClose">x</p><div class="all-snap-category"> <span>Category</span><button>View All</button> </div><div class="snap-list-box"> <ul class="snap-list" ng-repeat="elem in comp.snap_sim_data.element_array" ng-show="comp.snap_sim_data.element_count > 0"><li><div class="snap-img"><img src="{{ elem.imageurl }}" alt="{{ elem.itemheading }}"></div><div class="snap-area"> <h5 class="snap-heading">{{ elem.itemheading || "Category Name"}}</h5><p class="snap-detail">{{ elem.itemdesc || "Short Description"}}</p></div><div class="snap-arrow"><i class="fa fa-angle-right fa-lg" aria-hidden="true"></i></div></li></ul><ul class="snap-list" ng-hide="comp.snap_sim_data.element_count > 0"><li><div class="snap-img"><img src="http://www.instappy.com/images/category-img_118.png" alt=""></div><div class="snap-area"> <h5 class="snap-heading">Category Name</h5><p class="snap-detail">Short Description</p></div><div class="snap-arrow"><i class="fa fa-angle-right fa-lg" aria-hidden="true"></i></div></li><li><div class="snap-img"><img src="http://www.instappy.com/images/category-img_118.png" alt=""></div><div class="snap-area"> <h5 class="snap-heading">Category Name</h5><p class="snap-detail">Short Description</p></div><div class="snap-arrow"><i class="fa fa-angle-right fa-lg" aria-hidden="true"></i></div></li><li><div class="snap-img"><img src="http://www.instappy.com/images/category-img_118.png" alt=""></div><div class="snap-area"> <h5 class="snap-heading">Category Name</h5><p class="snap-detail">Short Description</p></div><div class="snap-arrow"><i class="fa fa-angle-right fa-lg" aria-hidden="true"></i></div></li><li><div class="snap-img"><img src="http://www.instappy.com/images/category-img_118.png" alt=""></div><div class="snap-area"> <h5 class="snap-heading">Category Name</h5><p class="snap-detail">Short Description</p></div><div class="snap-arrow"><i class="fa fa-angle-right fa-lg" aria-hidden="true"></i></div></li></ul></div><div class="common_input_overlay"></div><section> ',
          'heading': 'Category Name',
          'subheading': 'Short Description',
          'dummyImg': 'images-new/product-img.jpg',
          'width': 1080,
          'height': 360,
          'elem_count': 1,
          'background-color': '#fff',
          'title': {
            'name': '',
            'id': '0'
          }
        }   
    };
    
    var scrollBlockMap = {
      '-1': {
        selector: '.name-your-app',
        cb: function () {
          $scope.compsVisible = 'hidden';
        }
      },
      0: {
        selector: '.name-your-app'
      },
      1: {
        selector: '.basic-details'
      },
      2: {
        selector: '.add-widgets',
        cb: function () {
          $scope.$broadcast('compReady', $scope.componentsHtml);
        }
      },
      3: {
        selector: '.update-card-dtls'
      },
      4: {
        selector: '.additional-features'
      }
    };
    
    $scope.$watch('currentBlock', function (val) {
      if (val) {
        $timeout(function () {
          if (scrollBlockMap[$scope.currentBlock].cb) {
            scrollBlockMap[$scope.currentBlock].cb();
          }
          if (scrollBlockMap[$scope.currentBlock].selector) {
            $("#content-2").mCustomScrollbar('stop').mCustomScrollbar('scrollTo', scrollBlockMap[$scope.currentBlock].selector);
          }
          if ($(window).scrollTop()) {
            $('html, body').animate({ scrollTop: 0, duration: 200 });
          }
        });
      }
    });
    
    var errCodeMap = {
      '500': {
        msg: 'Oops! Something went wrong. Please try again after sometime.'
      },
      '501': {
        msg: 'Login To Continue.',
        cb: function () {
          $(".popup_container").show();
          $(".login_popup").show();
        }
      },
      '502': {
        msg: 'Please Select App Name.',
        scroll: '.name-your-app'
      },
      'app-name': {
        msg: 'Please Select App Name.',
        scroll: '.name-your-app'
      },
      'app-type': {
        msg: 'Please Select App Type.',
        scroll: '.name-your-app'
      },
      'app-currency': {
        msg: 'Please Select Currency.',
        scroll: '.name-your-app'
      },
      'no-image': {
        msg: 'No Image Found.'
      },
      'app-name-exists': {
        msg: 'App Name Already Exists.'
      },
      'reseller-check': {
        msg: 'You can not create app.Please contact to your reseller.'
      },
      'products-only': {
        msg: 'Please Select Product.',
        scroll: '.other_than_banner'
      },
      'category-only': {
        msg: 'Please Select Category.',
        scroll: '.other_than_banner'
      },
      'add-feedback-email': {
        msg: 'Please add feedback email.',
        scroll: '.is_feedback'
      },
      'add-valid-feedback-email': {
        msg: 'Please add valid email.',
        scroll: '.is_feedback'
      },
      'add-contact-email': {
        msg: 'Please add contact email.',
        scroll: '.is_contactus'
      },
      'add-tnc-link': {
        msg: 'Please add terms & condition link.',
        scroll: '.is_tnc'
      },
      'add-order-logo': {
        msg: 'Please upload company logo.',
        scroll: '.is_order'
      },
      'add-order-package': {
        msg: 'Please select order package.',
        scroll: '.is_order'
      },
      'add-order-email': {
        msg: 'Please add order email.',
        scroll: '.is_order'
      },
      'no-small-image': {
        msg: 'Please add order email.'
      }
    };
    
    $scope.popupDtls = {
      msg: '',
      code: '',
      show: false
    };
    
    $scope.showPopup = function (code) {
      if (!errCodeMap[code].cb) {        
        $scope.popupDtls.msg = errCodeMap[code].msg;
        $scope.popupDtls.code = code;
        $scope.popupDtls.show = true;
      }
      else {
        errCodeMap[code].cb();
      }
      if (errCodeMap[code].scroll) {
        $timeout(function () {
          $('html, body').animate({ scrollTop: 0, duration: 200 });
          $("#content-2").mCustomScrollbar('scrollTo', errCodeMap[code].scroll);
        });
      }
    };

    function hitApi(type, dtls, callback) {

      catalogueFactory.clientRequest(type, dtls).then(function (data) {
        callback(data);
      }, function (error) {
        $scope.dataLoaded = true;
        $scope.showPopup('500');
        console.log('Error', error);
      });
    }

    hitApi('getComponents', {}, function (data) {
      
      if (data.response) {
        var strComponents = '';
        angular.forEach(data.response.data, function (comp, i) {
          strComponents += compMap[comp.id].markup;
        });
        
        $scope.componentsHtml = strComponents;
        
        $timeout(function () {
          $("#content-2").mCustomScrollbar('destroy');
          $("#content-2").mCustomScrollbar({
            autoHideScrollbar: true,
            scrollInertia: 400,
            callbacks: {
              onScrollStart: function () {
                $('.colpick').hide();
              }
            }
          });
        });
      }

    });
    
    var getAppData, resetDataStore, resetCurrent;
    
    (getAppData = function () {
      
      var url, postObj;
      
      if (gl_app_id) {
        url = 'getAppData';
        postObj = {
          app_id: gl_app_id
        };
      }
      else if (gl_theme_id) {
        url = 'currentTheme';
        postObj = {
          theme_id: gl_theme_id
        };
      }
      else {
//        console.log('Paramters missing! Error while getting app/theme data.');
        return false;
      }
      
      hitApi(url, postObj, function (data) {
        
        var appDtlsHtml = '';
        $('.container.droparea').html('');
        $scope.screenVisible = 'hidden';

        angular.forEach(data.screen_data.comp_array, function (comp, i) {
          var ts = new Date().getTime().toString() + '_' + i;
          comp.id = ts;
          comp.comp_html = compMap[comp.comp_type]['ng-markup'] || compMap[comp.comp_type].markup;
          comp.comp_html = $(comp.comp_html).attr('data-uid', ts)[0].outerHTML;
          comp.dummy_dtls = {
            img: compMap[comp.comp_type].dummyImg,
            width: compMap[comp.comp_type].width,
            height: compMap[comp.comp_type].height
          };
          
          if (!comp.title) {
            comp.title = angular.copy(compMap[comp.comp_type].title);
          }
          if (!comp.elements || !comp.elements.element_array) {
            comp.elements = {
              element_array: []
            };
          }
          
		  if(comp.elements.element_array.length == 0)
		  {
          if (comp.elements.element_array.length < compMap[comp.comp_type].elem_count) {
            var len = compMap[comp.comp_type].elem_count - comp.elements.element_array.length;
            for (var i = 0; i < len; i++) {
              comp.elements.element_array.push({
                itemheading: compMap[comp.comp_type].heading,
                itemdesc: compMap[comp.comp_type].subheading,
                imageurl: compMap[comp.comp_type].dummyImg,
                image_height: '',
                image_width: '',
                itemid: '',
                imagename: compMap[comp.comp_type].dummyImg
              });
            }
          }
		  }
          if (!comp.comp_row_id && comp.comp_row_id !== 0) {
            comp.comp_row_id = 'new';
          }
          
          if(!comp.label_bg){
            comp.label_bg = compMap[comp.comp_type]["background-color"]
            
          }
          
          appDtlsHtml += comp.comp_html;
          
          var newScope = $scope.$new();
          newScope.comp = comp;
          
          $('.container.droparea').append($compile(comp.comp_html)(newScope));
        });

        $timeout (function () {
          $scope.$broadcast('screenReady', appDtlsHtml);
        });

        $scope.appDtls = data.screen_data;

        if (!$scope.appDtls.screen_properties) {
          $scope.appDtls.screen_properties = {
            background_color: app_bg_color,
            font_color: text_color,
            discount_color: discount_color
          };
        }
        
        $scope.appDtls.screen_properties.theme_id = gl_theme_id;
        $scope.appDtls.screen_properties.cat_id = gl_cat_id;
        
        $scope.fetchCumulativeCats();
        
        if (!$scope.appDtls.logo_dtls) {
          $scope.appDtls.logo_dtls = {};
        }
        $scope.appDtls.logo_dtls.dummy_dtls = {
          'img': 'images/catalogue_img_upload.png',
          'width': 500,
          'height': 500
        };

        $('.editPickerTitle').css('background', $scope.appDtls.screen_properties.background_color);
        $('.editPickerText').css('background', $scope.appDtls.screen_properties.font_color);
        $('.editPickerDiscount').css('background', $scope.appDtls.screen_properties.discount_color);
        
		if(typeof $scope.nameYourAppForm != 'undefined')
		{
			$scope.nameYourAppForm.$setPristine();
		}
      });
      
    })();
    
    $scope.$on('updateSimulator', function (e) {
      var element = $('.container.droparea');
      element.contents().remove();
      if (sliders) {
        try {
          sliders.destroySlider();
        }
        catch (e) {
          // Do Nothing
        }
      }
      
      angular.forEach($scope.appDtls.comp_array, function (comp, i) {
        var newScope = $scope.$new();
        newScope.comp = comp;
        element.append($compile(comp.comp_html)(newScope));
      });
      
      $timeout(function () {
        element.find('.slider1').bxSlider({
          slideWidth: 84,
          minSlides: 3,
          maxSlides: 3,
          infiniteLoop: false,
          hideControlOnEnd: true,
          pager: false,
          slideMargin: 2
        });
        element.find('.slider2').bxSlider({
          slideWidth: 130,
          minSlides: 2,
          maxSlides: 2,
          infiniteLoop: false,
          hideControlOnEnd: true,
          pager: false,
          slideMargin: 2
        });
          
        if (element.find('.bxslider').length) {
          sliders = element.find('.bxslider').bxSlider({
            auto: true,
            pause: 2000,
            onSliderLoad: function () {
              element.shapeshift({
                colWidth: 139.5,
                minColumns: 2,
                enableDrag: true
              });

              $scope.screenVisible = 'visible';
              $scope.dataLoaded = true;
            }
          });
        }
        else {
          $timeout(function () {
            element.shapeshift({
              colWidth: 139.5,
              minColumns: 2,
              enableDrag: true
            });

            $scope.screenVisible = 'visible';
            $scope.dataLoaded = true;
          }, 200);
        }
      });
    });
    
    (resetDataStore = function () {
      $scope.dataStore = {
        cats: [],
        subcats: [],
        loaded: false
      };
    })();
    
    (resetCurrent = function () {
      $scope.current = {
        cat: '',
        subcat: []
      };
    })();
    
//    var compWithCatsOnly = ['101', '102', '103'];
    
    $scope.fetchCumulativeCats = function () {
      
      if ($scope.appDtls.screen_properties.app_id) {
        hitApi('getCumulativeCats', {
          app_id: $scope.appDtls.screen_properties.app_id
        }, function (data) {

          $scope.cumulativeCats = data.main_category_array;

        });
      }
      else {
        $scope.cumulativeCats = [];
      }
    };
    
    $scope.fetchProds = function (elem) {
      
      elem.prods = [];
      
      if (elem.itemcat && elem.itemcat.itemid) {
        elem.loaded = false;
        hitApi('getProducts', {
          app_id: $scope.appDtls.screen_properties.app_id,
          category_id: elem.itemcat.itemid
        }, function (data) {

          elem.prods = data.products_array;
          elem.loaded = true;

        });
      }
      else {
        elem.itemprod = {};
      }
    };
    
    $scope.reOrderElems = function (action, elem, index, container) {
      if (action === 'add') {
        $scope.dataLoaded = false;
        $scope.screenVisible = 'hidden';
        
        if ($(container).children('section').length === ($scope.appDtls.comp_array.length + 1)) {
          var curr_type = $(elem).attr('data-cid'),
              ts = new Date().getTime().toString();
          $(elem).attr('data-uid', ts);
          $scope.appDtls.comp_array.splice(index, 0, {
            id: ts,
            comp_type: curr_type,
            title: angular.copy(compMap[curr_type].title),
            comp_html: $(compMap[curr_type]['ng-markup'] || compMap[curr_type].markup).attr('data-uid', ts)[0].outerHTML,
            comp_id: '',
            comp_row_id: 'new',
            comp_properties: {},
            dummy_dtls: {
              img: compMap[curr_type].dummyImg,
              width: compMap[curr_type].width,
              height: compMap[curr_type].height
            },
            elements: {
              element_array: []
            }
          });
          for (var i = 0; i < compMap[curr_type].elem_count; i++) {
            $scope.appDtls.comp_array[index].elements.element_array.push({
              itemheading: compMap[curr_type].heading,
              itemdesc: compMap[curr_type].subheading,
              imageurl: compMap[curr_type].dummyImg,
              image_height: '',
              image_width: '',
              itemid: '',
              imagename: compMap[curr_type].dummyImg
            });
          }
        }
        
        $scope.resetCurrentComp();
        $timeout(function () {
          $scope.$broadcast('updateSimulator');
        });
	  
		// fix for multiple dots
		if(typeof sliders != 'undefined')
		{
			sliders.reloadShow(); 
			//sliders.reloadSlider();
			/* $('.clones').trigger("ss-destroy");
			$('.clones').shapeshift({
			dragClone: true,
			enableCrossDrop: false,
			colWidth: 139.5,
			minColumns: 2,
			enableTrash: true
			}); */
		}
      }
      else if (action === 'arrange') {
        var elemId = $(elem).attr('data-uid');
        for (var k = 0; k < $scope.appDtls.comp_array.length; k++) {
          if ($scope.appDtls.comp_array[k].id === elemId) {
            var oldIndex = k;
            break;
          }
        }
        if (oldIndex >= 0) {
          if (oldIndex !== index) {
            $scope.appDtls.comp_array.splice(index, 0, $scope.appDtls.comp_array.splice(oldIndex, 1)[0]);
          }
        }
        else {
//          console.log('Error while rearranging elements!');
        }
      }
    };
    
    ($scope.resetCurrentComp = function () {
      $scope.currentComp = {
        id: '',
        comp_type: '',
        title: '',
        comp_html: '',
        comp_id: '',
        comp_added: '',
        comp_properties: {},
        dummy_dtls: {
          img: '',
          width: '',
          height: ''
        },
        elements: {
          element_array: [{
            itemheading: '',
            itemdesc: '',
            imageurl: '',
            image_height: '',
            image_width: '',
            itemid: '',
            imagename: ''
          }]
        }
      };
    })();
    
    var fieldsMap = {
		/*
		'heading': ['101', '102', '103', '109', '110'],
		'tag': ['104', '105', '112', '113'],
		'subHeading': [],
		'catProdSelect': ['101', '102', '103', '109', '110'],
		'imageUpload': [],
		'imgHeadSubhead': ['106', '107', '108', '111'],
		'noHeadSubhead': ['106', '111'],
		'compWithCatsOnly': ['101', '102', '103'],
		'labelColpick': ['116'],
		'onlyCatWithProdDropdwn' : ['116']
		*/
		'heading': ['101', '102', '103', '109', '110'],
		'tag': ['104', '105', '112', '113', '114', '115'],
		'subHeading': [],
		'catProdSelect': ['101', '102', '103', '109', '110'],
		'imageUpload': [],
		'imgHeadSubhead': ['106', '107', '108', '111'],
		'noHeadSubhead': ['106', '111'],
		'compWithCatsOnly': ['101', '102', '103', '117', '118'],
		//'labelColpick': ['101', '102', '103', '104', '105', '107', '108', '109', '110', '112', '113', '114', '115'],
		'labelColpick': [],
		'onlyCatWithProdDropdwn' : ['116', '117','118']
    };
        
    $scope.fnEditComponent = function (event) {
      var index = $(event.currentTarget).index('.container.droparea > section'),
          deleteElem = $(event.target).hasClass('widgetClose');
      if (!deleteElem) {
        if (index > -1) {
          $scope.currentComp = $scope.appDtls.comp_array[index];
          $scope.currentBlock = 3;
          $timeout(function () {
            if (!$scope.currentComp.loaded) {
              $scope.fetchProds($scope.currentComp);
            }
          });
        }
        else {
          $scope.resetCurrentComp();
        }

        resetCurrent();
        $scope.dataStore.subcats = [];
      }
      else {
        $scope.dataLoaded = false;
        $scope.screenVisible = 'hidden';
        $scope.appDtls.comp_array.splice(index, 1);
        $scope.resetCurrentComp();
        $timeout(function () {
          $scope.$broadcast('updateSimulator');
        });
      }
      
    };
    
    $scope.fnShowField = function (type) {
      if (fieldsMap[type].indexOf($scope.currentComp.comp_type) > -1) {
        return true;
      }
      else {
        return false;
      }
    };
    
    $scope.imagesCountMap = {
      '106': {
        count: 1,
        container: '.full-banner',
        hasHeading: false
      },
      '107': {
        count: 2,
        container: '.half-white-four',
        hasHeading: true
      },
      '108': {
        count: 4,
        container: '.half-white-four',
        hasHeading: true
      },
      '111': {
        count: 5,
        container: '',
        hasHeading: false
      }
    };
    
    $scope.addMoreImages = function () {
      var curr_type = $scope.currentComp.comp_type;
      $scope.currentComp.elements.element_array.push({
        itemheading: $scope.imagesCountMap[curr_type].hasHeading ? compMap[curr_type].heading : '',
        itemdesc: $scope.imagesCountMap[curr_type].hasHeading ? compMap[curr_type].subheading : '',
        imageurl: compMap[curr_type].dummyImg,
        image_height: '',
        image_width: '',
        itemid: '',
        imagename: compMap[curr_type].dummyImg
      });
      
      if (curr_type === '111') {
        $timeout (function () {
          sliders.reloadSlider();
        });
      }
    };
    
    function saveScreenshot (canvas, redirectUrl) {
      var screenshot = canvas.toDataURL("image/png");
      
      hitApi('saveScreenshot', {
        data: screenshot,
        token: gl_token,
        hasid: gl_app_id,
        is_ajax: 2
      }, function (data) {
        window.location.href = redirectUrl;
      });
    }
    
    $scope.postAppDtls = function (redirectUrl) {
	
		if ($scope.nameYourAppForm.$invalid && angular.element(nameYourAppForm).parents('.name-your-app').hasClass('active') == true)
		{
			if ($scope.nameYourAppForm.app_name.$invalid) {
				$scope.showPopup('app-name');
			}
			else if ($scope.nameYourAppForm.app_type.$invalid) {
				$scope.showPopup('app-type');
			}
			else if ($scope.nameYourAppForm.app_currency.$invalid) {
				$scope.showPopup('app-currency');
			}
			return false;
		}
		else if($scope.update_widgets_details.$valid && angular.element(update_widgets_details).parents('.update-card-dtls').hasClass('active') == true)
		{
			if(!angular.element(update_widgets_details).find('.other_than_banner').hasClass('ng-hide'))
			{
				if($scope.update_widgets_details.itemcatagory.$modelValue.itemid == '' || $scope.update_widgets_details.itemcatagory.$modelValue.itemid == 0)
				{
					$scope.showPopup('category-only');
					return false;
				}
				
				if(($scope.update_widgets_details.itemcatagory.$modelValue.itemid > 0) && angular.element(update_widgets_details).find('.other_than_banner .cat_dependent_prod').hasClass('ng-hide') == false)
				{
					if($scope.update_widgets_details.itemprod.$modelValue.itemid == '' || $scope.update_widgets_details.itemprod.$modelValue.itemid == 0)
					{
						$scope.showPopup('products-only');
						return false;
					}
				}
			}
		}
		else if($scope.retail_additional_features.$valid && angular.element(retail_additional_features).parents('.additional-features').hasClass('active') == true)
		{
			if(($scope.appDtls.feedback_dtls.is_feedback == true) && ($scope.appDtls.feedback_dtls.feedback_email == false))
			{
				$scope.showPopup('add-feedback-email');
				return false;
			}
			/* else if($scope.retail_additional_features.feedback_email.$error)
			{
				$scope.showPopup('add-valid-feedback-email');
				return false;
			} */
			
			if(($scope.appDtls.contact_details.is_contactus == true) && ($scope.appDtls.contact_details.contact_email == false))
			{
				$scope.showPopup('add-contact-email');
				return false;
			}
			
			if(($scope.appDtls.tnc.is_tnc == true) && ($scope.appDtls.tnc.tnc_email == false))
			{
				$scope.showPopup('add-tnc-link');
				return false;
			}
			
			if(($scope.appDtls.order_dtls.is_order == true) && ($scope.appDtls.logo_dtls.imageurl == false))
			{
				$scope.showPopup('add-order-logo');
				return false;
			}
			
			if(($scope.appDtls.order_dtls.is_order == true) && ($scope.appDtls.order_dtls.package == false))
			{
				$scope.showPopup('add-order-package');
				return false;
			}
			
			if(($scope.appDtls.order_dtls.is_order == true) && ($scope.appDtls.order_dtls.orderconfirm_email == false))
			{
				$scope.showPopup('add-order-email');
				return false;
			}
		}
      
		$scope.resetCurrentComp();
		$scope.dataLoaded = false;

		$scope.appDtls.screen_properties.background_color = app_bg_color;
		$scope.appDtls.screen_properties.font_color       = text_color;
		$scope.appDtls.screen_properties.discount_color   = discount_color;
      
      hitApi('postAppData', {
        app_data: {
          'screen_data': $scope.appDtls
        },
        author_id: gl_author_id
      }, function (data) {
        
        if (data.app_id) {
          $scope.appDtls.screen_properties.app_id = data.app_id;
          gl_app_id = data.app_id;
          
          if (!redirectUrl) {            
            getAppData();
          }
          else {
			
			if(typeof sliders != 'undefined'){
            sliders.goToSlide(0);
            sliders.stopAuto();
            $timeout (function () {
              html2canvas($('#content-1'), {
                onrendered: function (canvas) {
                  saveScreenshot(canvas, redirectUrl);
                }
              });
            }, 100);
          }
		  else{
			       $timeout (function () {
              html2canvas($('#content-1'), {
                onrendered: function (canvas) {
                  saveScreenshot(canvas, redirectUrl);
                }
              });
            }, 100); 
		  }
		  }
        }
        else if (data.msg_code) {
          $scope.dataLoaded = true;
          $scope.showPopup(data.msg_code);
        }
      });
    };
    
    $scope.checkAppName = function () {
      
      if ($scope.appDtls.screen_properties.title) {
        
        hitApi('checkAppName', {
          app_name: $scope.appDtls.screen_properties.title,
          app_id: $scope.appDtls.screen_properties.app_id || '',
          author_id: gl_author_id
        }, function (data) {
          
          if (data.response && !data.response.app_name_available) {
            $scope.showPopup('app-name-exists');
            $scope.appDtls.screen_properties.title = '';
          }

        });
      }
    };
    
    $scope.updateCroppedImage = function (e) {
      
      if ($scope.nameYourAppForm.$invalid) {
        if ($scope.nameYourAppForm.app_name.$invalid) {
          $scope.showPopup('app-name');
        }
        else if ($scope.nameYourAppForm.app_type.$invalid) {
          $scope.showPopup('app-type');
        }
        else if ($scope.nameYourAppForm.app_currency.$invalid) {
          $scope.showPopup('app-currency');
        }
        $('#cropper-example-2-modal').modal('hide');
        return false;
      }
      
      var data = $(e.currentTarget).data(),
        $imageCurrent = $('#cropper-example-2-modal img#modalimage1').attr('src'),
        $target,
        result;

      if ($imageCurrent.length === 0) {
        $('#cropper-example-2-modal').modal('hide');
        $scope.showPopup('no-image');
        return false;
      }

      if (!$image.data('cropper')) {
        return;
      }
      
      $scope.dataLoaded = false;

      if (data.method) {
        data = $.extend({}, data); // Clone a new one

        if (typeof data.target !== 'undefined') {
          $target = $(data.target);
        }

        result = $image.cropper(data.method, {
          'width': $scope.currentComp.dummy_dtls.width,
          'height': $scope.currentComp.dummy_dtls.height
        });

        if (data.method === 'getCroppedCanvas') {
          
          $('#canvasShow').css("display", "none").html(result);
          
          $(".modal").css('display', 'none');
          $(".modal-backdrop.in").css({
            'display': 'none',
            'opacity': '0'
          });

          var filename = $("#filenamest").val();
          var filetype = $("#filetypest").val();

          $.ajax({
            type: "POST",
            url: "login.php",
            data: "check=login",
            success: function (response) {
              if (response) {
                var res = response.split("##");

                external_app_id = res[1];
                external_user_id = res[0];
                var out = res[2];
                
                if (!res[0]) {
                  $scope.dataLoaded = true;
                  $scope.showPopup('501');
                  $scope.$apply();
                }

                if (out == 0) {
                  var app_id = res[1];
                  var autherId = res[0];
                  if (app_id != '') {
                    $("#app_id").val(app_id);
                    $("#author_id").val(autherId);

                    var canvas = $('#canvasShow > canvas')[0];

                    var dataURL1 = canvas.toDataURL(filetype, 0.7); // 70% compressed version of the original image
                    
                    var start = new Date().getTime();
                    $.ajax({
                      type: "POST",
                      url: "imageload.php",
                      data: "image=" + dataURL1 + "&imgname=" + "panelimage/" + start + filename,
                      success: function (response) {
                        if (response) {
                          var newresponse = $.parseJSON(response);
                          var imagename = newresponse.imageurl,
                              tempImgName = imagename.split('/');
                          
                          tempImgName = tempImgName[tempImgName.length - 1];
                          
                          var imagePath = imagename;

                          $("#filenamest").val('');
                          $("#filetypest").val('');
                          $(".modal-backdrop.in").css({
                            'display': 'none',
                            'opacity': '0'
                          });
                          $(".modal").css('display', 'none');
                          
                          var curr_type = $scope.currentComp.comp_type;
                          
                          if (!curr_type) {
                            $scope.currentComp.imageurl = imagePath;
                            $scope.currentComp.imagename = tempImgName;
                          }
                          else {
                            if ($scope.currentComp.elements.element_array[$scope.currImgIndex]) {
                              $scope.currentComp.elements.element_array[$scope.currImgIndex].imageurl = imagePath;
                              $scope.currentComp.elements.element_array[$scope.currImgIndex].imagename = tempImgName;
                            }
                            else {
                              $scope.currentComp.elements.element_array[$scope.currImgIndex] = {
                                itemheading: '',
                                itemdesc: '',
                                imageurl: imagePath,
                                image_height: '',
                                image_width: '',
                                itemid: '',
                                imagename: tempImgName
                              };
                            }
                          }
                          $scope.$apply();
                          
                          $('#cropper-example-2-modal').modal('hide');
                          
                          if (curr_type && $scope.imagesCountMap[curr_type].container) {                            
                            $('.container.droparea > section[data-uid="' + $scope.currentComp.id + '"] ' + $scope.imagesCountMap[curr_type].container + ' > li:eq(' + $scope.currImgIndex + ') > img').attr('src', imagePath);
                          }

                          var dfd = $.Deferred();
                          var tmpImg = new Image();
                          tmpImg.src = imagePath;
                          tmpImg.onload = function () {
                            dfd.resolve();
                            if (sliders) {
                              //sliders.reloadSlider();
							  $timeout (function () {
								  sliders.reloadShow();
								});
                            }
                            $scope.dataLoaded = true;
                            $scope.$apply();
                          };
                          
                        } else {
                          $scope.showPopup('501');
                          $scope.dataLoaded = true;
                          $scope.$apply();
                        }
                      },
                      error: function () {
                        $scope.showPopup('500');
                      }
                    });
                    $(".editbrowse_img").replaceWith('<input type="file" class="editbrowse_img">');
                  } else {
                    // Do Nothing
                  }
                } else {
                  window.location = 'userprofile.php';
                  return false;
                }
              }
            }
          });
        }

        if ($.isPlainObject(result) && $target) {
          try {
            $target.val(JSON.stringify(result));
          } catch (e) {
            console.log(e.message);
          }
        }

      }
      
    };
    
    $scope.sendToOpencart = function () {
      
      if ($scope.nameYourAppForm.$invalid) {
        if ($scope.nameYourAppForm.app_name.$invalid) {
          $scope.showPopup('app-name');
        }
        else if ($scope.nameYourAppForm.app_type.$invalid) {
          $scope.showPopup('app-type');
        }
        else if ($scope.nameYourAppForm.app_currency.$invalid) {
          $scope.showPopup('app-currency');
        }
        return false;
      }
      
      $scope.dataLoaded = false;
      var email = $('#login_email').val();
      var app_id = $scope.appDtls.screen_properties.app_id;
      var app_name = $scope.appDtls.screen_properties.title;
      var curr_id = $scope.appDtls.defaultcurrency;
      $.ajax({
        type: "POST",
        url: "ajax.php",
        data: "type=catlog_app_login_check&email="+email,
        success: function (response) {
          if (response == 'fails') {
            $("#screenoverlay").css("display", "none");
            window.location = "seller-profile.php?app_id=" + app_id + "&app_name=" + app_name + "&curr_id=" + curr_id;
          } else {
            var res = response.split("##");
            password = res[1];
            email = res[0];
            $("#screenoverlay").css("display", "none");
            /*if ($("#action").val() == 'add') {
              $(".popup_container").css("display", "block");
              $(".confirm_name").css("display", "block");
              $(".confirm_name_form p").text("App created successfully");
            }
            if ($("#action").val() == 'edit') {
              $(".popup_container").css("display", "block");
              $(".confirm_name").css("display", "block");
              $(".confirm_name_form p").text("App updated successfully");
            }*/

            window.location = catalogueUrl + "catalogue/admin/index.php?route=common/login&email=" + email + "&password=" + password + "&app_id=" + app_id + "&app_name=" + app_name + "&curr_id=" + curr_id;
          }
        },
      });
    }
    
    $scope.sendToOpencartCatProd = function (redirectType) {
      var email = $('#login_email').val();
      var app_id = $scope.appDtls.screen_properties.app_id;
      var app_name = $scope.appDtls.screen_properties.title;
      var curr_id = $scope.appDtls.defaultcurrency;
	  //var redirectType = $(this).attr('title');
	  
      $.ajax({
        type: "POST",
        url: "ajax.php",
        data: "type=catlog_app_login_check&email="+email,
        success: function (response) {
          if (response == 'fails') {
            $("#screenoverlay").css("display", "none");
            window.location = "seller-profile.php?app_id=" + app_id + "&app_name=" + app_name + "&curr_id=" + curr_id;
          } else {
            var res = response.split("##");
            password = res[1];
            email = res[0];
            $("#screenoverlay").css("display", "none");

            window.location = catalogueUrl + "catalogue/admin/index.php?route=common/login&email=" + email + "&password=" + password + "&app_id=" + app_id + "&app_name=" + app_name + "&curr_id=" + curr_id+'&redirect='+redirectType;
          }
        },
      });
    }
    
    $scope.$on('screenReady', function (e, data) {
      var element = $('.container.droparea');
      $timeout(function () {
        $(element).find('.slider1').bxSlider({
          slideWidth: 84,
          minSlides: 3,
          maxSlides: 3,
          infiniteLoop: false,
          hideControlOnEnd: true,
          pager: false,
          slideMargin: 2
        });
        
        if(element.find('.bxslider').length) {
          sliders = element.find('.bxslider').bxSlider({
            auto: true,
            pause: 2000,
            onSliderLoad: function () {
              $(element).shapeshift({
                colWidth: 139.5,
                minColumns: 2,
                enableDrag: true
              })
              .on('ss-added', function (e, elem) {
                var i = $(elem).index();
                $scope.reOrderElems('add', elem, i, element);
              })
              .on('ss-rearranged', function (e, elem) {
                var i = $(elem).index();
                $scope.reOrderElems('arrange', elem, i);
              });

              $("#content-1").mCustomScrollbar('destroy');
              $("#content-1").mCustomScrollbar({
                autoHideScrollbar: true,
                scrollInertia: 200
              });

              $scope.screenVisible = 'visible';
              $scope.dataLoaded = true;
              $scope.$apply();
            }
          });
        }
        else {
          $timeout(function () {
            $(element).shapeshift({
              colWidth: 139.5,
              minColumns: 2,
              enableDrag: true
            })
            .on('ss-added', function (e, elem) {
              var i = $(elem).index();
              $scope.reOrderElems('add', elem, i, element);
            })
            .on('ss-rearranged', function (e, elem) {
              var i = $(elem).index();
              $scope.reOrderElems('arrange', elem, i);
            });                

            $("#content-1").mCustomScrollbar('destroy');
            $("#content-1").mCustomScrollbar({
              autoHideScrollbar: true,
              scrollInertia: 200
            });

            $scope.screenVisible = 'visible';
            $scope.dataLoaded = true;
            $scope.$apply();

          }, 100);
        }
      });
    });

    $scope.$on('compReady', function (e, data) {
      
      $scope.compsVisible = 'hidden';

      $('.clones').html(data);
      $compile($('.clones').contents())($scope);

      $timeout (function () {
        $('.clones .slider1').bxSlider({
          slideWidth: 84,
          minSlides: 3,
          maxSlides: 3,
          infiniteLoop: false,
          hideControlOnEnd: true,
          pager: false,
          slideMargin: 2
        });
        if ($('.clones .bxslider').length) {
          $('.clones .bxslider').bxSlider({
            auto: true,
            pause: 2000,
            onSliderLoad: function () {
              $('.clones').shapeshift({
                dragClone: true,
                enableCrossDrop: false,
                colWidth: 139.5,
                minColumns: 2,
                enableTrash: true
              });

              $scope.compsVisible = 'visible';
              $scope.$apply();
            }
          });
        }
        else {
          $timeout(function () {
            $('.clones').shapeshift({
              dragClone: true,
              enableCrossDrop: false,
              colWidth: 139.5,
              minColumns: 2,
              enableTrash: true
            });
            
            $scope.compsVisible = 'visible';
            $scope.$apply();
          }, 100);
        }
      });

    });

  }]);

  angular.element(document).ready(function () {
    angular.bootstrap(document, ['catalogueApp']);
  });

});


function navOpenFunction(e) {
  e.stopPropagation();
  $(".mobile nav").css("height", "430px");
  $(".mobile nav ul").css("height", "370px");

  if ($(".mobile nav .navfooter").length == 0) {

    $(".mobile nav").append("<div class='navfooter'><p >Powered By Instappy.</p><p >© 2016 Instappy.com All Rights Reserved</p></div>")
  }
  if ($(".mobile nav ul").hasClass("mCustomScrollbar") == false) {
    $(".mobile nav ul").mCustomScrollbar({
      scrollInertia: 200
    });
  }

  if ($(".mobile nav").hasClass('show_div')) {
    $("#content-1").mCustomScrollbar({
      scrollInertia: 200
    });
  } else {
    $("#content-1").mCustomScrollbar('destroy');
  }
  $(".mobile nav").css("background-color", $(".mobile .theme_head").css("background-color"));
  $(".navItemEdit").show();
  $("nav").toggleClass("show_div");
  $(".overlay").fadeToggle("fast");
  $("[class*='widget'] span[class^='icon'], .contact_card span[class^='icon']").css("color", $(".mobile .theme_head").css("background-color"));
  if ($('.previewEditArea p.edit').hasClass('active')) {
    $('.mobile nav ul div:first div:first').sortable({
      disabled: false,
      items: "li:not(.unsortable)"
    });
    $('.mobile nav ul li a').css('cursor', 'n-resize');
  } else {
    $('.mobile nav ul div:first div:first').sortable({
      disabled: true
    });
    $('.mobile nav ul li a').css('cursor', 'pointer');
  }
  $('.mobile nav ul li[data-link=s1]').addClass('unsortable');
  $('.mobile nav ul li[data-link=s2]').addClass('unsortable');
}