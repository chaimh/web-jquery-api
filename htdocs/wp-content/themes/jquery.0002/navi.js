$(document).ready(function() {
  
  
  $.navi  = {
    eventClass: '.keynav',
    focusClass: '.withfocus',
    //  CACHE INDICES
    last: 0,
    crnt: 0,
    //  REF CURRENTLY VISIBLE LIST ITEMS
    visibles:  function () {
      return jQuery.makeArray($( $.navi.eventClass + ':visible'));
    },
    reset: function () {
      $.navi.last = 0;
      $.navi.crnt = 0;
    },
    //  ADAPTED AND HEAVILY REFACTORED FROM BOAZ SENDER's $.keynav.activate()
    goto: function() {
      if ( $($.navi.focusClass).children().find('a').length ) {
        location.href = $($.navi.focusClass).children().find('a').attr('href');
        return;
      }
      location.href = $($.navi.focusClass).children('a').attr('href');
    },
    //  ADAPTED AND REFACTORED FROM http://www.eyecon.ro/interface/
    getPos: function (_element)  {
      var l = 0, t  = 0, w = $.navi.int($.css(_element,'width')), h = $.navi.int($.css(_element,'height'));

      while (_element.offsetParent){
          l += _element.offsetLeft + (_element.currentStyle ? $.navi.int(_element.currentStyle.borderLeftWidth):0);
          t += _element.offsetTop  + (_element.currentStyle ? $.navi.int(_element.currentStyle.borderTopWidth):0);
          _element = _element.offsetParent;
      }

      l += _element.offsetLeft + (_element.currentStyle ? $.navi.int(_element.currentStyle.borderLeftWidth):0);
      t += _element.offsetTop  + (_element.currentStyle ? $.navi.int(_element.currentStyle.borderTopWidth):0);

      var cx = Math.round(t+(h/2)), cy = Math.round(l+(w/2));

      return {x:l, y:t, w:w, h:h, cx:cx, cy:cy};
    },
    //  ADAPTED FROM http://www.eyecon.ro/interface/
    int: function (v) {
      v = parseInt(v);
      return isNaN(v) ? 0 : v;
    },
    //  ADAPTED AND REFACTORED FROM jquery.keynav.js
    getClosest: function(_current,_quad) {
      var closest, od = 1000000, nd = 0, found = false;

      for(i=0;i<_quad.length;i++) {
        var _element  = _quad[i];
        nd = Math.sqrt(Math.pow(_current.pos.cx-_element.pos.cx,2)+Math.pow(_current.pos.cy-_element.pos.cy,2));


        if(nd < od) {
          closest = _element;
          od      = nd;
          found   = true;
        }
      }

      return closest;
    },
    //  ADAPTED AND REFACTORED FROM jquery.keynav.js
    quad: function(_current, fQuad) {

      var $visibles = $.navi.visibles(), 
          _visible  = '',
          quad      = [];

      for( var i = 0, _len  = $visibles.length; i <  _len; i++ ) {
        _visible = $visibles[i];

        if(_current == _visible) {
          continue;
        }

        _current.pos  = $.navi.getPos(_current);
        _visible.pos  = $.navi.getPos(_visible);


        if(  fQuad( (_current.pos.cx - _visible.pos.cx),(_current.pos.cy - _visible.pos.cy)) ) {
          quad.push(_visible);
        }
      }
      return quad;
    }        
  };
  
  //  HACK-JOB.
  $('#categories ul > li')
    .addClass($.navi.eventClass.substr(1));
  
  //  SETUP THE SEARCH BOX
  $('#jq-primarySearch')
    .addClass($.navi.eventClass.substr(1))
      .liveUpdate('#method-list')
        .focus();
  
  //  MORE SEARCH BOX SET UP
  if ( location.href.indexOf("category") > -1 || location.pathname === "/" ) {
    $('#jq-primarySearch')
      .focus();
  }

  //  FOCUS ON THE INPUT SEARCH FIELD WILL RESET THE $.navi CACHE INDICES    
  $($.navi.eventClass + ':first')
    .bind('focus', function () {
      $.navi.reset();
    });

  //  LET'S JQUERY MANAGE THE VISUAL STATE OF AN ELEMENT WITH FOCUS
  $('li' + $.navi.eventClass)
    .focus(function () {
      $(this).addClass($.navi.focusClass.substr(1));
    });      


  $(document)
    .bind('keydown', function(event) {

    //  A BIT HACKISH, BUT LIMITS THE KEYS WE CARE ABOUT
    if ( [ 37, 38, 39, 40, 9, 13, 56 ].indexOf(event.keyCode) == -1 ) {
      return;
    }

    //  PREVENT BROWSER FOR MOVING DOWN THE PAGE
    event.preventDefault();

    //  AQUIRE CURRENT VISIBLE LIST ITEMS
    var $visibles = $.navi.visibles();

    //  SET UP SOME PRIVATE PARTS
    var _quadFn   = null,
        _dir      = null,
        _key      = event.keyCode,
        _crnt     = $.navi.crnt,//$visibles.indexOf( event.originalTarget ),
        _next     = 0; 

    //  A FEW MORE PRIVATE PARTS
    var _triggerQuad,
        _triggerElement, 
        _triggerIndex,
        _targetScrollTop;


    //  _quadFn = fn() DEFINITIONS FROM jquery.keynav.js
    switch(_key) {
      case 13: 
        $.navi.goto();
        return;
      case 37:
        //  LEFT
        _quadFn = function (dx,dy) { 
                    if((dy >= 0) && (Math.abs(dx) - dy) <= 0)
                      return true;  
                    else
                      return false;
                   };
        break;
      case 39: 
        //  RIGHT
        _quadFn = function (dx,dy) { 
                    if((dy <= 0) && (Math.abs(dx) + dy) <= 0)
                      return true;  
                    else
                      return false;
                   };
        break;
      case 38: 
        //  UP
        _quadFn = function (dx,dy) { 
                    if((dx >= 0) && (Math.abs(dy) - dx) <= 0)
                      return true;  
                    else
                      return false;
                   };

        _dir    = 'up';
        break;
      case 40: 
        //  DOWN
        _quadFn = function (dx,dy) { 
                    if((dx <= 0) && (Math.abs(dy) + dx) <= 0)
                      return true;  
                    else
                      return false;
                   };

        _dir    = 'down';
        break;
      case 9: 

        if ( event.shiftKey ) {
          //  UP  [tab]
          _quadFn = function (dx,dy) { 
                      if((dx >= 0) && (Math.abs(dy) - dx) <= 0)
                        return true;  
                      else
                        return false;
                   };
          _dir    = 'up';
        } else {
          //  DOWN [tab]
          _quadFn = function (dx,dy) { 
                      if((dx <= 0) && (Math.abs(dy) + dx) <= 0)
                        return true;  
                      else
                        return false;
                     };
          _dir    = 'down';
        }

        break;
      case 56: 
        _triggerIndex = 0;
        break;
    }

    $($.navi.eventClass)
      .removeClass($.navi.focusClass.substr(1));

    //  IF THE EVENT HAS AN ASSOCIATED _quadFn          
    if ( _quadFn ) {
      _triggerQuad      = $.navi.quad( $visibles[$.navi.crnt],  _quadFn);          
      _triggerElement   = $.navi.getClosest($visibles[$.navi.crnt], _triggerQuad), 
      _triggerIndex     = $visibles.indexOf(_triggerElement);
    }

    //  FORCE CHANGE FOR INDEX -1
    if ( _triggerIndex < 0 ) {
      _triggerIndex = 0;
    }

    //  STUPID OVERRIDE FOR FIRST ELEMENT BEING "MISSED" ... ONLY SOMETIMES.
    if ( _crnt == 0 ) {
      _triggerIndex = 1;
    }

    //  BOAZ SAID IT SHOULD STOP AT THE BOTTOM OF THE METHOD LIST
    if ( _dir == 'down' && $($visibles[_crnt]).hasClass('hentry') && !$($visibles[_triggerIndex]).hasClass('hentry') ) {
      _triggerIndex = _crnt;
    }

    //  PULL THE TRIGGER        
    $($visibles[_crnt]).trigger('blur');
    $($visibles[_triggerIndex]).trigger('focus');

    //  STORE THE LAST AND CURRENT INDICES
    $.navi.last = $.navi.crnt;
    $.navi.crnt = _triggerIndex;


    //  KEEP CURRENT FOCUSED ELEMENT IN VIEWPORT
    //  CONCEPT CONTRIBUTED BY BOAZ SENDER
    if ( [ 'up', 'down' ].indexOf(_dir) >= 0 && _triggerIndex > 0 ) {
      var currentHeight = $($visibles[$.navi.crnt]).outerHeight(), offset = $($visibles[$.navi.crnt]).offset();
      _targetScrollTop  = Math.round( offset.top - currentHeight - ($(window).height()/3) );
      //$('html,body').animate({ scrollTop: _targetScrollTop }, 100);
      $('html,body').scrollTop(_targetScrollTop);
    }
  });      
  
  //awful rush job. <3
});