(function ($) {
  $(document).ready(function () {

    $(".hamburger_menu").on("click", function (e) {
      e.preventDefault();
      $(".slide-bar").toggleClass("show");
      $("body").addClass("on-side");
      $('.body-overlay').addClass('active');
      $(this).addClass('active');
    });
  
    $(".close-mobile-menu > a").on("click", function (e) {
      e.preventDefault();
      $(".slide-bar").removeClass("show");
      $("body").removeClass("on-side");
      $('.body-overlay').removeClass('active');
      $('.hamburger_menu').removeClass('active');
    });
    

    {
      function animateElements() {
        $('.progressbar').each(function () {
          var elementPos = $(this).offset().top;
          var topOfWindow = $(window).scrollTop();
          var percent = $(this).find('.circle').attr('data-percent');
          var percentage = parseInt(percent, 10) / parseInt(100, 10);
          var animate = $(this).data('animate');
          if (elementPos < topOfWindow + $(window).height() - 10 && !animate) {
            $(this).data('animate', true);
            $(this).find('.circle').circleProgress({
              startAngle: -Math.PI / 2,
              value: percent / 100,
              size: 150,
              thickness: 10,
              emptyFill: "#E7E6F1",
              fill: {
                color: '#0A0571'
              }
            }).on('circle-animation-progress', function (event, progress, stepValue) {
              $(this).find('div').text((stepValue*100).toFixed() + "%");
            }).stop();
          }
        });
      }
    
      // Show animated elements
      animateElements();
      $(window).scroll(animateElements);
    };
     // sticky header active
     if ($("#header").length > 0) {
      $(window).on("scroll", function (event) {
        var scroll = $(window).scrollTop();
        if (scroll < 1) {
          $("#header").removeClass("sticky");
        } else {
          $("#header").addClass("sticky");
        }
      });
    }

          //Aos animation active
            AOS.init({
              offset: 0,
              duration: 600,
              easing: "ease-in-out",
              anchorPlacement: "top-bottom",
              disable: false,
              once: true,
            });


            //Video poppup
            if ($(".play-btn").length > 0) {
              $(".play-btn").magnificPopup({
                type: "iframe",
              });
            };

              // page-progress
              var progressPath = document.querySelector(".progress-wrap path");
              var pathLength = progressPath.getTotalLength();
              progressPath.style.transition = progressPath.style.WebkitTransition =
                "none";
              progressPath.style.strokeDasharray = pathLength + " " + pathLength;
              progressPath.style.strokeDashoffset = pathLength;
              progressPath.getBoundingClientRect();
              progressPath.style.transition = progressPath.style.WebkitTransition =
                "stroke-dashoffset 10ms linear";
              var updateProgress = function () {
                var scroll = $(window).scrollTop();
                var height = $(document).height() - $(window).height();
                var progress = pathLength - (scroll * pathLength) / height;
                progressPath.style.strokeDashoffset = progress;
              };
              updateProgress();
              $(window).scroll(updateProgress);
              var offset = 50;
              var duration = 550;
              jQuery(window).on("scroll", function () {
                if (jQuery(this).scrollTop() > offset) {
                  jQuery(".progress-wrap").addClass("active-progress");
                } else {
                  jQuery(".progress-wrap").removeClass("active-progress");
                }
              });
              jQuery(".progress-wrap").on("click", function (event) {
                event.preventDefault();
                jQuery("html, body").animate({ scrollTop: 0 }, duration);
                return false;
              });

              //product colors
              const colors = $(".accordion1 .accordion-item");
              colors.on("click", function () {
                $(".accordion1 .accordion-item").removeClass("active");
                $(this).addClass("active");
              });

              //tes1 active
              const tes1 = $(".controls li");
              tes1.on("click", function () {
                $(".controls li").removeClass("active");
                $(this).addClass("active");
              });

              $("#ce-toggle1").click(function (event) {
                $(".plan-toggle-wrap1").toggleClass("active");
              });
              
              $("#ce-toggle1").change(function () {
                if ($(this).is(":checked")) {
                  $(".tab-content #yearly1").hide();
                  $(".tab-content #monthly1").show();
                } else {
                  $(".tab-content #yearly1").show();
                  $(".tab-content #monthly1").hide();
                }
              });

            //hero 1 slider
            $(".hero1-slider").slick({
              margin: "30",
              slidesToShow: 1,
              arrows: false,
              centerMode: true,
              dots: true,
              loop: true,
              centerMode: false,
              draggable: true,
              autoplay: true,
              autoplaySpeed: 4000,
              fade: true,
              fadeSpeed: 1000,
              responsive: [
                {
                  breakpoint: 769,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 1,
                  },
                },
                {
                  breakpoint: 480,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 1,
                  },
                },
              ],
            });

            //hero 5 slider
            $(".hero5-slider").slick({
              margin: "30",
              slidesToShow: 1,
              arrows: false,
              centerMode: true,
              dots: true,
              loop: true,
              centerMode: false,
              draggable: true,
              autoplay: true,
              autoplaySpeed: 4000,
              fade: true,
              fadeSpeed: 1000,
              responsive: [
                {
                  breakpoint: 769,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 1,
                  },
                },
                {
                  breakpoint: 480,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 1,
                  },
                },
              ],
            });

            //testimonial 1 slider
            $(".tes1-slider-all").slick({
              margin: "30",
              slidesToShow: 2,
              arrows: false,
              dots: true,
              infinite: true,
              centerMode: false,
              draggable: true,
              autoplay: true,
              autoplaySpeed: 3000,
              fade: false,
              fadeSpeed: 1000,
              responsive: [
                {
                  breakpoint: 769,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 1,
                  },
                },
                {
                  breakpoint: 480,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 1,
                  },
                },
              ],
            });

            //service 2 slider
            $(".service2-slider").slick({
              margin: "30",
              slidesToShow: 4,
              arrows: false,
              centerMode: true,
              dots: true,
              loop: true,
              centerMode: false,
              draggable: true,
              autoplay: true,
              autoplaySpeed: 4000,
              fade: false,
              fadeSpeed: 1000,
              responsive: [
                {
                  breakpoint: 769,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 2,
                  },
                },
                {
                  breakpoint: 480,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 1,
                  },
                },
              ],
            });

            //testimonial 2 slider
            $(".tes2-slider").slick({
              margin: "30",
              slidesToShow: 1,
              arrows: true,
              prevArrow: $(".tes2-prev-arrow"),
              nextArrow: $(".tes2-next-arrow"),
              centerMode: true,
              dots: false,
              loop: true,
              centerMode: false,
              draggable: true,
              autoplay: true,
              autoplaySpeed: 4000,
              fade: false,
              fadeSpeed: 1000,
              responsive: [
                {
                  breakpoint: 769,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 1,
                  },
                },
                {
                  breakpoint: 480,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 1,
                  },
                },
              ],
            });


            //service 4 slider
            $(".service4-slider-all").slick({
              margin: "30",
              slidesToShow: 3,
              arrows: true,
              prevArrow: $(".service4-prev-arrow"),
              nextArrow: $(".service4-next-arrow"),
              centerMode: true,
              dots: false,
              loop: true,
              centerMode: false,
              draggable: true,
              autoplay: true,
              autoplaySpeed: 4000,
              fade: false,
              fadeSpeed: 1000,
              responsive: [
                {
                  breakpoint: 769,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 2,
                  },
                },
                {
                  breakpoint: 480,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 1,
                  },
                },
              ],
            });

            //testimonial 4 slider
            $(".tes4-slider-all").slick({
              margin: "30",
              slidesToShow: 3,
              arrows: true,
              prevArrow: $(".tes4-prev-arrow"),
              nextArrow: $(".tes4-next-arrow"),
              centerMode: true,
              dots: true,
              loop: true,
              centerMode: false,
              draggable: true,
              autoplay: true,
              autoplaySpeed: 4000,
              fade: false,
              fadeSpeed: 1000,
              responsive: [
                {
                  breakpoint: 769,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 2,
                  },
                },
                {
                  breakpoint: 480,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 1,
                  },
                },
              ],
            });

            //testimonial 5 slider
            $(".tes5-slider-all").slick({
              margin: "30",
              slidesToShow: 3,
              arrows: false,
              centerMode: true,
              dots: true,
              loop: true,
              centerMode: false,
              draggable: true,
              autoplay: true,
              autoplaySpeed: 4000,
              fade: false,
              fadeSpeed: 1000,
              responsive: [
                {
                  breakpoint: 769,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 2,
                  },
                },
                {
                  breakpoint: 480,
                  settings: {
                    arrows: false,
                    centerMode: false,
                    centerPadding: "40px",
                    slidesToShow: 1,
                  },
                },
              ],
            });

            $('.hero5-logo-slider').owlCarousel({
              loop:true,
              margin:10,
              autoplay:true,
              autoplayTimeout:1000,
              autoplayHoverPause:true,
              dots: false,
              nav: false,
              responsive:{
                0:{
                  items:1
                },
                600:{
                  items:3
                },
                1000:{
                  items:5
                }
              }
            });


            //testimonial slider
     $(".testimonial-nav").slick({
      slidesToShow: 5,
      slidesToScroll: 1,
      asNavFor: ".slider-testimonial",
      dots: false,
      loop: true,
      centerMode: true,
      focusOnSelect: true,
      arrows: false,
    });

    $(".slider-testimonial").slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: true,
      prevArrow: $(".testimonial-prev-arrow"),
      nextArrow: $(".testimonial-next-arrow"),
      fade: true,
      loop: true,
      asNavFor: ".testimonial-nav",
    });

          
  });



    //preloader
    $(window).on("load", function (event) {
      setTimeout(function () {
        $(".preloader-sec").fadeToggle();
      }, 500);
    });

    	/* Text Effect Animation */
	if ($('.text-anime-style-1').length) {
		let staggerAmount 	= 0.05,
			translateXValue = 0,
			delayValue 		= 0.5,
		   animatedTextElements = document.querySelectorAll('.text-anime-style-1');
		
		animatedTextElements.forEach((element) => {
			let animationSplitText = new SplitText(element, { type: "chars, words" });
				gsap.from(animationSplitText.words, {
				duration: 1,
				delay: delayValue,
				x: 20,
				autoAlpha: 0,
				stagger: staggerAmount,
				scrollTrigger: { trigger: element, start: "top 85%" },
				});
		});		
	}
	
	if ($('.text-anime-style-2').length) {				
		let	 staggerAmount 		= 0.05,
			 translateXValue	= 20,
			 delayValue 		= 0.5,
			 easeType 			= "power2.out",
			 animatedTextElements = document.querySelectorAll('.text-anime-style-2');
		
		animatedTextElements.forEach((element) => {
			let animationSplitText = new SplitText(element, { type: "chars, words" });
				gsap.from(animationSplitText.chars, {
					duration: 1,
					delay: delayValue,
					x: translateXValue,
					autoAlpha: 0,
					stagger: staggerAmount,
					ease: easeType,
					scrollTrigger: { trigger: element, start: "top 85%"},
				});
		});		
	}
	
	if ($('.text-anime-style-3').length) {		
		let	animatedTextElements = document.querySelectorAll('.text-anime-style-3');
		
		 animatedTextElements.forEach((element) => {
			//Reset if needed
			if (element.animation) {
				element.animation.progress(1).kill();
				element.split.revert();
			}

			element.split = new SplitText(element, {
				type: "lines,words,chars",
				linesClass: "split-line",
			});
			gsap.set(element, { perspective: 400 });

			gsap.set(element.split.chars, {
				opacity: 0,
				x: "50",
			});

			element.animation = gsap.to(element.split.chars, {
				scrollTrigger: { trigger: element,	start: "top 95%" },
				x: "0",
				y: "0",
				rotateX: "0",
				opacity: 1,
				duration: 1,
				ease: Back.easeOut,
				stagger: 0.02,
			});
		});		
	}

  $('select').niceSelect();

  

  // btn_theme
  $(function() {  
    $('.theme-btn4')
      .on('mouseenter', function(e) {
              var parentOffset = $(this).offset(),
                relX = e.pageX - parentOffset.left,
                relY = e.pageY - parentOffset.top;
              $(this).find('span').css({top:relY, left:relX})
      })
      .on('mouseout', function(e) {
              var parentOffset = $(this).offset(),
                relX = e.pageX - parentOffset.left,
                relY = e.pageY - parentOffset.top;
          $(this).find('span').css({top:relY, left:relX})
      });
  });

   // btn_theme
   $(function() {  
    $('.theme-btn5')
      .on('mouseenter', function(e) {
              var parentOffset = $(this).offset(),
                relX = e.pageX - parentOffset.left,
                relY = e.pageY - parentOffset.top;
              $(this).find('span').css({top:relY, left:relX})
      })
      .on('mouseout', function(e) {
              var parentOffset = $(this).offset(),
                relX = e.pageX - parentOffset.left,
                relY = e.pageY - parentOffset.top;
          $(this).find('span').css({top:relY, left:relX})
      });
  });

  // btn_theme
  $(function() {  
    $('.theme-btn6')
      .on('mouseenter', function(e) {
              var parentOffset = $(this).offset(),
                relX = e.pageX - parentOffset.left,
                relY = e.pageY - parentOffset.top;
              $(this).find('span').css({top:relY, left:relX})
      })
      .on('mouseout', function(e) {
              var parentOffset = $(this).offset(),
                relX = e.pageX - parentOffset.left,
                relY = e.pageY - parentOffset.top;
          $(this).find('span').css({top:relY, left:relX})
      });
  });

  // Dynamic repositioning for third-level submenus to keep them in viewport
  $('.main-menu-ex1 li.has-dropdown').on('mouseenter', function() {
    const submenu = $(this).children('ul.sub-menu');
    if (submenu.length === 0) return;

    submenu.css({
      'top': '0px'
    });

    const rect = this.getBoundingClientRect();
    const submenuHeight = submenu.outerHeight();
    const windowHeight = $(window).height();

    const bottomSpace = windowHeight - rect.top;
    if (submenuHeight > bottomSpace) {
      let shiftUp = submenuHeight - bottomSpace + 20;
      const maxShift = rect.top - 20;
      if (shiftUp > maxShift) {
        shiftUp = maxShift;
      }
      submenu.css('top', '-' + shiftUp + 'px');
    }
  });

  $('.main-menu-ex1 li.has-dropdown').on('mouseleave', function() {
    const submenu = $(this).children('ul.sub-menu');
    if (submenu.length > 0) {
      submenu.css('top', '0px');
    }
  });
            
})(jQuery);

    

