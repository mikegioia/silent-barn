/**
 * Main application JS
 */
jQuery( function( $ ) {
// Page object
var MainPage = {

    // set the search form
    search: function () {
        var $form = $( '#search-form' )
          , $query = $( '#search-query' )
          , $submit = $( '#search-submit' );

        $query.on( 'keypress', function ( e ) {
            if ( e.keyCode == 13 ) {
                submitSearch();
                e.preventDefault();
                return false;
            }
        });

        $submit.on( 'click', function() {
            submitSearch();
            return false;
        });

        function submitSearch () {
            var siteUrl = $query.data( 'siteurl' )
              , url = 'https://www.google.com/#q=site:' + siteUrl + '+'
              , query = $query.val().replace( ' ', '+' )
              , action = url + query;
            window.location = action;
        }
    },

    // set up homepage filmroll
    carousel: function () {
        if ( ! $( '#hero-carousel' ).length ) {
            return;
        }

        var filmRoll = new FilmRoll({
            animation: 1000,
            container: '#hero-carousel',
            scroll: true,
            interval: 15000,
            start_index: 0,
            height: 420,
            prev: '#carousel-prev',
            next: '#carousel-next'
        });
    },

    // sticky nav on scroll
    stickyNav: function () {
        var navOffset = 122,
            logoOffset = 110,
            $document = $( document ),
            $window = $( window ),
            $body = $( 'body' ),
            pageNavClass = ( $( '#page-nav-wrap' ).length ) ? ' pagenav' : '';

        $( document ).bind( 'ready scroll', function () {
            var docScroll = $document.scrollTop(),
                windowWidth = $window.width();

            if ( windowWidth <= 320 ) {
                $body.removeClass( 'sticky' + pageNavClass )
                    .removeClass( 'shiftlogo' );
                return;
            }

            if ( docScroll >= navOffset ) {
                $body.addClass( 'sticky' + pageNavClass )
                    .addClass( 'shiftlogo' );
            }
            else if ( docScroll >= logoOffset ) {
                $body.removeClass( 'sticky' + pageNavClass )
                    .addClass( 'shiftlogo' );
            }
            else {
                $body.removeClass( 'sticky' + pageNavClass )
                    .removeClass( 'shiftlogo' );
            }
        });
    },

    // set up clndr
    calendar: function () {
        var $fullClndr = $( '#full-clndr' );
        if ( ! $fullClndr.length ) {
            return;
        }
        // stores whether we've requested a month's events
        var monthLog = [],
            $overlay = $fullClndr.find( '.clndr-overlay' );
        monthLog[ moment().format( "MM/01/YYYY" ) ] = true;
        // ajax call to request events
        var getMonthEvents = function ( date ) {
            if ( _.has( monthLog, date ) ) {
                return true;
            }

            $overlay.fadeIn( 250 );
            $.ajax({
                url: window.Environment.apiPath + 'events/getbymonth',
                data: {
                    date: date
                },
                dataType: 'json',
                success: function ( response ) {
                    $overlay.hide();
                    if ( ! _.has( response, 'data' )
                        || ! _.has( response.data, 'events' ) ) {
                        return false;
                    }
                    monthLog[ date ] = response.data.events;
                    clndr.addEvents( response.data.events );
                },
                type: 'post'
            });
        };
        // store clndr reference
        var clndr = $( '#full-clndr' ).clndr({
            template: $( '#full-clndr-template' ).html(),
            events: calendarEvents,
            clickEvents: {
                // when the month changes, get the events
                onMonthChange: function( month ) {
                    getMonthEvents( month.format( 'MM/DD/YYYY' ) );
                }
            }
        });
    },

    // load more upcoming events/archives
    loadMore: function () {
        var $loadMore = $( '#load-more' ),
            $loadMoreButton = $( '#load-more-button' );

        if ( ! $loadMoreButton.length ) {
            return;
        }

        // get our event variables
        var paging = {
                url: $loadMoreButton.data( 'url' ),
                limit: $loadMoreButton.data( 'limit' ),
                offset: $loadMoreButton.data( 'offset' )
            },
            $container = $( $loadMoreButton.data( 'container' ) );
        // event handler
        $loadMoreButton.on( 'click', function () {
            $.ajax({
                url: window.Environment.apiPath + paging.url,
                dataType: 'json',
                type: 'post',
                data: {
                    offset: paging.offset
                },
                success: function ( response ) {
                    // error handle
                    if ( ! _.has( response, 'data' ) ) {
                        return;
                    }
                    // if we recieved less items than the limit, hide loadMore
                    if ( response.data.count < paging.limit
                        || ! response.data.html ) {
                        $loadMore.hide();
                    }
                    // insert items before loadMore
                    $html = $( response.data.html );
                    $container.append( $html);
                    $( window ).scrollTo( $html.offset().top - 85, 250 );
                    paging.offset += response.data.count;
                }
            })
        });
    },

    // show more links
    showMore: function () {
        $( 'a.show-more' ).on( 'click', function () {
            var $this = $( this ),
                $target = $( $this.data( 'target' ) );
            $target.show();
            $this.parent().hide();
        });
    },

    // chefs page
    community: function () {
        var $members = $( '#members' );
        if ( ! $members.length ) {
            return;
        }

        // get the elements
        var $callout = $( '#member-callout' ),
            $overlay = $( '#member-overlay' ),
            $page = $( '#page' );
        // on member click show the callout/overlay
        $members.on( 'click', '.member:not(".callout")', function () {
            var $this = $( this ),
                index = $this.data( 'index' );
            // remove callout from all members
            $members.find( '.member' ).removeClass( 'callout' );
            // if this index is 4th or 5th, change display mode
            if ( index >= 4 ) {
                $callout.addClass( 'right-align' );
            }
            else {
                $callout.removeClass( 'right-align' );
            }
            // detach the callout, fade it in, fade overlay in
            $callout.detach().prependTo( $this ).fadeIn( 250 );
            $overlay.fadeIn( 250 );
            // apply callout to this member
            $this.addClass( 'callout' );
            // set the name/bio
            $callout.find( '.name' ).text( $this.find( '.img-wrap .name' ).text() );
            $callout.find( '.bio' ).html( $this.find( '.more-info .bio' ).html() );
        });
        // hide everything when overlay is clicked
        $overlay.on( 'click', function () {
            $overlay.fadeOut( 250 );
            $callout.hide();
            $callout.find( 'div' ).html( '' );
            $members.find( '.member' ).removeClass( 'callout' );
        });
    },

    // spaces pages
    spaces: function () {
        var $spaces = $( '#spaces' );
        if ( ! $spaces.length ) {
            return;
        }

        $( '.space[rel*=modal]' ).modal({
            top : 60,
            closeButton: ".modal-close"
        });
    }

}; // Page object

MainPage.search();
MainPage.carousel();
MainPage.stickyNav();
MainPage.calendar();
MainPage.loadMore();
MainPage.showMore();
MainPage.community();
MainPage.spaces();

});